# Các Lệnh Terminal Liên Quan Laravel Boost

Tài liệu này liệt kê các lệnh terminal hay dùng khi làm việc với Laravel Boost và project demo.

Mục tiêu:

- cài Boost
- kiểm tra Boost
- chạy demo
- reset môi trường
- phục vụ thuyết trình dễ hơn

## 1. Cài Laravel Boost

Nếu muốn gõ ngắn trong PowerShell, tạo alias tạm:

```powershell
function php84 { & "C:\Users\Voduybinhv\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe" @args }
```

Nếu chưa có Boost trong project:

```bash
composer require laravel/boost --dev
```

Ý nghĩa:

- thêm package Boost vào `require-dev`
- chỉ dùng cho môi trường phát triển

## 2. Cài đặt Boost vào repo

Sau khi đã có package:

```bash
php84 artisan boost:install
```

Có thể dùng thêm các cờ tuỳ nhu cầu:

```bash
php84 artisan boost:install --guidelines --skills --mcp --no-interaction
```

Ý nghĩa:

- sinh file cấu hình và guideline cho agent
- bật MCP
- tạo/điều chỉnh skills theo project

## 3. Khởi động MCP của Boost

```bash
php84 artisan boost:mcp
```

Ý nghĩa:

- khởi động MCP server của Laravel Boost
- giúp AI agent đọc ngữ cảnh thật của project

Khi demo theo hướng official, nên:

- `cd` vào đúng thư mục `seminar-manager`
- mở MCP ở một terminal riêng
- để nó chạy nền cùng với Laravel server

## 4. Kiểm tra package và cấu hình

```bash
composer show laravel/boost
```

```bash
php84 artisan about
```

```bash
php84 artisan optimize:clear
```

Ý nghĩa:

- xem Boost đã được cài chưa
- xem thông tin app
- xoá cache cấu hình / route / view để tránh dữ liệu cũ

## 5. Chạy demo local

```bash
php84 artisan migrate:fresh --seed
```

```bash
npm install
```

```bash
npm run dev
```

```bash
php84 artisan serve
```

Nếu demo theo đúng flow Boost, chạy thêm:

```bash
php84 artisan boost:mcp
```

Ý nghĩa:

- tạo lại database demo
- cài frontend dependencies
- bật Vite
- bật Laravel server
- bật MCP server của Boost để AI agent có thể đọc ngữ cảnh project

## 6. Chạy demo theo cách ổn định trên máy này

Nếu `php` trên máy trỏ nhầm sang bản cũ, dùng PHP 8.4 trực tiếp:

```powershell
php84 artisan boost:mcp
```

```powershell
php84 artisan optimize:clear
```

```powershell
php84 artisan migrate:fresh --seed
```

```powershell
php84 artisan serve --host=127.0.0.1 --port=8002
```

## 7. Kiểm tra test

```bash
php84 artisan test
```

Hoặc test riêng AI chat:

```bash
php84 artisan test --filter=AiChatTest --compact
```

Ý nghĩa:

- xác nhận local demo mode vẫn chạy
- xác nhận code Boost demo không bị vỡ

## 8. Chạy lại khi bị lỗi cache

```bash
php84 artisan optimize:clear
```

```bash
php84 artisan config:clear
```

```bash
php84 artisan route:clear
```

```bash
php84 artisan view:clear
```

## 9. Các lệnh hữu ích khi demo

```bash
php84 artisan route:list
```

```bash
php84 artisan tinker
```

```bash
php84 artisan pail
```

```bash
php84 artisan queue:listen --tries=1 --timeout=0
```

Ý nghĩa:

- `route:list` để xem route nào đang có
- `tinker` để thử query / model nhanh
- `pail` để xem log
- `queue:listen` để chạy job nếu project cần

## 10. Lệnh dừng dự án

Nếu mở từ terminal:

- nhấn `Ctrl + C` ở terminal Laravel
- nhấn `Ctrl + C` ở terminal Vite

Nếu bị treo tiến trình:

```powershell
Get-Process php,node -ErrorAction SilentlyContinue | Stop-Process -Force
```

## 11. Lệnh liên quan OpenAI

Trong project này, AI chat local demo mode là mặc định nên **không bắt buộc** phải có key.

Nếu muốn thử cloud AI thật:

- sửa `.env`
- thêm `OPENAI_API_KEY`

Sau đó:

```bash
php84 artisan optimize:clear
```

## 12. Bộ lệnh demo gọn nhất

Nếu chỉ cần chạy nhanh để thuyết trình:

### Cách 1: chạy launcher

```powershell
powershell -ExecutionPolicy Bypass -File .\run-demo.ps1 -Seed
```

Mình đã test thực tế cách này và nó đã:

- clear cache
- reset database demo
- mở Boost MCP
- mở Laravel server trên `http://127.0.0.1:8002`
- mở Vite ở terminal riêng

### Cách 2: chạy thủ công

```powershell
cd "D:\HSU\2533Semester 3(2025-2026)\Phát triển Web sd Framework\Seminar\seminar-manager"
php84 artisan boost:mcp
php84 artisan optimize:clear
php84 artisan migrate:fresh --seed
npm run dev
php84 artisan serve --host=127.0.0.1 --port=8002
```

Nếu cần thì mở AI chat và hỏi:

- `Laravel Boost là gì?`
- `Boost đã được gắn vào project này ở những file nào?`
- `Khi không có OpenAI key thì chatbot chạy thế nào?`

Lưu ý khi copy lệnh:

- chạy từng lệnh một, hoặc chia ra nhiều terminal
- không dán luôn dấu ``` vào PowerShell
- phải đứng trong thư mục `seminar-manager` trước khi chạy `php84 artisan ...`
- nếu lệnh `php84 artisan ...` báo PHP 8.3, hãy đổi sang đường dẫn PHP 8.4 đầy đủ như trên

Quy trình mình đã kiểm tra thực tế:

1. Chạy `powershell -ExecutionPolicy Bypass -File .\run-demo.ps1 -Seed`
2. Đợi script báo đã sẵn sàng
3. Mở `http://127.0.0.1:8002/login`
4. Xác nhận trang login trả `200`
5. Đăng nhập tài khoản demo
6. Mở `AI Chat` và hỏi các câu về Boost



