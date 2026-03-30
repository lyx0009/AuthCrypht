<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

include 'db.php';

$success = $_SESSION['msg'] ?? '';
$error = '';
unset($_SESSION['msg']);

function log_action($conn, $admin, $action, $target) {
    $stmt = $conn->prepare("INSERT INTO access_logs (admin_username, action, target_username) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $admin, $action, $target);
    $stmt->execute();
}

// Change password
if (isset($_POST['change_password'])) {
    $uid    = (int) $_POST['user_id'];
    $target = $conn->query("SELECT username FROM users WHERE id = $uid")->fetch_assoc()['username'] ?? '';
    $new_pass = password_hash(trim($_POST['new_password']), PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ? AND role != 'admin'");
    $stmt->bind_param("si", $new_pass, $uid);
    if ($stmt->execute()) {
        log_action($conn, $_SESSION['username'], 'Changed Password', $target);
        $_SESSION['msg'] = "Password updated successfully.";
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Failed to update password.";
    }
}

// Delete user
if (isset($_POST['delete_user'])) {
    $uid    = (int) $_POST['user_id'];
    $target = $conn->query("SELECT username FROM users WHERE id = $uid AND role != 'admin'")->fetch_assoc()['username'] ?? '';
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        log_action($conn, $_SESSION['username'], 'Deleted User', $target);
        $_SESSION['msg'] = "User deleted successfully.";
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Failed to delete user.";
    }
}

// Fetch users and logs
$users = $conn->query("SELECT id, username, last_login, is_active FROM users WHERE role = 'user' ORDER BY id DESC");
$logs  = $conn->query("SELECT * FROM access_logs ORDER BY created_at DESC LIMIT 50");
$total  = $conn->query("SELECT COUNT(*) as c FROM users WHERE role = 'user'")->fetch_assoc()['c'];
$active = $conn->query("SELECT COUNT(*) as c FROM users WHERE role = 'user' AND is_active = 1")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f4f6fb; min-height: 100vh; }

        .navbar {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        .navbar-brand { font-weight: 700; font-size: 1.1rem; }
        .btn-logout {
            background: rgba(255,255,255,0.15);
            border: 1.5px solid rgba(255,255,255,0.3);
            color: white; border-radius: 8px;
            font-size: 0.85rem; font-weight: 500;
            transition: background 0.2s;
        }
        .btn-logout:hover { background: rgba(255,255,255,0.25); color: white; }

        .stat-card {
            border: none; border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            display: flex; align-items: center; gap: 1rem;
        }
        .stat-icon {
            width: 52px; height: 52px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; flex-shrink: 0;
        }
        .stat-label { font-size: 0.78rem; color: #94a3b8; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 1.6rem; font-weight: 700; color: #1e293b; line-height: 1; }

        .main-card {
            border: none; border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .main-card .card-header {
            background: white;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.2rem 1.5rem;
            font-weight: 600; font-size: 0.95rem; color: #1e293b;
        }

        table thead th {
            background: #f8fafc; color: #64748b;
            font-size: 0.75rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.5px;
            border: none; padding: 0.9rem 1rem;
        }
        table tbody td {
            padding: 1rem; vertical-align: middle;
            border-color: #f1f5f9; font-size: 0.88rem; color: #334155;
        }

        .user-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            color: white; font-size: 0.85rem; font-weight: 600; margin-right: 8px;
        }

        .badge-active {
            background: #dcfce7; color: #16a34a;
            border-radius: 20px; padding: 4px 10px;
            font-size: 0.75rem; font-weight: 600;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .badge-inactive {
            background: #f1f5f9; color: #94a3b8;
            border-radius: 20px; padding: 4px 10px;
            font-size: 0.75rem; font-weight: 600;
        }

        .pulse {
            width: 7px; height: 7px; background: #22c55e;
            border-radius: 50%; display: inline-block;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

        .btn-action {
            border-radius: 8px; font-size: 0.8rem;
            font-weight: 500; padding: 0.35rem 0.75rem; border: none;
        }
        .btn-change { background: #eff6ff; color: #3b82f6; }
        .btn-change:hover { background: #dbeafe; color: #2563eb; }
        .btn-delete { background: #fef2f2; color: #ef4444; }
        .btn-delete:hover { background: #fee2e2; color: #dc2626; }

        .log-badge-password { background: #eff6ff; color: #3b82f6; border-radius: 20px; padding: 3px 10px; font-size: 0.75rem; font-weight: 600; }
        .log-badge-delete { background: #fef2f2; color: #ef4444; border-radius: 20px; padding: 3px 10px; font-size: 0.75rem; font-weight: 600; }

        .modal-content { border: none; border-radius: 16px; }
        .modal-header { border-bottom: 1px solid #f1f5f9; padding: 1.2rem 1.5rem; }
        .modal-footer { border-top: 1px solid #f1f5f9; }

        .form-control {
            border-radius: 10px; border: 1.5px solid #e2e8f0;
            font-size: 0.9rem; padding: 0.6rem 1rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.15);
        }
        .modal .input-group .form-control { border-right: none; }
        .modal .input-group .form-control::-ms-reveal,
        .modal .input-group .form-control::-webkit-credentials-auto-fill-button { display: none; }
        .modal .input-group-text {
            border-radius: 0 10px 10px 0;
            border: 1.5px solid #e2e8f0;
            border-left: none;
            background: white;
            color: #94a3b8;
            cursor: pointer;
            transition: color 0.2s;
        }
        .modal .input-group-text:hover { color: #667eea; }
        .modal .input-group:focus-within .input-group-text { border-color: #667eea; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <span class="navbar-brand">
            <i class="bi bi-shield-fill-check me-2 text-warning"></i>Admin Panel
        </span>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white-50 small">Logged in as <strong class="text-white"><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
            <a href="logout.php" class="btn btn-logout"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">

    <?php if ($success): ?>
        <div class="alert alert-success py-2 mb-3"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger py-2 mb-3"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card bg-white">
                <div class="stat-icon" style="background:#eff6ff; color:#3b82f6;"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value"><?php echo $total; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-white">
                <div class="stat-icon" style="background:#f0fdf4; color:#22c55e;"><i class="bi bi-person-check-fill"></i></div>
                <div>
                    <div class="stat-label">Active</div>
                    <div class="stat-value"><?php echo $active; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-white">
                <div class="stat-icon" style="background:#fdf4ff; color:#a855f7;"><i class="bi bi-person-x-fill"></i></div>
                <div>
                    <div class="stat-label">Inactive</div>
                    <div class="stat-value"><?php echo $total - $active; ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="main-card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span><i class="bi bi-people me-2 text-primary"></i>Registered Users</span>
            <span class="text-muted small"><?php echo $total; ?> total</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Last Login</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                while ($row = $users->fetch_assoc()):
                    $is_active = $row['is_active'];
                ?>
                <tr>
                    <td class="text-muted"><?php echo $i++; ?></td>
                    <td>
                        <span class="user-avatar"><?php echo strtoupper($row['username'][0]); ?></span>
                        <?php echo htmlspecialchars($row['username']); ?>
                    </td>
                    <td class="text-muted"><?php echo $row['last_login'] ? date('M d, Y h:i A', strtotime($row['last_login'])) : 'Never'; ?></td>
                    <td>
                        <?php if ($is_active): ?>
                            <span class="badge-active"><span class="pulse"></span> Active</span>
                        <?php else: ?>
                            <span class="badge-inactive"><i class="bi bi-circle me-1"></i>Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-action btn-change me-1"
                            data-bs-toggle="modal" data-bs-target="#changePassModal"
                            data-id="<?php echo $row['id']; ?>"
                            data-username="<?php echo htmlspecialchars($row['username']); ?>">
                            <i class="bi bi-key me-1"></i>Change Password
                        </button>
                        <button class="btn btn-action btn-delete"
                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                            data-id="<?php echo $row['id']; ?>"
                            data-username="<?php echo htmlspecialchars($row['username']); ?>">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($i === 1): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No users registered yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Access Logs -->
    <div class="main-card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span><i class="bi bi-clock-history me-2 text-warning"></i>Access Logs</span>
            <span class="text-muted small">Last 50 actions</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Target User</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $j = 1;
                while ($log = $logs->fetch_assoc()):
                ?>
                <tr>
                    <td class="text-muted"><?php echo $j++; ?></td>
                    <td>
                        <span class="user-avatar" style="background: linear-gradient(135deg, #f59e0b, #ef4444);"><?php echo strtoupper($log['admin_username'][0]); ?></span>
                        <?php echo htmlspecialchars($log['admin_username']); ?>
                    </td>
                    <td>
                        <?php if ($log['action'] === 'Changed Password'): ?>
                            <span class="log-badge-password"><i class="bi bi-key me-1"></i><?php echo $log['action']; ?></span>
                        <?php else: ?>
                            <span class="log-badge-delete"><i class="bi bi-trash me-1"></i><?php echo $log['action']; ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($log['target_username']); ?></td>
                    <td class="text-muted"><?php echo date('M d, Y h:i A', strtotime($log['created_at'])); ?></td>
                </tr>
                <?php endwhile; ?>
                <?php if ($j === 1): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No actions logged yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePassModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bi bi-key me-2 text-primary"></i>Change Password</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="changePassForm">
                <div class="modal-body">
                    <p class="text-muted small mb-3">Changing password for <strong id="modal_username"></strong></p>
                    <input type="hidden" name="user_id" id="modal_user_id">
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Enter new password" required>
                            <span class="input-group-text toggle-pass" data-target="new_password"><i class="bi bi-eye-slash"></i></span>
                        </div>
                    </div>
                    <div>
                        <div class="input-group">
                            <input type="password" id="confirm_new_password" class="form-control" placeholder="Confirm new password" required>
                            <span class="input-group-text toggle-pass" data-target="confirm_new_password"><i class="bi bi-eye-slash"></i></span>
                        </div>
                        <div id="pass_match_error" class="text-danger small mt-1" style="display:none;">Passwords do not match.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="change_password" class="btn btn-sm btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bi bi-trash me-2 text-danger"></i>Delete User</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <p class="text-muted small">Are you sure you want to delete <strong id="delete_username"></strong>? This cannot be undone.</p>
                    <input type="hidden" name="user_id" id="delete_user_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_user" class="btn btn-sm btn-danger">Delete User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.toggle-pass').forEach(function(el) {
        el.addEventListener('click', function() {
            const input = document.getElementById(this.dataset.target);
            const icon  = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        });
    });

    document.getElementById('changePassModal').addEventListener('show.bs.modal', function(e) {
        document.getElementById('modal_user_id').value        = e.relatedTarget.dataset.id;
        document.getElementById('modal_username').textContent = e.relatedTarget.dataset.username;
        document.getElementById('new_password').value         = '';
        document.getElementById('confirm_new_password').value = '';
        document.getElementById('pass_match_error').style.display = 'none';
        document.querySelectorAll('.toggle-pass i').forEach(i => {
            i.classList.remove('bi-eye');
            i.classList.add('bi-eye-slash');
        });
        document.getElementById('new_password').type         = 'password';
        document.getElementById('confirm_new_password').type = 'password';
    });

    document.getElementById('changePassForm').addEventListener('submit', function(e) {
        const pass    = document.getElementById('new_password').value;
        const confirm = document.getElementById('confirm_new_password').value;
        const error   = document.getElementById('pass_match_error');
        if (pass !== confirm) {
            e.preventDefault();
            error.style.display = 'block';
        }
    });
    document.getElementById('deleteModal').addEventListener('show.bs.modal', function(e) {
        document.getElementById('delete_user_id').value        = e.relatedTarget.dataset.id;
        document.getElementById('delete_username').textContent = e.relatedTarget.dataset.username;
    });
</script>
</body>
</html>
