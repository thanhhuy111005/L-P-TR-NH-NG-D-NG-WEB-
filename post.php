<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cards.php';

$slug = get_str('slug');
$p = $slug !== '' ? fetch_one(post_select() . " WHERE po.slug = ? AND po.status = 'published'", [$slug]) : null;
if (!$p) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy bài viết';
    $active = 'guides';
    require __DIR__ . '/includes/not_found.php';
    exit;
}
$seenKey = 'seen_post_' . $p['id'];
if (empty($_SESSION[$seenKey])) {
    q('UPDATE posts SET views = views + 1 WHERE id = ?', [$p['id']]);
    $_SESSION[$seenKey] = 1;
    $p['views']++;
}
$related = fetch_all(post_select() . " WHERE po.status='published' AND po.id <> ? ORDER BY (po.category_id = ?) DESC, po.created_at DESC LIMIT 3", [$p['id'], $p['category_id']]);
$readMin = max(1, (int)ceil(count(preg_split('/\s+/u', trim(strip_tags($p['content'])), -1, PREG_SPLIT_NO_EMPTY)) / 200));

$pageTitle = $p['title'];
$active = 'guides';
$metaDesc = excerpt($p['excerpt'], 160);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="page-hero-bg"><img src="<?= e(img($p['image'])) ?>" alt=""></div>
    <div class="container">
        <div class="breadcrumb"><a href="<?= e(url('index.php')) ?>">Trang chủ</a> / <a href="<?= e(url('guides.php')) ?>">Cẩm nang</a> / <?= e($p['category_name']) ?></div>
        <h1><?= e($p['title']) ?></h1>
        <p><?= e($p['excerpt']) ?></p>
        <div class="meta-row"><span>🗓️ <?= e(format_date($p['created_at'])) ?></span><span>✍️ <?= e($p['author_name'] ?: SITE_NAME) ?></span><span>👁️ <?= (int)$p['views'] ?> lượt xem</span></div>
    </div>
</section>
<section class="section">
    <div class="container detail-grid">
        <article>
            <div class="detail-cover"><img src="<?= e(img($p['image'])) ?>" alt="<?= e($p['title']) ?>"></div>
            <div class="prose"><?= paragraphs($p['content']) ?></div>
            <div class="share-row">
                <a class="btn btn-outline btn-sm" href="<?= e(url('guides.php?category=' . (int)$p['category_id'])) ?>">Thêm bài về “<?= e($p['category_name']) ?>”</a>
                <a class="btn btn-outline btn-sm" href="<?= e(url('guides.php')) ?>">← Tất cả cẩm nang</a>
            </div>
        </article>
        <aside>
            <div class="aside-box">
                <h3>Thông tin bài viết</h3>
                <ul class="info-list">
                    <li><span class="ic">🏷️</span><div><strong>Chuyên mục</strong><span><?= e($p['category_name']) ?></span></div></li>
                    <li><span class="ic">⏱️</span><div><strong>Thời gian đọc</strong><span>Khoảng <?= $readMin ?> phút</span></div></li>
                    <li><span class="ic">✍️</span><div><strong>Tác giả</strong><span><?= e($p['author_name'] ?: SITE_NAME) ?></span></div></li>
                </ul>
            </div>
        </aside>
    </div>
    <?php if ($related): ?>
    <div class="container"><h2 class="related-title">Bài viết khác</h2>
        <div class="grid grid-3"><?php foreach ($related as $r) { echo post_card($r); } ?></div></div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
