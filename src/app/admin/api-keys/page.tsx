"use client";

import { useCallback, useEffect, useState } from "react";
import { Button, Card, Form, Input, Modal, Select, Space, Spin, Table, Tag, Typography } from "antd";
import type { TableColumnsType } from "antd";
import { message } from "@/lib/antd-message";
import {
  BankOutlined,
  HistoryOutlined,
  MailOutlined,
  SaveOutlined,
  SendOutlined,
  WalletOutlined,
} from "@ant-design/icons";
import api from "@/lib/axios";
import styles from "./api-keys.module.css";

const { Text, Title } = Typography;
type ConfigValues = {
  mb_account_number?: string; mb_api_key?: string; vcb_account_number?: string; vcb_api_key?: string;
  acb_account_number?: string; acb_api_key?: string; vtb_account_number?: string; vtb_api_key?: string;
  ocb_account_number?: string; ocb_username?: string; ocb_password?: string;
  usdt_wallet?: string; usdt_wallet_token?: string; usdt_exchange_rate?: string;
  telegram_bot?: string; telegram_chat_id?: string; telegram_status?: string;
  smtp_host?: string; smtp_port?: string; smtp_username?: string; smtp_password?: string;
};
type FieldName = keyof ConfigValues;
type OcbTransaction = {
  id: string;
  reference?: string;
  description: string;
  amount: number;
  currency: string;
  type?: "CRDT" | "DBIT" | string;
  booking_date?: string;
  creation_time?: string;
  counterparty?: string;
  balance?: number | null;
};

const banks: Array<{ title: string; account: FieldName; key: FieldName }> = [
  { title: "MBBANK", account: "mb_account_number", key: "mb_api_key" },
  { title: "Vietcombank", account: "vcb_account_number", key: "vcb_api_key" },
  { title: "ACB", account: "acb_account_number", key: "acb_api_key" },
  { title: "Viettinbank", account: "vtb_account_number", key: "vtb_api_key" },
];

export default function AdminPaymentApiKeysPage() {
  const [form] = Form.useForm<ConfigValues>();
  const [loading, setLoading] = useState(true);
  const [savingCard, setSavingCard] = useState("");
  const [ocbOtp, setOcbOtp] = useState("");
  const [ocbOtpRequired, setOcbOtpRequired] = useState(false);
  const [ocbAuthLoading, setOcbAuthLoading] = useState(false);
  const [ocbHistoryLoading, setOcbHistoryLoading] = useState(false);
  const [ocbHistoryOpen, setOcbHistoryOpen] = useState(false);
  const [ocbTransactions, setOcbTransactions] = useState<OcbTransaction[]>([]);
  const [ocbBalance, setOcbBalance] = useState<number | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const response = await api.get("/admin/payment-api-keys");
      form.setFieldsValue(response.data.data || {});
    } catch {
      message.error("Không thể tải cấu hình API Key.");
    } finally {
      setLoading(false);
    }
  }, [form]);
  useEffect(() => {
    void Promise.resolve().then(load);
  }, [load]);

  const save = async (card: string, fields: FieldName[]) => {
    try {
      const values = await form.validateFields(fields);
      setSavingCard(card);
      const response = await api.put("/admin/payment-api-keys", values);
      message.success(response.data.message || "Đã lưu cấu hình.");
    } catch (error: unknown) {
      if (typeof error === "object" && error && "errorFields" in error) return;
      const detail = error as { response?: { data?: { message?: string } } };
      message.error(
        detail.response?.data?.message || "Không thể lưu cấu hình.",
      );
    } finally {
      setSavingCard("");
    }
  };

  const authenticateOcb = async () => {
    setOcbAuthLoading(true);
    try {
      const response = await api.post("/admin/ocb/login");
      setOcbOtpRequired(Boolean(response.data.requires_otp));
      message.success(response.data.message || "Đã xác thực OCB.");
    } catch (error: unknown) {
      const detail = error as { response?: { data?: { requires_otp?: boolean; message?: string } } };
      if (detail.response?.data?.requires_otp) setOcbOtpRequired(true);
      message.error(detail.response?.data?.message || "Không thể đăng nhập OCB.");
    } finally { setOcbAuthLoading(false); }
  };

  const verifyOcbOtp = async () => {
    setOcbAuthLoading(true);
    try {
      await api.post("/admin/ocb/otp", { otp: ocbOtp });
      setOcbOtpRequired(false); setOcbOtp("");
      message.success("Xác thực OTP OCB thành công.");
    } catch (error: unknown) {
      const detail = error as { response?: { data?: { message?: string } } };
      message.error(detail.response?.data?.message || "OTP không hợp lệ.");
    } finally { setOcbAuthLoading(false); }
  };

  const loadOcbTransactions = async () => {
    setOcbHistoryLoading(true);
    try {
      const response = await api.get("/admin/ocb/transactions", {
        params: { limit: 50 },
      });
      setOcbTransactions(response.data.data || []);
      setOcbBalance(
        typeof response.data.balance === "number" ? response.data.balance : null,
      );
      setOcbHistoryOpen(true);
    } catch (error: unknown) {
      const detail = error as {
        response?: { data?: { requires_otp?: boolean; message?: string } };
      };
      if (detail.response?.data?.requires_otp) setOcbOtpRequired(true);
      message.error(
        detail.response?.data?.message || "Không thể lấy lịch sử giao dịch OCB.",
      );
    } finally {
      setOcbHistoryLoading(false);
    }
  };

  const ocbColumns: TableColumnsType<OcbTransaction> = [
    {
      title: "Thời gian",
      dataIndex: "creation_time",
      width: 170,
      render: (value?: string) =>
        value ? new Date(value).toLocaleString("vi-VN") : "—",
    },
    {
      title: "Loại",
      dataIndex: "type",
      width: 90,
      render: (value?: string) => (
        <Tag color={value === "CRDT" ? "green" : "red"}>
          {value === "CRDT" ? "Tiền vào" : "Tiền ra"}
        </Tag>
      ),
    },
    {
      title: "Số tiền",
      dataIndex: "amount",
      width: 150,
      align: "right",
      render: (value: number, record) => (
        <Text strong type={record.type === "CRDT" ? "success" : "danger"}>
          {record.type === "CRDT" ? "+" : "-"}
          {new Intl.NumberFormat("vi-VN").format(value)} {record.currency}
        </Text>
      ),
    },
    {
      title: "Nội dung",
      dataIndex: "description",
      ellipsis: true,
    },
    {
      title: "Đối tác",
      dataIndex: "counterparty",
      width: 180,
      ellipsis: true,
      render: (value?: string) => value || "—",
    },
    {
      title: "Mã giao dịch",
      dataIndex: "reference",
      width: 150,
      render: (value?: string) => value || "—",
    },
  ];

  return (
    <main className={styles.page}>
      <header className={styles.heading}>
        <div>
          <Title level={2}>Cấu hình API Key nạp tiền</Title>
          <Text type="secondary">
            Kết nối ngân hàng tự động, ví USDT và hệ thống thông báo
          </Text>
        </div>
      </header>
      <Spin spinning={loading}>
        <Form form={form} layout="vertical" requiredMark={false}>
          <section className={styles.grid}>
            {banks.map((bank) => (
              <Card
                key={bank.title}
                className={styles.card}
                title={
                  <span>
                    <BankOutlined /> AutoBank | {bank.title} | SPAY5S.COM
                  </span>
                }
              >
                <div className={styles.twoColumns}>
                  <Form.Item
                    name={bank.account}
                    label="Số tài khoản"
                    rules={[{ required: true, message: "Nhập số tài khoản." }]}
                  >
                    <Input placeholder="Nhập số tài khoản" />
                  </Form.Item>
                  <Form.Item
                    name={bank.key}
                    label="API KEY SPAY5S.COM"
                    rules={[{ required: true, message: "Nhập API Key." }]}
                  >
                    <Input.Password placeholder="Nhập API KEY" />
                  </Form.Item>
                </div>
                <Button
                  type="primary"
                  icon={<SaveOutlined />}
                  loading={savingCard === bank.title}
                  onClick={() =>
                    void save(bank.title, [bank.account, bank.key])
                  }
                >
                  Lưu
                </Button>
              </Card>
            ))}

            <Card
              className={styles.card}
              title={
                <span>
                  <WalletOutlined /> AutoBank | FPAYMENT | USDT
                </span>
              }
            >
              <div className={styles.threeColumns}>
                <Form.Item
                  name="usdt_wallet"
                  label="Address Wallet"
                  rules={[{ required: true, message: "Nhập địa chỉ ví." }]}
                >
                  <Input placeholder="Nhập địa chỉ ví" />
                </Form.Item>
                <Form.Item
                  name="usdt_wallet_token"
                  label="Token Wallet"
                  rules={[{ required: true, message: "Nhập token ví." }]}
                >
                  <Input.Password placeholder="Nhập API KEY" />
                </Form.Item>
                <Form.Item
                  name="usdt_exchange_rate"
                  label="Exchange Rate"
                  rules={[{ required: true, message: "Nhập tỷ giá." }]}
                >
                  <Input placeholder="Nhập tỷ giá" />
                </Form.Item>
              </div>
              <Button
                type="primary"
                icon={<SaveOutlined />}
                loading={savingCard === "usdt"}
                onClick={() =>
                  void save("usdt", [
                    "usdt_wallet",
                    "usdt_wallet_token",
                    "usdt_exchange_rate",
                  ])
                }
              >
                Lưu
              </Button>
              <div style={{ display: "none" }}>
                <Button loading={ocbAuthLoading} onClick={() => void authenticateOcb()}>Xác thực OCB</Button>
                {ocbOtpRequired && <>
                  <Input
                    value={ocbOtp}
                    onChange={(event) => setOcbOtp(event.target.value.replace(/\D/g, "").slice(0, 6))}
                    placeholder="Nhập OTP OCB"
                    maxLength={6}
                    style={{ width: 160 }}
                  />
                  <Button type="default" loading={ocbAuthLoading} disabled={ocbOtp.length !== 6} onClick={() => void verifyOcbOtp()}>Xác nhận OTP</Button>
                </>}
              </div>
            </Card>

            <Card
              className={styles.card}
              title={
                <span>
                  <BankOutlined /> AutoBank | OCB | OMNI
                </span>
              }
            >
              <div className={styles.threeColumns}>
                <Form.Item
                  name="ocb_account_number"
                  label="Số tài khoản"
                  rules={[{ required: true, message: "Nhập số tài khoản." }]}
                >
                  <Input placeholder="Nhập số tài khoản" />
                </Form.Item>
                <Form.Item
                  name="ocb_username"
                  label="Tài khoản (Username)"
                  rules={[{ required: true, message: "Nhập tên đăng nhập." }]}
                >
                  <Input placeholder="Tên đăng nhập" />
                </Form.Item>
                <Form.Item
                  name="ocb_password"
                  label="Mật khẩu"
                  rules={[{ required: true, message: "Nhập mật khẩu." }]}
                >
                  <Input.Password placeholder="Mật khẩu" />
                </Form.Item>
              </div>
              <Button
                type="primary"
                icon={<SaveOutlined />}
                loading={savingCard === "ocb"}
                onClick={() =>
                  void save("ocb", [
                    "ocb_account_number",
                    "ocb_username",
                    "ocb_password",
                  ])
                }
              >
                Lưu
              </Button>
              <Space wrap style={{ marginTop: 12 }}>
                <Button loading={ocbAuthLoading} onClick={() => void authenticateOcb()}>Xác thực OCB</Button>
                <Button icon={<HistoryOutlined />} loading={ocbHistoryLoading} onClick={() => void loadOcbTransactions()}>Lịch sử giao dịch</Button>
                <Text strong>
                  Số dư:{" "}
                  {ocbBalance === null
                    ? "Chưa tải"
                    : `${new Intl.NumberFormat("vi-VN").format(ocbBalance)} VND`}
                </Text>
                {ocbOtpRequired && <>
                  <Input value={ocbOtp} onChange={(e) => setOcbOtp(e.target.value.replace(/\D/g, "").slice(0, 6))} placeholder="Nhập OTP OCB" maxLength={6} style={{ width: 160 }} />
                  <Button loading={ocbAuthLoading} disabled={ocbOtp.length !== 6} onClick={() => void verifyOcbOtp()}>Xác nhận OTP</Button>
                </>}
              </Space>
            </Card>

            <Card
              className={styles.card}
              title={
                <span>
                  <SendOutlined /> Notification
                </span>
              }
            >
              <div className={styles.threeColumns}>
                <Form.Item
                  name="telegram_bot"
                  label="Bot Token"
                  rules={[{ required: true, message: "Nhập Bot Token." }]}
                >
                  <Input.Password placeholder="Nhập Bot Token" />
                </Form.Item>
                <Form.Item
                  name="telegram_chat_id"
                  label="ChatID (Nhận)"
                  rules={[{ required: true, message: "Nhập Chat ID." }]}
                >
                  <Input placeholder="Nhập Chat ID" />
                </Form.Item>
                <Form.Item
                  name="telegram_status"
                  label="Trạng thái"
                  rules={[{ required: true }]}
                >
                  <Select
                    options={[
                      { value: "active", label: "Bật" },
                      { value: "inactive", label: "Tắt" },
                    ]}
                  />
                </Form.Item>
              </div>
              <Button
                type="primary"
                icon={<SaveOutlined />}
                loading={savingCard === "telegram"}
                onClick={() =>
                  void save("telegram", [
                    "telegram_bot",
                    "telegram_chat_id",
                    "telegram_status",
                  ])
                }
              >
                Lưu
              </Button>
            </Card>

            <Card
              className={`${styles.card} ${styles.smtpCard}`}
              title={
                <span>
                  <MailOutlined /> Notification | Email | SMTP
                </span>
              }
            >
              <div className={styles.twoColumns}>
                <Form.Item
                  name="smtp_host"
                  label="SMTP Host"
                  rules={[{ required: true, message: "Nhập SMTP Host." }]}
                >
                  <Input placeholder="Nhập SMTP Host" />
                </Form.Item>
                <Form.Item
                  name="smtp_port"
                  label="SMTP Port"
                  rules={[{ required: true, message: "Nhập SMTP Port." }]}
                >
                  <Input placeholder="Nhập SMTP Port" />
                </Form.Item>
                <Form.Item
                  name="smtp_username"
                  label="SMTP Username"
                  rules={[{ required: true, message: "Nhập SMTP Username." }]}
                >
                  <Input placeholder="Nhập SMTP User" />
                </Form.Item>
                <Form.Item
                  name="smtp_password"
                  label="SMTP Password"
                  rules={[{ required: true, message: "Nhập SMTP Password." }]}
                >
                  <Input.Password placeholder="Nhập SMTP Pass" />
                </Form.Item>
              </div>
              <Button
                type="primary"
                icon={<SaveOutlined />}
                loading={savingCard === "smtp"}
                onClick={() =>
                  void save("smtp", [
                    "smtp_host",
                    "smtp_port",
                    "smtp_username",
                    "smtp_password",
                  ])
                }
              >
                Lưu
              </Button>
            </Card>
          </section>
        </Form>
      </Spin>
      <Modal
        title="Lịch sử giao dịch OCB"
        open={ocbHistoryOpen}
        onCancel={() => setOcbHistoryOpen(false)}
        footer={null}
        width={1100}
      >
        <Table<OcbTransaction>
          rowKey={(record) => record.id || record.reference || `${record.creation_time}-${record.amount}`}
          columns={ocbColumns}
          dataSource={ocbTransactions}
          loading={ocbHistoryLoading}
          pagination={{ pageSize: 10, showSizeChanger: true }}
          scroll={{ x: 950 }}
          locale={{ emptyText: "Không có giao dịch OCB." }}
        />
      </Modal>
    </main>
  );
}
