<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cards.php';

$slug = get_str('slug');
$c = $slug !== '' ? fetch_one(culture_select() . " WHERE ci.slug = ? AND ci.status = 'published'", [$slug]) : null;
if (!$c) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy nội dung';
    $active = 'culture';
    require __DIR__ . '/includes/not_found.php';
    exit;
}
$t = CULTURE_TYPES[$c['type']];
$related = fetch_all(culture_select() . " WHERE ci.status='published' AND ci.type = ? AND ci.id <> ? ORDER BY ci.featured DESC, ci.id DESC LIMIT 3", [$c['type'], $c['id']]);

$pageTitle = $c['title'];
$active = 'culture';
$metaDesc = excerpt($c['summary'], 160);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="page-hero-bg"><img src="<?= e(img($c['image'])) ?>" alt=""></div>
    <div class="container">
        <div class="breadcrumb"><a href="<?= e(url('index.php')) ?>">Trang chủ</a> / <a href="<?= e(url('culture.php')) ?>">Văn hóa</a> / <a href="<?= e(url('culture.php?type=' . $c['type'])) ?>"><?= e($t['label']) ?></a></div>
        <h1><?= e($c['title']) ?></h1>
        <p><?= e($c['summary']) ?></p>
        <div class="meta-row"><span><?= $t['icon'] ?> <?= e($t['label']) ?></span><?php if ($c['province_name']): ?><span>📍 <?= e($c['province_name']) ?></span><?php endif; ?></div>
    </div>
</section>
<section class="section">
    <div class="container detail-grid">
        <article>
            <div class="detail-cover"><img src="<?= e(img($c['image'])) ?>" alt="<?= e($c['title']) ?>"></div>
            <p class="lead"><?= e($c['summary']) ?></p>
            <div class="prose"><?= paragraphs($c['content']) ?></div>
        </article>
        <aside>
            <div class="aside-box">
                <h3>Về nội dung này</h3>
                <ul class="info-list">
                    <li><span class="ic"><?= $t['icon'] ?></span><div><strong>Chuyên mục</strong><span><?= e($t['label']) ?></span></div></li>
                    <?php if ($c['province_name']): ?><li><span class="ic">📍</span><div><strong>Gắn với</strong><span><?= e($c['province_name']) ?></span></div></li><?php endif; ?>
                    <li><span class="ic">🗓️</span><div><strong>Cập nhật</strong><span><?= e(format_date($c['updated_at'])) ?></span></div></li>
                </ul>
                <a class="btn btn-outline" style="width:100%;margin-top:14px" href="<?= e(url('culture.php?type=' . $c['type'])) ?>">Xem thêm về <?= e(mb_strtolower($t['label'])) ?></a>
            </div>
        </aside>
    </div>
    <?php if ($related): ?>
    <div class="container"><h2 class="related-title">Nội dung liên quan</h2>
        <div class="grid grid-3"><?php foreach ($related as $r) { echo culture_card($r); } ?></div></div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
