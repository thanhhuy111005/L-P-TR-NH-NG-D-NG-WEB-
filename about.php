<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$n = [
    'dest' => (int)fetch_value("SELECT COUNT(*) FROM destinations WHERE status='published'"),
    'culture' => (int)fetch_value("SELECT COUNT(*) FROM culture_items WHERE status='published'"),
    'posts' => (int)fetch_value("SELECT COUNT(*) FROM posts WHERE status='published'"),
    'community' => (int)fetch_value("SELECT COUNT(*) FROM community_posts WHERE status='approved'"),
];
$pageTitle = 'Giới thiệu';
$active = 'about';
$metaDesc = 'Câu chuyện, sứ mệnh và giá trị của Explore Vietnam.';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="page-hero-bg"><img src="<?= e(url('assets/img/seed/about.svg')) ?>" alt=""></div>
    <div class="container">
        <div class="breadcrumb"><a href="<?= e(url('index.php')) ?>">Trang chủ</a> / Giới thiệu</div>
        <h1>Về Explore Vietnam</h1>
        <p>Chạm đến Việt Nam, khám phá những điều tuyệt vời.</p>
    </div>
</section>
<section class="section">
    <div class="container about-split">
        <div class="pic reveal"><img src="<?= e(url('assets/img/seed/ninh-binh.svg')) ?>" alt="Phong cảnh Ninh Bình" loading="lazy"></div>
        <div class="reveal">
            <span class="eyebrow eyebrow-dark">Câu chuyện của chúng tôi</span>
            <h2>Một cánh cửa mở ra Việt Nam</h2>
            <p>Explore Vietnam là website quảng bá du lịch, văn hóa, thiên nhiên, ẩm thực, di sản và con người Việt Nam, dành cho du khách trong nước lẫn quốc tế.</p>
            <p>Chúng tôi tin rằng một chuyến đi đáng nhớ bắt đầu từ thông tin đáng tin cậy, cảm hứng chân thật và sự tôn trọng dành cho con người, văn hóa nơi mình đến.</p>
            <a class="btn btn-primary" href="<?= e(url('destinations.php')) ?>">Bắt đầu khám phá</a>
        </div>
    </div>
</section>
<section class="section section-dark">
    <div class="container stats">
        <div class="stat"><strong data-count="<?= $n['dest'] ?>">0</strong><span>Địa điểm</span></div>
        <div class="stat"><strong data-count="<?= $n['culture'] ?>">0</strong><span>Nội dung văn hóa</span></div>
        <div class="stat"><strong data-count="<?= $n['posts'] ?>">0</strong><span>Bài cẩm nang</span></div>
        <div class="stat"><strong data-count="<?= $n['community'] ?>">0</strong><span>Chia sẻ từ cộng đồng</span></div>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="section-head"><span class="eyebrow eyebrow-dark">Giá trị cốt lõi</span><h2>Chúng tôi làm gì?</h2></div>
        <div class="values">
            <div class="value reveal"><span class="ico">🌿</span><h3>Thiên nhiên &amp; bền vững</h3><p>Khuyến khích du lịch có trách nhiệm, giữ gìn cảnh quan và hệ sinh thái cho thế hệ sau.</p></div>
            <div class="value reveal"><span class="ico">🏮</span><h3>Văn hóa &amp; di sản</h3><p>Kể lại câu chuyện của ẩm thực, lễ hội, làng nghề và di sản bằng sự tôn trọng và chính xác.</p></div>
            <div class="value reveal"><span class="ico">🤝</span><h3>Cộng đồng &amp; kết nối</h3><p>Là nơi người đi chia sẻ cho người sắp đi, để mỗi hành trình thêm an toàn và ý nghĩa.</p></div>
        </div>
    </div>
</section>
<section class="section section-alt">
    <div class="container">
        <div class="cta-band reveal">
            <img src="<?= e(url('assets/img/seed/hero.svg')) ?>" alt="" loading="lazy">
            <h2>Cùng viết tiếp câu chuyện Việt Nam</h2>
            <p>Gửi góp ý, đề xuất địa điểm hoặc hợp tác nội dung với chúng tôi.</p>
            <a class="btn btn-gold" href="<?= e(url('contact.php')) ?>">Liên hệ ngay</a>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
