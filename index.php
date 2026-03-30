<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Auth System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .auth-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .card-header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }

        .card-header-section .logo-icon {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }

        .card-body-section { padding: 2rem; }

        .nav-pills {
            background: #f1f3f9;
            border-radius: 10px;
            padding: 4px;
        }

        .nav-pills .nav-link {
            border-radius: 8px;
            color: #6c757d;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 1.5rem;
            transition: all 0.2s;
        }

        .nav-pills .nav-link.active {
            background: white;
            color: #667eea;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .form-control {
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            padding: 0.65rem 1rem;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.15);
        }

        .input-group .form-control { border-right: none; }

        .input-group-text {
            border-radius: 0 10px 10px 0;
            border: 1.5px solid #e2e8f0;
            border-left: none;
            background: white;
            color: #94a3b8;
            cursor: pointer;
            transition: color 0.2s;
        }

        .input-group-text:hover { color: #667eea; }

        .input-group:focus-within .input-group-text {
            border-color: #667eea;
        }

        .btn-signin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.7rem;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            transition: opacity 0.2s, transform 0.1s;
        }

        .btn-signin:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-signin:active { transform: translateY(0); }

        .btn-register {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            border-radius: 10px;
            padding: 0.7rem;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            transition: opacity 0.2s, transform 0.1s;
        }

        .btn-register:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-register:active { transform: translateY(0); }

        .form-label-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.95rem;
            pointer-events: none;
        }

        .input-with-icon { padding-left: 2.2rem !important; }

        .alert {
            border-radius: 10px;
            font-size: 0.85rem;
            border: none;
        }

        .alert-info { background: #eff6ff; color: #3b82f6; }
        .alert-danger { background: #fef2f2; color: #ef4444; }
        .alert-success { background: #f0fdf4; color: #22c55e; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card auth-card">
                <div class="card-header-section">
                    <div class="logo-icon"><i class="bi bi-shield-lock-fill"></i></div>
                    <h5 class="fw-700 mb-0">User Portal</h5>
                    <small class="opacity-75">Secure Authentication System</small>
                </div>

                <div class="card-body-section">
                    <?php if(isset($_SESSION['msg'])): ?>
                        <div class="alert py-2 text-center mb-3
                            <?php
                                $msg = $_SESSION['msg'];
                                echo (str_contains($msg, 'Error') || str_contains($msg, 'Invalid')) ? 'alert-danger' : (str_contains($msg, 'Success') ? 'alert-success' : 'alert-info');
                            ?>">
                            <?php echo $msg; unset($_SESSION['msg']); ?>
                        </div>
                    <?php endif; ?>

                    <ul class="nav nav-pills mb-4 justify-content-center" id="pills-tab" role="tablist">
                        <li class="nav-item flex-fill text-center">
                            <button class="nav-link active w-100" data-bs-toggle="pill" data-bs-target="#login">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </button>
                        </li>
                        <li class="nav-item flex-fill text-center">
                            <button class="nav-link w-100" data-bs-toggle="pill" data-bs-target="#register">
                                <i class="bi bi-person-plus me-1"></i> Register
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="login">
                            <form action="login_action.php" method="POST">
                                <div class="mb-3 form-label-icon">
                                    <i class="bi bi-person input-icon"></i>
                                    <input type="text" name="username" class="form-control input-with-icon" placeholder="Username" required>
                                </div>
                                <div class="mb-4">
                                    <div class="input-group form-label-icon">
                                        <i class="bi bi-lock input-icon" style="z-index:5;"></i>
                                        <input type="password" name="password" id="login_password" class="form-control input-with-icon" placeholder="Password" required>
                                        <span class="input-group-text toggle-password" data-target="login_password"><i class="bi bi-eye-slash"></i></span>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-signin text-white w-100">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                                </button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="register">
                            <form action="register_action.php" method="POST">
                                <div class="mb-3 form-label-icon">
                                    <i class="bi bi-person input-icon"></i>
                                    <input type="text" name="username" class="form-control input-with-icon" placeholder="Choose a Username" required>
                                </div>
                                <div class="mb-3">
                                    <div class="input-group form-label-icon">
                                        <i class="bi bi-lock input-icon" style="z-index:5;"></i>
                                        <input type="password" name="password" id="reg_password" class="form-control input-with-icon" placeholder="Password" required>
                                        <span class="input-group-text toggle-password" data-target="reg_password"><i class="bi bi-eye-slash"></i></span>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="input-group form-label-icon">
                                        <i class="bi bi-lock-fill input-icon" style="z-index:5;"></i>
                                        <input type="password" name="confirm_password" id="reg_confirm" class="form-control input-with-icon" placeholder="Confirm Password" required>
                                        <span class="input-group-text toggle-password" data-target="reg_confirm"><i class="bi bi-eye-slash"></i></span>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-register text-white w-100">
                                    <i class="bi bi-person-check me-1"></i> Create Account
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.toggle-password').forEach(function(el) {
        el.addEventListener('click', function() {
            const input = document.getElementById(this.dataset.target);
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        });
    });
</script>
</body>
</html>
