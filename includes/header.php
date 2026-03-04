<?php
if (!isset($page_title)) $page_title = 'Dalili';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $isArabic ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Dalili</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="./" class="nav-logo">
                <i class="fas fa-compass"></i>
                <span>Dalili</span>
            </a>

            <ul class="nav-links" id="navLinks">
                <li><a href="./" class="<?php echo $current_page === 'index' ? 'active' : ''; ?>"><?php echo t('Home', 'الرئيسية'); ?></a></li>
                <?php if ($user): ?>
                <li><a href="map.php" class="<?php echo $current_page === 'map' ? 'active' : ''; ?>"><?php echo t('Explore Map', 'استكشف الخريطة'); ?></a></li>
                <li><a href="timeline.php" class="<?php echo $current_page === 'timeline' ? 'active' : ''; ?>"><?php echo t('Timeline', 'الخط الزمني'); ?></a></li>
                <?php endif; ?>
                <li><a href="about.php" class="<?php echo $current_page === 'about' ? 'active' : ''; ?>"><?php echo t('About', 'حول'); ?></a></li>
                <?php if ($user): ?>
                    <li><a href="favorites.php" class="<?php echo $current_page === 'favorites' ? 'active' : ''; ?>"><?php echo t('Favorites', 'المفضلة'); ?></a></li>
                    <li class="nav-user-dropdown">
                        <button class="user-avatar-btn" id="userAvatarBtn" title="<?php echo sanitize($user['name']); ?>">
                            <span class="user-avatar-circle"><?php echo mb_strtoupper(mb_substr($user['name'], 0, 1, 'UTF-8'), 'UTF-8'); ?></span>
                        </button>
                        <div class="user-dropdown" id="userDropdown">
                            <div class="dropdown-header">
                                <span class="dropdown-avatar"><?php echo mb_strtoupper(mb_substr($user['name'], 0, 1, 'UTF-8'), 'UTF-8'); ?></span>
                                <div class="dropdown-user-info">
                                    <strong><?php echo sanitize($user['name']); ?></strong>
                                    <small><?php echo sanitize($user['email']); ?></small>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="settings.php" class="dropdown-item"><i class="fas fa-cog"></i> <?php echo t('Settings', 'الإعدادات'); ?></a>
                            <?php if (isAdmin()): ?>
                            <a href="admin.php" class="dropdown-item"><i class="fas fa-shield-alt"></i> <?php echo t('Admin Panel', 'لوحة التحكم'); ?></a>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item dropdown-logout"><i class="fas fa-sign-out-alt"></i> <?php echo t('Logout', 'تسجيل الخروج'); ?></a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-login <?php echo $current_page === 'login' ? 'active' : ''; ?>"><?php echo t('Login', 'دخول'); ?></a></li>
                <?php endif; ?>
            </ul>

            <?php
            // Build language toggle URL preserving current page & query params
            $toggleLang = $isArabic ? 'en' : 'ar';
            $langParams = $_GET;
            $langParams['lang'] = $toggleLang;
            $basePage = basename($_SERVER['PHP_SELF']);
            $langBase = ($basePage === 'index.php') ? './' : $basePage;
            $langUrl = $langBase . '?' . http_build_query($langParams);
            ?>
            <a href="<?php echo $langUrl; ?>" class="btn-lang" title="<?php echo $isArabic ? 'Switch to English' : 'التبديل للعربية'; ?>">
                <i class="fas fa-globe"></i>
                <span><?php echo $isArabic ? 'EN' : 'عربي'; ?></span>
            </a>

            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>
