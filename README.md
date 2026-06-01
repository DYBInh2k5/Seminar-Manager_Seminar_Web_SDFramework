# Seminar Manager

Seminar Manager là một Laravel demo project cho quy trình seminar trong môi trường học thuật.

Project này được dùng để:

- minh hoạ cách tổ chức một workflow seminar
- làm bối cảnh thực tế để tìm hiểu Laravel Boost
- trình diễn cấu trúc Laravel, database, phân quyền và AI chat

## Tài liệu

Điểm bắt đầu tốt nhất:

- `docs/DOCUMENTATION_INDEX.md`

Các file quan trọng nhất:

- `docs/README-DEMO.md` - cách chạy demo
- `docs/PROJECT_OVERVIEW.md` - tổng quan dự án
- `docs/ARCHITECTURE.md` - kiến trúc code
- `docs/DATABASE.md` - cấu trúc dữ liệu
- `docs/USER_PERMISSIONS.md` - quyền user
- `docs/API_FLOW.md` - luồng route và dữ liệu
- `docs/BOOST_CODE_TOUR.md` - hướng dẫn đọc code về Laravel Boost
- `docs/LARAVEL_BOOST_SEMINAR_GUIDE.md` - hướng dẫn thuyết trình Laravel Boost
- `docs/LECTURER_PRESENTATION.md` - bài nói cho giảng viên
- `docs/AI_KNOWLEDGE_BASE.md` - knowledge base cho AI chat
- `docs/DEPLOYMENT.md` - chạy và triển khai

## Chức năng chính

- đăng nhập theo role `admin`, `lecturer`, `student`
- quản lý topic seminar
- student đăng ký topic
- upload và review báo cáo
- lên lịch bảo vệ
- chấm điểm
- activity logs
- dashboard analytics
- AI chat
- admin user management
- trang tóm tắt topic để in

## Tech stack

- Laravel 13
- PHP 8.4
- Blade
- React cho dashboard analytics và AI chat
- SQLite cho môi trường local demo
- PHPUnit feature tests

## Cấu trúc code

- `app/Http/Controllers` - xử lý request
- `app/Models` - quan hệ dữ liệu
- `app/Support` - logic dùng chung
- `resources/views` - giao diện Blade
- `resources/js` - phần React
- `database/migrations` - schema
- `database/seeders` - dữ liệu demo

## Workflow ngắn

1. Lecturer tạo topic.
2. Student đăng ký topic.
3. Lecturer duyệt hoặc từ chối.
4. Student upload báo cáo.
5. Lecturer review báo cáo.
6. Student nộp lại nếu cần.
7. Lecturer lên lịch bảo vệ.
8. Lecturer chấm điểm.

## Demo account

- Admin: `admin@seminar.test` / `password`
- Lecturer: `lecturer@seminar.test` / `password`
- Student 1: `student1@seminar.test` / `password`
- Student 2: `student2@seminar.test` / `password`

## Chạy project nhanh

### Cách dễ nhất

Chạy launcher:

```powershell
powershell -ExecutionPolicy Bypass -File .\run-demo.ps1 -Seed
```

### Cách thủ công

Nếu muốn gõ ngắn trong PowerShell, tạo alias tạm `php84` trước:

```powershell
function php84 { & "C:\Users\Voduybinhv\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe" @args }
```

```bash
composer install
npm install
php84 artisan migrate:fresh --seed
npm run dev
php84 artisan serve
```

Mở:

- `http://127.0.0.1:8000`

Lưu ý:

- Nếu terminal của bạn đang trỏ `php` 8.3, hãy tạo alias `php84` hoặc chạy các lệnh `artisan` bằng PHP 8.4 đầy đủ như trong `docs/README-DEMO.md`.
- AI chat local demo mode là mặc định nên không cần OpenAI key để chạy demo.
- Nếu muốn demo đúng kiểu Boost hơn, hãy mở `docs/BOOST_OFFICIAL_STYLE_DEMO.md` trước khi thuyết trình.

## Ghi chú

- Nếu không có `OPENAI_API_KEY`, AI chat vẫn chạy ở local demo mode.
- Nếu frontend React chưa chạy, Laravel vẫn hoạt động.
- Nếu cần reset dữ liệu, chạy lại `php84 artisan migrate:fresh --seed`.


