<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cards.php';

$slug = get_str('slug');
$d = $slug !== '' ? fetch_one(destination_select() . " WHERE d.slug = ? AND d.status = 'published'", [$slug]) : null;
if (!$d) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy địa điểm';
    require __DIR__ . '/includes/not_found.php';
    exit;
}
// Tăng lượt xem mỗi phiên một lần
$seenKey = 'seen_dest_' . $d['id'];
if (empty($_SESSION[$seenKey])) {
    q('UPDATE destinations SET views = views + 1 WHERE id = ?', [$d['id']]);
    $_SESSION[$seenKey] = 1;
    $d['views']++;
}
$related = fetch_all(destination_select() . " WHERE d.status='published' AND d.id <> ? AND (d.province_id = ? OR p.region = ?) ORDER BY (d.province_id = ?) DESC, d.featured DESC, d.id DESC LIMIT 3",
    [$d['id'], $d['province_id'], $d['region'], $d['province_id']]);

$pageTitle = $d['title'];
$active = 'destinations';
$metaDesc = excerpt($d['summary'], 160);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="page-hero-bg"><img src="<?= e(img($d['image'])) ?>" alt=""></div>
    <div class="container">
        <div class="breadcrumb"><a href="<?= e(url('index.php')) ?>">Trang chủ</a> / <a href="<?= e(url('destinations.php')) ?>">Khám phá</a> / <?= e($d['title']) ?></div>
        <h1><?= e($d['title']) ?></h1>
        <p><?= e($d['summary']) ?></p>
        <div class="meta-row">
            <span>📍 <?= e($d['province_name']) ?> · <?= e(REGIONS[$d['region']] ?? '') ?></span>
            <span>🏷️ <?= e($d['category_name']) ?></span>
            <span>👁️ <?= (int)$d['views'] ?> lượt xem</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container detail-grid">
        <article>
            <div class="detail-cover"><img src="<?= e(img($d['image'])) ?>" alt="<?= e($d['title']) ?>"></div>
            <p class="lead"><?= e($d['summary']) ?></p>
            <div class="prose"><?= paragraphs($d['content']) ?></div>
            <?php $mapQuery = trim(($d['address'] ?: $d['title']) . ', ' . $d['province_name'] . ', Việt Nam'); ?>
            <div class="map-card">
                <h2>Vị trí trên bản đồ</h2>
                <div class="map-frame"><iframe class="map-embed" title="Bản đồ <?= e($d['title']) ?>" src="<?= e(maps_embed_url($mapQuery)) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe></div>
                <div class="map-actions">
                    <a class="btn btn-outline btn-sm" href="<?= e(maps_link_url($mapQuery)) ?>" target="_blank" rel="noopener">Mở trong Google Maps / Chỉ đường</a>
                    <a class="btn btn-outline btn-sm" href="<?= e(url('map.php?slug=' . $d['slug'])) ?>">Xem bản đồ tất cả địa điểm</a>
                </div>
            </div>
            <div class="share-row">
                <a class="btn btn-outline btn-sm" href="<?= e(url('destinations.php?province=' . (int)$d['province_id'])) ?>">Xem thêm tại <?= e($d['province_name']) ?></a>
                <a class="btn btn-outline btn-sm" href="<?= e(url('community.php#share')) ?>">Chia sẻ trải nghiệm của bạn</a>
            </div>
        </article>
        <aside>
            <div class="aside-box">
                <h3>Thông tin chuyến đi</h3>
                <ul class="info-list">
                    <li><span class="ic">📍</span><div><strong>Vị trí</strong><span><?= e($d['address'] ?: $d['province_name']) ?></span></div></li>
                    <?php if ($d['best_time']): ?><li><span class="ic">🗓️</span><div><strong>Thời điểm đẹp nhất</strong><span><?= e($d['best_time']) ?></span></div></li><?php endif; ?>
                    <?php if ($d['visit_info']): ?><li><span class="ic">🎫</span><div><strong>Lưu ý tham quan</strong><span><?= e($d['visit_info']) ?></span></div></li><?php endif; ?>
                    <li><span class="ic">🧭</span><div><strong>Vùng miền</strong><span><?= e(REGIONS[$d['region']] ?? '') ?></span></div></li>
                </ul>
                <a class="btn btn-primary" style="width:100%;margin-top:14px" href="<?= e(url('contact.php?subject=' . rawurlencode('Tư vấn chuyến đi ' . $d['title']))) ?>">Nhận tư vấn</a>
            </div>
        </aside>
    </div>
    <?php if ($related): ?>
    <div class="container">
        <h2 class="related-title">Có thể bạn cũng thích</h2>
        <div class="grid grid-3"><?php foreach ($related as $r) { echo destination_card($r); } ?></div>
    </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
