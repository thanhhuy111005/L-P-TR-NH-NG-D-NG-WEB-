<?php
/**
 * API hỏi đáp nhanh cho bong bóng chat (trả JSON).
 * Không dùng dịch vụ AI bên ngoài: trả lời theo từ khoá + tra cứu địa điểm trong CSDL.
 * GET ?q=câu hỏi
 */
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function chat_out(string $reply, array $links = [], array $suggest = []): never
{
    echo json_encode(['reply' => $reply, 'links' => $links, 'suggest' => $suggest], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$q = trim(mb_substr(get_str('q'), 0, 120));
$defaultSuggest = ['Thời điểm đẹp để đi?', 'Món ăn nên thử', 'Xem bản đồ', 'Liên hệ'];
if ($q === '') {
    chat_out('Xin chào! Mình là trợ lý của ' . SITE_NAME . '. Bạn muốn hỏi về điểm đến, ẩm thực, lễ hội hay cách liên hệ?', [], $defaultSuggest);
}

// Chuẩn hoá: bỏ dấu, chữ thường, nối bằng dấu gạch ngang; bao quanh để khớp theo từ
$s = '-' . slugify($q) . '-';
$has = static function (array $keywords) use ($s): bool {
    foreach ($keywords as $k) {
        if (str_contains($s, '-' . $k . '-')) { return true; }
    }
    return false;
};

// ---- Câu hỏi thường gặp ----
if ($has(['xin-chao', 'chao', 'hello', 'hi', 'alo'])) {
    chat_out('Chào bạn! Bạn muốn tìm hiểu điều gì về du lịch Việt Nam?', [], $defaultSuggest);
}
if ($has(['cam-on', 'thanks', 'thank-you'])) {
    chat_out('Rất vui được giúp bạn! Chúc bạn có chuyến đi thật vui.', [], $defaultSuggest);
}
if ($has(['ban-do', 'duong-di', 'chi-duong', 'google-map', 'google-maps', 'map'])) {
    chat_out('Bạn có thể xem vị trí các điểm đến trên Google Maps và bấm để mở chỉ đường.', [['label' => 'Mở trang Bản đồ', 'url' => url('map.php')]], ['Thời điểm đẹp để đi?', 'Liên hệ']);
}
if ($has(['thoi-diem', 'mua', 'khi-nao', 'thang-may', 'thoi-tiet'])) {
    chat_out('Miền Bắc đẹp nhất vào mùa thu (tháng 9–11) và mùa xuân (tháng 2–4). Miền Trung nắng đẹp từ tháng 2 đến tháng 8, tháng 9–12 có thể mưa bão. Miền Nam đẹp vào mùa khô (tháng 12–4). Nên xem dự báo thời tiết gần ngày đi.',
        [['label' => 'Cẩm nang du lịch theo mùa', 'url' => url('post.php?slug=du-lich-viet-nam-theo-mua')]], ['Món ăn nên thử', 'Xem bản đồ']);
}
if ($has(['an-uong', 'am-thuc', 'mon-an', 'an-gi', 'pho', 'banh-mi'])) {
    chat_out('Ẩm thực ba miền rất đa dạng: phở, bún chả (Bắc), bún bò Huế, mì Quảng, cao lầu (Trung), cơm tấm, hủ tiếu (Nam). Xem mục Ẩm thực để biết thêm.',
        [['label' => 'Mục Ẩm thực', 'url' => url('culture.php?type=am-thuc')]], ['Lễ hội', 'Thời điểm đẹp để đi?']);
}
if ($has(['le-hoi', 'tet', 'trung-thu'])) {
    chat_out('Một số lễ hội tiêu biểu: Tết Nguyên đán, Tết Trung thu, lễ hội chùa Hương. Xem mục Lễ hội để biết chi tiết.',
        [['label' => 'Mục Lễ hội', 'url' => url('culture.php?type=le-hoi')]], ['Món ăn nên thử', 'Xem bản đồ']);
}
if ($has(['lien-he', 'email', 'dien-thoai', 'sdt', 'hotline', 'dia-chi', 'tu-van'])) {
    chat_out('Bạn có thể gửi tin nhắn ở trang Liên hệ, đội ngũ sẽ trả lời trong vòng 24 giờ làm việc (Thứ Hai–Thứ Sáu, 8:00–17:30).',
        [['label' => 'Trang Liên hệ', 'url' => url('contact.php')]], ['Xem bản đồ', 'Thời điểm đẹp để đi?']);
}
if ($has(['cong-dong', 'chia-se', 'gui-bai', 'dang-bai'])) {
    chat_out('Bạn có thể gửi bài chia sẻ ở trang Cộng đồng. Bài sẽ được quản trị viên duyệt trước khi hiển thị.',
        [['label' => 'Trang Cộng đồng', 'url' => url('community.php#share')]], $defaultSuggest);
}
if ($has(['visa', 'thi-thuc', 'ho-chieu', 'nhap-canh'])) {
    chat_out('Điều kiện nhập cảnh và thị thực có thể thay đổi. Hãy kiểm tra trên cổng thông tin chính thức của cơ quan quản lý xuất nhập cảnh Việt Nam trước khi đi.',
        [['label' => 'Cẩm nang: chuẩn bị cho chuyến đi', 'url' => url('post.php?slug=chuan-bi-du-lich-viet-nam-lan-dau')]], $defaultSuggest);
}
if ($has(['gia-ve', 'dat-ve', 'dat-phong', 'khach-san', 'bao-nhieu-tien'])) {
    chat_out('Website cung cấp thông tin tham khảo, không bán vé hay đặt phòng. Giá vé và lịch hoạt động có thể thay đổi, bạn nên kiểm tra với đơn vị quản lý điểm đến.',
        [['label' => 'Khám phá địa điểm', 'url' => url('destinations.php')]], $defaultSuggest);
}
foreach (['mien-bac' => 'Bắc', 'mien-trung' => 'Trung', 'mien-nam' => 'Nam'] as $kw => $region) {
    if ($has([$kw])) {
        $names = fetch_all("SELECT d.title, d.slug FROM destinations d JOIN provinces p ON p.id=d.province_id WHERE d.status='published' AND p.region=? ORDER BY d.featured DESC, d.views DESC LIMIT 3", [$region]);
        $links = [['label' => 'Tất cả địa điểm Miền ' . $region, 'url' => url('destinations.php?region=' . $region)]];
        foreach ($names as $n) { $links[] = ['label' => $n['title'], 'url' => url('destination.php?slug=' . $n['slug'])]; }
        chat_out('Một vài gợi ý ở Miền ' . $region . ':', $links, ['Thời điểm đẹp để đi?', 'Xem bản đồ']);
    }
}
if ($has(['cam-nang', 'lich-trinh', 'kinh-nghiem'])) {
    chat_out('Mục Cẩm nang có các bài về lịch trình, kinh nghiệm và chuẩn bị cho chuyến đi.', [['label' => 'Cẩm nang du lịch', 'url' => url('guides.php')]], $defaultSuggest);
}

// ---- Tra cứu địa điểm theo tên (slug không dấu nên gõ không dấu vẫn tìm được) ----
$slugQ = trim(slugify($q), '-');
$found = [];
if (mb_strlen($slugQ) >= 3) {
    $like = static fn(string $v): string => '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $v) . '%';
    $sql = "SELECT d.title, d.slug FROM destinations d JOIN provinces p ON p.id=d.province_id WHERE d.status='published' AND (d.slug LIKE ? OR p.slug LIKE ?) ORDER BY d.featured DESC, d.views DESC LIMIT 4";
    $found = fetch_all($sql, [$like($slugQ), $like($slugQ)]);
    if (!$found) {
        $tokens = array_slice(array_filter(explode('-', $slugQ), static fn($t) => mb_strlen($t) >= 4), 0, 3);
        foreach ($tokens as $t) {
            $found = fetch_all($sql, [$like($t), $like($t)]);
            if ($found) { break; }
        }
    }
}
if ($found) {
    $links = [];
    foreach ($found as $f) {
        $links[] = ['label' => $f['title'], 'url' => url('destination.php?slug=' . $f['slug'])];
    }
    $links[] = ['label' => 'Xem trên bản đồ', 'url' => url('map.php?slug=' . $found[0]['slug'])];
    chat_out('Mình tìm thấy ' . count($found) . ' địa điểm phù hợp:', $links, ['Thời điểm đẹp để đi?', 'Món ăn nên thử']);
}

chat_out('Mình chưa hiểu rõ câu hỏi. Bạn thử gõ tên địa điểm (ví dụ "Hội An", "Sa Pa") hoặc chọn một chủ đề bên dưới nhé.',
    [['label' => 'Khám phá địa điểm', 'url' => url('destinations.php')]], $defaultSuggest);
