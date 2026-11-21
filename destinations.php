<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cards.php';

$q = mb_substr(get_str('q'), 0, 80);
$region = get_str('region');
if (!isset(REGIONS[$region])) { $region = ''; }
$province = get_int('province');
$category = get_int('category');
$page = max(1, get_int('page', 1));

$where = ["d.status = 'published'"];
$params = [];
if ($q !== '') {
    $where[] = '(d.title LIKE ? OR d.summary LIKE ? OR p.name LIKE ?)';
    $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q) . '%';
    array_push($params, $like, $like, $like);
}
if ($region !== '')  { $where[] = 'p.region = ?';      $params[] = $region; }
if ($province > 0)   { $where[] = 'd.province_id = ?'; $params[] = $province; }
if ($category > 0)   { $where[] = 'd.category_id = ?'; $params[] = $category; }
$whereSql = ' WHERE ' . implode(' AND ', $where);

$total = (int)fetch_value('SELECT COUNT(*) FROM destinations d JOIN provinces p ON p.id=d.province_id JOIN categories c ON c.id=d.category_id' . $whereSql, $params);
$pg = paginate($total, PER_PAGE, $page);
$items = fetch_all(destination_select() . $whereSql . ' ORDER BY d.featured DESC, d.created_at DESC, d.id DESC LIMIT ' . (int)$pg['per_page'] . ' OFFSET ' . (int)$pg['offset'], $params);

$provinces = all_provinces();
$cats = categories_of('destination');
$query = array_filter(['q' => $q, 'region' => $region, 'province' => $province ?: '', 'category' => $category ?: ''], fn($v) => $v !== '');

$pageTitle = 'Khám phá địa điểm';
$active = 'destinations';
$metaDesc = 'Tìm kiếm và lọc các điểm đến nổi bật ở Bắc, Trung, Nam Việt Nam.';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="page-hero-bg"><img src="<?= e(url('assets/img/seed/ha-giang.svg')) ?>" alt=""></div>
    <div class="container">
        <div class="breadcrumb"><a href="<?= e(url('index.php')) ?>">Trang chủ</a> / Khám phá</div>
        <h1>Khám phá địa điểm</h1>
        <p>Hàng chục điểm đến từ cực Bắc đến miền Tây, từ đại ngàn đến đảo xa đang chờ bạn.</p>
    </div>
</section>

<div class="container">
    <form class="filter-bar" method="get" action="">
        <div><label for="fq">Từ khóa</label><input id="fq" type="search" name="q" value="<?= e($q) ?>" placeholder="Tên địa điểm, tỉnh thành…" maxlength="80"></div>
        <div><label for="fr">Vùng miền</label>
            <select id="fr" name="region"><option value="">Tất cả</option>
                <?php foreach (REGIONS as $k => $v): ?><option value="<?= e($k) ?>" <?= $region === $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?>
            </select></div>
        <div><label for="fp">Tỉnh thành</label>
            <select id="fp" name="province"><option value="">Tất cả</option>
                <?php foreach ($provinces as $p): ?><option value="<?= (int)$p['id'] ?>" <?= $province === (int)$p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option><?php endforeach; ?>
            </select></div>
        <div><label for="fc">Danh mục</label>
            <select id="fc" name="category"><option value="">Tất cả</option>
                <?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $category === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
            </select></div>
        <button class="btn btn-primary" type="submit">Lọc</button>
    </form>
</div>

<section class="section" style="padding-top:40px">
    <div class="container">
        <div class="chips" aria-label="Lọc nhanh theo danh mục">
            <a class="chip <?= $category === 0 ? 'is-active' : '' ?>" href="<?= e('?' . http_build_query(array_diff_key($query, ['category' => 1]))) ?>">Tất cả</a>
            <?php foreach ($cats as $c): ?>
                <a class="chip <?= $category === (int)$c['id'] ? 'is-active' : '' ?>" href="<?= e('?' . http_build_query(array_merge($query, ['category' => $c['id']]))) ?>"><?= e($c['name']) ?></a>
            <?php endforeach; ?>
        </div>
        <p class="result-info">Tìm thấy <strong><?= $total ?></strong> địa điểm<?= $q !== '' ? ' cho “' . e($q) . '”' : '' ?>.
            <?php if ($query): ?><a href="<?= e(url('destinations.php')) ?>">Xóa bộ lọc</a><?php endif; ?></p>
        <?php if ($items): ?>
            <div class="grid grid-3"><?php foreach ($items as $d) { echo destination_card($d); } ?></div>
            <?= render_pagination($pg, $query) ?>
        <?php else: ?>
            <div class="empty"><span class="ico">🔍</span><h3>Không tìm thấy địa điểm phù hợp</h3><p>Hãy thử từ khóa khác hoặc <a href="<?= e(url('destinations.php')) ?>">xem tất cả địa điểm</a>.</p></div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
