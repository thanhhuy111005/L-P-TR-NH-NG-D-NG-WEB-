<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cards.php';

$items = fetch_all(destination_select() . " WHERE d.status = 'published' ORDER BY CASE p.region WHEN 'Bắc' THEN 1 WHEN 'Trung' THEN 2 ELSE 3 END, d.title");
$selSlug = get_str('slug');
$current = null;
foreach ($items as $it) {
    if ($it['slug'] === $selSlug) { $current = $it; break; }
}
$queryOf = static fn(array $d): string => trim(($d['address'] ?: $d['title']) . ', ' . $d['province_name'] . ', Việt Nam');
$firstQuery = $current ? $queryOf($current) : 'Việt Nam';
$firstTitle = $current ? $current['title'] : 'Toàn cảnh Việt Nam';

$grouped = [];
foreach ($items as $it) { $grouped[$it['region']][] = $it; }

$pageTitle = 'Bản đồ điểm đến';
$active = 'map';
$metaDesc = 'Xem vị trí các điểm đến du lịch Việt Nam trên Google Maps và mở chỉ đường.';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="page-hero-bg"><img src="<?= e(url('assets/img/seed/ha-long.svg')) ?>" alt=""></div>
    <div class="container">
        <div class="breadcrumb"><a href="<?= e(url('index.php')) ?>">Trang chủ</a> / Bản đồ</div>
        <h1>Bản đồ điểm đến</h1>
        <p>Chọn một địa điểm để xem vị trí trên Google Maps và mở chỉ đường.</p>
    </div>
</section>

<section class="section">
    <div class="container map-layout" data-map-list>
        <aside class="map-side" aria-label="Danh sách địa điểm">
            <input type="search" id="mapFilter" placeholder="Lọc địa điểm…" aria-label="Lọc địa điểm">
            <button type="button" class="map-item<?= $current ? '' : ' is-active' ?>" data-query="Việt Nam" data-title="Toàn cảnh Việt Nam" data-name="viet nam">Toàn cảnh Việt Nam<small>Xem bản đồ cả nước</small></button>
            <?php foreach ($grouped as $region => $list): ?>
                <h3><?= e(REGIONS[$region] ?? $region) ?></h3>
                <?php foreach ($list as $it): ?>
                    <button type="button" class="map-item<?= $current && $current['id'] === $it['id'] ? ' is-active' : '' ?>"
                        data-query="<?= e($queryOf($it)) ?>" data-title="<?= e($it['title']) ?>" data-name="<?= e(mb_strtolower($it['title'] . ' ' . $it['province_name'])) ?>">
                        <?= e($it['title']) ?><small><?= e($it['province_name']) ?></small>
                    </button>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </aside>
        <div class="map-main">
            <div class="map-bar">
                <strong id="mapTitle"><?= e($firstTitle) ?></strong>
                <span>
                    <a class="btn btn-outline btn-sm" id="mapOpen" href="<?= e(maps_link_url($firstQuery)) ?>" target="_blank" rel="noopener">Mở Google Maps / Chỉ đường</a>
                    <?php if ($current): ?><a class="btn btn-outline btn-sm" href="<?= e(url('destination.php?slug=' . $current['slug'])) ?>">Xem bài giới thiệu</a><?php endif; ?>
                </span>
            </div>
            <div class="map-frame"><iframe class="map-embed" id="mapFrame" title="Bản đồ Google Maps" src="<?= e(maps_embed_url($firstQuery)) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe></div>
        </div>
    </div>
</section>
<script>
window.MAP_URLS = {
    embed: <?= json_encode('https://www.google.com/maps?hl=vi&z=12&output=embed&q=', JSON_UNESCAPED_SLASHES) ?>,
    link: <?= json_encode('https://www.google.com/maps/search/?api=1&query=', JSON_UNESCAPED_SLASHES) ?>
};
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
