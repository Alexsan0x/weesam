<?php
require_once 'config.php';
require_once 'db.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$page_title = t('Favorites', 'المفضلة');
include 'includes/header.php';

$user = getCurrentUser();
$favoriteIds = getUserFavorites($user['id']);
$allPlaces = getAllPlaces();

$favorites = array_filter($allPlaces, function($place) use ($favoriteIds) {
    return in_array($place['id'], $favoriteIds);
});
$favCount = count($favorites);
?>

    <div class="favorites-page">
        <div class="fav-hero">
            <div class="fav-hero-content">
                <div class="fav-hero-icon"><i class="fas fa-heart"></i></div>
                <h1><?php echo t('My Favorites', 'مفضلاتي'); ?></h1>
                <p><?php echo t('Your personal collection of Jordan\'s best destinations', 'مجموعتك الشخصية من أفضل وجهات الأردن'); ?></p>
                <?php if ($favCount > 0): ?>
                <div class="fav-count-badge"><i class="fas fa-bookmark"></i> <?php echo $favCount; ?> <?php echo t('saved places', 'مكان محفوظ'); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="fav-container">
            <?php if (empty($favorites)): ?>
                <div class="favorites-empty-card">
                    <div class="empty-illustration">
                        <div class="empty-circle">
                            <i class="far fa-heart"></i>
                        </div>
                        <div class="empty-dots">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                    <h3><?php echo t('No favorites yet', 'لا توجد مفضلات بعد'); ?></h3>
                    <p><?php echo t('Discover amazing places across Jordan and save the ones you love. They\'ll appear right here for easy access.', 'اكتشف أماكن مذهلة في جميع أنحاء الأردن واحفظ الأماكن التي تحبها. ستظهر هنا للوصول السريع.'); ?></p>
                    <div class="empty-actions">
                        <a href="map.php" class="btn btn-primary"><i class="fas fa-map-marked-alt"></i> <?php echo t('Explore the Map', 'استكشف الخريطة'); ?></a>
                        <a href="timeline.php" class="btn btn-outline-dark"><i class="fas fa-clock"></i> <?php echo t('View Timeline', 'شاهد الخط الزمني'); ?></a>
                    </div>
                </div>
            <?php else: ?>
                <div class="fav-grid">
                    <?php foreach ($favorites as $place): 
                        $pName = $isArabic && $place['name_ar'] ? $place['name_ar'] : $place['name'];
                        $pCity = $isArabic && ($place['city_ar'] ?? '') ? $place['city_ar'] : $place['city'];
                        $pCat = $isArabic && ($place['category_ar'] ?? '') ? $place['category_ar'] : $place['category'];
                        $pDesc = $isArabic && ($place['description_ar'] ?? '') ? $place['description_ar'] : $place['description'];
                    ?>
                    <div class="fav-card" id="fav-<?php echo $place['id']; ?>">
                        <div class="fav-card-img">
                            <img src="<?php echo $place['image']; ?>" alt="<?php echo sanitize($pName); ?>" loading="lazy">
                            <div class="fav-card-overlay">
                                <span class="fav-category"><?php echo sanitize($pCat); ?></span>
                                <button class="fav-remove-btn" onclick="removeFav('<?php echo $place['id']; ?>')" title="<?php echo t('Remove', 'إزالة'); ?>">
                                    <i class="fas fa-heart-broken"></i>
                                </button>
                            </div>
                        </div>
                        <div class="fav-card-body">
                            <h3><?php echo sanitize($pName); ?></h3>
                            <p class="fav-desc"><?php echo sanitize(mb_substr($pDesc, 0, 120, 'UTF-8')); ?>...</p>
                            <div class="fav-card-meta">
                                <span class="fav-location"><i class="fas fa-map-marker-alt"></i> <?php echo sanitize($pCity); ?></span>
                            </div>
                        </div>
                        <a href="map.php?place=<?php echo $place['id']; ?>" class="fav-card-link">
                            <?php echo t('View on Map', 'عرض على الخريطة'); ?> <i class="fas fa-arrow-<?php echo $isArabic ? 'left' : 'right'; ?>"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function removeFav(placeId) {
        if (!confirm(<?php echo json_encode(t('Remove this place from your favorites?', 'إزالة هذا المكان من مفضلاتك؟')); ?>)) return;
        var card = document.getElementById('fav-' + placeId);
        if (card) {
            card.style.transform = 'scale(0.9)';
            card.style.opacity = '0';
        }
        var formData = new FormData();
        formData.append('action', 'remove');
        formData.append('place_id', placeId);
        fetch('api/favorites.php', { method: 'POST', body: formData })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    setTimeout(function() {
                        if (card) card.remove();
                        var remaining = document.querySelectorAll('.fav-card');
                        if (remaining.length === 0) location.reload();
                        var badge = document.querySelector('.fav-count-badge');
                        if (badge) badge.innerHTML = '<i class="fas fa-bookmark"></i> ' + remaining.length + ' <?php echo t('saved places', 'مكان محفوظ'); ?>';
                    }, 300);
                }
            });
    }
    </script>

<?php include 'includes/footer.php'; ?>
