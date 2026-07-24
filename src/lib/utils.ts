export function formatCurrency(amount: number | string | null | undefined): string {
  const value = typeof amount === 'string' ? Number.parseFloat(amount) : amount;
  const normalizedValue =
    typeof value === 'number' && Number.isFinite(value) ? value : 0;

  return `${Math.round(normalizedValue).toLocaleString('vi-VN')} đ`;
}
