# Bài Thuyết Trình Laravel Boost

https://gamma.app/docs/Laravel-focused-MCP-server-cho-phat-trien-AI-assisted-rz6i3emmf2jl57a 

File này dành cho buổi thuyết trình với giảng viên.

**Trọng tâm là Laravel Boost**. Demo project chỉ là mã nguồn minh hoạ để Boost có ngữ cảnh thật.

Quy tắc khi nói:

- nói về Boost trước
- nói về project demo sau
- project demo chỉ là ngữ cảnh để chỉ vào code thật
- không trình bày demo project như một sản phẩm phải xây hoàn chỉnh từ đầu

Mục tiêu của bài thuyết trình:

- hiểu Laravel Boost là gì
- hiểu Boost giải quyết vấn đề gì cho AI trên Laravel
- hiểu các file code nào làm nhiệm vụ gì
- hiểu luồng hoạt động của AI chat trong dự án
- biết lúc demo thì mở file nào để chỉ đúng chỗ đang hoạt động
- không trình bày project như một sản phẩm hoàn chỉnh cần xây từ đầu

## 1. Mở bài

> Laravel Boost là công cụ hỗ trợ AI cho Laravel, giúp AI hiểu đúng project thật thay vì đoán theo kiến thức chung. Em dùng một demo project để minh hoạ Boost hoạt động như thế nào trong thực tế, còn trọng tâm của bài là giải thích Boost và các file liên quan trong repo.

## 2. Boost giải quyết gì?

AI bình thường dễ:

- đoán sai route
- đoán sai schema
- dùng sai API Laravel
- trả lời theo version cũ

Boost giảm lỗi đó bằng cách cung cấp:

- ngữ cảnh của project
- tài liệu đúng version
- MCP tools
- AI guidelines
- agent skills

## 3. Boost nằm ở đâu trong repo?

Các file quan trọng:

- `composer.json`
- `boost.json`
- `AGENTS.md`
- `.github/skills/laravel-best-practices`
- `.github/skills/tailwindcss-development`
- `.vscode/mcp.json`
- `app/Support/SeminarAiChat.php`
- `app/Support/SeminarKnowledgeBase.php`
- `app/Http/Controllers/AiChatController.php`
- `docs/AI_KNOWLEDGE_BASE.md`

### 3.8 Câu nói ngắn để mở phần này

> Trong repo này, phần quan trọng nhất để chứng minh Laravel Boost không nằm ở giao diện, mà nằm ở `composer.json`, `boost.json`, `AGENTS.md`, `.github/skills/*`, `.vscode/mcp.json` và các file AI chat support.

### 3.1 `composer.json`

```json
"require-dev": {
    "laravel/boost": "^2.4"
}
```

Ý nghĩa:

- Boost là dependency cho môi trường phát triển
- nó không phải tính năng dành cho người dùng cuối

### 3.2 `boost.json`

File này cho biết repo đã bật Boost và MCP như thế nào.

Khi trình bày, cần nói:

- Boost đọc ngữ cảnh từ project
- Boost không hoạt động tách rời khỏi codebase
- Boost dùng tài liệu và công cụ của Laravel để hiểu repo

### 3.3 `AGENTS.md` và `.github/skills/*`

Khi chạy `php84 artisan boost:install` với agent GitHub Copilot, Boost sinh ra:

- `AGENTS.md`
- thư mục `.github/skills/`
- cấu hình MCP trong `.vscode/mcp.json`

Ý nghĩa:

- đây là dấu hiệu Boost đã được cài thật vào repo
- agent đọc hướng dẫn ngay từ file trong project
- skills tách phần hướng dẫn chuyên biệt cho từng domain

Khi demo, nên chỉ vào cùng lúc:

- `AGENTS.md`
- `.github/skills/laravel-best-practices/SKILL.md`
- `.github/skills/tailwindcss-development/SKILL.md`

và nói:

> Đây là nơi Boost/agent đọc cách làm việc trong repo, chứ không phải đoán bằng prompt chung.

### 3.4 `SeminarKnowledgeBase.php`

File này là cơ sở tri thức nội bộ cho trợ lý AI.

```php
return implode("\n", [
    'Cơ sở tri thức của dự án:',
    '- Demo project là ứng dụng Laravel phục vụ quy trình seminar học thuật trong môi trường đại học.',
    '- Ứng dụng hỗ trợ ba vai trò chính: admin, lecturer và student.',
]);
```

Ý nghĩa:

- AI có thêm thông tin dự án thay vì chỉ đoán
- nội dung này được chèn vào prompt
- đây là lý do local demo mode vẫn trả lời được đúng ngữ cảnh

### 3.5 `SeminarAiChat.php`

File này quyết định cách AI trả lời.

```php
if (! $apiKey) {
    return [
        'reply' => $this->localReply($user, $message),
        'response_id' => null,
        'model' => 'local-demo',
    ];
}
```

Ý nghĩa:

- nếu không có API key, trợ lý vẫn chạy
- đây là fallback quan trọng để demo ổn định

Các hàm cần nhớ:

- `reply()` - điểm vào chính
- `instructions()` - dựng prompt có ngữ cảnh
- `studentContext()` - ngữ cảnh riêng cho student
- `lecturerContext()` - ngữ cảnh riêng cho lecturer
- `adminContext()` - ngữ cảnh riêng cho admin
- `localReply()` - câu trả lời cục bộ khi không gọi OpenAI

Điểm rất quan trọng để nói:

> AI chat trong demo này không phải là phần “tự nghĩ” từ đầu. Nó được bơm ngữ cảnh từ knowledge base nội bộ và trả về local demo mode nếu không có key.

### 3.6 `AiChatController.php`

Controller này làm nhiệm vụ:

- nhận request chat
- tạo conversation
- lưu message user
- gọi `SeminarAiChat`
- lưu message assistant
- trả JSON hoặc redirect

Code cần nhớ:

```php
$result = $chat->reply(
    $user,
    $effectiveMessage,
    $conversation->last_response_id,
);
```

Ý nghĩa:

- controller không tự viết logic AI
- controller chỉ điều phối request và response
- phần trả lời nằm trong service riêng

Khi mở file này trên lớp, hãy chỉ vào 3 chỗ:

1. `index()` để thấy dữ liệu conversation được nạp ra sao
2. `store()` để thấy request đi vào rồi lưu lại thế nào
3. `redirect()->route('ai-chat.index', ['conversation' => $conversation->id])` để thấy sau khi gửi xong, trang quay về đúng hội thoại vừa trả lời

### 3.7 `AI_KNOWLEDGE_BASE.md`

File này mô tả AI chat biết gì về dự án.

Điểm cần nói:

- AI chat không học máy lại toàn bộ dự án
- nó dùng bộ tri thức được chọn lọc
- mục tiêu là bám sát code thật, không bịa

## 4. Cấu trúc code cần nói

### Routes

```php
Route::middleware('auth')->group(function () {
    Route::get('/ai-chat', [AiChatController::class, 'index'])->name('ai-chat.index');
    Route::post('/ai-chat', [AiChatController::class, 'store'])->name('ai-chat.store');
});
```

Ý nghĩa:

- route là cửa vào của feature
- AI chat được bảo vệ bởi `auth`
- route chỉ là điểm vào, không phải nơi chứa nghiệp vụ

Khi demo, nên mở thêm:

- `routes/web.php`
- `app/Http/Controllers/AiChatController.php`

và nói:

> Route chỉ trỏ đường đi, còn logic thật nằm ở controller và support class.

### AI chat service

```php
if (! $apiKey) {
    return [
        'reply' => $this->localReply($user, $message),
        'response_id' => null,
        'model' => 'local-demo',
    ];
}
```

Ý nghĩa:

- không có API key thì vẫn demo được
- chế độ demo cục bộ là điểm rất hữu ích khi thuyết trình
- logic này cho thấy Boost demo được cả khi không gọi OpenAI

> Khi không có key, app không dừng lại. Nó chuyển sang local demo mode và vẫn trả lời được dựa trên knowledge base.

### Knowledge base

```php
return implode("\n", [
    'Cơ sở tri thức của dự án:',
    '- Demo project là ứng dụng Laravel phục vụ quy trình seminar học thuật trong môi trường đại học.',
    '- Ứng dụng hỗ trợ ba vai trò chính: admin, lecturer và student.',
]);
```

Ý nghĩa:

- AI không trả lời chung chung
- AI có cơ sở tri thức nội bộ của dự án
- đây là phần giúp Boost hiểu project thật

Khi demo, có thể mở song song:

- `app/Support/SeminarKnowledgeBase.php`
- `docs/AI_KNOWLEDGE_BASE.md`

để chỉ ra câu hỏi nào đang map vào câu trả lời nào.

## 5. Demo project này dùng để minh hoạ cái gì?

Project giúp minh hoạ 3 điều:

1. Boost cần ngữ cảnh thật để AI trả lời đúng.
2. Cơ sở tri thức giúp AI hiểu project tốt hơn.
3. Laravel app có thể gắn AI vào mà vẫn giữ kiến trúc rõ ràng.

### Câu nói mẫu

> Demo project này không phải là mục tiêu chính của bài. Nó là dữ liệu minh hoạ để Laravel Boost có ngữ cảnh thật, giúp AI trả lời đúng hơn về code và workflow của project.

## 6. Kiến trúc nên nói

- Laravel chịu trách nhiệm chính cho luồng xử lý
- Database lưu dữ liệu seminar
- Support classes gom logic dùng chung
- AI chat là nơi thể hiện Boost trong thực tế
- React chỉ dùng ở dashboard/AI chat để tăng tương tác
- toàn bộ phần còn lại chỉ là bối cảnh demo để bạn chỉ vào code thật khi nói

### File nên mở khi nói phần này

- `docs/ARCHITECTURE.md`
- `docs/BOOST_CODE_TOUR.md`
- `docs/DATABASE.md`
- `docs/API_FLOW.md`
- `app/Http/Controllers/*`
- `app/Support/*`

## 7. Luồng chạy của toàn bộ demo project

Phần này chỉ dùng để chứng minh em hiểu code chạy như thế nào trong project minh hoạ. Nó không phải trọng tâm của seminar.

### 7.1 Từ route đến controller

File trung tâm:

- `routes/web.php`

Vai trò:

- khai báo đường đi của từng màn
- gắn middleware theo vai trò
- trỏ request vào controller đúng
- giúp Boost và AI chat có thể hiểu được luồng xử lý của demo project

### Câu nói mẫu

> Em chỉ dùng project này để minh hoạ cách Boost đọc code thật: route đi vào controller, controller lấy dữ liệu, service xử lý logic và knowledge base giúp AI trả lời đúng.

### 7.2 Dashboard

File:

- `app/Http/Controllers/DashboardController.php`

Vai trò:

- gom số liệu tổng quan
- chuẩn bị data cho Blade và React
- tách thống kê theo trạng thái, vai trò, khoa/phòng, category

Khi mở file này trên lớp, hãy chỉ vào:

- `app/Http/Controllers/DashboardController.php`
- `resources/js/components/DashboardAnalytics.jsx`

và nói:

> Dashboard là nơi Laravel và React gặp nhau, nhưng nó vẫn chỉ là phần minh hoạ cho Boost đọc được ngữ cảnh thật của app.

### 7.3 Topic

File:

- `app/Http/Controllers/TopicController.php`

Vai trò:

- liệt kê topic
- tạo topic
- cập nhật topic
- xóa topic
- nạp quan hệ lecturer, registrations, submissions, scores, presentations

Nên mở thêm:

- `resources/views/topics/*`
- `app/Models/Topic.php`

để chỉ ra topic là mô hình dữ liệu trung tâm của demo workflow.

### 7.4 Registration

File:

- `app/Http/Controllers/RegistrationController.php`

Vai trò:

- student đăng ký topic
- lecturer/admin duyệt hoặc từ chối
- kiểm tra trạng thái open/closed
- kiểm tra capacity

Nên nói:

> Đây là chỗ demo project cho thấy workflow có điều kiện thật, để Boost có dữ liệu và logic cụ thể thay vì chỉ có text mô tả.

### 7.5 Submission

File:

- `app/Http/Controllers/SubmissionController.php`

Vai trò:

- student upload report
- lecturer/admin review report
- quản lý resubmission và revision
- tải xuống hoặc xóa báo cáo

Nên mở:

- `app/Models/Submission.php`
- `resources/views/topics/show.blade.php`

để chỉ ra vòng đời báo cáo trong demo.

### 7.6 Presentation

File:

- `app/Http/Controllers/PresentationController.php`

Vai trò:

- lên lịch bảo vệ
- sửa lịch bảo vệ
- chỉ làm việc trên registration đã approved

Nên nhấn mạnh:

> Đây là dữ liệu nối theo workflow, giúp dự án minh hoạ cách nhiều bảng liên kết với nhau trong một luồng seminar thật.

### 7.7 Score

File:

- `app/Http/Controllers/ScoreController.php`

Vai trò:

- nhập điểm cuối cùng
- chỉnh sửa điểm nếu cần
- gắn điểm vào registration

Nên chỉ vào:

- `app/Models/Score.php`
- `resources/views/topics/show.blade.php`

### 7.8 Activity log

File:

- `app/Http/Controllers/ActivityLogController.php`
- `app/Support/ActivityLogger.php`

Vai trò:

- ghi lại những hành động quan trọng
- lọc log theo vai trò
- giúp giảng viên nhìn thấy hệ thống đang vận hành ra sao

Mở thêm:

- `app/Support/ActivityLogger.php`
- `resources/views/activity/index.blade.php`

và nói:

> Activity log cho thấy hệ thống có dấu vết thao tác, nhờ đó demo nhìn thật hơn và AI chat cũng có thêm ngữ cảnh để trả lời.

### 7.9 Support classes

Các file:

- `app/Support/SeminarNotifier.php`
- `app/Support/SeminarAiChat.php`
- `app/Support/SeminarKnowledgeBase.php`
- `app/Support/ActivityLogger.php`

Vai trò:

- gom logic gửi thông báo
- gom logic AI
- gom logic knowledge base
- gom logic ghi log

Khi cần chốt ý:

> Các support class là nơi gom logic dùng chung để controller gọn hơn, còn Boost thì đọc các file này để hiểu cách project vận hành.

### 7.10 Models

Các file:

- `app/Models/User.php`
- `app/Models/Topic.php`
- `app/Models/Registration.php`
- `app/Models/Submission.php`
- `app/Models/Presentation.php`
- `app/Models/Score.php`
- `app/Models/ActivityLog.php`

Vai trò:

- mô tả bảng dữ liệu
- mô tả quan hệ giữa bảng
- cho biết dữ liệu nào là trung tâm

Khi demo, nên nhấn vào:

- `Registration` là trung tâm của workflow
- các model còn lại nối theo nó
- đây là lý do AI chat có thể tóm tắt đúng luồng học thuật của project

### 7.11 Giao diện Blade và React

Các file:

- `resources/views/dashboard.blade.php`
- `resources/views/ai-chat.blade.php`
- `resources/views/topics/*`
- `resources/views/users/*`
- `resources/js/app.jsx`
- `resources/js/components/AiChat.jsx`
- `resources/js/components/DashboardAnalytics.jsx`

Vai trò:

- Blade render khung giao diện
- React xử lý phần tương tác cao
- dashboard và AI chat là hai nơi React xuất hiện rõ nhất
- chỉ là lớp giao diện để minh hoạ code thật, không phải trọng tâm của seminar

Nói một câu ngắn:

> Giao diện ở đây chỉ là lớp trình bày; trọng tâm của bài là Boost hiểu project qua code, config, skill và MCP.

## 8. Câu hỏi giảng viên hay hỏi

### Boost có phải model AI không?

Không. Boost không phải model AI. Nó là lớp hỗ trợ AI cho Laravel.

### Tại sao cần project demo?

Vì Boost dễ hiểu hơn khi gắn vào một mã nguồn thật có route, model, database và luồng xử lý rõ ràng.

Nhưng khi thuyết trình, trọng tâm vẫn là Boost và cách AI đọc ngữ cảnh của dự án, không phải khoe project seminar.

### Nếu không có khóa OpenAI thì sao?

Vẫn chạy được bằng chế độ demo cục bộ.

### Nếu giảng viên hỏi: demo này có phải để chứng minh xây hệ thống seminar không?

Trả lời:

> Không ạ. Demo project chỉ là bối cảnh để Boost có code thật mà đọc. Mục tiêu chính là hiểu cách Laravel Boost giúp AI hiểu project Laravel.

## 9. Bố cục nói khi thuyết trình

1. Nói Boost là gì
2. Nói Boost giải quyết vấn đề gì
3. Nói Boost gồm những gì
4. Chỉ ra file code liên quan
5. Giải thích luồng chạy của demo project
6. Giải thích demo project minh hoạ Boost ra sao
7. Cho xem AI chat / cơ sở tri thức
8. Kết luận

## 10. Kịch bản demo theo từng điểm dừng

### Điểm dừng A. Boost được cài ở đâu?

Mở:

- `composer.json`
- `boost.json`
- `AGENTS.md`
- `.github/skills/*`
- `.vscode/mcp.json`

Nói:

> Đây là cấu hình và guideline để AI hiểu repo. Boost không chỉ là một thư viện, nó là một bộ công cụ và file cấu hình giúp agent đọc đúng project.

### Điểm dừng B. AI chat hoạt động thế nào?

Mở:

- `app/Http/Controllers/AiChatController.php`
- `app/Support/SeminarAiChat.php`
- `app/Support/SeminarKnowledgeBase.php`
- `docs/AI_KNOWLEDGE_BASE.md`

Nói:

> Khi user gửi câu hỏi, controller nhận request, service quyết định trả lời local demo mode hay context khác, còn knowledge base giúp câu trả lời bám vào nội dung thật của repo.

### Điểm dừng C. Nếu hỏi về Boost thì mở gì?

Mở:

- `docs/LARAVEL_BOOST_SEMINAR_GUIDE.md`
- `docs/BOOST_CODE_TOUR.md`
- `docs/README-DEMO.md`
- `docs/BOOST_OFFICIAL_STYLE_DEMO.md`

Nói:

> Đây là các file em dùng để học và demo đúng trọng tâm Laravel Boost. Nếu muốn bám sát cách Laravel giới thiệu Boost trên trang chính thức, em sẽ mở thêm `BOOST_OFFICIAL_STYLE_DEMO.md`. Còn Seminar Manager chỉ là ngữ cảnh minh hoạ.

## 11. Câu nói kết thúc ngắn để học thuộc

> Laravel Boost giúp AI hiểu project Laravel bằng ngữ cảnh thật, guideline, skills, MCP và knowledge base. Demo project chỉ là bối cảnh để minh hoạ Boost đọc code, hiểu cấu trúc và trả lời theo dữ liệu thật của repo.

### Điều không nên nói quá nhiều

- không sa đà vào chuyện xây seminar manager từ đầu
- không sa đà vào chuyện xây demo project từ đầu
- không biến bài thuyết trình thành demo tính năng CRUD
- không kể dài về UI nếu không liên quan đến Boost
- không bỏ qua phần `composer.json`, `boost.json`, `SeminarAiChat.php`, `SeminarKnowledgeBase.php`
- không trình bày project demo như một sản phẩm độc lập; luôn kéo câu chuyện quay về Laravel Boost

## 12. Kết luận ngắn

> Laravel Boost giúp AI hiểu đúng project Laravel bằng ngữ cảnh, tài liệu, schema và công cụ hỗ trợ. Demo project chỉ là bối cảnh để minh hoạ cách Boost hoạt động trong thực tế.

## 13. Mấu chốt để nhớ

Khi nhìn vào code, hãy trả lời 4 câu:

1. Boost nằm ở đâu trong repo?
2. AI lấy ngữ cảnh từ đâu?
3. Trợ lý AI trả lời bằng logic nào?
4. Project demo này chỉ đang minh hoạ điều gì?


