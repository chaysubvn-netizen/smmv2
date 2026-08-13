import mysql, { type PoolConnection, type RowDataPacket } from 'mysql2/promise';

const globalDb = globalThis as typeof globalThis & { smmMysqlPool?: mysql.Pool };

export const db = globalDb.smmMysqlPool ??= mysql.createPool({
  host: process.env.DB_HOST || '127.0.0.1',
  port: Number(process.env.DB_PORT || 3306),
  database: process.env.DB_DATABASE,
  user: process.env.DB_USERNAME,
  password: process.env.DB_PASSWORD,
  connectionLimit: 12,
  decimalNumbers: true,
  timezone: '+07:00',
  charset: 'utf8mb4',
});

export async function rows<T extends RowDataPacket>(sql: string, values: unknown[] = []): Promise<T[]> {
  const [result] = await db.query<T[]>(sql, values);
  return result;
}

export async function one<T extends RowDataPacket>(sql: string, values: unknown[] = []): Promise<T | null> {
  return (await rows<T>(sql, values))[0] ?? null;
}

export async function transaction<T>(callback: (connection: PoolConnection) => Promise<T>): Promise<T> {
  const connection = await db.getConnection();
  try {
    await connection.beginTransaction();
    const value = await callback(connection);
    await connection.commit();
    return value;
  } catch (error) {
    await connection.rollback();
    throw error;
  } finally {
    connection.release();
  }
}
