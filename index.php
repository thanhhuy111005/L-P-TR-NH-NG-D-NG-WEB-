<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cards.php';

$pageTitle = SITE_NAME;
$active = 'home';
$metaDesc = 'Khám phá du lịch, văn hóa, ẩm thực, di sản và con người Việt Nam từ Bắc vào Nam.';
$bodyClass = 'is-home';

$stats = [
    'destinations' => (int)fetch_value("SELECT COUNT(*) FROM destinations WHERE status='published'"),
    'provinces'    => (int)fetch_value("SELECT COUNT(DISTINCT province_id) FROM destinations WHERE status='published'"),
    'culture'      => (int)fetch_value("SELECT COUNT(*) FROM culture_items WHERE status='published'"),
    'posts'        => (int)fetch_value("SELECT COUNT(*) FROM posts WHERE status='published'"),
];

$featured = fetch_all(destination_select() . " WHERE d.status='published' AND d.featured=1 ORDER BY d.views DESC, d.id DESC LIMIT 5");
$regionCounts = [];
foreach (fetch_all("SELECT p.region, COUNT(*) AS n FROM destinations d JOIN provinces p ON p.id=d.province_id WHERE d.status='published' GROUP BY p.region") as $r) {
    $regionCounts[$r['region']] = (int)$r['n'];
}
$regionInfo = [
    'Bắc'   => ['Miền Bắc', 'Núi non hùng vĩ, vịnh biển kỳ vĩ và nghìn năm văn hiến Thăng Long.', 'assets/img/mienbac.webp'],
    'Trung' => ['Miền Trung', 'Con đường di sản, phố cổ lung linh đèn lồng và những bãi biển xanh ngọc.', 'assets/img/mientrung.jpg'],
    'Nam'   => ['Miền Nam', 'Sông nước miệt vườn, thành phố sôi động và những hòn đảo nắng gió.', 'assets/img/miennam.jpg'],
];
$latestDest = fetch_all(destination_select() . " WHERE d.status='published' ORDER BY d.created_at DESC, d.id DESC LIMIT 3");
$culture = fetch_all(culture_select() . " WHERE ci.status='published' ORDER BY ci.featured DESC, ci.id DESC LIMIT 3");
$posts = fetch_all(post_select() . " WHERE po.status='published' ORDER BY po.created_at DESC, po.id DESC LIMIT 3");
$community = fetch_all("SELECT cp.*, pr.name AS province_name FROM community_posts cp LEFT JOIN provinces pr ON pr.id=cp.province_id WHERE cp.status='approved' ORDER BY cp.created_at DESC, cp.id DESC LIMIT 3");

require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="hero-bg"><img src="<?= e(url('assets/img/anhnen.webp')) ?>" alt=""></div>
    <div class="container">
        <div class="hero-inner">
            <span class="eyebrow">Việt Nam · Hình chữ S diệu kỳ</span>
            <h1>Chạm đến <em>Việt Nam</em>, khám phá những điều tuyệt vời</h1>
            <p class="hero-lead">Từ đỉnh Fansipan mờ sương đến miền Tây sông nước, mỗi hành trình là một câu chuyện về thiên nhiên, di sản, ẩm thực và những con người hiếu khách.</p>
            <form class="hero-search" action="<?= e(url('destinations.php')) ?>" method="get" role="search">
                <input type="search" name="q" placeholder="Bạn muốn đi đâu? Ví dụ: Hội An, Hạ Long, Đà Lạt…" aria-label="Tìm địa điểm" maxlength="80">
                <button class="btn btn-gold" type="submit">Tìm kiếm</button>
            </form>
            <div class="hero-actions">
                <a class="btn btn-ghost" href="<?= e(url('destinations.php')) ?>">Khám phá địa điểm</a>
                <a class="btn btn-ghost" href="<?= e(url('culture.php')) ?>">Văn hóa Việt Nam</a>
            </div>
        </div>
    </div>
    <div class="scroll-hint">Cuộn xuống</div>
</section>

<section class="section section-dark" aria-label="Thống kê">
    <div class="container stats">
        <div class="stat"><strong data-count="<?= $stats['destinations'] ?>">0</strong><span>Địa điểm nổi bật</span></div>
        <div class="stat"><strong data-count="<?= $stats['provinces'] ?>">0</strong><span>Tỉnh thành được giới thiệu</span></div>
        <div class="stat"><strong data-count="<?= $stats['culture'] ?>">0</strong><span>Nét văn hóa đặc sắc</span></div>
        <div class="stat"><strong data-count="<?= $stats['posts'] ?>">0</strong><span>Bài cẩm nang hữu ích</span></div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="head-row">
            <div class="section-head">
                <span class="eyebrow eyebrow-dark">Điểm đến nổi bật</span>
                <h2>Những nơi khiến bạn muốn xách balo lên ngay</h2>
            </div>
            <a class="btn btn-outline" href="<?= e(url('destinations.php')) ?>">Xem tất cả địa điểm</a>
        </div>
        <?php if ($featured): ?>
        <div class="bento">
            <?php foreach ($featured as $d): ?>
                <a class="bento-item reveal" href="<?= e(url('destination.php?slug=' . rawurlencode($d['slug']))) ?>">
                    <img src="<?= e(img($d['image'])) ?>" alt="<?= e($d['title']) ?>" loading="lazy">
                    <div class="bento-info"><small><?= e($d['province_name']) ?></small><h3><?= e($d['title']) ?></h3></div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="empty"><span class="ico">🧭</span><h3>Chưa có địa điểm nổi bật</h3><p>Quản trị viên có thể đánh dấu địa điểm nổi bật trong trang quản trị.</p></div>
        <?php endif; ?>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow eyebrow-dark">Ba miền một dải đất</span>
            <h2>Chọn miền để bắt đầu hành trình</h2>
            <p>Mỗi vùng đất mang một sắc thái riêng: khí hậu, ẩm thực, giọng nói và phong tục.</p>
        </div>
        <div class="grid grid-3">
            <?php foreach ($regionInfo as $key => [$name, $desc, $image]): ?>
                <a class="region-card reveal" href="<?= e(url('destinations.php?region=' . rawurlencode($key))) ?>">
                    <img src="<?= e(url($image)) ?>" alt="<?= e($name) ?>" loading="lazy">
                    <div class="region-info">
                        <h3><?= e($name) ?></h3>
                        <p><?= e($desc) ?></p>
                        <span class="count"><?= (int)($regionCounts[$key] ?? 0) ?> địa điểm →</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow eyebrow-dark">Mới cập nhật</span>
            <h2>Điểm đến vừa được thêm</h2>
        </div>
        <div class="grid grid-3">
            <?php foreach ($latestDest as $d) { echo destination_card($d); } ?>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="head-row">
            <div class="section-head">
                <span class="eyebrow eyebrow-dark">Bản sắc Việt</span>
                <h2>Văn hóa: ẩm thực, lễ hội, di sản, làng nghề</h2>
            </div>
            <a class="btn btn-outline" href="<?= e(url('culture.php')) ?>">Khám phá văn hóa</a>
        </div>
        <div class="culture-pills" style="margin-bottom:44px">
            <?php foreach (CULTURE_TYPES as $key => $t): ?>
                <a class="pill-card reveal" href="<?= e(url('culture.php?type=' . $key)) ?>">
                    <span class="ico"><?= $t['icon'] ?></span><h3><?= e($t['label']) ?></h3><p><?= e($t['desc']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="grid grid-3">
            <?php foreach ($culture as $c) { echo culture_card($c); } ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="head-row">
            <div class="section-head">
                <span class="eyebrow eyebrow-dark">Cẩm nang du lịch</span>
                <h2>Chuẩn bị chuyến đi trọn vẹn</h2>
            </div>
            <a class="btn btn-outline" href="<?= e(url('guides.php')) ?>">Xem tất cả bài viết</a>
        </div>
        <div class="grid grid-3">
            <?php foreach ($posts as $p) { echo post_card($p); } ?>
        </div>
    </div>
</section>

<?php if ($community): ?>
<section class="section section-alt">
    <div class="container">
        <div class="head-row">
            <div class="section-head">
                <span class="eyebrow eyebrow-dark">Cộng đồng</span>
                <h2>Câu chuyện từ những người đã đi</h2>
            </div>
            <a class="btn btn-outline" href="<?= e(url('community.php')) ?>">Đọc thêm &amp; chia sẻ</a>
        </div>
        <div class="grid grid-3">
            <?php foreach ($community as $c) { echo community_card($c); } ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="container">
        <div class="cta-band reveal">
            <img src="<?= e(url('assets/img/seed/about.svg')) ?>" alt="" loading="lazy">
            <h2>Bạn đã có câu chuyện của riêng mình?</h2>
            <p>Chia sẻ trải nghiệm, hình ảnh và mẹo hay để truyền cảm hứng cho những người bạn đồng hành.</p>
            <a class="btn btn-gold" href="<?= e(url('community.php#share')) ?>">Chia sẻ hành trình</a>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>