<?php
// Include authentication check
require_once __DIR__ . '/auth_check.php';

// Include database connection
require_once __DIR__ . '/../../includes/Database.php';

$db = Database::getInstance();

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $name = trim($_POST['name']);
                $email = trim($_POST['email']);
                $password = $_POST['password'];
                $usertype = $_POST['usertype'];
                
                if (!empty($name) && !empty($email) && !empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $user_data = [
                        'name' => $name,
                        'email' => $email,
                        'password' => $hashed_password,
                        'usertype' => $usertype,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    try {
                        $user_id = $db->insert('users', $user_data);
                        if ($user_id) {
                            $_SESSION['success'] = 'User created successfully!';
                        } else {
                            $_SESSION['error'] = 'Failed to create user.';
                        }
                    } catch (Exception $e) {
                        $_SESSION['error'] = 'Error: ' . $e->getMessage();
                    }
                } else {
                    $_SESSION['error'] = 'Please fill in all required fields.';
                }
                break;
                
            case 'update':
                $user_id = $_POST['user_id'];
                $name = trim($_POST['name']);
                $email = trim($_POST['email']);
                $usertype = $_POST['usertype'];
                
                if (!empty($name) && !empty($email)) {
                    $update_data = [
                        'name' => $name,
                        'email' => $email,
                        'usertype' => $usertype,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    // Update password if provided
                    if (!empty($_POST['password'])) {
                        $update_data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    }
                    
                    try {
                        $result = $db->update('users', $update_data, 'id = ?', [$user_id]);
                        if ($result > 0) {
                            $_SESSION['success'] = 'User updated successfully!';
                        } else {
                            $_SESSION['error'] = 'Failed to update user.';
                        }
                    } catch (Exception $e) {
                        $_SESSION['error'] = 'Error: ' . $e->getMessage();
                    }
                } else {
                    $_SESSION['error'] = 'Please fill in all required fields.';
                }
                break;
                
            case 'delete':
                $user_id = $_POST['user_id'];
                
                try {
                    $result = $db->delete('users', 'id = ?', [$user_id]);
                    if ($result > 0) {
                        $_SESSION['success'] = 'User deleted successfully!';
                    } else {
                        $_SESSION['error'] = 'Failed to delete user.';
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Error: ' . $e->getMessage();
                }
                break;
        }
        
        header('Location: user_management');
        exit();
    }
}

// Get all users
$users = $db->fetchAll("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Super Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
        }
        
        .header {
            background-image: url('../../images/baramgay.jpg');
            background-size: cover;
            background-position: center;
            background-color: #1f2937;
            color: white;
            padding: 20px;
            text-align: center;
            height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-right: 10px;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background-color: #2563eb;
            color: white;
        }
        
        .btn-success {
            background-color: #059669;
            color: white;
        }
        
        .btn-warning {
            background-color: #d97706;
            color: white;
        }
        
        .btn-danger {
            background-color: #dc2626;
            color: white;
        }
        
        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .table th {
            background-color: #f9fafb;
            font-weight: bold;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>User Management</h1>
        <p>Manage system users and permissions</p>
    </div>
    
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>System Users</h2>
            <button class="btn btn-success" onclick="openCreateModal()">
                <i class="fas fa-plus"></i> Add New User
            </button>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span style="
                                padding: 4px 8px;
                                border-radius: 4px;
                                font-size: 12px;
                                font-weight: bold;
                                <?php
                                switch($user['usertype']) {
                                    case 'super_admin':
                                        echo 'background-color: #dc2626; color: white;';
                                        break;
                                    case 'sec_admin':
                                        echo 'background-color: #d97706; color: white;';
                                        break;
                                    case 'admin':
                                        echo 'background-color: #2563eb; color: white;';
                                        break;
                                    default:
                                        echo 'background-color: #6b7280; color: white;';
                                }
                                ?>
                            ">
                                <?php echo ucfirst(str_replace('_', ' ', $user['usertype'])); ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <button class="btn btn-warning" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <button class="btn btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['name']); ?>')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Create User Modal -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCreateModal()">&times;</span>
            <h3>Create New User</h3>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="usertype">User Type</label>
                    <select id="usertype" name="usertype" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                        <option value="sec_admin">Secretary Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success">Create User</button>
                <button type="button" class="btn btn-danger" onclick="closeCreateModal()">Cancel</button>
            </form>
        </div>
    </div>
    
    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h3>Edit User</h3>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" id="edit_user_id" name="user_id">
                
                <div class="form-group">
                    <label for="edit_name">Name</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_password">Password (leave blank to keep current)</label>
                    <input type="password" id="edit_password" name="password">
                </div>
                
                <div class="form-group">
                    <label for="edit_usertype">User Type</label>
                    <select id="edit_usertype" name="usertype" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                        <option value="sec_admin">Secretary Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success">Update User</button>
                <button type="button" class="btn btn-danger" onclick="closeEditModal()">Cancel</button>
            </form>
        </div>
    </div>
    
    <script>
        function openCreateModal() {
            document.getElementById('createModal').style.display = 'block';
        }
        
        function closeCreateModal() {
            document.getElementById('createModal').style.display = 'none';
        }
        
        function openEditModal(user) {
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_usertype').value = user.usertype;
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        function deleteUser(userId, userName) {
            if (confirm('Are you sure you want to delete user "' + userName + '"?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
