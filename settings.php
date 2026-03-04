<?php
require_once 'config.php';
require_once 'db.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user = findUserById($_SESSION['user_id']);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');

        if (empty($name) || empty($email)) {
            $error = t('Please fill in all fields.', 'يرجى ملء جميع الحقول.');
        } elseif ($email !== $user['email'] && findUserByEmail($email)) {
            $error = t('This email is already registered.', 'هذا البريد الإلكتروني مسجل مسبقاً.');
        } else {
            updateUser($_SESSION['user_id'], $name, $email);
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $user = findUserById($_SESSION['user_id']);
            $success = t('Profile updated successfully!', 'تم تحديث الملف الشخصي بنجاح!');
        }
    }

    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($current) || empty($newPass) || empty($confirm)) {
            $error = t('Please fill in all password fields.', 'يرجى ملء جميع حقول كلمة المرور.');
        } elseif (!password_verify($current, $user['password_hash'])) {
            $error = t('Current password is incorrect.', 'كلمة المرور الحالية غير صحيحة.');
        } elseif (strlen($newPass) < 6) {
            $error = t('New password must be at least 6 characters.', 'يجب أن تكون كلمة المرور الجديدة 6 أحرف على الأقل.');
        } elseif ($newPass !== $confirm) {
            $error = t('New passwords do not match.', 'كلمات المرور الجديدة غير متطابقة.');
        } else {
            updateUserPassword($_SESSION['user_id'], $newPass);
            $success = t('Password changed successfully!', 'تم تغيير كلمة المرور بنجاح!');
        }
    }
}

$page_title = t('Settings', 'الإعدادات');
include 'includes/header.php';
?>

    <div class="settings-page">
        <h1><i class="fas fa-cog"></i> <?php echo t('Account Settings', 'إعدادات الحساب'); ?></h1>
        <p class="subtitle"><?php echo t('Manage your profile and security preferences', 'إدارة ملفك الشخصي وتفضيلات الأمان'); ?></p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="settings-card">
            <h3><i class="fas fa-user"></i> <?php echo t('Profile Information', 'معلومات الملف الشخصي'); ?></h3>
            <form method="POST">
                <input type="hidden" name="action" value="update_profile">
                <div class="form-group">
                    <label for="name"><?php echo t('Full Name', 'الاسم الكامل'); ?></label>
                    <input type="text" id="name" name="name" value="<?php echo sanitize($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email"><?php echo t('Email Address', 'البريد الإلكتروني'); ?></label>
                    <input type="email" id="email" name="email" value="<?php echo sanitize($user['email']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo t('Save Changes', 'حفظ التغييرات'); ?></button>
            </form>
        </div>

        <div class="settings-card">
            <h3><i class="fas fa-lock"></i> <?php echo t('Change Password', 'تغيير كلمة المرور'); ?></h3>
            <form method="POST">
                <input type="hidden" name="action" value="change_password">
                <div class="form-group">
                    <label for="current_password"><?php echo t('Current Password', 'كلمة المرور الحالية'); ?></label>
                    <input type="password" id="current_password" name="current_password" placeholder="<?php echo t('Enter current password', 'أدخل كلمة المرور الحالية'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="new_password"><?php echo t('New Password', 'كلمة المرور الجديدة'); ?></label>
                    <input type="password" id="new_password" name="new_password" placeholder="<?php echo t('At least 6 characters', '6 أحرف على الأقل'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password"><?php echo t('Confirm New Password', 'تأكيد كلمة المرور الجديدة'); ?></label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="<?php echo t('Repeat new password', 'أعد كلمة المرور الجديدة'); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo t('Change Password', 'تغيير كلمة المرور'); ?></button>
            </form>
        </div>

        <div class="settings-card">
            <h3><i class="fas fa-info-circle"></i> <?php echo t('Account Info', 'معلومات الحساب'); ?></h3>
            <p style="color: var(--text-body); font-size: 0.9rem;">
                <?php echo t('Member since', 'عضو منذ'); ?>: <strong><?php echo isset($user['created_at']) ? date('F j, Y', strtotime($user['created_at'])) : 'N/A'; ?></strong><br>
                <?php echo t('Role', 'الدور'); ?>: <span class="role-badge <?php echo $user['role'] ?? 'user'; ?>"><?php echo ucfirst($user['role'] ?? 'user'); ?></span>
            </p>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
