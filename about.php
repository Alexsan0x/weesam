<?php
require_once 'config.php';
$page_title = t('About', 'حول');
include 'includes/header.php';
?>

    <div class="about-hero">
        <h1><?php echo t('About Dalili', 'حول دليلي'); ?></h1>
        <p><?php echo t('An intelligent tourism platform built to help visitors discover the beauty of Jordan', 'منصة سياحية ذكية صُمّمت لمساعدة الزوار على اكتشاف جمال الأردن'); ?></p>
    </div>

    <section class="about-section" style="background: var(--bg-cream);">
        <div class="about-content">
            <h2><?php echo t('What is Dalili?', 'ما هو دليلي؟'); ?></h2>
            <p>
                <?php echo t(
                    'Dalili (meaning "My Guide" in Arabic) is a web-based tourism platform designed to enhance visitors\' experiences in Jordan. The platform provides an interactive map with detailed information about tourist attractions, an AI-powered virtual assistant named "Abu Mahmoud," and a secure, user-friendly interface.',
                    'دليلي (تعني "مرشدي" بالعربية) هو منصة سياحية إلكترونية مصممة لتحسين تجارب الزوار في الأردن. توفر المنصة خريطة تفاعلية مع معلومات تفصيلية عن المعالم السياحية، ومساعداً افتراضياً مدعوماً بالذكاء الاصطناعي يُدعى "أبو محمود"، وواجهة آمنة وسهلة الاستخدام.'
                ); ?>
            </p>
            <p>
                <?php echo t(
                    'The project was developed as a graduation project at Mutah University, Faculty of Information Technology. It aims to solve the problem of fragmented tourism information by providing a single, intelligent platform where tourists can explore attractions, get directions, receive personalized recommendations, and interact with an AI assistant in both Arabic and English.',
                    'تم تطوير المشروع كمشروع تخرج في جامعة مؤتة، كلية تكنولوجيا المعلومات. يهدف إلى حل مشكلة تشتت المعلومات السياحية من خلال توفير منصة واحدة ذكية يمكن للسياح من خلالها استكشاف المعالم والحصول على الاتجاهات وتلقي توصيات مخصصة والتفاعل مع مساعد ذكاء اصطناعي بالعربية والإنجليزية.'
                ); ?>
            </p>
            <p>
                <?php echo t(
                    'Dalili integrates Google Maps API for geographic visualization and navigation, and Google Gemini AI for natural-language processing to power the Abu Mahmoud virtual assistant. The platform is built with modern web technologies and follows security best practices to protect user data.',
                    'يدمج دليلي واجهة برمجة تطبيقات خرائط جوجل للتصور الجغرافي والتنقل، وذكاء جوجل جيميناي الاصطناعي لمعالجة اللغة الطبيعية لتشغيل المساعد الافتراضي أبو محمود. المنصة مبنية بتقنيات ويب حديثة وتتبع أفضل ممارسات الأمان لحماية بيانات المستخدم.'
                ); ?>
            </p>
        </div>
    </section>

    <section class="about-section" style="background: var(--bg-light);">
        <div class="about-content">
            <h2><?php echo t('Technology Stack', 'التقنيات المستخدمة'); ?></h2>
            <p><?php echo t('Built using modern and reliable web technologies chosen for performance, security, and ease of integration.', 'مبنية باستخدام تقنيات ويب حديثة وموثوقة اختيرت للأداء والأمان وسهولة التكامل.'); ?></p>
            <div class="tech-stack">
                <div class="tech-item">
                    <i class="fab fa-html5"></i>
                    <h4>HTML5</h4>
                    <p><?php echo t('Semantic page structure', 'بنية صفحات دلالية'); ?></p>
                </div>
                <div class="tech-item">
                    <i class="fab fa-css3-alt"></i>
                    <h4>CSS3</h4>
                    <p><?php echo t('Responsive styling and layout', 'تنسيق وتخطيط متجاوب'); ?></p>
                </div>
                <div class="tech-item">
                    <i class="fab fa-js-square"></i>
                    <h4>JavaScript</h4>
                    <p><?php echo t('Interactive UI and API calls', 'واجهة تفاعلية واستدعاءات API'); ?></p>
                </div>
                <div class="tech-item">
                    <i class="fab fa-php"></i>
                    <h4>PHP</h4>
                    <p><?php echo t('Server-side logic and security', 'منطق الخادم والأمان'); ?></p>
                </div>
                <div class="tech-item">
                    <i class="fas fa-map-marked-alt"></i>
                    <h4><?php echo t('Google Maps API', 'واجهة خرائط جوجل'); ?></h4>
                    <p><?php echo t('Interactive maps and navigation', 'خرائط تفاعلية وتنقل'); ?></p>
                </div>
                <div class="tech-item">
                    <i class="fas fa-brain"></i>
                    <h4><?php echo t('Gemini AI API', 'واجهة جيميناي الذكية'); ?></h4>
                    <p><?php echo t('AI assistant and recommendations', 'مساعد ذكي وتوصيات'); ?></p>
                </div>
                <div class="tech-item">
                    <i class="fas fa-database"></i>
                    <h4>PostgreSQL</h4>
                    <p><?php echo t('User data and favorites storage', 'تخزين بيانات المستخدمين والمفضلة'); ?></p>
                </div>
                <div class="tech-item">
                    <i class="fas fa-lock"></i>
                    <h4><?php echo t('HTTPS / Security', 'HTTPS / الأمان'); ?></h4>
                    <p><?php echo t('Secure data transmission', 'نقل بيانات آمن'); ?></p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-section" style="background: var(--bg-cream);">
        <div class="about-content">
            <h2><?php echo t('Key Features', 'المميزات الرئيسية'); ?></h2>
            <div class="features-grid" style="margin-top: 20px;">
                <div class="feature-card">
                    <div class="feature-icon map-icon"><i class="fas fa-search-location"></i></div>
                    <h3><?php echo t('Search & Filter', 'البحث والتصفية'); ?></h3>
                    <p><?php echo t('Find attractions by name, category, or proximity. Filter results to match your interests.', 'ابحث عن المعالم بالاسم أو التصنيف أو القرب. صفّي النتائج لتتناسب مع اهتماماتك.'); ?></p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon ai-icon"><i class="fas fa-comments"></i></div>
                    <h3><?php echo t('Bilingual AI Chat', 'دردشة ذكية ثنائية اللغة'); ?></h3>
                    <p><?php echo t('Abu Mahmoud speaks Arabic and English, offering travel advice and cultural insights.', 'أبو محمود يتحدث العربية والإنجليزية، ويقدم نصائح السفر والمعلومات الثقافية.'); ?></p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon secure-icon"><i class="fas fa-heart"></i></div>
                    <h3><?php echo t('Save Favorites', 'حفظ المفضلة'); ?></h3>
                    <p><?php echo t('Create an account and save your favorite destinations for quick access later.', 'أنشئ حساباً واحفظ وجهاتك المفضلة للوصول السريع لاحقاً.'); ?></p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon lang-icon"><i class="fas fa-mobile-alt"></i></div>
                    <h3><?php echo t('Responsive Design', 'تصميم متجاوب'); ?></h3>
                    <p><?php echo t('Works seamlessly on desktops, tablets, and mobile phones.', 'يعمل بسلاسة على أجهزة الكمبيوتر والأجهزة اللوحية والهواتف المحمولة.'); ?></p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-section" style="background: var(--bg-light);">
        <div class="about-content text-center">
            <h2><?php echo t('Our Team', 'فريقنا'); ?></h2>
            <p><?php echo t('Developed by students at the Faculty of Information Technology, Mutah University', 'تم تطويره من قبل طلاب كلية تكنولوجيا المعلومات، جامعة مؤتة'); ?></p>
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-avatar"><i class="fas fa-user"></i></div>
                    <h4><?php echo t('Wesam Abu-Nsair', 'وسام أبو نصير'); ?></h4>
                    <p class="team-role"><?php echo t('Project Manager & Developer', 'مدير المشروع ومطور'); ?></p>
                    <p class="team-id">420222212507 — <?php echo t('Information Security', 'أمن المعلومات'); ?></p>
                </div>
                <div class="team-card">
                    <div class="team-avatar"><i class="fas fa-user"></i></div>
                    <h4><?php echo t('Abdalrahman Al-Majali', 'عبدالرحمن المجالي'); ?></h4>
                    <p class="team-role"><?php echo t('Backend Developer', 'مطور الخلفية'); ?></p>
                    <p class="team-id">120222212047 — <?php echo t('Information Security', 'أمن المعلومات'); ?></p>
                </div>
                <div class="team-card">
                    <div class="team-avatar"><i class="fas fa-user"></i></div>
                    <h4><?php echo t('Shahd Al-Salman', 'شهد السلمان'); ?></h4>
                    <p class="team-role"><?php echo t('Frontend Developer & UI/UX', 'مطورة الواجهة وتجربة المستخدم'); ?></p>
                    <p class="team-id">120222211048 — <?php echo t('Computer Science', 'علم الحاسوب'); ?></p>
                </div>
                <div class="team-card">
                    <div class="team-avatar"><i class="fas fa-user"></i></div>
                    <h4><?php echo t('Esra\'a Al-Masoud', 'إسراء المسعود'); ?></h4>
                    <p class="team-role"><?php echo t('QA & Content Specialist', 'ضمان الجودة وأخصائية المحتوى'); ?></p>
                    <p class="team-id">120222211007 — <?php echo t('Computer Science', 'علم الحاسوب'); ?></p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-section text-center" style="background: var(--bg-cream);">
        <div class="about-content">
            <h2><?php echo t('Supervised By', 'بإشراف'); ?></h2>
            <div class="team-card" style="max-width: 320px; margin: 20px auto;">
                <div class="team-avatar" style="background: linear-gradient(135deg, var(--secondary), var(--secondary-light));"><i class="fas fa-chalkboard-teacher"></i></div>
                <h4><?php echo t('Dr. Ghayth Al-Mahadin', 'د. غيث المحادين'); ?></h4>
                <p class="team-role"><?php echo t('Project Supervisor', 'مشرف المشروع'); ?></p>
                <p class="team-id"><?php echo t('Faculty of Information Technology, Mutah University', 'كلية تكنولوجيا المعلومات، جامعة مؤتة'); ?></p>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
