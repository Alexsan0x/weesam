<?php
require_once 'config.php';
require_once 'db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('./');
}

$tab = $_GET['tab'] ?? 'dashboard';
$action = $_GET['action'] ?? '';
$error = '';
$success = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    // PLACES
    if ($postAction === 'create_place' || $postAction === 'update_place') {
        $placeData = [
            'id' => strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', trim($_POST['id'] ?? ''))),
            'name' => trim($_POST['name'] ?? ''),
            'name_ar' => trim($_POST['name_ar'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'city_ar' => trim($_POST['city_ar'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'category_ar' => trim($_POST['category_ar'] ?? ''),
            'lat' => floatval($_POST['lat'] ?? 0),
            'lng' => floatval($_POST['lng'] ?? 0),
            'year_established' => intval($_POST['year_established'] ?? 0),
            'era' => trim($_POST['era'] ?? ''),
            'era_ar' => trim($_POST['era_ar'] ?? ''),
            'image' => trim($_POST['image'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'description_ar' => trim($_POST['description_ar'] ?? ''),
        ];

        if (empty($placeData['name']) || empty($placeData['id'])) {
            $error = t('Place ID and Name are required.', 'معرف المكان والاسم مطلوبان.');
        } else {
            if ($postAction === 'create_place') {
                $existing = getPlaceById($placeData['id']);
                if ($existing) {
                    $error = t('A place with this ID already exists.', 'يوجد مكان بهذا المعرف بالفعل.');
                } else {
                    createPlace($placeData);
                    $success = t('Place created successfully!', 'تم إنشاء المكان بنجاح!');
                }
            } else {
                $origId = $_POST['original_id'] ?? $placeData['id'];
                updatePlace($origId, $placeData);
                $success = t('Place updated successfully!', 'تم تحديث المكان بنجاح!');
            }
        }
    }

    if ($postAction === 'delete_place') {
        $placeId = $_POST['place_id'] ?? '';
        if ($placeId) {
            deletePlace($placeId);
            $success = t('Place deleted.', 'تم حذف المكان.');
        }
    }

    // USERS
    if ($postAction === 'update_user') {
        $uid = intval($_POST['user_id'] ?? 0);
        $uname = trim($_POST['name'] ?? '');
        $uemail = trim($_POST['email'] ?? '');
        $urole = $_POST['role'] ?? 'user';

        if ($uid && $uname && $uemail) {
            updateUser($uid, $uname, $uemail);
            updateUserRole($uid, $urole);
            $newPass = trim($_POST['new_password'] ?? '');
            if ($newPass) {
                updateUserPassword($uid, $newPass);
            }
            $success = t('User updated successfully!', 'تم تحديث المستخدم بنجاح!');
        }
    }

    if ($postAction === 'delete_user') {
        $uid = intval($_POST['user_id'] ?? 0);
        if ($uid && $uid !== $_SESSION['user_id']) {
            deleteUser($uid);
            $success = t('User deleted.', 'تم حذف المستخدم.');
        } else {
            $error = t('Cannot delete your own account.', 'لا يمكنك حذف حسابك الخاص.');
        }
    }
}

$places = getAllPlaces();
$users = getAllUsers();

$totalFavs = 0;
try {
    $db = getDB();
    $fRes = $db->query("SELECT COUNT(*) FROM favorites");
    $totalFavs = $fRes->fetchColumn();
} catch (Exception $e) {}

$page_title = t('Admin Panel', 'لوحة التحكم');
include 'includes/header.php';
?>

    <div class="admin-page">
        <h1><i class="fas fa-shield-alt"></i> <?php echo t('Admin Panel', 'لوحة التحكم'); ?></h1>
        <p class="subtitle"><?php echo t('Manage places, users, and site content', 'إدارة الأماكن والمستخدمين ومحتوى الموقع'); ?></p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="admin-tabs">
            <a href="admin.php?tab=dashboard" class="admin-tab <?php echo $tab === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> <?php echo t('Dashboard', 'لوحة القيادة'); ?>
            </a>
            <a href="admin.php?tab=places" class="admin-tab <?php echo $tab === 'places' ? 'active' : ''; ?>">
                <i class="fas fa-map-marker-alt"></i> <?php echo t('Places', 'الأماكن'); ?>
            </a>
            <a href="admin.php?tab=users" class="admin-tab <?php echo $tab === 'users' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> <?php echo t('Users', 'المستخدمين'); ?>
            </a>
        </div>

        <?php if ($tab === 'dashboard'): ?>
        <!-- Dashboard -->
        <div class="admin-stats">
            <div class="stat-card">
                <div class="stat-icon places"><i class="fas fa-map-marker-alt"></i></div>
                <div>
                    <h4><?php echo count($places); ?></h4>
                    <p><?php echo t('Total Places', 'إجمالي الأماكن'); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon users"><i class="fas fa-users"></i></div>
                <div>
                    <h4><?php echo count($users); ?></h4>
                    <p><?php echo t('Registered Users', 'المستخدمين المسجلين'); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon favs"><i class="fas fa-heart"></i></div>
                <div>
                    <h4><?php echo $totalFavs; ?></h4>
                    <p><?php echo t('Total Favorites', 'إجمالي المفضلة'); ?></p>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <h3><i class="fas fa-clock"></i> <?php echo t('Recent Users', 'المستخدمين الجدد'); ?></h3>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th><?php echo t('Name', 'الاسم'); ?></th>
                            <th><?php echo t('Email', 'البريد'); ?></th>
                            <th><?php echo t('Role', 'الدور'); ?></th>
                            <th><?php echo t('Joined', 'انضم'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($users, 0, 5) as $u): ?>
                        <tr>
                            <td><?php echo sanitize($u['name']); ?></td>
                            <td><?php echo sanitize($u['email']); ?></td>
                            <td><span class="role-badge <?php echo $u['role']; ?>"><?php echo $u['role']; ?></span></td>
                            <td><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif ($tab === 'places'): ?>
        <!-- Places Management -->
        <div class="admin-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin-bottom: 0;"><i class="fas fa-map-marker-alt"></i> <?php echo t('Manage Places', 'إدارة الأماكن'); ?> (<?php echo count($places); ?>)</h3>
                <button class="btn-add" onclick="openPlaceModal()"><i class="fas fa-plus"></i> <?php echo t('Add Place', 'إضافة مكان'); ?></button>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th><?php echo t('Name', 'الاسم'); ?></th>
                            <th><?php echo t('City', 'المدينة'); ?></th>
                            <th><?php echo t('Category', 'التصنيف'); ?></th>
                            <th><?php echo t('Coords', 'الإحداثيات'); ?></th>
                            <th><?php echo t('Actions', 'إجراءات'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($places as $p): ?>
                        <tr>
                            <td>
                                <strong><?php echo sanitize($p['name']); ?></strong>
                                <?php if ($p['name_ar']): ?>
                                    <br><small style="color: var(--text-light);"><?php echo sanitize($p['name_ar']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo sanitize($p['city']); ?></td>
                            <td><span class="role-badge user"><?php echo sanitize($p['category']); ?></span></td>
                            <td style="font-size: 0.78rem;"><?php echo $p['lat']; ?>, <?php echo $p['lng']; ?></td>
                            <td class="actions">
                                <button class="btn-sm btn-edit" onclick='editPlace(<?php echo json_encode($p); ?>)'><i class="fas fa-edit"></i></button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('<?php echo t('Delete this place?', 'حذف هذا المكان؟'); ?>');">
                                    <input type="hidden" name="action" value="delete_place">
                                    <input type="hidden" name="place_id" value="<?php echo sanitize($p['id']); ?>">
                                    <button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Place Modal -->
        <div class="modal-overlay" id="placeModal">
            <div class="modal-content">
                <h3 id="placeModalTitle"><?php echo t('Add New Place', 'إضافة مكان جديد'); ?></h3>
                <form method="POST" id="placeForm">
                    <input type="hidden" name="action" id="placeFormAction" value="create_place">
                    <input type="hidden" name="original_id" id="placeOriginalId" value="">

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo t('Place ID (slug)', 'معرف المكان'); ?></label>
                            <input type="text" name="id" id="placeId" placeholder="e.g. wadi-rum" required>
                        </div>
                        <div class="form-group">
                            <label><?php echo t('Category', 'التصنيف'); ?></label>
                            <select name="category" id="placeCategory" style="width:100%; padding:12px; border:1px solid var(--border); border-radius: var(--radius-sm); background: var(--bg-light); color: var(--text-dark); font-size: 0.92rem;">
                                <option value="Historical"><?php echo t('Historical', 'تاريخي'); ?></option>
                                <option value="Nature"><?php echo t('Nature', 'طبيعة'); ?></option>
                                <option value="Religious"><?php echo t('Religious', 'ديني'); ?></option>
                                <option value="Adventure"><?php echo t('Adventure', 'مغامرات'); ?></option>
                                <option value="City"><?php echo t('City', 'مدينة'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo t('Name (English)', 'الاسم (إنجليزي)'); ?></label>
                            <input type="text" name="name" id="placeName" required>
                        </div>
                        <div class="form-group">
                            <label><?php echo t('Name (Arabic)', 'الاسم (عربي)'); ?></label>
                            <input type="text" name="name_ar" id="placeNameAr" dir="rtl">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo t('City (English)', 'المدينة (إنجليزي)'); ?></label>
                            <input type="text" name="city" id="placeCity">
                        </div>
                        <div class="form-group">
                            <label><?php echo t('City (Arabic)', 'المدينة (عربي)'); ?></label>
                            <input type="text" name="city_ar" id="placeCityAr" dir="rtl">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo t('Category (Arabic)', 'التصنيف (عربي)'); ?></label>
                        <input type="text" name="category_ar" id="placeCategoryAr" dir="rtl">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo t('Latitude', 'خط العرض'); ?></label>
                            <input type="number" name="lat" id="placeLat" step="0.0001">
                        </div>
                        <div class="form-group">
                            <label><?php echo t('Longitude', 'خط الطول'); ?></label>
                            <input type="number" name="lng" id="placeLng" step="0.0001">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo t('Year Established', 'سنة التأسيس'); ?></label>
                            <input type="number" name="year_established" id="placeYear">
                        </div>
                        <div class="form-group">
                            <label><?php echo t('Era', 'الحقبة'); ?></label>
                            <input type="text" name="era" id="placeEra">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo t('Era (Arabic)', 'الحقبة (عربي)'); ?></label>
                        <input type="text" name="era_ar" id="placeEraAr" dir="rtl">
                    </div>

                    <div class="form-group">
                        <label><?php echo t('Image URL', 'رابط الصورة'); ?></label>
                        <input type="url" name="image" id="placeImage">
                    </div>

                    <div class="form-group">
                        <label><?php echo t('Description (English)', 'الوصف (إنجليزي)'); ?></label>
                        <textarea name="description" id="placeDesc" rows="3" style="width:100%; padding:12px; border:1px solid var(--border); border-radius: var(--radius-sm); background: var(--bg-light); color: var(--text-dark); font-size: 0.92rem; resize: vertical;"></textarea>
                    </div>

                    <div class="form-group">
                        <label><?php echo t('Description (Arabic)', 'الوصف (عربي)'); ?></label>
                        <textarea name="description_ar" id="placeDescAr" rows="3" dir="rtl" style="width:100%; padding:12px; border:1px solid var(--border); border-radius: var(--radius-sm); background: var(--bg-light); color: var(--text-dark); font-size: 0.92rem; resize: vertical;"></textarea>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn-cancel" onclick="closePlaceModal()"><?php echo t('Cancel', 'إلغاء'); ?></button>
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> <?php echo t('Save', 'حفظ'); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        function openPlaceModal() {
            document.getElementById('placeModalTitle').textContent = '<?php echo t('Add New Place', 'إضافة مكان جديد'); ?>';
            document.getElementById('placeFormAction').value = 'create_place';
            document.getElementById('placeForm').reset();
            document.getElementById('placeOriginalId').value = '';
            document.getElementById('placeId').readOnly = false;
            document.getElementById('placeModal').classList.add('show');
        }

        function editPlace(p) {
            document.getElementById('placeModalTitle').textContent = '<?php echo t('Edit Place', 'تعديل المكان'); ?>';
            document.getElementById('placeFormAction').value = 'update_place';
            document.getElementById('placeOriginalId').value = p.id;
            document.getElementById('placeId').value = p.id;
            document.getElementById('placeId').readOnly = true;
            document.getElementById('placeName').value = p.name || '';
            document.getElementById('placeNameAr').value = p.name_ar || '';
            document.getElementById('placeCity').value = p.city || '';
            document.getElementById('placeCityAr').value = p.city_ar || '';
            document.getElementById('placeCategory').value = p.category || 'Historical';
            document.getElementById('placeCategoryAr').value = p.category_ar || '';
            document.getElementById('placeLat').value = p.lat || '';
            document.getElementById('placeLng').value = p.lng || '';
            document.getElementById('placeYear').value = p.year_established || '';
            document.getElementById('placeEra').value = p.era || '';
            document.getElementById('placeEraAr').value = p.era_ar || '';
            document.getElementById('placeImage').value = p.image || '';
            document.getElementById('placeDesc').value = p.description || '';
            document.getElementById('placeDescAr').value = p.description_ar || '';
            document.getElementById('placeModal').classList.add('show');
        }

        function closePlaceModal() {
            document.getElementById('placeModal').classList.remove('show');
        }

        document.getElementById('placeModal').addEventListener('click', function(e) {
            if (e.target === this) closePlaceModal();
        });
        </script>

        <?php elseif ($tab === 'users'): ?>
        <!-- Users Management -->
        <div class="admin-card">
            <h3><i class="fas fa-users"></i> <?php echo t('Manage Users', 'إدارة المستخدمين'); ?> (<?php echo count($users); ?>)</h3>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><?php echo t('Name', 'الاسم'); ?></th>
                            <th><?php echo t('Email', 'البريد'); ?></th>
                            <th><?php echo t('Role', 'الدور'); ?></th>
                            <th><?php echo t('Joined', 'انضم'); ?></th>
                            <th><?php echo t('Actions', 'إجراءات'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td><?php echo sanitize($u['name']); ?></td>
                            <td><?php echo sanitize($u['email']); ?></td>
                            <td><span class="role-badge <?php echo $u['role']; ?>"><?php echo $u['role']; ?></span></td>
                            <td><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
                            <td class="actions">
                                <button class="btn-sm btn-edit" onclick='editUser(<?php echo json_encode($u); ?>)'><i class="fas fa-edit"></i></button>
                                <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('<?php echo t('Delete this user?', 'حذف هذا المستخدم؟'); ?>');">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- User Edit Modal -->
        <div class="modal-overlay" id="userModal">
            <div class="modal-content">
                <h3 id="userModalTitle"><?php echo t('Edit User', 'تعديل المستخدم'); ?></h3>
                <form method="POST" id="userForm">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="user_id" id="editUserId" value="">

                    <div class="form-group">
                        <label><?php echo t('Full Name', 'الاسم الكامل'); ?></label>
                        <input type="text" name="name" id="editUserName" required>
                    </div>
                    <div class="form-group">
                        <label><?php echo t('Email', 'البريد الإلكتروني'); ?></label>
                        <input type="email" name="email" id="editUserEmail" required>
                    </div>
                    <div class="form-group">
                        <label><?php echo t('Role', 'الدور'); ?></label>
                        <select name="role" id="editUserRole" style="width:100%; padding:12px; border:1px solid var(--border); border-radius: var(--radius-sm); background: var(--bg-light); color: var(--text-dark); font-size: 0.92rem;">
                            <option value="user"><?php echo t('User', 'مستخدم'); ?></option>
                            <option value="admin"><?php echo t('Admin', 'مدير'); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?php echo t('New Password (leave empty to keep current)', 'كلمة مرور جديدة (اتركها فارغة للإبقاء على الحالية)'); ?></label>
                        <input type="password" name="new_password" id="editUserPass" placeholder="<?php echo t('Optional', 'اختياري'); ?>">
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn-cancel" onclick="closeUserModal()"><?php echo t('Cancel', 'إلغاء'); ?></button>
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> <?php echo t('Save', 'حفظ'); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        function editUser(u) {
            document.getElementById('editUserId').value = u.id;
            document.getElementById('editUserName').value = u.name;
            document.getElementById('editUserEmail').value = u.email;
            document.getElementById('editUserRole').value = u.role || 'user';
            document.getElementById('editUserPass').value = '';
            document.getElementById('userModal').classList.add('show');
        }

        function closeUserModal() {
            document.getElementById('userModal').classList.remove('show');
        }

        document.getElementById('userModal').addEventListener('click', function(e) {
            if (e.target === this) closeUserModal();
        });
        </script>

        <?php endif; ?>
    </div>

<?php include 'includes/footer.php'; ?>
