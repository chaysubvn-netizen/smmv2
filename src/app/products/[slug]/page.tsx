'use client';

import { useCallback, useEffect, useState } from 'react';
import Link from 'next/link';
import { useParams, useRouter } from 'next/navigation';
import { Alert, App, Button, Card, Empty, Input, InputNumber, Modal, Rate, Result, Select, Skeleton, Space, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { AppstoreOutlined, ArrowLeftOutlined, ArrowRightOutlined, CheckCircleOutlined, DownOutlined, GiftOutlined, InfoCircleOutlined, MinusOutlined, PlusOutlined, ShoppingCartOutlined, StarFilled, UserOutlined } from '@ant-design/icons';
import dayjs from 'dayjs';
import ClientLayout from '@/components/ClientLayout';
import api from '@/lib/axios';
import baseStyles from '../detail.module.css';
import extraStyles from '../detail-extra.module.css';

const styles = Object.keys({ ...baseStyles, ...extraStyles }).reduce<Record<string, string>>((classes, key) => {
  classes[key] = [baseStyles[key as keyof typeof baseStyles], extraStyles[key as keyof typeof extraStyles]].filter(Boolean).join(' ');
  return classes;
}, {});

const { Title } = Typography;
const { TextArea } = Input;
const asset = (path?: string | null) => path ? (/^https?:/.test(path) ? path : `${(process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '')}${path.startsWith('/') ? '' : '/'}${path}`) : '';
type Product = { id: number; name: string; slug: string; image?: string; price: number | string; price_display: string; description?: string; short_description?: string; data_input?: string; available_stock_count: number; order_products_sum_quantity?: number; category?: { id: number; name: string } };
type Review = { id: number; rating: number; comment: string; created_at: string; user?: { username: string }; order_product?: { id: number; quantity: number }; replies?: Array<{ id: number; comment: string; created_at: string; user?: { username: string } }> };
type RatingSummary = { average: number; count: number; stars: Record<number, number> };
type PurchasedOrder = { id: number; quantity: number; created_at: string };

export default function ProductDetailPage() {
  const { modal } = App.useApp();
  const params = useParams<{ slug: string }>();
  const router = useRouter();
  const [product, setProduct] = useState<Product | null>(null);
  const [related, setRelated] = useState<Product[]>([]);
  const [reviews, setReviews] = useState<Review[]>([]);
  const [summary, setSummary] = useState<RatingSummary>({ average: 0, count: 0, stars: { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 } });
  const [purchasedOrders, setPurchasedOrders] = useState<PurchasedOrder[]>([]);
  const [reviewOpen, setReviewOpen] = useState(false);
  const [reviewOrder, setReviewOrder] = useState<number>();
  const [reviewRating, setReviewRating] = useState(5);
  const [reviewComment, setReviewComment] = useState('');
  const [reviewing, setReviewing] = useState(false);
  const [loading, setLoading] = useState(true);
  const [buying, setBuying] = useState(false);
  const [notFound, setNotFound] = useState(false);
  const [quantity, setQuantity] = useState(1);
  const [dataInput, setDataInput] = useState('');
  const [coupon, setCoupon] = useState('');
  const [appliedCoupon, setAppliedCoupon] = useState('');
  const [discountAmount, setDiscountAmount] = useState(0);
  const [couponLoading, setCouponLoading] = useState(false);

  const loadProduct = useCallback(async () => {
    setLoading(true);
    try {
      const response = await api.get(`/client/products/${encodeURIComponent(params.slug)}`);
      setProduct(response.data.data); setRelated(response.data.related || []); setReviews(response.data.reviews || []);
      setSummary(response.data.rating_summary || { average: 0, count: 0, stars: { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 } });
      setPurchasedOrders(response.data.purchased_orders || []);
    } catch (error: unknown) {
      const detail = error as { response?: { status?: number } };
      if (detail.response?.status === 404) setNotFound(true); else message.error('Không thể tải sản phẩm.');
    } finally { setLoading(false); }
  }, [params.slug]);
  useEffect(() => {
    const timer = window.setTimeout(() => { void loadProduct(); }, 0);
    return () => window.clearTimeout(timer);
  }, [loadProduct]);

  const submitReview = async () => {
    if (!product || !reviewOrder || !reviewComment.trim()) return message.warning('Vui lòng chọn đơn hàng và nhập nhận xét.');
    setReviewing(true);
    try {
      const response = await api.post(`/client/products/${product.id}/reviews`, { order_product_id: reviewOrder, rating: reviewRating, comment: reviewComment.trim() });
      message.success(response.data.message); setReviewOpen(false); setReviewComment(''); setReviewOrder(undefined); await loadProduct();
    } catch (error: unknown) { const detail = error as { response?: { data?: { message?: string } } }; message.error(detail.response?.data?.message || 'Không thể gửi đánh giá.'); }
    finally { setReviewing(false); }
  };
  const mask = (name?: string) => name ? (name.length < 3 ? `${name[0]}***` : `${name.slice(0, 2)}***${name.slice(-1)}`) : 'Khách hàng';
  const subtotal = Number(product?.price || 0) * quantity;
  const total = Math.max(0, subtotal - discountAmount);
  const money = (value: number) => `${Math.round(value).toLocaleString('vi-VN')}đ`;
  const applyCoupon = async () => {
    const code = coupon.trim().toUpperCase();
    if (!code) return message.warning('Vui lòng nhập mã giảm giá.');
    setCouponLoading(true);
    try {
      const response = await api.post('/check-discount', { code, amount: subtotal });
      if (!response.data.success) throw new Error(response.data.message || 'Mã giảm giá không hợp lệ.');
      setCoupon(code);
      setAppliedCoupon(code);
      setDiscountAmount(Number(response.data.discount || 0));
      message.success(response.data.message || 'Áp dụng mã giảm giá thành công.');
    } catch (error: unknown) {
      const detail = error as { message?: string; response?: { data?: { message?: string } } };
      setAppliedCoupon('');
      setDiscountAmount(0);
      message.error(detail.response?.data?.message || detail.message || 'Không thể kiểm tra mã giảm giá.');
    } finally { setCouponLoading(false); }
  };
  const updateQuantity = (value: number) => {
    setQuantity(value);
    setAppliedCoupon('');
    setDiscountAmount(0);
  };
  const buy = () => {
    if (!product) return;
    if (product.data_input && !dataInput.trim()) { message.warning(`Vui lòng nhập ${product.data_input}`); return; }
    modal.confirm({ width: 520, title: 'Xác nhận thanh toán', icon: <ShoppingCartOutlined />, content: <div className={styles.confirm}><p><strong>{product.name}</strong></p><p>Số lượng: <b>{quantity}</b></p><p>Tạm tính: <b>{money(subtotal)}</b></p>{appliedCoupon && <><p>Mã giảm giá: <code>{appliedCoupon}</code></p><p>Được giảm: <b>-{money(discountAmount)}</b></p></>}<p>Tổng thanh toán: <strong>{money(total)}</strong></p><Alert type="info" showIcon title="Mã giảm giá sẽ được máy chủ xác nhận lại trước khi trừ số dư." /></div>, okText: 'Thanh toán ngay', cancelText: 'Quay lại', onOk: async () => { setBuying(true); try { const response = await api.post(`/client/products/${product.id}/purchase`, { quantity, data_input: dataInput.trim() || undefined, coupon: appliedCoupon || undefined }); message.success(response.data.message || 'Đặt hàng thành công.'); router.push('/products/orders'); } catch (error: unknown) { const detail = error as { response?: { data?: { message?: string } } }; message.error(detail.response?.data?.message || 'Không thể mua sản phẩm.'); throw error; } finally { setBuying(false); } } });
  };

  if (loading) return <ClientLayout><div className={styles.loading}><Skeleton active paragraph={{ rows: 10 }} /></div></ClientLayout>;
  if (notFound || !product) return <ClientLayout><Result status="404" title="Không tìm thấy sản phẩm" extra={<Link href="/products"><Button type="primary">Về cửa hàng</Button></Link>} /></ClientLayout>;

  return <ClientLayout><main className={styles.page}>
    <section className={styles.hero}><Link className={styles.back} href="/products"><ArrowLeftOutlined /> Cửa hàng</Link><div className={styles.heroInner}><div className={styles.heroImage}>{product.image ? <img src={asset(product.image)} alt={product.name} /> : <GiftOutlined />}</div><div className={styles.heroInfo}><span className={styles.eyebrow}>SẢN PHẨM GIAO TỰ ĐỘNG</span><Title>{product.name}</Title><div className={styles.rating}><Rate className={styles.heroRate} disabled allowHalf value={summary.average} /><strong>{summary.average.toFixed(1)}</strong><small>({summary.count} đánh giá)</small></div><div className={styles.heroTags}><span><GiftOutlined />{product.category?.name || 'Sản phẩm số'}</span><span><ShoppingCartOutlined />Đã bán {Number(product.order_products_sum_quantity || 0).toLocaleString('vi-VN')}</span><span><CheckCircleOutlined />Còn {product.available_stock_count}</span></div></div></div></section>
    <section className={styles.purchaseGrid}><div className={styles.contentColumn}><div className={styles.selected}><span className={styles.selectedCheck}><CheckCircleOutlined /></span>{product.image ? <img src={asset(product.image)} alt="" /> : <GiftOutlined />}<strong>{product.name}</strong><em>Giao ngay</em><b>{product.price_display}</b></div><Card className={styles.packageCard}><div className={styles.sectionTitle}><span className={styles.packageIcon}><GiftOutlined /></span><div><small>CHI TIẾT GÓI</small><strong>{product.name}</strong></div><Button shape="circle" type="text" icon={<DownOutlined />} /></div><div className={styles.packageText} dangerouslySetInnerHTML={{ __html: product.short_description || 'Gói này chưa có mô tả.' }} /></Card><Card className={styles.description}><div className={styles.sectionTitle}><span className={styles.descriptionIcon}><InfoCircleOutlined /></span><div><small>GIỚI THIỆU</small><strong>Mô tả sản phẩm</strong></div></div><div className={styles.descriptionBody} dangerouslySetInnerHTML={{ __html: product.description || 'Sản phẩm chưa có mô tả chi tiết.' }} /><section className={styles.reviewCard}><div className={styles.reviewTitle}><Space><StarFilled className={styles.starActive} />Đánh giá sản phẩm</Space><Space>{summary.count > 0 && <span className={styles.reviewCount}>{summary.count} đánh giá</span>}<Button type="primary" ghost disabled={!purchasedOrders.length} onClick={() => setReviewOpen(true)}>Viết đánh giá</Button></Space></div><div className={styles.ratingSummary}><div className={styles.ratingScore}><strong>{summary.average.toFixed(1)}</strong><Rate disabled value={summary.average} /><span>{summary.count} đánh giá</span></div><div className={styles.ratingBars}>{[5, 4, 3, 2, 1].map(star => { const count = summary.stars?.[star] || 0; const percent = summary.count ? count / summary.count * 100 : 0; return <div key={star}><span>{star} <StarFilled /></span><i><b style={{ width: `${percent}%` }} /></i><em>{count}</em></div>; })}</div></div><div className={styles.reviewList}>{reviews.length ? reviews.map(review => <article key={review.id} className={styles.review}><div className={styles.avatar}><UserOutlined /></div><div><div className={styles.reviewHead}><div><strong>{mask(review.user?.username)}</strong><span className={styles.bought}><CheckCircleOutlined /> Đã mua hàng</span></div><span><Rate disabled value={review.rating} /> <small>{dayjs(review.created_at).format('DD/MM/YYYY')}</small></span></div>{review.order_product && <div className={styles.reviewProduct}><ShoppingCartOutlined /> {product.name} × {review.order_product.quantity}</div>}<p>{review.comment}</p>{review.replies?.map(reply => <div className={styles.shopReply} key={reply.id}><strong><GiftOutlined /> Phản hồi từ Shop</strong><small>{dayjs(reply.created_at).format('DD/MM/YYYY')}</small><p>{reply.comment}</p></div>)}</div></article>) : <Empty image={Empty.PRESENTED_IMAGE_SIMPLE} description="Chưa có đánh giá cho sản phẩm này" />}</div></section></Card></div>
      <Card className={styles.checkout}><div className={styles.qtyRow}><label>Số lượng</label><Space.Compact><Button icon={<MinusOutlined />} disabled={quantity <= 1} onClick={() => updateQuantity(Math.max(1, quantity - 1))} /><InputNumber className={styles.quantity} value={quantity} min={1} max={Math.max(1, product.available_stock_count)} precision={0} controls={false} onChange={value => updateQuantity(value || 1)} /><Button icon={<PlusOutlined />} disabled={quantity >= product.available_stock_count} onClick={() => updateQuantity(Math.min(product.available_stock_count, quantity + 1))} /></Space.Compact></div><label>Thông tin đơn hàng</label>{product.data_input ? <TextArea rows={3} value={dataInput} placeholder={product.data_input} onChange={event => setDataInput(event.target.value)} /> : <div className={styles.noInput}>Không có trường thông tin nào cần điền</div>}<div className={styles.couponBox}><label>Mã giảm giá</label><Space.Compact block><Input value={coupon} status={coupon && !appliedCoupon ? undefined : appliedCoupon ? 'warning' : undefined} placeholder="Nhập mã giảm giá" onPressEnter={() => void applyCoupon()} onChange={event => { setCoupon(event.target.value.toUpperCase()); setAppliedCoupon(''); setDiscountAmount(0); }} /><Button type="primary" loading={couponLoading} disabled={!coupon.trim()} onClick={() => void applyCoupon()}>Áp dụng</Button></Space.Compact>{appliedCoupon && <div className={styles.couponSuccess}><CheckCircleOutlined /> Đã áp dụng {appliedCoupon}, giảm {money(discountAmount)}</div>}</div><div className={styles.totalBox}><span>Tạm tính <b>{money(subtotal)}</b></span>{discountAmount > 0 && <span>Giảm giá <b className={styles.discountValue}>-{money(discountAmount)}</b></span>}<span>Tổng cộng <strong>{money(total)}</strong></span></div><div className={styles.stock}><GiftOutlined /> Kho hàng: <strong>{product.available_stock_count} sản phẩm</strong></div><Button className={styles.buyButton} block size="large" type="primary" icon={<ShoppingCartOutlined />} loading={buying} disabled={product.available_stock_count < 1} onClick={buy}>{product.available_stock_count ? 'Thanh toán ngay' : 'Sản phẩm đã hết hàng'}</Button><p className={styles.secure}>Giao tự động ngay sau khi thanh toán thành công</p></Card>
    </section>

    {related.length > 0 && <section className={styles.related}><div className={styles.relatedHead}><div><Title level={3}><AppstoreOutlined /> Sản phẩm liên quan</Title><p>Các sản phẩm cùng chuyên mục có thể bạn quan tâm</p></div><Link href="/products"><Button type="primary">Xem tất cả <ArrowRightOutlined /></Button></Link></div><div className={styles.relatedGrid}>{related.map(item => <Link key={item.id} href={`/products/${item.slug}`}><article>{item.image ? <img src={asset(item.image)} alt={item.name} /> : <Empty image={Empty.PRESENTED_IMAGE_SIMPLE} description={false} />}<h3>{item.name}</h3><strong>{item.price_display}</strong><span><ShoppingCartOutlined /> Đặt hàng</span></article></Link>)}</div></section>}
    <Modal rootClassName={styles.reviewModal} open={reviewOpen} title="Đánh giá sản phẩm" okText="Gửi đánh giá" cancelText="Đóng" confirmLoading={reviewing} onOk={() => void submitReview()} onCancel={() => setReviewOpen(false)}><label className={styles.reviewLabel}>Đơn hàng đã mua</label><Select className={styles.reviewInput} value={reviewOrder} placeholder="Chọn đơn hàng" onChange={setReviewOrder} options={purchasedOrders.map(order => ({ value: order.id, label: `#${order.id} — Số lượng ${order.quantity} — ${dayjs(order.created_at).format('DD/MM/YYYY')}` }))} /><label className={styles.reviewLabel}>Mức độ hài lòng</label><Rate value={reviewRating} onChange={setReviewRating} /><label className={styles.reviewLabel}>Nhận xét</label><TextArea className={styles.reviewInput} rows={4} maxLength={500} showCount value={reviewComment} placeholder="Chia sẻ trải nghiệm của bạn..." onChange={event => setReviewComment(event.target.value)} /></Modal>
  </main></ClientLayout>;
}
