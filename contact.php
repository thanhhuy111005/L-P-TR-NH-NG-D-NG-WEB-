<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$old = ['name' => '', 'email' => '', 'subject' => mb_substr(get_str('subject'), 0, 160), 'message' => ''];

if (is_post()) {
    csrf_verify();
    foreach ($old as $k => $_) { $old[$k] = post($k); }
    if (post('website') !== '') { redirect('contact.php'); }
    if (mb_strlen($old['name']) < 2 || mb_strlen($old['name']) > 100) { $errors['name'] = 'Vui lòng nhập họ tên từ 2 đến 100 ký tự.'; }
    if (!valid_email($old['email'])) { $errors['email'] = 'Vui lòng nhập email hợp lệ.'; }
    if (mb_strlen($old['subject']) < 3 || mb_strlen($old['subject']) > 160) { $errors['subject'] = 'Chủ đề cần từ 3 đến 160 ký tự.'; }
    if (mb_strlen($old['message']) < 10 || mb_strlen($old['message']) > 2000) { $errors['message'] = 'Nội dung cần từ 10 đến 2000 ký tự.'; }
    if (!$errors && time() - (int)($_SESSION['last_contact'] ?? 0) < 20) { $errors['message'] = 'Bạn gửi quá nhanh, vui lòng thử lại sau ít giây.'; }
    if (!$errors) {
        q('INSERT INTO contacts (name, email, subject, message) VALUES (?,?,?,?)', [$old['name'], $old['email'], $old['subject'], $old['message']]);
        $_SESSION['last_contact'] = time();
        flash('success', 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.');
        redirect('contact.php');
    }
}

$pageTitle = 'Liên hệ';
$active = 'contact';
$metaDesc = 'Liên hệ Explore Vietnam để được tư vấn lịch trình hoặc hợp tác nội dung.';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="page-hero-bg"><img src="<?= e(url('assets/img/seed/da-lat.svg')) ?>" alt=""></div>
    <div class="container">
        <div class="breadcrumb"><a href="<?= e(url('index.php')) ?>">Trang chủ</a> / Liên hệ</div>
        <h1>Liên hệ với chúng tôi</h1>
        <p>Cần tư vấn lịch trình, muốn hợp tác hay góp ý cho website? Hãy gửi tin nhắn cho Explore Vietnam.</p>
    </div>
</section>
<section class="section">
    <div class="container contact-grid">
        <div class="contact-info">
            <h2>Kết nối cùng Explore Vietnam</h2>
            <p>Đội ngũ luôn sẵn lòng lắng nghe và trả lời trong vòng 24 giờ làm việc.</p>
            <ul>
                <li><span class="ic">📍</span><div><strong>Địa chỉ</strong> Gò Vấp TP.HCM </div></li>
                <li><span class="ic">✉️</span><div><strong>Email</strong>huy545658@gmail.com</div></li>
                <li><span class="ic">☎️</span><div><strong>Điện thoại</strong>(+84) 868698927</div></li>
                <li><span class="ic">🕘</span><div><strong>Giờ làm việc</strong>Thứ Hai - Thứ Sáu, 8:00 - 17:30</div></li>
            </ul>
            <?php $officeAddr = 'SGò Vấp TP.HCM'; ?>
            <div class="map-card">
                <div class="map-frame"><iframe class="map-embed" title="Bản đồ địa chỉ liên hệ" src="<?= e(maps_embed_url($officeAddr)) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe></div>
                <div class="map-actions"><a class="btn btn-outline btn-sm" href="<?= e(maps_link_url($officeAddr)) ?>" target="_blank" rel="noopener">Chỉ đường đến văn phòng</a></div>
            </div>
        </div>
        <form class="form-card" method="post" novalidate>
            <?= csrf_field() ?>
            <div class="hp-field" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
            <div class="form-row">
                <div class="form-group"><label for="name">Họ tên <span class="req">*</span></label>
                    <input id="name" type="text" name="name" value="<?= e($old['name']) ?>" maxlength="100" required class="<?= isset($errors['name']) ? 'is-invalid' : '' ?>">
                    <?php if (isset($errors['name'])): ?><div class="field-error"><?= e($errors['name']) ?></div><?php endif; ?></div>
                <div class="form-group"><label for="email">Email <span class="req">*</span></label>
                    <input id="email" type="email" name="email" value="<?= e($old['email']) ?>" maxlength="120" required class="<?= isset($errors['email']) ? 'is-invalid' : '' ?>">
                    <?php if (isset($errors['email'])): ?><div class="field-error"><?= e($errors['email']) ?></div><?php endif; ?></div>
            </div>
            <div class="form-group"><label for="subject">Chủ đề <span class="req">*</span></label>
                <input id="subject" type="text" name="subject" value="<?= e($old['subject']) ?>" maxlength="160" required class="<?= isset($errors['subject']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['subject'])): ?><div class="field-error"><?= e($errors['subject']) ?></div><?php endif; ?></div>
            <div class="form-group"><label for="message">Nội dung <span class="req">*</span></label>
                <textarea id="message" name="message" data-maxlength="2000" maxlength="2000" required class="<?= isset($errors['message']) ? 'is-invalid' : '' ?>"><?= e($old['message']) ?></textarea>
                <?php if (isset($errors['message'])): ?><div class="field-error"><?= e($errors['message']) ?></div><?php endif; ?></div>
            <button class="btn btn-primary" type="submit">Gửi tin nhắn</button>
        </form>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
