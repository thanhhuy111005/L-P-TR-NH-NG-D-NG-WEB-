<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cards.php';

$type = get_str('type');
if (!isset(CULTURE_TYPES[$type])) { $type = ''; }
$q = mb_substr(get_str('q'), 0, 80);
$page = max(1, get_int('page', 1));

$where = ["ci.status = 'published'"];
$params = [];
if ($type !== '') { $where[] = 'ci.type = ?'; $params[] = $type; }
if ($q !== '') {
    $where[] = '(ci.title LIKE ? OR ci.summary LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like);
}
$whereSql = ' WHERE ' . implode(' AND ', $where);
$total = (int)fetch_value('SELECT COUNT(*) FROM culture_items ci' . $whereSql, $params);
$pg = paginate($total, PER_PAGE, $page);
$items = fetch_all(culture_select() . $whereSql . ' ORDER BY ci.featured DESC, ci.id DESC LIMIT ' . (int)$pg['per_page'] . ' OFFSET ' . (int)$pg['offset'], $params);
$query = array_filter(['type' => $type, 'q' => $q], fn($v) => $v !== '');

$pageTitle = $type ? CULTURE_TYPES[$type]['label'] . ' Việt Nam' : 'Văn hóa Việt Nam';
$active = 'culture';
$metaDesc = 'Khám phá ẩm thực, lễ hội, di sản và làng nghề truyền thống Việt Nam.';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="page-hero-bg"><img src="<?= e(url('assets/img/seed/hue.svg')) ?>" alt=""></div>
    <div class="container">
        <div class="breadcrumb"><a href="<?= e(url('index.php')) ?>">Trang chủ</a> / Văn hóa</div>
        <h1><?= $type ? e(CULTURE_TYPES[$type]['label']) : 'Văn hóa Việt Nam' ?></h1>
        <p><?= $type ? e(CULTURE_TYPES[$type]['desc']) : 'Bốn nghìn năm văn hiến kết tinh trong ẩm thực, lễ hội, di sản và những làng nghề truyền thống.' ?></p>
    </div>
</section>

<section class="section" style="padding-top:56px">
    <div class="container">
        <div class="chips">
            <a class="chip <?= $type === '' ? 'is-active' : '' ?>" href="<?= e(url('culture.php')) ?>">Tất cả</a>
            <?php foreach (CULTURE_TYPES as $k => $t): ?>
                <a class="chip <?= $type === $k ? 'is-active' : '' ?>" href="<?= e(url('culture.php?type=' . $k)) ?>"><?= $t['icon'] ?> <?= e($t['label']) ?></a>
            <?php endforeach; ?>
        </div>
        <form method="get" class="hero-search" style="margin:0 0 30px;box-shadow:var(--shadow-sm)" role="search">
            <?php if ($type !== ''): ?><input type="hidden" name="type" value="<?= e($type) ?>"><?php endif; ?>
            <input type="search" name="q" value="<?= e($q) ?>" placeholder="Tìm món ăn, lễ hội, di sản, làng nghề…" aria-label="Tìm kiếm văn hóa" maxlength="80">
            <button class="btn btn-primary" type="submit">Tìm</button>
        </form>
        <p class="result-info">Có <strong><?= $total ?></strong> nội dung.</p>
        <?php if ($items): ?>
            <div class="grid grid-3"><?php foreach ($items as $c) { echo culture_card($c); } ?></div>
            <?= render_pagination($pg, $query) ?>
        <?php else: ?>
            <div class="empty"><span class="ico">🏮</span><h3>Chưa có nội dung phù hợp</h3><p><a href="<?= e(url('culture.php')) ?>">Xem tất cả nội dung văn hóa</a></p></div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
