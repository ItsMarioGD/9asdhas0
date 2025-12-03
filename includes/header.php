<?php
require_once __DIR__ . '/auth.php';
requireLogin();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medi-Agenda AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="static/css/style.css" rel="stylesheet">
    <style>
        .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 1rem;
            font-weight: 600;
            font-size: 0.875rem;
            color: #64748b;
            transition: all 0.3s;
            position: relative;
        }
        .nav-link:hover {
            color: #2563eb;
            background: rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
        }
        .nav-link.active {
            color: #2563eb;
            background: rgba(37, 99, 235, 0.15);
        }
        .action-btn {
            padding: 0.75rem;
            border-radius: 1rem;
            color: #64748b;
            transition: all 0.3s;
        }
        .action-btn:hover {
            background: rgba(37, 99, 235, 0.1);
            color: #2563eb;
        }
    </style>
</head>
<body class="flex flex-col">
    
    <!-- Aurora Background -->
    <div class="aurora-bg">
        <div class="aurora-blob blob-1"></div>
        <div class="aurora-blob blob-2"></div>
        <div class="aurora-blob blob-3"></div>
    </div>

    <!-- Floating Particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <nav class="glass-panel fixed w-full z-50 transition-all duration-300 border-b-0 rounded-none shadow-sm">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between items-center" style="height: 5rem;">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fa-solid fa-heartbeat text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Medi<span class="text-blue-600">Agenda</span></h1>
                        <p class="text-xs text-gray-500 font-semibold">AI Powered</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="flex items-center gap-1">
                    <a href="index.php" class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-home"></i>
                        <span>Inicio</span>
                    </a>
                    
                    <a href="reception.php" class="nav-link <?php echo $currentPage == 'reception.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Recepción</span>
                    </a>
                    
                    <a href="patients.php" class="nav-link <?php echo $currentPage == 'patients.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-users"></i>
                        <span>Pacientes</span>
                    </a>
                    
                    <a href="calendar.php" class="nav-link <?php echo $currentPage == 'calendar.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>Calendario</span>
                    </a>
                    
                    <a href="consultation.php" class="nav-link <?php echo $currentPage == 'consultation.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-user-doctor"></i>
                        <span>Consulta</span>
                    </a>
                    
                    <a href="dashboard.php" class="nav-link <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="medication_stats.php" class="nav-link <?php echo $currentPage == 'medication_stats.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-pills"></i>
                        <span>Medicamentos</span>
                    </a>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-2">
                    <!-- Dark Mode Toggle -->
                    <button onclick="toggleDarkMode()" class="action-btn" title="Cambiar Tema">
                        <i id="theme-icon" class="fa-solid fa-moon text-xl"></i>
                    </button>
                    
                    <!-- Notifications Bell -->
                    <div class="relative">
                        <button onclick="toggleNotifications()" class="action-btn relative" title="Notificaciones">
                            <i class="fa-solid fa-bell text-xl"></i>
                            <span id="notif-badge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold animate-pulse">0</span>
                        </button>
                        
                        <!-- Notification Dropdown -->
                        <div id="notif-dropdown" class="hidden absolute right-0 mt-4 w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 z-50 animate-fade-in">
                            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="font-bold text-gray-900 text-lg">Notificaciones</h3>
                                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full font-semibold">Últimas</span>
                            </div>
                            <div id="notif-list" class="max-h-96 overflow-y-auto">
                                <!-- Populated by JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-28 pb-12 px-4 sm:px-6 lg:px-8 min-h-screen relative z-10">
