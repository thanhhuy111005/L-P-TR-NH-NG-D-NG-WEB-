# Explore Vietnam – Chạm đến Việt Nam, khám phá những điều tuyệt vời

## 1. Giới thiệu dự án
Website quảng bá du lịch, văn hóa, thiên nhiên, ẩm thực, di sản và con người Việt Nam, dành cho khách trong nước và quốc tế. Gồm phần website công khai (10 trang) và hệ thống quản trị nội dung (Admin) đầy đủ CRUD. Toàn bộ dữ liệu lấy từ MySQL.

## 2. Mục tiêu và ý nghĩa
- Giới thiệu hình ảnh Việt Nam theo ba miền Bắc – Trung – Nam.
- Lưu giữ, lan tỏa văn hóa (ẩm thực, lễ hội, di sản, làng nghề).
- Tạo nơi cộng đồng chia sẻ trải nghiệm (có kiểm duyệt).
- Thực hành xây dựng ứng dụng web PHP + MySQL hoàn chỉnh: giao diện, CRUD, upload, phân trang, tìm kiếm, bảo mật cơ bản.

## 3. Công nghệ sử dụng
HTML5, CSS3, JavaScript thuần · PHP 8.x (không framework) · MySQL/MariaDB · PDO + prepared statements · XAMPP (Apache + MySQL) · Font Be Vietnam Pro & Playfair Display (Google Fonts).

## 4. Cấu trúc thư mục
```
explore_vietnam/
├── index.php, destinations.php, destination.php   # Trang chủ, khám phá, chi tiết địa điểm
├── culture.php, culture-detail.php                # Văn hóa
├── guides.php, post.php                           # Cẩm nang, chi tiết bài viết
├── community.php, about.php, contact.php, 404.php
├── admin/                                         # Khu vực quản trị
│   ├── login.php, logout.php, index.php (dashboard), account.php
│   ├── destinations.php + destination-form.php
│   ├── categories.php
│   ├── posts.php + post-form.php
│   ├── culture.php + culture-form.php
│   ├── community.php + community-form.php
│   └── contacts.php
├── includes/        # config.php, db.php (PDO), functions.php, cards.php, header/footer, admin_layout.php
├── assets/          # css/style.css, css/admin.css, js/main.js, js/admin.js, img/
├── uploads/         # Ảnh do người dùng/admin tải lên
└── database/explore_vietnam.sql                   # Cấu trúc + dữ liệu mẫu
```

## 5. Yêu cầu cài đặt
XAMPP với PHP 8.0 trở lên (khuyến nghị 8.2), có Apache + MySQL. Trình duyệt hiện đại.

## 6. Hướng dẫn cài XAMPP
1. Tải XAMPP tại https://www.apachefriends.org và cài đặt.
2. Mở **XAMPP Control Panel**, bấm **Start** cho **Apache** và **MySQL** (cả hai phải hiện nền xanh).

## 7–8. Tạo database và import file SQL
1. Giải nén `ExploreVietnam_Final.zip`, chép thư mục `explore_vietnam` vào `C:\xampp\htdocs\`.
2. Mở http://localhost/phpmyadmin → tab **Import** → **Choose file** → chọn `database/explore_vietnam.sql` → **Import**.
   (File tự tạo database `explore_vietnam`, các bảng và dữ liệu mẫu; không cần tạo tay.)
3. Nếu import báo lỗi dung lượng, vào tab **SQL**, dán nội dung file và bấm **Go**.

## 9. Cấu hình kết nối PHP
Mặc định XAMPP (`root`, mật khẩu rỗng, `localhost`) chạy ngay, không cần sửa. Nếu khác, mở `includes/config.php` hoặc tạo `includes/config.local.php` (copy từ `config.local.example.php`) và sửa `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`. Muốn xem lỗi chi tiết khi phát triển: `define('DEBUG', true);`.

## 10. Chạy website trên localhost
- Website: http://localhost/explore_vietnam/
- Nếu để ở thư mục khác, đường dẫn gốc được tự nhận diện (BASE_URL).

## 11–12. Đăng nhập Admin
- Đường dẫn: http://localhost/explore_vietnam/admin/login.php
- Tài khoản: **admin** · Mật khẩu: **Admin@123** (hãy đổi tại *Tài khoản → Đổi mật khẩu*).
- Nhập sai 5 lần sẽ bị khoá đăng nhập 60 giây.

## 13. Hướng dẫn sử dụng các chức năng
| Module | Chức năng |
|---|---|
| Tổng quan | Thống kê địa điểm, bài viết, danh mục, văn hóa, cộng đồng, liên hệ; danh sách mới nhất |
| Địa điểm | Xem, thêm, sửa, xóa; tìm kiếm; lọc tỉnh/danh mục/trạng thái; phân trang; upload ảnh; đánh dấu nổi bật; ẩn/xuất bản |
| Danh mục | Thêm, sửa, xóa (chặn xóa khi đang được dùng); loại: địa điểm / bài viết |
| Bài viết | CRUD, upload ảnh, bản nháp/xuất bản |
| Văn hóa | CRUD cho Ẩm thực, Lễ hội, Di sản, Làng nghề (tab theo loại) |
| Cộng đồng | Xem, thêm, sửa, xóa; **Duyệt / Ẩn** bài do người dùng gửi |
| Liên hệ | Xem tin nhắn, đánh dấu đã/chưa đọc, trả lời bằng email, xóa |

Ảnh: chọn file tải lên (JPG/PNG/WEBP/GIF ≤ 3 MB) **hoặc** chọn ảnh minh họa có sẵn. Nội dung dài: tách đoạn bằng một dòng trống.

## 13b. Bong bóng chat và bản đồ Google Maps
- **Chat hỏi đáp nhanh:** nút 💬 ở góc phải dưới mọi trang. Dữ liệu trả lời lấy từ `chat.php` (từ khóa + tra cứu địa điểm trong CSDL), file giao diện `assets/js/chat.js`; sửa câu trả lời trong `chat.php`.
- **Bản đồ:** trang `map.php` (menu *Bản đồ*) và khung bản đồ ở trang chi tiết địa điểm, trang Liên hệ. Dùng bản nhúng công khai của Google Maps, không cần API key; cần có Internet.

## 14. Triển khai lên hosting miễn phí (InfinityFree…)
1. Đăng ký hosting PHP + MySQL miễn phí, tạo database trong control panel (ghi lại host, tên DB, user, mật khẩu).
2. Vào phpMyAdmin của hosting, chọn database vừa tạo, **Import** file SQL. Nếu tên DB khác `explore_vietnam` và import báo lỗi ở dòng `CREATE DATABASE`/`USE`, hãy xóa hai dòng đó khỏi file trước khi import.
3. Tải toàn bộ mã nguồn (trừ thư mục `database/`) lên `htdocs/` bằng FTP/File Manager.
4. Tạo `includes/config.local.php` với thông tin DB của hosting (xem file mẫu).
5. Cấp quyền ghi cho `uploads/` (thường 755). Truy cập tên miền và thử đăng nhập admin.

## 15. Lưu ý khi demo và bảo vệ đồ án
- Bật Apache **và** MySQL trước khi mở web; lỗi "Chưa kết nối được cơ sở dữ liệu" nghĩa là MySQL chưa bật hoặc chưa import SQL.
- Demo theo luồng: Trang chủ → Khám phá (tìm kiếm/lọc/phân trang) → Chi tiết → Gửi bài ở Cộng đồng và form Liên hệ → vào Admin duyệt bài/đọc tin → thêm/sửa/xóa địa điểm có upload ảnh.
- Điểm bảo mật nên nêu: PDO prepared statements, `htmlspecialchars` chống XSS, token CSRF trên mọi form, băm mật khẩu `password_hash`, kiểm tra loại file khi upload, session + phân quyền admin, khóa đăng nhập sai nhiều lần.
- Nhớ đổi mật khẩu admin và tắt `DEBUG` khi đưa lên hosting.
- Cần Internet để tải font Google Fonts; nếu mất mạng giao diện vẫn dùng font hệ thống thay thế.
