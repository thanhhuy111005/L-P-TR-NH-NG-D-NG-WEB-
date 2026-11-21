<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cards.php';

$errors = [];
$old = ['author_name' => '', 'author_email' => '', 'title' => '', 'content' => '', 'province_id' => ''];

if (is_post()) {
    csrf_verify();
    foreach ($old as $k => $_) { $old[$k] = post($k); }
    if (post('website') !== '') { // honeypot: bot điền vào ô ẩn
        redirect('community.php');
    }
    if (mb_strlen($old['author_name']) < 2 || mb_strlen($old['author_name']) > 100) { $errors['author_name'] = 'Vui lòng nhập tên từ 2 đến 100 ký tự.'; }
    if ($old['author_email'] !== '' && !valid_email($old['author_email'])) { $errors['author_email'] = 'Email không hợp lệ.'; }
    if (mb_strlen($old['title']) < 5 || mb_strlen($old['title']) > 180) { $errors['title'] = 'Tiêu đề cần từ 5 đến 180 ký tự.'; }
    if (mb_strlen($old['content']) < 30 || mb_strlen($old['content']) > 3000) { $errors['content'] = 'Nội dung cần từ 30 đến 3000 ký tự.'; }
    $provinceId = null;
    if ($old['province_id'] !== '') {
        $provinceId = ctype_digit($old['province_id']) ? (int)$old['province_id'] : 0;
        if (!$provinceId || !fetch_value('SELECT COUNT(*) FROM provinces WHERE id = ?', [$provinceId])) { $errors['province_id'] = 'Tỉnh thành không hợp lệ.'; }
    }
    if (time() - (int)($_SESSION['last_community_post'] ?? 0) < 20) { $errors['content'] = 'Bạn gửi quá nhanh, vui lòng thử lại sau ít giây.'; }
    [$image, $upErr] = $errors ? [null, null] : upload_image('image');
    if ($upErr) { $errors['image'] = $upErr; }

    if (!$errors) {
        q('INSERT INTO community_posts (author_name, author_email, title, content, image, province_id, status) VALUES (?,?,?,?,?,?,?)',
            [$old['author_name'], $old['author_email'] ?: null, $old['title'], $old['content'], $image, $provinceId, 'pending']);
        $_SESSION['last_community_post'] = time();
        flash('success', 'Cảm ơn bạn! Bài chia sẻ đã được gửi và sẽ hiển thị sau khi quản trị viên duyệt.');
        redirect('community.php');
    }
}

$page = max(1, get_int('page', 1));
$provinceFilter = get_int('province');
$where = "WHERE cp.status = 'approved'"; $params = [];
if ($provinceFilter > 0) { $where .= ' AND cp.province_id = ?'; $params[] = $provinceFilter; }
$total = (int)fetch_value('SELECT COUNT(*) FROM community_posts cp ' . $where, $params);
$pg = paginate($total, 6, $page);
$items = fetch_all('SELECT cp.*, pr.name AS province_name FROM community_posts cp LEFT JOIN provinces pr ON pr.id = cp.province_id ' . $where
    . ' ORDER BY cp.created_at DESC, cp.id DESC LIMIT ' . (int)$pg['per_page'] . ' OFFSET ' . (int)$pg['offset'], $params);
$provinces = all_provinces();

$pageTitle = 'Cộng đồng';
$active = 'community';
$metaDesc = 'Đọc và chia sẻ trải nghiệm du lịch Việt Nam cùng cộng đồng Explore Vietnam.';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="page-hero-bg"><img src="<?= e(url('assets/img/seed/cong-dong-1.svg')) ?>" alt=""></div>
    <div class="container">
        <div class="breadcrumb"><a href="<?= e(url('index.php')) ?>">Trang chủ</a> / Cộng đồng</div>
        <h1>Cộng đồng Explore Vietnam</h1>
        <p>Nơi những người yêu Việt Nam chia sẻ hành trình, hình ảnh và mẹo hay cho nhau.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <form method="get" class="hero-search" style="margin:0 0 30px;box-shadow:var(--shadow-sm);max-width:480px">
            <select name="province" aria-label="Lọc theo tỉnh thành" style="border:0;flex:1">
                <option value="">Tất cả tỉnh thành</option>
                <?php foreach ($provinces as $p): ?><option value="<?= (int)$p['id'] ?>" <?= $provinceFilter === (int)$p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option><?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Lọc</button>
        </form>
        <?php if ($items): ?>
            <div class="grid grid-3"><?php foreach ($items as $c) { echo community_card($c); } ?></div>
            <?= render_pagination($pg, array_filter(['province' => $provinceFilter ?: ''], fn($v) => $v !== '')) ?>
        <?php else: ?>
            <div class="empty"><span class="ico">💬</span><h3>Chưa có bài chia sẻ</h3><p>Hãy là người đầu tiên chia sẻ câu chuyện của bạn bên dưới.</p></div>
        <?php endif; ?>
    </div>
</section>

<section class="section section-alt" id="share">
    <div class="container" style="max-width:820px">
        <div class="section-head"><span class="eyebrow eyebrow-dark">Chia sẻ hành trình</span><h2>Gửi bài viết của bạn</h2>
            <p>Bài viết sẽ được quản trị viên duyệt trước khi hiển thị công khai.</p></div>
        <form class="form-card" method="post" enctype="multipart/form-data" novalidate>
            <?= csrf_field() ?>
            <div class="hp-field" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
            <div class="form-row">
                <div class="form-group"><label for="author_name">Họ tên <span class="req">*</span></label>
                    <input id="author_name" type="text" name="author_name" value="<?= e($old['author_name']) ?>" maxlength="100" required class="<?= isset($errors['author_name']) ? 'is-invalid' : '' ?>">
                    <?php if (isset($errors['author_name'])): ?><div class="field-error"><?= e($errors['author_name']) ?></div><?php endif; ?></div>
                <div class="form-group"><label for="author_email">Email (không bắt buộc, không hiển thị)</label>
                    <input id="author_email" type="email" name="author_email" value="<?= e($old['author_email']) ?>" maxlength="120" class="<?= isset($errors['author_email']) ? 'is-invalid' : '' ?>">
                    <?php if (isset($errors['author_email'])): ?><div class="field-error"><?= e($errors['author_email']) ?></div><?php endif; ?></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label for="title">Tiêu đề <span class="req">*</span></label>
                    <input id="title" type="text" name="title" value="<?= e($old['title']) ?>" maxlength="180" required class="<?= isset($errors['title']) ? 'is-invalid' : '' ?>">
                    <?php if (isset($errors['title'])): ?><div class="field-error"><?= e($errors['title']) ?></div><?php endif; ?></div>
                <div class="form-group"><label for="province_id">Địa điểm</label>
                    <select id="province_id" name="province_id"><option value="">Chọn tỉnh thành</option>
                        <?php foreach ($provinces as $p): ?><option value="<?= (int)$p['id'] ?>" <?= $old['province_id'] === (string)$p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option><?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['province_id'])): ?><div class="field-error"><?= e($errors['province_id']) ?></div><?php endif; ?></div>
            </div>
            <div class="form-group"><label for="content">Nội dung <span class="req">*</span></label>
                <textarea id="content" name="content" data-maxlength="3000" maxlength="3000" required class="<?= isset($errors['content']) ? 'is-invalid' : '' ?>"><?= e($old['content']) ?></textarea>
                <?php if (isset($errors['content'])): ?><div class="field-error"><?= e($errors['content']) ?></div><?php endif; ?></div>
            <div class="form-group"><label for="image">Ảnh minh họa (JPG, PNG, WEBP, GIF - tối đa 3 MB)</label>
                <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" data-preview="imgPreview">
                <div id="imgPreview" class="hint" hidden></div>
                <?php if (isset($errors['image'])): ?><div class="field-error"><?= e($errors['image']) ?></div><?php endif; ?></div>
            <button class="btn btn-primary" type="submit">Gửi bài chia sẻ</button>
        </form>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
