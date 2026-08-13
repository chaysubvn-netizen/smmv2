# Gói cập nhật đồng bộ

Máy chủ cập nhật trả JSON:

```json
{
  "status": "success",
  "version": "1.4.0",
  "download_url": "https://updates.example.com/smmv2-1.4.0.zip",
  "sha256": "sha256-cua-file-zip-gom-64-ky-tu",
  "changelog": "Nội dung thay đổi"
}
```

File ZIP phải có cấu trúc:

```text
update.json
backend/
  app/
  config/
  database/
  composer.json
  composer.lock
nextjs/
  src/
  public/
  package.json
  package-lock.json
  next.config.ts
```

`update.json`:

```json
{
  "format": 1,
  "version": "1.4.0"
}
```

Thiết lập production trong `.env` của Laravel:

```dotenv
UPDATE_SERVER_URL=https://updates.example.com/latest.json
NEXTJS_PATH=/var/www/top1smm-next
NEXTJS_RESTART_COMMAND=pm2 restart top1smm-next
COMPOSER_BINARY=/usr/local/bin/composer
NPM_BINARY=/usr/bin/npm
UPDATE_KEEP_BACKUPS=3
```

Gói phát hành không chứa `.env`, `node_modules`, `.next`, `storage`, file upload hoặc secret. Máy chủ cần PHP Zip, quyền ghi hai thư mục và Node.js/npm. `sha256` là bắt buộc; nên chỉ dùng HTTPS.
