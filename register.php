<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('./');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db.php';
    
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $error = t('Please fill in all fields.', 'يرجى ملء جميع الحقول.');
    } elseif (strlen($password) < 6) {
        $error = t('Password must be at least 6 characters long.', 'يجب أن تكون كلمة المرور 6 أحرف على الأقل.');
    } elseif ($password !== $confirm) {
        $error = t('Passwords do not match.', 'كلمات المرور غير متطابقة.');
    } elseif (findUserByEmail($email)) {
        $error = t('This email is already registered.', 'هذا البريد الإلكتروني مسجل مسبقاً.');
    } else {
        if (createUser($name, $email, $password)) {
            redirect('login.php');
        } else {
            $error = t('Something went wrong. Please try again.', 'حدث خطأ ما. يرجى المحاولة مرة أخرى.');
        }
    }
}

$page_title = t('Register', 'إنشاء حساب');
include 'includes/header.php';
?>

    <div class="auth-page">
        <div class="auth-card">
            <h2><?php echo t('Create Account', 'إنشاء حساب'); ?></h2>
            <p class="auth-subtitle"><?php echo t('Join Dalili and start exploring Jordan', 'انضم إلى دليلي وابدأ استكشاف الأردن'); ?></p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="form-group">
                    <label for="name"><?php echo t('Full Name', 'الاسم الكامل'); ?></label>
                    <input type="text" id="name" name="name" placeholder="<?php echo t('Your full name', 'اسمك الكامل'); ?>" required
                           value="<?php echo isset($name) ? sanitize($name) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="email"><?php echo t('Email Address', 'البريد الإلكتروني'); ?></label>
                    <input type="email" id="email" name="email" placeholder="<?php echo t('your@email.com', 'بريدك@email.com'); ?>" required
                           value="<?php echo isset($email) ? sanitize($email) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="password"><?php echo t('Password', 'كلمة المرور'); ?></label>
                    <input type="password" id="password" name="password" placeholder="<?php echo t('At least 6 characters', '6 أحرف على الأقل'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password"><?php echo t('Confirm Password', 'تأكيد كلمة المرور'); ?></label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="<?php echo t('Repeat your password', 'أعد كلمة المرور'); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo t('Create Account', 'إنشاء حساب'); ?></button>
            </form>

            <p class="auth-footer">
                <?php echo t('Already have an account?', 'لديك حساب بالفعل؟'); ?> <a href="login.php"><?php echo t('Sign in', 'سجّل دخولك'); ?></a>
            </p>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
