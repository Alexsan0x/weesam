<?php
require_once 'config.php';
require_once 'db.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$page_title = t('Explore Map', 'استكشف الخريطة');

$selectedPlace = $_GET['place'] ?? null;

include 'includes/header.php';
?>

    <div class="map-page">
        <div class="map-sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-map-marker-alt"></i> <?php echo t('Places', 'الأماكن'); ?></h3>
            </div>

            <div class="sidebar-panel active" id="panel-places">
                <div class="search-box">
                    <div class="search-input-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="<?php echo t('Search for a place...', 'ابحث عن مكان...'); ?>">
                    </div>
                </div>
                <div class="category-filters" id="categoryFilters">
                    <button class="filter-chip active" data-category="all"><?php echo t('All', 'الكل'); ?></button>
                    <button class="filter-chip" data-category="Historical"><?php echo t('Historical', 'تاريخي'); ?></button>
                    <button class="filter-chip" data-category="Nature"><?php echo t('Nature', 'طبيعة'); ?></button>
                    <button class="filter-chip" data-category="Religious"><?php echo t('Religious', 'ديني'); ?></button>
                    <button class="filter-chip" data-category="Adventure"><?php echo t('Adventure', 'مغامرات'); ?></button>
                </div>
                <div class="places-list" id="placesList"></div>
            </div>
        </div>

        <div class="map-container">
            <div id="googleMap"></div>
            <div class="map-place-detail" id="placeDetail">
                <button class="close-detail" id="closeDetail"><i class="fas fa-times"></i></button>
                <h3 id="detailName"></h3>
                <p id="detailDesc"></p>
                <div class="detail-actions">
                    <a id="detailDirections" href="#" target="_blank" class="btn btn-primary btn-sm">
                        <i class="fas fa-directions"></i> <?php echo t('Get Directions', 'الاتجاهات'); ?>
                    </a>
                    <?php if (isLoggedIn()): ?>
                    <button id="detailFavorite" class="btn btn-accent btn-sm" onclick="toggleFavorite()">
                        <i class="fas fa-heart"></i> <?php echo t('Save', 'حفظ'); ?>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="selectedPlace" value="<?php echo sanitize($selectedPlace ?? ''); ?>">
    <input type="hidden" id="isLoggedIn" value="<?php echo isLoggedIn() ? '1' : '0'; ?>">

    <script src="js/map.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_api_key; ?>&callback=initMap" async defer></script>
<?php include 'includes/footer.php'; ?>
