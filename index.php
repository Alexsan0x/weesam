<?php
require_once 'config.php';
require_once 'db.php';
$page_title = 'Home';
include 'includes/header.php';

$places = getAllPlaces();
$featured = array_slice($places, 0, 6);
?>
<script>
if (!localStorage.getItem('dalili_welcomed')) {
    window.location.replace('welcome.php<?= $isArabic ? "?lang=ar" : "" ?>');
}
</script>

    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge"><i class="fas fa-crown"></i> <?php echo t('Jordan\'s Smart Tourism Platform', 'منصة الأردن السياحية الذكية'); ?></div>
            <h1><?php echo t('Discover Jordan with <span>Dalili</span>', 'اكتشف الأردن مع <span>دليلي</span>'); ?></h1>
            <p><?php echo t('Your intelligent guide to exploring the Hashemite Kingdom. Navigate historic sites, get AI-powered recommendations, and plan unforgettable journeys across Jordan.', 'دليلك الذكي لاستكشاف المملكة الأردنية الهاشمية. تصفح المواقع التاريخية، واحصل على توصيات مدعومة بالذكاء الاصطناعي، وخطط لرحلات لا تُنسى عبر الأردن.'); ?></p>
            <div class="hero-buttons">
                <a href="map.php" class="btn btn-primary"><i class="fas fa-map-marked-alt"></i> <?php echo t('Explore the Map', 'استكشف الخريطة'); ?></a>
                <a href="about.php" class="btn btn-outline"><i class="fas fa-info-circle"></i> <?php echo t('Learn More', 'اعرف المزيد'); ?></a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <h3>13+</h3>
                    <p><?php echo t('Tourist Destinations', 'وجهة سياحية'); ?></p>
                </div>
                <div class="stat-item">
                    <h3><?php echo t('AI', 'ذكاء'); ?></h3>
                    <p><?php echo t('Powered Assistant', 'مساعد اصطناعي'); ?></p>
                </div>
                <div class="stat-item">
                    <h3>2</h3>
                    <p><?php echo t('Languages Supported', 'لغات مدعومة'); ?></p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="background: var(--bg-cream);">
        <div class="section-header">
            <h2><?php echo t('Why Choose Dalili?', 'لماذا تختار دليلي؟'); ?></h2>
            <p><?php echo t('A smart tourism platform that brings together maps, AI, and culture', 'منصة سياحية ذكية تجمع بين الخرائط والذكاء الاصطناعي والثقافة'); ?></p>
            <div class="accent-line"></div>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon map-icon"><i class="fas fa-map-marked-alt"></i></div>
                <h3><?php echo t('Interactive Maps', 'خرائط تفاعلية'); ?></h3>
                <p><?php echo t('Explore Jordan\'s tourist attractions with Google Maps integration, markers, directions and real-time navigation.', 'استكشف المعالم السياحية في الأردن من خلال خرائط جوجل التفاعلية مع العلامات والاتجاهات والملاحة الفورية.'); ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon ai-icon"><i class="fas fa-robot"></i></div>
                <h3><?php echo t('AI Assistant', 'المساعد الذكي'); ?></h3>
                <p><?php echo t('Meet Abu Mahmoud, your virtual tour guide. Ask about any place in Arabic or English and get smart recommendations.', 'تعرّف على أبو محمود، مرشدك السياحي الافتراضي. اسأله عن أي مكان بالعربية أو الإنجليزية واحصل على توصيات ذكية.'); ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon secure-icon"><i class="fas fa-shield-alt"></i></div>
                <h3><?php echo t('Secure Platform', 'منصة آمنة'); ?></h3>
                <p><?php echo t('Your data is protected with secure protocols and encrypted communication to ensure a safe browsing experience.', 'بياناتك محمية ببروتوكولات أمان واتصالات مشفرة لضمان تجربة تصفح آمنة.'); ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon lang-icon"><i class="fas fa-language"></i></div>
                <h3><?php echo t('Bilingual Support', 'دعم ثنائي اللغة'); ?></h3>
                <p><?php echo t('Fully supports both Arabic and English to help local and international tourists explore Jordan comfortably.', 'يدعم اللغتين العربية والإنجليزية بالكامل لمساعدة السياح المحليين والدوليين على استكشاف الأردن بسهولة.'); ?></p>
            </div>
        </div>
    </section>

    <section class="section" style="background: var(--bg-light);">
        <div class="section-header">
            <h2><?php echo t('Popular Destinations', 'وجهات شائعة'); ?></h2>
            <p><?php echo t('Discover some of Jordan\'s most iconic and breathtaking locations', 'اكتشف أشهر وأجمل المواقع في الأردن'); ?></p>
            <div class="accent-line"></div>
        </div>
        <div class="places-grid">
            <?php foreach ($featured as $place): ?>
            <div class="place-card" onclick="window.location.href='map.php?place=<?php echo $place['id']; ?>'">
                <div class="img-overflow-hidden">
                    <img src="<?php echo $place['image']; ?>" alt="<?php echo sanitize($isArabic && !empty($place['name_ar']) ? $place['name_ar'] : $place['name']); ?>" class="place-card-img" loading="lazy">
                </div>
                <div class="place-card-body">
                    <span class="place-card-category"><?php echo sanitize($isArabic && !empty($place['category_ar']) ? $place['category_ar'] : $place['category']); ?></span>
                    <h3><?php echo sanitize($isArabic && !empty($place['name_ar']) ? $place['name_ar'] : $place['name']); ?></h3>
                    <p><?php echo sanitize($isArabic && !empty($place['description_ar']) ? $place['description_ar'] : $place['description']); ?></p>
                </div>
                <div class="place-card-footer">
                    <span class="location"><i class="fas fa-map-marker-alt"></i> <?php echo sanitize($isArabic && !empty($place['city_ar']) ? $place['city_ar'] : $place['city']); ?></span>
                    <span class="view-btn"><?php echo t('View on Map', 'عرض على الخريطة'); ?> <i class="fas fa-arrow-<?php echo $isArabic ? 'left' : 'right'; ?>"></i></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-40">
            <a href="map.php" class="btn btn-secondary"><i class="fas fa-compass"></i> <?php echo t('Explore All Destinations', 'استكشف جميع الوجهات'); ?></a>
        </div>
    </section>

    <section class="section" style="background: linear-gradient(135deg, var(--bg-dark), #2C2C44); color: #fff;">
        <div class="container text-center">
            <h2 style="font-size: 2rem; margin-bottom: 16px;"><?php echo t('Ready to Explore Jordan?', 'مستعد لاستكشاف الأردن؟'); ?></h2>
            <p style="opacity: 0.8; max-width: 500px; margin: 0 auto 30px;"><?php echo t('Start your journey with Dalili and discover the wonders of the Hashemite Kingdom with the help of AI.', 'ابدأ رحلتك مع دليلي واكتشف عجائب المملكة الأردنية الهاشمية بمساعدة الذكاء الاصطناعي.'); ?></p>
            <div class="hero-buttons" style="justify-content: center;">
                <a href="map.php" class="btn btn-primary"><i class="fas fa-map"></i> <?php echo t('Open the Map', 'افتح الخريطة'); ?></a>
                <?php if (!$user): ?>
                <a href="register.php" class="btn btn-accent"><i class="fas fa-user-plus"></i> <?php echo t('Create Account', 'إنشاء حساب'); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
