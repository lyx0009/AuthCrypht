<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }

        body {
            background: #f4f6fb;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(102,126,234,0.3);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.3px;
        }

        .btn-logout {
            background: rgba(255,255,255,0.2);
            border: 1.5px solid rgba(255,255,255,0.4);
            color: white;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: background 0.2s;
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.35);
            color: white;
        }

        .welcome-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            overflow: hidden;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2.5rem 2rem;
            color: white;
            position: relative;
        }

        .welcome-banner .avatar {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .welcome-banner h2 { font-weight: 700; font-size: 1.6rem; }

        .welcome-body { padding: 2rem; background: white; }

        .info-card {
            border: none;
            border-radius: 14px;
            padding: 1.2rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .info-card .icon-box {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .info-card .label { font-size: 0.75rem; color: #94a3b8; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-card .value { font-size: 0.9rem; font-weight: 600; color: #1e293b; word-break: break-all; }

        .card-user { background: #eff6ff; }
        .card-user .icon-box { background: #dbeafe; color: #3b82f6; }

        .card-session { background: #f0fdf4; }
        .card-session .icon-box { background: #dcfce7; color: #22c55e; }

        .card-status { background: #fdf4ff; }
        .card-status .icon-box { background: #f3e8ff; color: #a855f7; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <span class="navbar-brand">
            <i class="bi bi-shield-lock-fill me-2"></i>IT 309 - BSIT 3B
        </span>
        <a href="logout.php" class="btn btn-logout">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="welcome-card">
                <div class="welcome-banner">
                    <div class="avatar"><i class="bi bi-person-fill"></i></div>
                    <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                    <p class="mb-0 opacity-75">You have successfully accessed the secured area.</p>
                </div>

                <div class="welcome-body">
                    <div class="d-flex flex-column gap-3">
                        <div class="info-card card-user">
                            <div class="icon-box"><i class="bi bi-person-fill"></i></div>
                            <div>
                                <div class="label">Logged in as</div>
                                <div class="value"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                            </div>
                        </div>

                        <div class="info-card card-session">
                            <div class="icon-box"><i class="bi bi-key-fill"></i></div>
                            <div>
                                <div class="label">Session ID</div>
                                <div class="value"><?php echo session_id(); ?></div>
                            </div>
                        </div>

                        <div class="info-card card-status">
                            <div class="icon-box"><i class="bi bi-shield-check"></i></div>
                            <div>
                                <div class="label">Status</div>
                                <div class="value">
                                    <span class="status-badge">
                                        <span class="status-dot"></span> Active Session
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
