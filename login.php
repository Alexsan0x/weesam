<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('./');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db.php';
    
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = t('Please fill in all fields.', 'يرجى ملء جميع الحقول.');
    } else {
        $user = findUserByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'] ?? 'user';
            redirect('./');
        } else {
            $error = t('Incorrect email or password.', 'البريد الإلكتروني أو كلمة المرور غير صحيحة.');
        }
    }
}

$page_title = t('Login', 'تسجيل الدخول');
include 'includes/header.php';
?>

    <div class="auth-page">
        <div class="auth-card">
            <h2><?php echo t('Welcome Back', 'مرحباً بعودتك'); ?></h2>
            <p class="auth-subtitle"><?php echo t('Sign in to access your Dalili account', 'سجّل دخولك للوصول إلى حسابك في دليلي'); ?></p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email"><?php echo t('Email Address', 'البريد الإلكتروني'); ?></label>
                    <input type="email" id="email" name="email" placeholder="<?php echo t('your@email.com', 'بريدك@email.com'); ?>" required 
                           value="<?php echo isset($email) ? sanitize($email) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="password"><?php echo t('Password', 'كلمة المرور'); ?></label>
                    <input type="password" id="password" name="password" placeholder="<?php echo t('Enter your password', 'أدخل كلمة المرور'); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo t('Sign In', 'تسجيل الدخول'); ?></button>
            </form>

            <p class="auth-footer">
                <?php echo t('Don\'t have an account?', 'ليس لديك حساب؟'); ?> <a href="register.php"><?php echo t('Create one', 'أنشئ حساباً'); ?></a>
            </p>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
