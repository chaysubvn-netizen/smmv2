'use client';

import { useCallback, useEffect, useState } from 'react';
import { App, Button, Card, Empty, Input, InputNumber, Modal, Pagination, Select, Space, Table, Tag, Typography } from 'antd';
import { message } from '@/lib/antd-message';
import { BankOutlined, CheckCircleFilled, CopyOutlined, DollarCircleOutlined, InfoCircleOutlined, PlusCircleOutlined, QrcodeOutlined, SearchOutlined, WalletOutlined } from '@ant-design/icons';
import ClientLayout from '@/components/ClientLayout';
import api from '@/lib/axios';
import styles from './addfunds.module.css';

const { Text } = Typography;
type Method = 'bank' | 'usdt' | 'binance' | 'trc20';
type Bank = { id: number; bank_name: string; bank_code?: string; account_number: string; account_name: string; icon?: string };
type Recharge = { id: number; transaction_id: string; gateway_name: string; method?: string; type?: string; amount: number; amount_display?: number; status: string; created_at: string };
type Payment = {
  id: number;
  type?: 'binance' | 'trc20';
  status?: string;
  transaction_id: string;
  amount: number;
  amount_usdt?: number;
  qr_url?: string;
  bank?: Bank;
  binance_id?: string;
  transfer_code?: string;
  wallet?: string;
  network?: string;
};
type Options = {
  bank_enabled: boolean;
  usdt_enabled: boolean;
  binance_enabled: boolean;
  trc20_enabled: boolean;
  usdt_exchange_rate: number;
  binance_exchange_rate: number;
  binance_id?: string;
  binance_qr?: string;
  transfer_code?: string;
  notice_recharge?: string;
  trc20_wallet?: string;
  banks: Bank[];
};

const defaultOptions: Options = {
  bank_enabled: false,
  usdt_enabled: false,
  binance_enabled: false,
  trc20_enabled: false,
  usdt_exchange_rate: 0,
  binance_exchange_rate: 23000,
  banks: [],
};
const quickAmounts = [10000, 20000, 30000, 50000, 100000, 200000, 500000, 1000000];
const quickCryptoAmounts = [5, 10, 20, 50, 100];
const labels: Record<string, string> = { waiting: 'Đang chờ', completed: 'Thành công', canceled: 'Đã hủy', expired: 'Hết hạn', pending: 'Đang chờ' };
const colors: Record<string, string> = { waiting: 'gold', completed: 'green', canceled: 'default', expired: 'red', pending: 'gold' };

export default function AddFundsPage() {
  const { modal } = App.useApp();
  const [options, setOptions] = useState<Options>(defaultOptions);
  const [method, setMethod] = useState<Method>('bank');
  const [bankId, setBankId] = useState<number>();
  const [amount, setAmount] = useState<number>(10000);
  const [binanceOrderId, setBinanceOrderId] = useState('');
  const [binanceStep, setBinanceStep] = useState<0 | 1 | 2>(0);
  const [binanceError, setBinanceError] = useState('');
  const [history, setHistory] = useState<Recharge[]>([]);
  const [pagination, setPagination] = useState({ current: 1, pageSize: 15, total: 0 });
  const [searchText, setSearchText] = useState('');
  const [appliedSearch, setAppliedSearch] = useState('');
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [payment, setPayment] = useState<Payment | null>(null);
  const [currency, setCurrency] = useState('VND');

  const assetUrl = (path?: string) => path
    ? (path.startsWith('http') ? path : `${(process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '')}${path}`)
    : '';

  const loadHistory = useCallback(async (page = 1, pageSize = 15) => {
    setLoading(true);
    try {
      const response = await api.get('/client/recharges', { params: { page, per_page: pageSize, search: appliedSearch }, timeout: 15000 });
      const data = response.data.data;
      setHistory(data.data || []);
      setCurrency(response.data.currency || 'VND');
      setPagination({ current: data.current_page, pageSize: data.per_page, total: data.total });
    } catch {
      message.error('Không thể tải lịch sử nạp tiền.');
    } finally {
      setLoading(false);
    }
  }, [appliedSearch]);

  useEffect(() => {
    api.get('/client/recharge-options', { timeout: 15000 }).then((response) => {
      const data: Options = response.data.data;
      setOptions(data);
      if (data.banks?.length) setBankId(data.banks[0].id);
      if (!data.bank_enabled) {
        if (data.binance_enabled) {
          setMethod('binance');
          setAmount(10);
        } else if (data.trc20_enabled) {
          setMethod('trc20');
          setAmount(10);
        } else if (data.usdt_enabled) {
          setMethod('usdt');
          setAmount(10);
        }
      }
    }).catch(() => message.error('Không thể tải cấu hình nạp tiền.'));
  }, []);

  useEffect(() => {
    void Promise.resolve().then(() => loadHistory(1));
  }, [loadHistory]);

  useEffect(() => {
    if (!payment?.id || payment.status === 'completed') return;

    let stopped = false;
    let checking = false;

    const checkPaymentStatus = async () => {
      if (checking || stopped) return;
      checking = true;
      try {
        const response = await api.get(`/client/recharges/${payment.id}`, {
          timeout: 15000,
        });
        const updated = response.data.data as Payment | undefined;
        if (updated?.status === 'completed' && !stopped) {
          stopped = true;
          setPayment(null);
          await loadHistory(1);
          window.dispatchEvent(new CustomEvent('user-balance-updated'));
          modal.success({
            centered: true,
            title: 'Nạp tiền thành công!',
            content: (
              <div>
                <p>Số tiền đã được cộng vào tài khoản của bạn.</p>
                <p><b>{Number(updated.amount || payment.amount).toLocaleString('vi-VN')} đ</b></p>
                <p>Mã giao dịch: <b>{updated.transaction_id || payment.transaction_id}</b></p>
              </div>
            ),
            okText: 'Đã hiểu',
          });
        } else if (updated?.status && ['failed', 'canceled', 'expired'].includes(updated.status)) {
          stopped = true;
          setPayment(null);
          await loadHistory(1);
          message.error('Yêu cầu nạp tiền đã hết hạn hoặc không thành công.');
        }
      } catch {
        // Lỗi mạng tạm thời sẽ được thử lại ở lần kiểm tra tiếp theo.
      } finally {
        checking = false;
      }
    };

    void checkPaymentStatus();
    const timer = window.setInterval(() => void checkPaymentStatus(), 3000);
    return () => {
      stopped = true;
      window.clearInterval(timer);
    };
  }, [payment, loadHistory, modal]);

  const selectMethod = (nextMethod: Method) => {
    setMethod(nextMethod);
    setAmount(nextMethod === 'bank' ? 10000 : 10);
    setPayment(null);
  };

  const createRecharge = async () => {
    if (method === 'bank' && !bankId) return message.warning('Vui lòng chọn tài khoản ngân hàng.');
    if (method === 'bank' && amount > 1000000000) return message.warning('Số tiền nạp tối đa là 1.000.000.000đ.');
    if (method === 'binance') {
      if (amount < 1) return message.warning('Số tiền nạp tối thiểu là 1 USDT.');
      setBinanceError('');
      setBinanceStep(1);
      return;
    }
    setSubmitting(true);
    try {
      const endpoint = `/client/recharges/${method}`;
      const payload = method === 'bank' ? { amount, bank_id: bankId } : { amount };
      const response = await api.post(endpoint, payload);
      if (!response.data.success) return message.error(response.data.message);
      message.success(response.data.message);
      if (response.data.redirect) window.location.href = response.data.redirect;
      else if (response.data.data) setPayment(response.data.data);
      await loadHistory(1);
    } catch (error: unknown) {
      const detail = error as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } };
      const validationMessage = detail.response?.data?.errors && Object.values(detail.response.data.errors)[0]?.[0];
      message.error(validationMessage || detail.response?.data?.message || 'Không thể tạo yêu cầu nạp tiền.');
    } finally {
      setSubmitting(false);
    }
  };

  const verifyBinancePayment = async () => {
    if (binanceOrderId.trim().length < 5) return message.warning('Vui lòng nhập mã giao dịch Binance hợp lệ.');
    setSubmitting(true);
    try {
      const response = await api.post('/client/recharges/binance', {
        amount,
        binance_order_id: binanceOrderId.trim(),
      });
      if (!response.data.success) return message.error(response.data.message);
      message.success(response.data.message);
      setBinanceOrderId('');
      setBinanceError('');
      setBinanceStep(0);
      await loadHistory(1);
    } catch (error: unknown) {
      const detail = error as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } };
      const validationMessage = detail.response?.data?.errors && Object.values(detail.response.data.errors)[0]?.[0];
      const errorMessage = validationMessage || detail.response?.data?.message || 'Không tìm thấy thanh toán. Vui lòng kiểm tra xem mã đơn hàng có chính xác không.';
      setBinanceError(errorMessage);
    } finally {
      setSubmitting(false);
    }
  };

  const openPayment = async (id: number) => {
    try {
      const response = await api.get(`/client/recharges/${id}`);
      if (response.data.data?.qr_url || response.data.data?.type === 'binance') setPayment(response.data.data);
    } catch {
      message.error('Không thể mở thông tin thanh toán.');
    }
  };
  const copy = (value: string) => navigator.clipboard.writeText(value).then(() => message.success('Đã sao chép.'));
  const formatMoney = (value: number) => currency === 'VND'
    ? `${Math.round(Number(value || 0)).toLocaleString('vi-VN')} đ`
    : Number(value || 0).toLocaleString('en-US', { style: 'currency', currency });
  const isCrypto = method !== 'bank';
  const exchangeRate = method === 'binance' ? options.binance_exchange_rate : options.usdt_exchange_rate;

  const columns = [
    { title: 'Mã giao dịch', dataIndex: 'transaction_id', render: (value: string) => <Text copyable>{value}</Text> },
    { title: 'Cổng nạp', dataIndex: 'gateway_name' },
    { title: 'Số tiền', dataIndex: 'amount_display', render: (value: number, item: Recharge) => <b>{formatMoney(value ?? item.amount)}</b> },
    { title: 'Trạng thái', dataIndex: 'status', render: (value: string) => <Tag color={colors[value] || 'default'}>{labels[value] || value}</Tag> },
    { title: 'Thời gian', dataIndex: 'created_at', render: (value: string) => new Date(value).toLocaleString('vi-VN') },
    {
      title: 'Thao tác',
      render: (_: unknown, item: Recharge) => {
        const isPayable = ['waiting', 'pending'].includes(String(item.status).trim().toLowerCase());
        if (!isPayable) return null;
        const gateway = `${item.gateway_name || ''} ${item.method || ''} ${item.type || ''}`.toLowerCase();
        if (gateway.includes('usdt')) {
          return (
            <Button
              type="primary"
              size="small"
              href={`https://app.fpayment.net/payment/${encodeURIComponent(item.transaction_id)}`}
              target="_blank"
              rel="noopener noreferrer"
            >
              Thanh toán
            </Button>
          );
        }
        return <Button size="small" onClick={() => openPayment(item.id)}>Thanh toán</Button>;
      },
    },
  ];

  return <ClientLayout><div className={styles.grid}>
    <Card className={styles.panel} title={<span><PlusCircleOutlined /> Tạo yêu cầu nạp tiền</span>}>
      <div className={styles.topLayout}>
      <div className={styles.formColumn}>
      <div className={styles.methods}>
        {options.bank_enabled ? <button className={method === 'bank' ? styles.selected : ''} onClick={() => selectMethod('bank')}><BankOutlined /><span><b>Ngân hàng Việt Nam</b><small>Qua VietQR tự động</small></span></button> : null}
        {options.binance_enabled ? <button className={`${styles.binanceMethod} ${method === 'binance' ? styles.selected : ''}`} onClick={() => selectMethod('binance')}><WalletOutlined /><span><b>Binance Pay</b><small>Đối soát tự động 1–3 phút</small></span></button> : null}
        {options.trc20_enabled ? <button className={method === 'trc20' ? styles.selected : ''} onClick={() => selectMethod('trc20')}><DollarCircleOutlined /><span><b>USDT TRC20 Auto</b><small>Quét tự động qua TronGrid</small></span></button> : null}
        {options.usdt_enabled ? <button className={method === 'usdt' ? styles.selected : ''} onClick={() => selectMethod('usdt')}><DollarCircleOutlined /><span><b>USDT (Blockchain)</b><small>Tự động qua Blockchain</small></span></button> : null}
      </div>

      {method === 'bank' ? <>
        <div className={styles.banks}>
          {options.banks.map((bank) => (
            <button
              key={bank.id}
              className={bankId === bank.id ? styles.selectedBank : ''}
              onClick={() => setBankId(bank.id)}
            >
              <span className={styles.bankLogo}>
                {bank.icon ? <img src={assetUrl(bank.icon)} alt={bank.bank_name} /> : <BankOutlined />}
              </span>
              <strong>{bank.bank_name}</strong>
              <span className={styles.bankAccount}>Số tài khoản: <b>{bank.account_number}</b></span>
              <span className={styles.bankAccount}>Chủ tài khoản: <b>{bank.account_name}</b></span>
              {bank.icon ? <img className={styles.bankWatermark} src={assetUrl(bank.icon)} alt="" aria-hidden="true" /> : null}
              {bankId === bank.id ? <CheckCircleFilled className={styles.bankCheck} /> : null}
              <span className={styles.bankLimits}>
                <small><span>TỐI THIỂU</span><b>10,000đ</b></small>
              </span>
            </button>
          ))}
        </div>
      </> : null}

      {method !== 'bank' ? <p className={styles.required}>* 1. Số tiền nạp (USDT)</p> : null}
      <div className={method === 'bank' ? styles.bankAmountField : styles.cryptoAmountField}>
        {method === 'bank' ? <DollarCircleOutlined /> : null}
        <InputNumber className={styles.amount} min={method === 'bank' ? 10000 : 1} max={method === 'bank' ? 1000000000 : undefined} value={amount} onChange={(value) => setAmount(Number(value || 0))} formatter={(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')} parser={(value) => Number((value || '').replace(/,/g, '')) as unknown as 0} />
        {method === 'bank' ? <b>VNĐ</b> : null}
      </div>
      {isCrypto ? <div className={styles.estimated}>Số tiền quy đổi (VND): <b>≈ {(amount * exchangeRate).toLocaleString('vi-VN')} đ</b> <span>(Tỷ giá: 1 USDT = {Number(exchangeRate).toLocaleString('vi-VN')}đ)</span></div> : null}
      <div className={styles.quick}>{(method === 'bank' ? quickAmounts : quickCryptoAmounts).map((value) => <button key={value} className={method !== 'bank' && amount === value ? styles.activeAmount : ''} onClick={() => setAmount(method === 'bank' ? Math.min(1000000000, amount + value) : value)}>{method === 'bank' ? `+${value.toLocaleString('en-US')}đ` : value.toLocaleString('vi-VN')}</button>)}</div>

      <Button type="primary" block size="large" className={`${styles.continue} ${method === 'bank' ? styles.bankCreateButton : ''} ${method === 'binance' ? styles.binancePayButton : ''}`} loading={submitting} onClick={createRecharge}>{method === 'bank' ? 'Tạo hóa đơn nạp tiền' : method === 'binance' ? 'Thanh toán' : method === 'trc20' ? 'Tạo hóa đơn TRC20' : 'Khởi tạo hóa đơn USDT'}</Button>
      </div>
      <aside className={styles.guide}>
        <h3><InfoCircleOutlined /> Hướng dẫn nạp tiền</h3>
        {options.notice_recharge
          ? <div className={styles.guideContent} dangerouslySetInnerHTML={{ __html: options.notice_recharge }} />
          : <div className={styles.guideEmpty}>Admin có thể nhập nội dung hướng dẫn tại <b>Thông báo → Thông báo Nạp tiền</b>.</div>}
      </aside>
      </div>
    </Card>

    <Card className={styles.panel} title="Lịch sử nạp tiền"><div className={styles.historySearch}><Space.Compact><Select value="transaction_id" options={[{ value: 'transaction_id', label: 'Mã giao dịch' }]} /><Input allowClear prefix={<SearchOutlined />} placeholder="Nhập mã giao dịch..." value={searchText} onChange={(event) => setSearchText(event.target.value)} onPressEnter={() => { setAppliedSearch(searchText.trim()); if (appliedSearch === searchText.trim()) loadHistory(1); }} /><Button type="primary" icon={<SearchOutlined />} onClick={() => { setAppliedSearch(searchText.trim()); if (appliedSearch === searchText.trim()) loadHistory(1); }}>Tìm</Button></Space.Compact></div><Table rowKey="id" columns={columns} dataSource={history} loading={loading} scroll={{ x: 850 }} pagination={false} locale={{ emptyText: <Empty image={Empty.PRESENTED_IMAGE_SIMPLE} description="Chưa có giao dịch nạp tiền" /> }} /><div className={styles.pagination}><Pagination {...pagination} showTotal={(total) => `${total} giao dịch`} showSizeChanger pageSizeOptions={[10, 15, 20, 50]} locale={{ items_per_page: '/ trang' }} onChange={(page, pageSize) => loadHistory(page, pageSize)} /></div></Card>

    <Modal open={binanceStep > 0} onCancel={() => { if (!submitting) { setBinanceStep(0); setBinanceError(''); } }} footer={null} title="Chuyển tiền nội bộ Binance" width={470} centered className={styles.binanceModal} mask={{ closable: !submitting }}>
      {binanceStep === 2 && binanceError ? <div className={styles.binanceError}>{binanceError}</div> : null}
      <div className={styles.steps}>
        <div className={styles.stepActive}><i>1</i><span>Thanh toán</span></div>
        <div className={binanceStep === 2 ? styles.stepActive : ''}><i>2</i><span>Xác minh</span></div>
      </div>
      {binanceStep === 1 ? <>
        <div className={styles.binanceAmount}><strong>{amount.toLocaleString('en-US')}</strong> USDT</div>
        <label className={styles.binanceLabel}>Gửi đến Binance ID</label>
        <div className={styles.copyField}><span>{options.binance_id}</span><Button icon={<CopyOutlined />} onClick={() => copy(options.binance_id || '')}>Sao chép</Button></div>
        <div className={styles.binanceQrPanel}>
          {options.binance_qr ? <img src={assetUrl(options.binance_qr)} alt="QR Binance Pay" /> : <QrcodeOutlined className={styles.qrPlaceholder} />}
          <ol>
            <li>Quét mã QR bằng ứng dụng Binance hoặc gửi tiền qua Binance ID.</li>
            <li>Chuyển đúng <b>{amount.toLocaleString('en-US')} USDT</b>{options.transfer_code ? <> với nội dung <b>{options.transfer_code}</b></> : null}.</li>
            <li>Sau khi thanh toán, nhấn “Xác nhận đã thanh toán”.</li>
          </ol>
        </div>
        <Button type="primary" block size="large" className={styles.confirmPayment} onClick={() => setBinanceStep(2)}>Xác nhận đã thanh toán</Button>
      </> : <>
        <div className={styles.verifySummary}>
          <div><small>Số lượng</small><strong>{amount.toLocaleString('en-US')} USDT</strong></div>
          <div><small>Gửi đến ID Binance</small><strong>{options.binance_id} <CopyOutlined onClick={() => copy(options.binance_id || '')} /></strong></div>
        </div>
        <div className={styles.verifyPanel}>
          <label className={styles.binanceLabel}>Nhập ID lệnh Binance của bạn.</label>
          <Input size="large" autoFocus value={binanceOrderId} onChange={(event) => { setBinanceOrderId(event.target.value); setBinanceError(''); }} onPressEnter={() => void verifyBinancePayment()} placeholder="Nhập Order ID" />
          <div className={styles.orderGuide}>
            <div className={styles.receiptMockup}>
              <CheckCircleFilled />
              <b>Payment Successful</b>
              <strong>{amount.toLocaleString('en-US')} USDT</strong>
              <span>Order ID</span>
              <i>1234567890123456 <CopyOutlined /></i>
            </div>
            <ol>
              <li>Sao chép Mã đơn hàng (Order ID) từ chi tiết thanh toán thành công trong tài khoản Binance của bạn.</li>
              <li>Dán vào ô phía trên và nhấn “Xác nhận thanh toán”.</li>
            </ol>
          </div>
        </div>
        <div className={styles.verifyActions}>
          <Button type="primary" block size="large" loading={submitting} onClick={verifyBinancePayment}>Xác minh thanh toán</Button>
        </div>
      </>}
    </Modal>

    <Modal open={Boolean(payment)} onCancel={() => setPayment(null)} footer={null} title={<span className={styles.modalTitle}><QrcodeOutlined /> {payment?.type === 'binance' ? 'Thông tin Binance Pay' : payment?.type === 'trc20' ? 'Nạp USDT TRC20' : 'Thông tin chuyển khoản'}</span>} width={430} centered className={styles.paymentModal} styles={{ mask: { background: 'rgba(15, 23, 42, 0.10)' } }}>
      {payment?.type === 'trc20' ? <div className={styles.payment}>
        <div className={styles.qrBox}><img src={payment.qr_url} alt="QR ví USDT TRC20" /></div>
        <div><span>Mạng lưới</span><b>USDT — TRC20</b></div>
        <div><span>Địa chỉ ví</span><b className={styles.walletValue}>{payment.wallet} <CopyOutlined onClick={() => copy(payment.wallet || '')} /></b></div>
        <div><span>Số tiền chính xác</span><b className={styles.highlight}>{Number(payment.amount_usdt).toFixed(6)} USDT <CopyOutlined onClick={() => copy(Number(payment.amount_usdt).toFixed(6))} /></b></div>
        <p>Chỉ gửi USDT qua mạng TRC20. Hãy gửi đúng số tiền chính xác ở trên; hệ thống sẽ tự động cộng số dư sau khi blockchain xác nhận.</p>
      </div> : payment?.type === 'binance' ? <div className={styles.payment}>
        {payment.qr_url ? <div className={styles.qrBox}><img src={assetUrl(payment.qr_url)} alt="QR Binance Pay" /></div> : null}
        <div><span>Binance Pay ID</span><b>{payment.binance_id} <CopyOutlined onClick={() => copy(payment.binance_id || '')} /></b></div>
        <div><span>Số tiền</span><b className={styles.highlight}>{payment.amount_usdt} USDT <CopyOutlined onClick={() => copy(String(payment.amount_usdt || ''))} /></b></div>
        <div><span>Nội dung</span><b className={styles.highlight}>{payment.transfer_code} <CopyOutlined onClick={() => copy(payment.transfer_code || '')} /></b></div>
        <div><span>Mã giao dịch</span><b>{payment.transaction_id}</b></div>
        <p>Yêu cầu đang được đối soát tự động. Số dư thường được cộng trong 1–3 phút sau khi giao dịch được tìm thấy.</p>
      </div> : payment?.bank ? <div className={styles.payment}><div className={styles.qrBox}><img src={payment.qr_url} alt="VietQR" /></div><div><span>Ngân hàng</span><b>{payment.bank.bank_name}</b></div><div><span>Số tài khoản</span><b>{payment.bank.account_number} <CopyOutlined onClick={() => copy(payment.bank!.account_number)} /></b></div><div><span>Chủ tài khoản</span><b>{payment.bank.account_name}</b></div><div><span>Số tiền</span><b className={styles.highlight}>{Number(payment.amount).toLocaleString('vi-VN')} đ <CopyOutlined onClick={() => copy(String(payment.amount))} /></b></div><div><span>Nội dung</span><b className={styles.highlight}>{payment.transaction_id} <CopyOutlined onClick={() => copy(payment.transaction_id)} /></b></div><p>Vui lòng chuyển đúng số tiền và nội dung để hệ thống tự động cộng số dư.</p></div> : null}
    </Modal>
  </div></ClientLayout>;
}
