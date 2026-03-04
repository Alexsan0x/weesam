<?php
require_once 'config.php';
require_once 'db.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$page_title = t('Timeline', 'الخط الزمني');
include 'includes/header.php';

$places = getAllPlaces();

usort($places, function($a, $b) {
    return ($a['year_established'] ?? 0) - ($b['year_established'] ?? 0);
});
?>

    <div class="timeline-hero">
        <h1><?php echo t('Jordan Through Time', 'الأردن عبر الزمن'); ?></h1>
        <p><?php echo t('A journey through the ages — explore the rich history of Jordan\'s most iconic destinations', 'رحلة عبر العصور — استكشف التاريخ الغني لأشهر وجهات الأردن'); ?></p>
    </div>

    <div class="timeline-page">
        <div class="timeline">
            <?php foreach ($places as $place): ?>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-card">
                    <img src="<?php echo $place['image']; ?>" alt="<?php echo sanitize($isArabic && $place['name_ar'] ? $place['name_ar'] : $place['name']); ?>" loading="lazy">
                    <div class="timeline-card-body">
                        <span class="timeline-year"><?php echo sanitize($isArabic && ($place['era_ar'] ?? '') ? $place['era_ar'] : ($place['era'] ?? t('Ancient', 'قديم'))); ?></span>
                        <h3><?php echo sanitize($isArabic && $place['name_ar'] ? $place['name_ar'] : $place['name']); ?></h3>
                        <p><?php echo sanitize($isArabic && ($place['description_ar'] ?? '') ? $place['description_ar'] : $place['description']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
