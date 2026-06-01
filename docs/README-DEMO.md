# Hướng Dẫn Chạy Demo Laravel Boost

Tài liệu này chỉ nhằm giúp bạn mở project demo để quan sát Laravel Boost trong bối cảnh một codebase thật. Đây **không phải** tài liệu hướng dẫn xây dựng toàn bộ hệ thống seminar từ đầu.

Khi học hoặc thuyết trình, hãy xem đây là:

- hướng dẫn mở demo
- hướng dẫn kiểm tra luồng code
- hướng dẫn xem Boost được minh hoạ như thế nào trong một repo thật

## Các phần trong demo và ý nghĩa của chúng

Các màn hình dưới đây **không phải trọng tâm seminar**. Chúng chỉ là dữ liệu và luồng minh hoạ để Laravel Boost có ngữ cảnh thật:

- Đăng nhập theo vai trò `admin`, `lecturer`, `student` -> minh hoạ phân quyền để Boost có ngữ cảnh người dùng
- Dashboard tổng quan -> minh hoạ dữ liệu mà AI và docs có thể đọc
- Quản lý topic -> minh hoạ route, controller, model và query trong code thật
- Tìm kiếm và lọc topic -> minh hoạ request/response flow
- Student đăng ký topic -> minh hoạ validation, capacity và trạng thái dữ liệu
- Upload báo cáo -> minh hoạ file handling và review flow
- Lecturer review báo cáo -> minh hoạ xử lý nghiệp vụ nhiều bước
- Gửi yêu cầu chỉnh sửa hoặc chấp nhận báo cáo -> minh hoạ trạng thái dữ liệu
- Lên lịch bảo vệ -> minh hoạ dữ liệu liên kết giữa các bảng
- Chấm điểm và ghi chú -> minh hoạ cập nhật dữ liệu cuối luồng
- Activity logs -> minh hoạ logging và quan sát hệ thống
- AI chat hỗ trợ demo -> minh hoạ chỗ Boost và knowledge base phát huy tác dụng
- Admin quản lý user -> minh hoạ phân quyền quản trị
- Trang in tóm tắt topic -> minh hoạ xuất báo cáo / preview

## Boost nhìn thấy gì trong demo này

Boost không học từ UI nhìn đẹp hay xấu. Nó cần ngữ cảnh của repo:

- `composer.json` -> có `laravel/boost`
- `boost.json` -> cấu hình Boost
- `AGENTS.md` -> guideline cho agent chính
- `.github/skills/*` -> skills theo domain
- `.vscode/mcp.json` -> MCP server để agent đọc project
- `app/Support/SeminarAiChat.php` -> logic trả lời AI
- `app/Support/SeminarKnowledgeBase.php` -> cơ sở tri thức nội bộ
- `docs/AI_KNOWLEDGE_BASE.md` -> mô tả kiến thức mà AI dùng để trả lời

## Kịch bản demo chuẩn để thuyết trình

Đây là luồng nên dùng nếu bạn muốn giảng viên thấy rõ Laravel Boost đang hoạt động ở đâu.

### Bước 1. Mở phần cấu hình Boost trong repo

Trước khi chạy app, mở và chỉ vào các file này:

- `AGENTS.md`
- `boost.json`
- `.github/skills/laravel-best-practices/SKILL.md`
- `.github/skills/tailwindcss-development/SKILL.md`
- `.vscode/mcp.json`

Nói ngắn gọn:

- đây là nơi Boost cài guideline, skill và MCP vào repo
- đây là bằng chứng Boost không chỉ là lý thuyết

### Bước 2. Chạy project

Chạy các lệnh:

```bash
composer install
npm install
php84 artisan migrate:fresh --seed
npm run dev
php84 artisan serve
```

Nếu môi trường của bạn đã cài xong từ trước thì thường chỉ cần:

```bash
npm run dev
php84 artisan serve
```

Mở URL Laravel mà terminal in ra, thường là:

- `http://127.0.0.1:8000/login`

### Bước 3. Đăng nhập và mở chỗ có Boost rõ nhất

Đăng nhập bằng:

- `admin@seminar.test` / `password`
- hoặc `lecturer@seminar.test` / `password`

Sau đó mở:

- dashboard
- `AI Chat`

### Bước 4. Nói về Boost chứ không nói về seminar system

Khi demo, hãy chỉ vào:

- `SeminarAiChat.php`
- `SeminarKnowledgeBase.php`
- `AiChatController.php`
- `AGENTS.md`
- `.github/skills/*`
- `.vscode/mcp.json`

Điểm cần nói:

- Boost giúp AI hiểu đúng project
- AI không chỉ trả lời theo kiến thức chung
- demo project chỉ là ngữ cảnh để Boost đọc được route, model, DB, skills và MCP

### Bước 5. Hỏi thử AI chat các câu đúng trọng tâm Boost

Ví dụ:

1. `Laravel Boost trong dự án này dùng để làm gì?`
2. `File nào cho thấy Boost đã được cài vào repo?`
3. `Khi không có OpenAI key thì AI chat chạy theo cách nào?`
4. `AGENTS.md, .github/skills và .vscode/mcp.json có vai trò gì?`

Nếu câu trả lời bám đúng repo và nhắc đến các file trên, đó là lúc bạn chứng minh được Boost đang hoạt động theo đúng mục tiêu seminar.

### Danh sách câu hỏi mẫu để hỏi AI chat

Bạn có thể dùng các câu hỏi dưới đây khi demo:

#### Câu hỏi về Laravel Boost

- `Laravel Boost là gì và nó giúp gì cho lập trình viên Laravel?`
- `Boost đã được gắn vào project này ở những file nào?`
- `AGENTS.md có vai trò gì trong repo này?`
- `.github/skills/* dùng để làm gì?`
- `.vscode/mcp.json giúp AI đọc project như thế nào?`
- `SeminarAiChat.php đang làm nhiệm vụ gì?`
- `SeminarKnowledgeBase.php chứa loại thông tin nào?`
- `Khi không có OpenAI key thì chatbot chuyển sang chế độ nào?`
- `Demo project này chỉ đóng vai trò gì trong bài seminar?`
- `Tại sao Laravel Boost cần ngữ cảnh từ code thật thay vì chỉ prompt chung?`
- `Nếu muốn AI hiểu đúng project thì Boost đọc từ đâu?`
- `Khi hỏi về database thì AI chat sẽ dựa vào những file nào?`
- `Laravel Boost khác gì với một chatbot AI bình thường?`
- `Phần nào trong repo là bằng chứng rõ nhất cho việc đã cài Boost?`
- `Nếu thuyết trình ngắn 1 phút, tôi nên giải thích Boost thế nào?`

#### Câu hỏi về Seminar Manager

- `Tổng quan của demo project Seminar Manager là gì?`
- `Trong project này có những vai trò nào?`
- `Admin làm được gì trong hệ thống?`
- `Lecturer làm được gì trong hệ thống?`
- `Student làm được gì trong hệ thống?`
- `Topic seminar được lưu ở bảng nào?`
- `Bảng nào là trung tâm của toàn bộ workflow?`
- `Student đăng ký topic bằng cách nào?`
- `Topic có giới hạn số lượng đăng ký không?`
- `Lecturer duyệt đăng ký ở đâu?`
- `Quy trình upload báo cáo diễn ra như thế nào?`
- `Nếu báo cáo bị yêu cầu sửa thì sinh viên làm gì tiếp?`
- `Lập lịch bảo vệ được liên kết với dữ liệu nào?`
- `Điểm số cuối cùng được lưu và hiển thị như thế nào?`
- `Activity logs ghi lại những hành động nào?`
- `Trang in tóm tắt topic dùng để làm gì?`
- `Dashboard analytics hiển thị những loại thống kê nào?`
- `AI chat lấy dữ liệu dự án từ đâu để trả lời?`
- `Nếu muốn tìm hiểu code thì nên mở file nào trước?`
- `Nếu muốn xem luồng dữ liệu thì nên đọc file nào?`
- `Nếu muốn hiểu phân quyền thì nên hỏi AI câu gì?`
- `Nếu muốn demo ngắn gọn trước lớp thì nên hỏi AI những câu nào?`

### Bước 6. Kết luận demo

Kết luận bằng 1 câu:

> Laravel Boost không phải là tính năng cho user cuối. Nó là lớp hỗ trợ AI giúp hiểu đúng project Laravel. Demo project chỉ là ngữ cảnh để minh hoạ Boost đọc code, hiểu cấu trúc và trả lời theo dữ liệu thật của repo.

## Tài khoản demo

- Admin: `admin@seminar.test` / `password`
- Lecturer: `lecturer@seminar.test` / `password`
- Lecturer 2: `lecturer2@seminar.test` / `password`
- Lecturer 3: `lecturer3@seminar.test` / `password`
- Student 1: `student1@seminar.test` / `password`
- Student 2: `student2@seminar.test` / `password`
- Student 3: `student3@seminar.test` / `password`
- Student 4: `student4@seminar.test` / `password`
- Student 5: `student5@seminar.test` / `password`

## Cách chạy nhanh

```bash
composer install
npm install
php84 artisan migrate:fresh --seed
npm run dev
php84 artisan serve
```

Mở:

- `http://127.0.0.1:8000`

## Luồng demo ngắn

1. Chỉ vào `AGENTS.md`, `boost.json`, `.github/skills/*`, `.vscode/mcp.json`.
2. Chạy project bằng các lệnh ở trên.
3. Đăng nhập bằng `lecturer`.
4. Mở `AI Chat`.
5. Hỏi AI các câu về Boost và các file cài đặt.
6. Nếu cần, mở dashboard hoặc topic detail để nói thêm demo project chỉ là ngữ cảnh.

## Kịch bản demo có chỉ file code

Phần này dành cho lúc bạn vừa chạy app vừa mở file code để giảng viên thấy rõ "nó hoạt động ở đâu".

### Điểm dừng 1. Boost được cài ở đâu?

Mở và chỉ vào:

- `composer.json`
- `boost.json`
- `AGENTS.md`
- `.github/skills/laravel-best-practices/SKILL.md`
- `.github/skills/tailwindcss-development/SKILL.md`
- `.vscode/mcp.json`

Nói ngắn gọn:

- `composer.json` cho biết project có `laravel/boost`
- `boost.json` là cấu hình Boost của repo
- `AGENTS.md` là hướng dẫn agent chính
- `.github/skills/*` là kỹ năng theo domain
- `.vscode/mcp.json` là phần để AI đọc project qua MCP

### Điểm dừng 2. AI chat lấy dữ liệu từ đâu?

Mở và chỉ vào:

- `app/Http/Controllers/AiChatController.php`
- `app/Support/SeminarAiChat.php`
- `app/Support/SeminarKnowledgeBase.php`
- `docs/AI_KNOWLEDGE_BASE.md`

Nói ngắn gọn:

- `AiChatController` nhận message từ form hoặc React
- `SeminarAiChat` quyết định trả lời theo local demo mode
- `SeminarKnowledgeBase` chứa câu trả lời mẫu theo keyword
- `AI_KNOWLEDGE_BASE.md` giải thích bộ tri thức này để AI trả lời đúng

### Điểm dừng 3. Vì sao chatbot không cần key mà vẫn chạy?

Mở và chỉ vào:

- `app/Support/SeminarAiChat.php`
- `tests/Feature/AiChatTest.php`
- `docs/README-DEMO.md`

Nói ngắn gọn:

- local demo mode là mặc định để seminar ổn định
- code đã được thiết kế để trả lời từ knowledge base nội bộ
- test đã xác nhận chatbot chạy được khi không có OpenAI key

### Điểm dừng 4. Demo project lấy dữ liệu ở đâu?

Mở và chỉ vào:

- `routes/web.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/TopicController.php`
- `app/Http/Controllers/RegistrationController.php`
- `app/Http/Controllers/SubmissionController.php`
- `app/Http/Controllers/PresentationController.php`
- `app/Http/Controllers/ScoreController.php`
- `app/Http/Controllers/ActivityLogController.php`
- `database/seeders/DatabaseSeeder.php`

Nói ngắn gọn:

- route quyết định URL nào gọi controller nào
- controller là nơi nhận request và lấy dữ liệu
- seeder tạo dữ liệu demo để AI chat và dashboard có ngữ cảnh thật

### Điểm dừng 5. Nếu hỏi về seminar manager thì mở file nào?

Mở và chỉ vào:

- `docs/PROJECT_OVERVIEW.md`
- `docs/ARCHITECTURE.md`
- `docs/DATABASE.md`
- `docs/USER_PERMISSIONS.md`
- `docs/API_FLOW.md`
- `docs/BOOST_CODE_TOUR.md`

Nói ngắn gọn:

- đây là các file giúp người nghe hiểu luồng dự án
- nhưng nhấn mạnh rằng chúng chỉ là demo context
- trọng tâm chính của seminar vẫn là Laravel Boost

### Điểm dừng 6. Khi nào mở màn nào trên trình duyệt?

Thứ tự gợi ý:

1. Mở `http://127.0.0.1:8002/login`
2. Đăng nhập `lecturer@seminar.test`
3. Mở `dashboard`
4. Mở `AI Chat`
5. Hỏi AI các câu trong phần danh sách bên dưới
6. Nếu cần, mở `topics`, `topic detail` hoặc `activity` để chỉ file code tương ứng

## Chọn kiểu demo

Nếu bạn cần chọn thời lượng và cách trình bày, xem thêm:

- [HOC_THUOC_NHANH.md](HOC_THUOC_NHANH.md)
- [DEMO_MODES.md](DEMO_MODES.md)
- [BOOST_TERMINAL_COMMANDS.md](BOOST_TERMINAL_COMMANDS.md)
- [BOOST_VIDEO_DEMO.md](BOOST_VIDEO_DEMO.md)
- [BOOST_OFFICIAL_STYLE_DEMO.md](BOOST_OFFICIAL_STYLE_DEMO.md)

Gợi ý nhanh:

- **3 phút**: nói nhanh Boost là gì và chỉ file cấu hình
- **5 phút**: thêm AI chat local demo mode
- **10 phút**: thêm code tour và câu hỏi mẫu
- **Wow**: chỉ dùng nếu bạn đã test live agent/debug trước trên máy

Nếu bạn muốn bám sát cách Laravel giới thiệu Boost trên trang chính thức, hãy đọc thêm:

- `BOOST_OFFICIAL_STYLE_DEMO.md`

Tài liệu này ưu tiên các ý:

- Boost là MCP server cho Laravel
- AI đọc project thật qua route, schema, logs, docs
- AI guidelines và agent skills quan trọng hơn phần giao diện demo
- Seminar Manager chỉ là ngữ cảnh minh hoạ, không phải trọng tâm bài

## AI chat mặc định chạy ở local demo mode

Project **không cần OpenAI key** để demo.

Khi bạn không cấu hình:

```env
OPENAI_API_KEY=...
```

thì chatbot sẽ tự chuyển sang **local demo mode** và trả lời bằng cơ sở tri thức nội bộ.

Nếu có key hợp lệ, app mới thử gọi OpenAI. Nhưng với seminar, chế độ local demo mode là lựa chọn ổn định hơn.

## Lệnh chạy khuyến nghị cho seminar

Mở PowerShell tại thư mục `seminar-manager`, sau đó chạy:

### Cách dễ nhất

Chạy luôn file launcher:

```powershell
powershell -ExecutionPolicy Bypass -File .\run-demo.ps1 -Seed
```

Ý nghĩa:

- tự clear cache
- tự reset database demo nếu có `-Seed`
- tự mở `boost:mcp`
- tự mở Laravel server
- tự mở Vite

### Cách chạy thủ công

Nếu muốn gõ ngắn, tạo alias tạm `php84` trước:

```powershell
function php84 { & "C:\Users\Voduybinhv\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe" @args }
```

```powershell
cd "D:\HSU\2533Semester 3(2025-2026)\Phát triển Web sd Framework\Seminar\seminar-manager"
php84 artisan boost:mcp
php84 artisan optimize:clear
php84 artisan migrate:fresh --seed
php84 artisan serve --host=127.0.0.1 --port=8002
```

Lưu ý:

- chạy từng lệnh một, không dán cả khối code kèm dấu ```
- `boost:mcp` nên mở ở terminal riêng và để chạy nền
- `npm run dev` nên chạy ở terminal riêng khác
- nếu đã tạo alias `php84` ở trên, có thể thay đường dẫn dài bằng `php84`

Mở terminal khác và chạy:

```powershell
npm run dev
```

Nếu terminal Boost MCP đã chạy ở trên rồi thì ở terminal này chỉ cần `npm run dev`.

Sau đó mở:

- `http://127.0.0.1:8002/login`

Tài khoản demo:

- `lecturer@seminar.test` / `password`
- `student1@seminar.test` / `password`

Sau khi đăng nhập:

- mở `AI Chat`
- hỏi các câu về Boost, `AGENTS.md`, `.github/skills/*`, `.vscode/mcp.json`

## Khi nào cần OpenAI key?

Chỉ khi bạn muốn thử chat cloud thật.

Với seminar, không nên phụ thuộc vào key. Dùng local demo mode sẽ an toàn hơn vì:

- không sợ key hết hạn
- không sợ billing/quota
- không sợ mạng chập chờn
- vẫn chứng minh được Laravel Boost đang có mặt trong repo

## Khi thuyết trình, nên nhấn mạnh

- Boost là đề tài chính
- demo project chỉ là bối cảnh để hiểu Boost hoạt động ra sao
- file agent, skills, MCP và knowledge base mới là phần thể hiện Boost rõ nhất
- phần seminar workflow chỉ là ví dụ để AI có ngữ cảnh thật
- khi demo, hãy trả lời 3 câu: Boost nằm ở đâu, AI đọc ngữ cảnh ở đâu, và khi không có key thì fallback thế nào

## Lưu ý khi chạy trên máy này

- Nếu giao diện React chưa chạy, phần Laravel vẫn hoạt động bình thường.
- Nếu dữ liệu demo chưa đúng, chạy lại `php84 artisan migrate:fresh --seed`.
- Nếu SQLite báo lỗi I/O trên Windows, đổi `DB_DATABASE` sang file `.sqlite` trong `C:/Users/<you>/AppData/Local/Temp/` rồi chạy lại migrate.
- Nếu cần reset hẳn, chạy lại toàn bộ lệnh cài và seed ở trên.

## Kiểm tra nhanh

- `php84 artisan test` phải pass
- `php84 artisan migrate:fresh --seed` phải pass

## Tài liệu liên quan

- `DOCUMENTATION_INDEX.md`
- `PROJECT_OVERVIEW.md`
- `ARCHITECTURE.md`
- `DATABASE.md`
- `BOOST_CODE_TOUR.md`



