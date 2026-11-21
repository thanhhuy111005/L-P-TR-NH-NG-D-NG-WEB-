<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cards.php';

$q = mb_substr(get_str('q'), 0, 80);
$category = get_int('category');
$page = max(1, get_int('page', 1));
$cats = categories_of('post');

$where = ["po.status = 'published'"];
$params = [];
if ($category > 0) { $where[] = 'po.category_id = ?'; $params[] = $category; }
if ($q !== '') {
    $where[] = '(po.title LIKE ? OR po.excerpt LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like);
}
$whereSql = ' WHERE ' . implode(' AND ', $where);
$total = (int)fetch_value('SELECT COUNT(*) FROM posts po' . $whereSql, $params);
$pg = paginate($total, PER_PAGE, $page);
$items = fetch_all(post_select() . $whereSql . ' ORDER BY po.created_at DESC, po.id DESC LIMIT ' . (int)$pg['per_page'] . ' OFFSET ' . (int)$pg['offset'], $params);
$query = array_filter(['q' => $q, 'category' => $category ?: ''], fn($v) => $v !== '');

$pageTitle = 'Cẩm nang du lịch';
$active = 'guides';
$metaDesc = 'Kinh nghiệm, lịch trình và mẹo du lịch Việt Nam từ những người đi trước.';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="page-hero-bg"><img src="<?= e(url('assets/img/seed/cam-nang-1.svg')) ?>" alt=""></div>
    <div class="container">
        <div class="breadcrumb"><a href="<?= e(url('index.php')) ?>">Trang chủ</a> / Cẩm nang</div>
        <h1>Cẩm nang du lịch</h1>
        <p>Lịch trình, kinh nghiệm và những mẹo nhỏ giúp chuyến đi của bạn trọn vẹn hơn.</p>
    </div>
</section>
<section class="section" style="padding-top:56px">
    <div class="container">
        <div class="chips">
            <a class="chip <?= $category === 0 ? 'is-active' : '' ?>" href="<?= e('?' . http_build_query(array_diff_key($query, ['category' => 1]))) ?>">Tất cả</a>
            <?php foreach ($cats as $c): ?>
                <a class="chip <?= $category === (int)$c['id'] ? 'is-active' : '' ?>" href="<?= e('?' . http_build_query(array_merge($query, ['category' => $c['id']]))) ?>"><?= e($c['name']) ?></a>
            <?php endforeach; ?>
        </div>
        <form method="get" class="hero-search" style="margin:0 0 30px;box-shadow:var(--shadow-sm)" role="search">
            <?php if ($category > 0): ?><input type="hidden" name="category" value="<?= $category ?>"><?php endif; ?>
            <input type="search" name="q" value="<?= e($q) ?>" placeholder="Tìm bài viết cẩm nang…" aria-label="Tìm bài viết" maxlength="80">
            <button class="btn btn-primary" type="submit">Tìm</button>
        </form>
        <p class="result-info">Có <strong><?= $total ?></strong> bài viết.</p>
        <?php if ($items): ?>
            <div class="grid grid-3"><?php foreach ($items as $p) { echo post_card($p); } ?></div>
            <?= render_pagination($pg, $query) ?>
        <?php else: ?>
            <div class="empty"><span class="ico">📖</span><h3>Chưa có bài viết phù hợp</h3><p><a href="<?= e(url('guides.php')) ?>">Xem tất cả bài viết</a></p></div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
