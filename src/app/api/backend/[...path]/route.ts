import { NextRequest, NextResponse } from 'next/server';
import bcrypt from 'bcryptjs';
import type { ResultSetHeader, RowDataPacket } from 'mysql2';
import { db, one, rows, transaction } from '@/lib/db';
import { currentUser, SESSION_COOKIE, siteHost, type AuthUser } from '@/lib/backend-auth';
import { signSessionJwt } from '@/lib/session-jwt';

export const runtime = 'nodejs';
export const dynamic = 'force-dynamic';
const SESSION_SECONDS = 60 * 60 * 24 * 7;

const ok = (data: unknown = null, extra: Record<string, unknown> = {}) => NextResponse.json({ status: true, data, ...extra }, { headers: { 'Cache-Control': 'no-store' } });
const fail = (message: string, status = 400, extra: Record<string, unknown> = {}) => NextResponse.json({ status: false, success: false, message, ...extra }, { status });
const number = (value: unknown, fallback = 0) => Number.isFinite(Number(value)) ? Number(value) : fallback;
const clean = (value: unknown) => String(value ?? '').trim();

async function body(request: NextRequest): Promise<Record<string, unknown>> {
  if (request.headers.get('content-type')?.includes('multipart/form-data')) {
    const form = await request.formData(); return Object.fromEntries(form.entries());
  }
  return request.json().catch(() => ({}));
}

function pageParams(request: NextRequest) {
  return { page: Math.max(1, number(request.nextUrl.searchParams.get('page'), 1)), perPage: Math.min(100, Math.max(10, number(request.nextUrl.searchParams.get('per_page'), 20))) };
}

function paginated(data: unknown[], total: number, page: number, perPage: number) {
  return { data, current_page: page, per_page: perPage, total, last_page: Math.max(1, Math.ceil(total / perPage)), from: total ? (page - 1) * perPage + 1 : null, to: total ? Math.min(page * perPage, total) : null };
}

async function requireUser(request: NextRequest, admin = false): Promise<AuthUser | NextResponse> {
  const user = await currentUser(request);
  if (!user) return fail('Phiên đăng nhập đã hết hạn.', 401);
  if (admin && user.role !== 'admin') return fail('Bạn không có quyền quản trị.', 403);
  return user;
}

async function auth(request: NextRequest, path: string, method: string) {
  if (path === 'auth/api/logout') { const response = ok(null, { message: 'Đăng xuất thành công' }); response.cookies.delete(SESSION_COOKIE); return response; }
  if (path === 'auth/api/me') { const user = await requireUser(request); if (user instanceof NextResponse) return user; const spent = await one<RowDataPacket>('SELECT COALESCE(SUM(total),0) total_spent FROM orders WHERE user_id=?', [user.id]); return NextResponse.json({ status: true, user: { ...user, total_spent: number(spent?.total_spent) } }); }
  if (!['auth/api/login', 'auth/api/register'].includes(path) || method !== 'POST') return null;
  const input = await body(request); const username = clean(input.username); const password = clean(input.password); const domain = siteHost(request);
  if (path.endsWith('register')) {
    const email = clean(input.email).toLowerCase();
    if (username.length < 6 || password.length < 6 || !/^\S+@\S+\.\S+$/.test(email)) return fail('Thông tin đăng ký không hợp lệ.', 422);
    if (password !== clean(input.password_confirmation)) return fail('Xác nhận mật khẩu không khớp.', 422);
    const exists = await one<RowDataPacket>('SELECT id FROM users WHERE username=? OR email=? LIMIT 1', [username, email]); if (exists) return fail('Tên đăng nhập hoặc email đã tồn tại.', 422);
    const hash = await bcrypt.hash(password, 12); const apiKey = crypto.randomUUID().replaceAll('-', '');
    const [created] = await db.execute<ResultSetHeader>('INSERT INTO users (email,username,password,role,level,balance,total_deposit,timezone,language,currency,status,api_key,domain,created_at,updated_at) VALUES (?,?,?,\'user\',\'member\',0,0,\'Asia/Ho_Chi_Minh\',\'vi\',\'VND\',\'active\',?,?,NOW(),NOW())', [email, username, hash, apiKey, domain]);
    input.id = created.insertId;
  }
  const user = await one<AuthUser>('SELECT * FROM users WHERE (username=? OR email=?) AND (domain=? OR domain IS NULL OR domain=\'\') LIMIT 1', [username, username.toLowerCase(), domain]);
  if (!user || !(await bcrypt.compare(password, user.password))) return fail('Tài khoản hoặc mật khẩu không đúng.', 401);
  if (user.status !== 'active') return fail('Tài khoản đã bị khóa.', 403);
  if (!user.domain) await db.execute('UPDATE users SET domain=? WHERE id=?', [domain, user.id]);
  await db.execute('UPDATE users SET last_login=NOW() WHERE id=?', [user.id]);
  const session = await signSessionJwt({ sub: String(user.id), role: user.role, domain, username: user.username }, SESSION_SECONDS);
  const safeUser = { id: user.id, username: user.username, email: user.email, role: user.role, domain, balance: user.balance, currency: user.currency, level: user.level };
  const response = NextResponse.json({ status: true, message: path.endsWith('register') ? 'Đăng ký thành công' : 'Đăng nhập thành công', user: safeUser });
  response.cookies.set(SESSION_COOKIE, session, { httpOnly: true, secure: process.env.NODE_ENV === 'production', sameSite: 'lax', path: '/', maxAge: SESSION_SECONDS }); return response;
}

async function client(request: NextRequest, path: string, method: string) {
  const domain = siteHost(request);
  if (path === 'client/config' && method === 'GET') {
    const config = await one<RowDataPacket>('SELECT * FROM configs WHERE domain=? LIMIT 1', [domain]) || await one<RowDataPacket>('SELECT * FROM configs ORDER BY id LIMIT 1');
    const orderCount = await one<RowDataPacket>('SELECT COUNT(*) total FROM orders');
    const widgets = await rows<RowDataPacket>('SELECT id,name,url,image FROM contact_widgets WHERE is_active=1 ORDER BY sort_order,id').catch(() => []);
    return ok({ ...(config || {}), title: config?.title || 'SMM Panel', currency: config?.currency || 'VND', total_orders_system: number(orderCount?.total), landing_page: config?.landing_page || 'default', contact_widgets: widgets });
  }
  if (path === 'client/platforms' && method === 'GET') return ok(await rows('SELECT * FROM platforms WHERE status=\'active\' ORDER BY sort_order,id'));
  if (path === 'client/categories' && method === 'GET') return ok(await rows('SELECT c.*,JSON_OBJECT(\'id\',p.id,\'name\',p.name,\'code\',p.code,\'icon\',p.icon) platform FROM categories c JOIN platforms p ON p.id=c.platform_id WHERE c.status=\'active\' ORDER BY c.sort_order,c.id'));
  if (path === 'client/currencies' && method === 'GET') return ok(await rows('SELECT * FROM currencies WHERE status=\'active\' ORDER BY id'));
  const user = await requireUser(request); if (user instanceof NextResponse) return user;
  if (path === 'client/services' && method === 'GET') return ok(await rows('SELECT id,category_id,name,description,note,rate,min,max,type,average_time,attributes,refill,cancel,dripfeed,sort_order FROM services WHERE status=\'active\' AND (domain=? OR domain IS NULL OR domain=\'\') ORDER BY sort_order,id', [domain]), { currency: user.currency || 'VND' });
  if (path === 'client/profile' && method === 'GET') return ok(user);
  if (path === 'client/profile' && method === 'PUT') { const input=await body(request); await db.execute('UPDATE users SET email=?,phone=?,timezone=?,language=?,updated_at=NOW() WHERE id=?', [clean(input.email)||user.email, clean(input.phone)||null, clean(input.timezone)||'Asia/Ho_Chi_Minh', clean(input.language)||'vi', user.id]); return ok(null,{message:'Đã cập nhật hồ sơ.'}); }
  if (path === 'client/change-password' && method === 'POST') { const input=await body(request); const full=await one<RowDataPacket>('SELECT password FROM users WHERE id=?',[user.id]); if(!full||!await bcrypt.compare(clean(input.current_password),full.password)) return fail('Mật khẩu hiện tại không đúng.',422); if(clean(input.password).length<6) return fail('Mật khẩu mới tối thiểu 6 ký tự.',422); await db.execute('UPDATE users SET password=?,updated_at=NOW() WHERE id=?',[await bcrypt.hash(clean(input.password),12),user.id]); return ok(null,{message:'Đổi mật khẩu thành công.'}); }
  if (path === 'client/orders' && method === 'GET') {
    const {page,perPage}=pageParams(request), status=request.nextUrl.searchParams.get('status'), search=request.nextUrl.searchParams.get('search'); const where=['o.user_id=?']; const values:unknown[]=[user.id];
    if(status&&status!=='all'){where.push('o.status=?');values.push(status)} if(search){where.push(request.nextUrl.searchParams.get('search_by')==='link'?'o.link LIKE ?':'o.id LIKE ?');values.push(`%${search}%`)}
    const total=number((await one<RowDataPacket>(`SELECT COUNT(*) total FROM orders o WHERE ${where.join(' AND ')}`,values))?.total); const data=await rows(`SELECT o.*,JSON_OBJECT('id',s.id,'name',s.name,'rate',s.rate,'category_id',s.category_id) service FROM orders o LEFT JOIN services s ON s.id=o.service_id WHERE ${where.join(' AND ')} ORDER BY o.id DESC LIMIT ? OFFSET ?`,[...values,perPage,(page-1)*perPage]);
    const countRows=await rows<RowDataPacket>('SELECT status,COUNT(*) total FROM orders WHERE user_id=? GROUP BY status',[user.id]); const counts:Record<string,number>={all:countRows.reduce((a,x)=>a+number(x.total),0)}; countRows.forEach(x=>counts[x.status]=number(x.total)); return ok(paginated(data,total,page,perPage),{counts});
  }
  const orderMatch=path.match(/^client\/orders\/(\d+)$/); if(orderMatch&&method==='GET'){const order=await one<RowDataPacket>("SELECT o.*,JSON_OBJECT('id',s.id,'name',s.name,'rate',s.rate,'category_id',s.category_id) service FROM orders o LEFT JOIN services s ON s.id=o.service_id WHERE o.id=? AND o.user_id=?",[number(orderMatch[1]),user.id]); return order?ok(order):fail('Không tìm thấy đơn hàng.',404)}
  if (path === 'client/orders' && method === 'POST') {
    const input=await body(request), serviceId=number(input.service_id), quantity=number(input.quantity), link=clean(input.link); if(!serviceId||!quantity||!link) return fail('Thiếu dịch vụ, liên kết hoặc số lượng.',422);
    try { const result=await transaction(async connection=>{ const [userRows]=await connection.query<RowDataPacket[]>('SELECT * FROM users WHERE id=? FOR UPDATE',[user.id]); const locked=userRows[0]; const [serviceRows]=await connection.query<RowDataPacket[]>('SELECT * FROM services WHERE id=? AND status=\'active\' AND (domain=? OR domain IS NULL OR domain=\'\') LIMIT 1',[serviceId,domain]); const service=serviceRows[0]; if(!service) throw new Error('Dịch vụ không tồn tại.'); if(quantity<number(service.min)||quantity>number(service.max)) throw new Error(`Số lượng phải từ ${service.min} đến ${service.max}.`); const charge=Math.round(number(service.rate)*quantity)/1000; if(number(locked.balance)<charge) throw new Error('Số dư không đủ.'); const before=number(locked.balance),after=before-charge; const [created]=await connection.execute<ResultSetHeader>('INSERT INTO orders (user_id,service_id,category_id,link,quantity,rate,payment,total,start_count,remains,status,provider,domain,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,0,?,\'Pending\',?,?,NOW(),NOW())',[user.id,service.id,service.category_id,link,quantity,service.rate,charge,charge,quantity,service.provider||null,domain]); await connection.execute('UPDATE users SET balance=? WHERE id=?',[after,user.id]); await connection.execute('INSERT INTO transactions (user_id,transaction_code,type,balance_before,balance_after,amount,description,status,domain,created_at,updated_at) VALUES (?,?,?,?,?,?,?,\'Success\',?,NOW(),NOW())',[user.id,`ORDER_${created.insertId}`,'Order',before,after,charge,`Thanh toán đơn hàng #${created.insertId}`,domain]); return {order_id:created.insertId,payment:charge,balance:after}; }); return NextResponse.json({success:true,status:true,message:'Đặt đơn thành công.',data:result}); } catch(error){return fail(error instanceof Error?error.message:'Không thể tạo đơn.',422)}
  }
  return null;
}

async function admin(request: NextRequest, path: string, method: string) {
  if(!path.startsWith('admin/')) return null; const user=await requireUser(request,true); if(user instanceof NextResponse)return user; const domain=siteHost(request);
  if(path==='admin/dashboard'&&method==='GET'){const [usersCount,ordersCount,revenue,balance,recent]=await Promise.all([one<RowDataPacket>('SELECT COUNT(*) total FROM users WHERE domain=?',[domain]),one<RowDataPacket>('SELECT COUNT(*) total FROM orders WHERE domain=?',[domain]),one<RowDataPacket>('SELECT COALESCE(SUM(total),0) total FROM orders WHERE domain=?',[domain]),one<RowDataPacket>('SELECT COALESCE(SUM(balance),0) total FROM users WHERE domain=?',[domain]),rows("SELECT o.id,o.link,o.quantity,o.total,o.status,o.created_at,JSON_OBJECT('id',u.id,'username',u.username) user,JSON_OBJECT('id',s.id,'name',s.name) service FROM orders o JOIN users u ON u.id=o.user_id LEFT JOIN services s ON s.id=o.service_id WHERE o.domain=? ORDER BY o.id DESC LIMIT 8",[domain])]);return ok({stats:{users:number(usersCount?.total),orders:number(ordersCount?.total),revenue:number(revenue?.total),user_balance:number(balance?.total)},recent_orders:recent,order_statuses:{}})}
  if(path==='admin/users'&&method==='GET'){const {page,perPage}=pageParams(request),search=clean(request.nextUrl.searchParams.get('search')),where=['domain=?'],values:unknown[]=[domain];if(search){where.push('(username LIKE ? OR email LIKE ?)');values.push(`%${search}%`,`%${search}%`)}const total=number((await one<RowDataPacket>(`SELECT COUNT(*) total FROM users WHERE ${where.join(' AND ')}`,values))?.total);const data=await rows(`SELECT id,username,email,phone,balance,total_deposit,level,role,status,currency,last_login,created_at FROM users WHERE ${where.join(' AND ')} ORDER BY id DESC LIMIT ? OFFSET ?`,[...values,perPage,(page-1)*perPage]);return ok(paginated(data,total,page,perPage),{meta:{total_users:total}})}
  if(path==='admin/services'&&method==='GET'){const {page,perPage}=pageParams(request);const total=number((await one<RowDataPacket>('SELECT COUNT(*) total FROM services WHERE domain=? OR domain IS NULL OR domain=\'\'',[domain]))?.total);const data=await rows("SELECT s.*,JSON_OBJECT('id',c.id,'name',c.name,'platform_id',c.platform_id) category FROM services s LEFT JOIN categories c ON c.id=s.category_id WHERE s.domain=? OR s.domain IS NULL OR s.domain='' ORDER BY s.sort_order,s.id DESC LIMIT ? OFFSET ?",[domain,perPage,(page-1)*perPage]);return ok(paginated(data,total,page,perPage),{meta:{total}})}
  if(path==='admin/orders'&&method==='GET'){const {page,perPage}=pageParams(request),status=request.nextUrl.searchParams.get('status'),where=['o.domain=?'],values:unknown[]=[domain];if(status){where.push('o.status=?');values.push(status)}const total=number((await one<RowDataPacket>(`SELECT COUNT(*) total FROM orders o WHERE ${where.join(' AND ')}`,values))?.total);const data=await rows(`SELECT o.*,JSON_OBJECT('id',u.id,'username',u.username,'email',u.email) user,JSON_OBJECT('id',s.id,'name',s.name,'rate_original',s.rate_original,'category_id',s.category_id) service FROM orders o JOIN users u ON u.id=o.user_id LEFT JOIN services s ON s.id=o.service_id WHERE ${where.join(' AND ')} ORDER BY o.id DESC LIMIT ? OFFSET ?`,[...values,perPage,(page-1)*perPage]);return ok(paginated(data,total,page,perPage),{meta:{counts:{total}}})}
  const updateOrder=path.match(/^admin\/orders\/(\d+)$/);if(updateOrder&&method==='PUT'){const input=await body(request),status=clean(input.status);await transaction(async c=>{const [found]=await c.query<RowDataPacket[]>('SELECT * FROM orders WHERE id=? AND domain=? FOR UPDATE',[number(updateOrder[1]),domain]);const order=found[0];if(!order)throw new Error('Không tìm thấy đơn.');await c.execute('UPDATE orders SET status=?,start_count=?,remains=?,updated_at=NOW() WHERE id=?',[status,number(input.start_count),number(input.remains),order.id]);if(order.status!=='Partial'&&status==='Partial'&&number(input.remains)>0){const refund=number(order.rate)*number(input.remains)/1000;await c.execute('UPDATE users SET balance=balance+? WHERE id=?',[refund,order.user_id])}});return ok(null,{message:'Đã cập nhật đơn hàng.'})}
  return null;
}

async function handle(request: NextRequest, context: { params: Promise<{ path: string[] }> }) {
  try { const path=(await context.params).path.join('/'),method=request.method; return await auth(request,path,method) || await client(request,path,method) || await admin(request,path,method) || fail('API Next.js chưa hỗ trợ endpoint này.',404,{path}); } catch(error){console.error(error);return fail(process.env.NODE_ENV==='development'&&error instanceof Error?error.message:'Lỗi máy chủ.',500)}
}
export const GET=handle;export const POST=handle;export const PUT=handle;export const PATCH=handle;export const DELETE=handle;
