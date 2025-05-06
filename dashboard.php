<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include "connect.php";

$user_count = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
            --success-color: #10b981;
            --info-color: #3b82f6;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }
        
        body {
            background-color: #f8fafc;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            overflow-x: hidden;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 1.5rem 1rem;
            box-shadow: 4px 0 20px rgba(0,0,0,0.05);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
            padding: 0 0.5rem;
        }
        
        .sidebar-brand-icon {
            background: var(--primary-color);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-brand-text {
            font-weight: 600;
            font-size: 1.25rem;
        }
        
        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .sidebar-menu-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .sidebar-menu-item:hover, .sidebar-menu-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar-menu-item i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: all 0.3s ease;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .header-title h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: #1e293b;
        }
        
        .header-title p {
            color: #64748b;
            margin-bottom: 0;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: white;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem;
        }
        
        .card-title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .stats-card {
            position: relative;
            overflow: hidden;
            color: white;
            border: none;
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }
        
        .stats-card-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        .stats-card-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #34d399 100%);
        }
        
        .stats-card-info {
            background: linear-gradient(135deg, var(--info-color) 0%, #60a5fa 100%);
        }
        
        .stats-card-warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #fbbf24 100%);
        }
        
        .stats-card-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #f87171 100%);
        }
        
        .stats-card .card-body {
            position: relative;
            z-index: 1;
        }
        
        .stats-card h5 {
            font-size: 0.875rem;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }
        
        .stats-card .stats-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stats-card .stats-change {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            opacity: 0.9;
        }
        
        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
        }
        
        [data-bs-theme="dark"] body {
            background-color: #0f172a;
        }
        
        [data-bs-theme="dark"] .card {
            background: #1e293b;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }
        
        [data-bs-theme="dark"] .card-header {
            border-bottom-color: #334155;
        }
        
        [data-bs-theme="dark"] .card-title {
            color: #f8fafc;
        }
        
        [data-bs-theme="dark"] .header-title h1 {
            color: #f8fafc;
        }
        
        [data-bs-theme="dark"] .header-title p {
            color: #94a3b8;
        }
        
        [data-bs-theme="dark"] .header {
            border-bottom-color: #334155;
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
            <div class="sidebar-brand-text">AdminPanel</div>
        </div>
        
        <div class="sidebar-menu">
            <a href="dashboard.php" class="sidebar-menu-item active">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="users.php" class="sidebar-menu-item">
                <i class="bi bi-people"></i>
                <span>Manage Users</span>
            </a>
            <a href="settings.php" class="sidebar-menu-item">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
            <a href="logout.php" class="sidebar-menu-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="header-title">
                <h1>Welcome back, <?= $_SESSION['admin'] ?></h1>
                <p>Here's what's happening with your platform today</p>
            </div>
            
            <div class="header-actions">
                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" onclick="toggleTheme()">
                    <i class="bi bi-moon-stars"></i>
                    <span>Toggle Theme</span>
                </button>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none" data-bs-toggle="dropdown">
                        <img src="https://i.pravatar.cc/150?img=12" alt="Profile" class="profile-img">
                        <span class="d-none d-md-inline">Admin</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card stats-card stats-card-primary">
                    <div class="card-body">
                        <h5>Total Users</h5>
                        <div class="stats-value"><?= $user_count ?></div>
                        <div class="stats-change">
                            <i class="bi bi-arrow-up-circle me-1"></i>
                            <span>12% from last month</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card stats-card stats-card-success">
                    <div class="card-body">
                        <h5>Active Sessions</h5>
                        <div class="stats-value">1 (You)</div>
                        <div class="stats-change">
                            <i class="bi bi-arrow-down-circle me-1"></i>
                            <span>5% from yesterday</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card stats-card stats-card-info">
                    <div class="card-body">
                        <h5>New Signups</h5>
                        <div class="stats-value"><?= rand(1, 10) ?></div>
                        <div class="stats-change">
                            <i class="bi bi-arrow-up-circle me-1"></i>
                            <span>24% from yesterday</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card stats-card stats-card-warning">
                    <div class="card-body">
                        <h5>Pending Actions</h5>
                        <div class="stats-value">3</div>
                        <div class="stats-change">
                            <i class="bi bi-arrow-down-circle me-1"></i>
                            <span>2 resolved today</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">User Activity</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="userChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">User Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="userDistributionChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Activity</h5>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>User</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>New user registration</td>
                                <td>John Doe</td>
                                <td>2 mins ago</td>
                                <td><span class="badge bg-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td>Profile update</td>
                                <td>Jane Smith</td>
                                <td>15 mins ago</td>
                                <td><span class="badge bg-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td>Password reset</td>
                                <td>Robert Johnson</td>
                                <td>1 hour ago</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                            </tr>
                            <tr>
                                <td>Account deletion</td>
                                <td>Sarah Williams</td>
                                <td>3 hours ago</td>
                                <td><span class="badge bg-danger">Failed</span></td>
                            </tr>
                            <tr>
                                <td>New login</td>
                                <td>Michael Brown</td>
                                <td>5 hours ago</td>
                                <td><span class="badge bg-success">Completed</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const userChartCtx = document.getElementById('userChart').getContext('2d');
        const userChart = new Chart(userChartCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'New Users',
                    data: [65, 59, 80, 81, 56, 55, 40],
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderColor: '#6366f1',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }, {
                    label: 'Active Users',
                    data: [28, 48, 40, 19, 86, 27, 90],
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderColor: '#10b981',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        const userDistributionCtx = document.getElementById('userDistributionChart').getContext('2d');
        const userDistributionChart = new Chart(userDistributionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive', 'New', 'Suspended'],
                datasets: [{
                    data: [300, 50, 100, 20],
                    backgroundColor: [
                        '#6366f1',
                        '#8b5cf6',
                        '#10b981',
                        '#ef4444'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                cutout: '70%'
            }
        });

        function toggleTheme() {
            const htmlTag = document.documentElement;
            const current = htmlTag.getAttribute("data-bs-theme");
            const newTheme = current === "light" ? "dark" : "light";
            htmlTag.setAttribute("data-bs-theme", newTheme);
            
            localStorage.setItem('theme', newTheme);
        }

        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        }
    </script>
</body>
</html>