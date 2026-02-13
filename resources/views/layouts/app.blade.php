<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Okaro') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>

    <style>
        /* Mobile Background Image */
        @media (max-width: 768px) {
            body {
                background-image: linear-gradient(rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.8)), url('{{ asset("assets/images/hero-bg.jpg") }}') !important;
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
            }
        }

        /* Chatbot Widget Styles */
        #chatbot-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        #chatbot-toggle {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: all 0.3s ease;
        }
        #chatbot-toggle:hover {
            transform: scale(1.1);
            background-color: var(--primary-hover);
        }
        #chatbot-window {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 350px;
            height: 500px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 1000;
            border: 1px solid rgba(124, 58, 237, 0.2);
        }
        #chatbot-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #chatbot-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background-color: #f9f9f9;
        }
        .chat-message {
            margin-bottom: 15px;
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 15px;
            font-size: 0.9rem;
        }
        .user-message {
            background-color: var(--primary-color);
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 5px;
        }
        .bot-message {
            background-color: #e9ecef;
            color: #333;
            margin-right: auto;
            border-bottom-left-radius: 5px;
        }
        #chatbot-input-area {
            padding: 15px;
            border-top: 1px solid #eee;
            background-color: white;
            display: flex;
            gap: 10px;
        }
        #chatbot-input {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 8px 15px;
            outline: none;
        }
        #chatbot-input:focus {
            border-color: var(--primary-color);
        }
        #chatbot-send {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        :root {
            --sidebar-width: 260px;
            --header-height: 60px;
            --primary-color: #7c3aed;
            --primary-hover: #6d28d9;
            --bg-light: #fdfbf7;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--bg-light);
            overflow-x: hidden;
        }

        /* Top Navbar */
        .navbar-top {
            height: var(--header-height);
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            padding: 0 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #212529; /* Darker clean background */
            color: #fff;
            z-index: 1040;
            transition: transform 0.3s ease-in-out;
            padding-top: var(--header-height);
            overflow-y: auto;
            border-right: 1px solid rgba(255,255,255,0.05);
            /* Hide Scrollbar but keep functionality */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none;  /* IE and Edge */
        }
        .sidebar::-webkit-scrollbar {
            display: none; /* Chrome, Safari and Opera */
        }

        /* Sidebar Header (Logo area inside sidebar) */
        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            position: absolute;
            top: 0;
            width: 100%;
            background: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        /* Sidebar Links */
        .sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 0.8rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.05);
            border-left-color: var(--primary-color);
        }
        .sidebar .nav-item-header {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(255,255,255,0.4);
            padding: 1.5rem 1.5rem 0.5rem;
            font-weight: 700;
        }

        /* Main Content Wrapper */
        .main-content {
            margin-left: 0;
            padding-top: 1.5rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        /* Overlay for mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.5);
            z-index: 1035;
            display: none;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }
        
        /* Stylish Toggle Button */
        .sidebar-toggle {
            background: transparent;
            border: none;
            color: var(--primary-color);
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.2s;
            padding: 0.25rem 0.5rem;
            border-radius: 0.5rem;
        }
        .sidebar-toggle:hover {
            background-color: rgba(124, 58, 237, 0.1);
            transform: scale(1.05);
        }

        /* Purple Theme Utilities */
        .text-primary {
            color: var(--primary-color) !important;
        }
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        .btn-primary { 
            background-color: var(--primary-color) !important; 
            border-color: var(--primary-color) !important; 
        }
        .btn-primary:hover { 
            background-color: var(--primary-hover) !important; 
            border-color: var(--primary-hover) !important; 
        }
        .bg-purple-light {
            background-color: #a78bfa !important;
            color: white !important;
        }
        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #8b5cf6 100%) !important;
            color: white !important;
        }
        .bg-gradient-info {
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%) !important;
            color: white !important;
        }
        .bg-gradient-success {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%) !important;
            color: white !important;
        }
        .bg-gradient-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%) !important;
            color: white !important;
        }
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            border-radius: 0.75rem;
        }

        /* Table Styles */
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(124, 58, 237, 0.05);
        }

        /* Form Styles */
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(124, 58, 237, 0.25);
        }

        /* Responsive Logic */
        .sidebar {
            transform: translateX(-100%);
        }
        .sidebar.show {
            transform: translateX(0);
        }

        /* Clicker Game Styles */
        .card.interactive {
            cursor: pointer;
            transition: transform 0.1s, box-shadow 0.2s;
            user-select: none;
            position: relative;
            overflow: hidden;
        }
        .card.interactive:active {
            transform: scale(0.98);
        }
        .ripple {
            position: absolute;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        .floating-text {
            position: absolute;
            color: #ffd700;
            font-weight: bold;
            font-size: 1.2rem;
            pointer-events: none;
            animation: float-up 1s ease-out forwards;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            z-index: 1000;
        }
        @keyframes float-up {
            0% {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
            100% {
                transform: translateY(-50px) scale(1.5);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    
    <div class="container-fluid p-0">
        @auth
        <div class="mobile-header d-lg-none">
            <button class="sidebar-toggle" id="sidebarToggleBtn" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <div class="ms-3 fw-bold text-primary fs-5">
                <a href="{{ url('/') }}" class="text-decoration-none text-primary">
                    <i class="bi bi-building"></i> Okaro & Associates
                </a>
            </div>
        </div>
        @endauth

        <div class="row g-0">
            <!-- Sidebar -->
            @auth
            <div class="sidebar" id="sidebarMenu">
                <div class="nav-header">
                    <span><i class="bi bi-building"></i> Okaro & Associates</span>
                    <button class="btn btn-sm text-white" onclick="toggleSidebar()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">
                                <i class="bi bi-house me-2"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        
                        @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                        <li class="nav-item mt-2">
                            <span class="px-3 text-uppercase small text-muted" style="font-size: 0.75rem;">Properties</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('buildings.*') ? 'active' : '' }}" href="{{ route('buildings.index') }}">
                                <i class="bi bi-houses me-2"></i> Buildings
                            </a>
                        </li>

                        <li class="nav-item mt-2">
                            <span class="px-3 text-uppercase small text-muted" style="font-size: 0.75rem;">Tenancy</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tenants.*') ? 'active' : '' }}" href="{{ route('tenants.index') }}">
                                <i class="bi bi-people me-2"></i> Tenants
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('rents.*') ? 'active' : '' }}" href="{{ route('rents.index') }}">
                                <i class="bi bi-file-earmark-text me-2"></i> Rentals
                            </a>
                        </li>

                        <li class="nav-item mt-2">
                            <span class="px-3 text-uppercase small text-muted" style="font-size: 0.75rem;">Operations</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}">
                                <i class="bi bi-cash-stack me-2"></i> Payments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}" href="{{ route('maintenance.index') }}">
                                <i class="bi bi-tools me-2"></i> Maintenance
                            </a>
                        </li>
                        
                        @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                        <li class="nav-item mt-2">
                            <span class="px-3 text-uppercase small text-muted" style="font-size: 0.75rem;">Administration</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="bi bi-person-gear me-2"></i> Users
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                                <i class="bi bi-shield-lock me-2"></i> Roles
                            </a>
                        </li>
                        @endif
                        @endif

                        @if(Auth::user()->isTenant())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}" href="{{ route('maintenance.index') }}">
                                <i class="bi bi-tools me-2"></i> Request Maintenance
                            </a>
                        </li>
                        @endif
                    </ul>

                    <hr class="text-white">
                    <div class="px-3 pb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <i class="bi bi-person-circle fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-2 overflow-hidden">
                                <div class="small fw-bold text-truncate">{{ Auth::user()->name }}</div>
                                <div class="small text-muted text-truncate">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm w-100">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endauth

            <!-- Main Content -->
            <main class="@auth main-content @else col-12 @endauth">
                @auth
                <!-- Desktop Collapsed Header -->
                <div id="desktopHeader" class="d-none d-lg-flex align-items-center mb-0 pb-2 border-bottom">
                    <button class="sidebar-toggle me-3" onclick="toggleSidebar()">
                        <i class="bi bi-list"></i>
                    </button>
                    <span class="h4 mb-0 text-primary fw-bold">Okaro & Associates</span>
                    
                    <!-- Coin Wallet Display -->
                    <div class="ms-auto d-flex align-items-center bg-white border rounded-pill px-3 py-1 shadow-sm">
                        <i class="bi bi-coin text-warning fs-5 me-2"></i>
                        <span id="coin-wallet-desktop" class="fw-bold text-dark">0.00</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom d-md-none">
                    <h1 class="h2 text-primary fw-bold">{{ Auth::user()->isAdmin() ? 'Admin' : (Auth::user()->isManager() ? 'Manager' : 'Tenant') }}</h1>
                    
                    <!-- Mobile Coin Wallet Display -->
                    <div class="d-flex align-items-center bg-white border rounded-pill px-3 py-1 shadow-sm ms-auto">
                        <i class="bi bi-coin text-warning fs-5 me-2"></i>
                        <span id="coin-wallet-mobile" class="fw-bold text-dark">0.00</span>
                    </div>
                </div>
                @endauth

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @auth
                    @if(!request()->routeIs('dashboard'))
                        <div class="mb-2">
                            <a href="javascript:history.back()" class="text-decoration-none text-secondary">
                                <i class="bi bi-arrow-left fs-5"></i>
                            </a>
                        </div>
                    @endif
                @endauth

                @yield('content')
            </main>
        </div>
    </div>

    <footer class="text-center py-3 bg-white border-top mt-auto" style="position: relative; z-index: 10;">
        <div class="container">
            <p class="mb-0 text-muted small">
                produced by Noyb Fundamentals 2026 &copy;
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>
    <script>
        // Clicker Game Logic
        document.addEventListener('DOMContentLoaded', function() {
            let coins = parseFloat(localStorage.getItem('okaro_coins') || '0');
            const walletDisplayDesktop = document.getElementById('coin-wallet-desktop');
            const walletDisplayMobile = document.getElementById('coin-wallet-mobile');
            
            // Initialize Display
            updateWalletDisplay();

            // Make all cards interactive
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.classList.add('interactive');
                card.addEventListener('click', handleCardClick);
            });

            function handleCardClick(e) {
                // Determine increment value based on thresholds
                let increment = 0.25;
                if (coins >= 1000) {
                    increment = 1.0;
                } else if (coins >= 100) {
                    increment = 0.5;
                }

                // Update State
                coins += increment;
                localStorage.setItem('okaro_coins', coins.toString());
                updateWalletDisplay();

                // Trigger Visuals
                createRipple(e, this);
                createFloatingText(e, increment);
            }

            function updateWalletDisplay() {
                if(walletDisplayDesktop) {
                    walletDisplayDesktop.textContent = coins.toFixed(2);
                }
                if(walletDisplayMobile) {
                    walletDisplayMobile.textContent = coins.toFixed(2);
                }
            }

            function createRipple(event, element) {
                const circle = document.createElement('span');
                const diameter = Math.max(element.clientWidth, element.clientHeight);
                const radius = diameter / 2;

                const rect = element.getBoundingClientRect();
                
                circle.style.width = circle.style.height = `${diameter}px`;
                circle.style.left = `${event.clientX - rect.left - radius}px`;
                circle.style.top = `${event.clientY - rect.top - radius}px`;
                circle.classList.add('ripple');

                const ripple = element.getElementsByClassName('ripple')[0];
                if (ripple) {
                    ripple.remove();
                }

                element.appendChild(circle);
            }

            function createFloatingText(event, amount) {
                const text = document.createElement('div');
                text.textContent = '+' + amount.toFixed(2);
                text.classList.add('floating-text');
                
                // Position at click coordinates relative to viewport to avoid overflow issues
                text.style.left = `${event.clientX}px`;
                text.style.top = `${event.clientY}px`;
                
                document.body.appendChild(text); // Append to body to float freely

                // Clean up after animation
                setTimeout(() => {
                    text.remove();
                }, 1000);
            }
        });

        // Tour Configuration
        document.addEventListener('DOMContentLoaded', function() {
            window.startTour = function() {
                const driver = window.driver.js.driver;
                
                const userRole = "{{ Auth::check() ? (Auth::user()->isAdmin() ? 'admin' : (Auth::user()->isManager() ? 'manager' : 'tenant')) : 'guest' }}";
                const currentRoute = "{{ Route::currentRouteName() }}";
                
                let steps = [];

                // Common Steps (Navigation)
                const commonSteps = [
                    { element: '#sidebarMenu', popover: { title: 'Navigation', description: 'Use this sidebar to access different sections of the application.', side: 'right' } },
                    { element: '.bi-person-circle', popover: { title: 'User Profile', description: 'View your profile info here.', side: 'bottom' } },
                ];

                // Page-Specific Steps Definition
                const pageSteps = {
                    // Dashboard (Role-based logic handled below if not explicitly matched)
                    
                    // Buildings
                    'buildings.index': [
                        { element: '#buildings-header', popover: { title: 'Building Management', description: 'Manage your property portfolio here.', side: 'bottom' } },
                        { element: '#add-building-btn', popover: { title: 'Add New Property', description: 'Click here to register a new building in the system.', side: 'left' } },
                        { element: '#buildingSearch', popover: { title: 'Quick Search', description: 'Find specific buildings by name or address.', side: 'bottom' } },
                        { element: '#buildingsTable', popover: { title: 'Building List', description: 'View details, occupancy rates, and manage individual buildings.', side: 'top' } }
                    ],

                    // Tenants
                    'tenants.index': [
                        { element: '#tenants-header', popover: { title: 'Tenant Registry', description: 'Complete list of all registered tenants.', side: 'bottom' } },
                        { element: '#add-tenant-btn', popover: { title: 'Onboard Tenant', description: 'Register a new tenant and assign them to a unit.', side: 'left' } },
                        { element: '#tenantSearch', popover: { title: 'Find Tenants', description: 'Search by name, email, or phone number.', side: 'bottom' } },
                        { element: '#tenantsTable', popover: { title: 'Tenant Directory', description: 'View status, contact info, and manage tenant profiles.', side: 'top' } }
                    ],

                    // Rents (Leases)
                    'rents.index': [
                        { element: '#rents-header', popover: { title: 'Lease Management', description: 'Track active and past rental agreements.', side: 'bottom' } },
                        { element: '#add-rent-btn', popover: { title: 'New Lease', description: 'Create a new rental agreement for a tenant.', side: 'left' } },
                        { element: '#rentSearch', popover: { title: 'Search Agreements', description: 'Find leases by tenant name or unit.', side: 'bottom' } },
                        { element: '#rentsTable', popover: { title: 'Agreements List', description: 'Monitor lease terms, amounts, and statuses.', side: 'top' } }
                    ],

                    // Maintenance
                    'maintenance.index': [
                        { element: '#maintenance-header', popover: { title: 'Maintenance Hub', description: 'Track and manage property repairs and requests.', side: 'bottom' } },
                        { element: '#add-maintenance-btn', popover: { title: 'Log Request', description: 'Manually record a new maintenance issue.', side: 'left' } },
                        { element: '#maintenanceSearch', popover: { title: 'Filter Requests', description: 'Search by issue type, priority, or unit.', side: 'bottom' } },
                        { element: '#maintenanceTable', popover: { title: 'Request Log', description: 'View status, priority, and take action on tickets.', side: 'top' } }
                    ],

                    // Payments
                    'payments.index': [
                        { element: '#payments-header', popover: { title: 'Financial Records', description: 'Comprehensive log of all rent payments.', side: 'bottom' } },
                        { element: '#add-payment-btn', popover: { title: 'Record Transaction', description: 'Manually log a rent payment.', side: 'left' } },
                        { element: '#paymentSearch', popover: { title: 'Search Transactions', description: 'Find payments by tenant or date.', side: 'bottom' } },
                        { element: '#paymentsTable', popover: { title: 'Payment History', description: 'Review amounts, methods, and payment statuses.', side: 'top' } }
                    ]
                };

                // Determine steps based on route or role
                if (pageSteps[currentRoute]) {
                    steps = [...pageSteps[currentRoute], ...commonSteps];
                } else if (currentRoute === 'dashboard') {
                    // Dashboard specific logic
                    if (userRole === 'admin') {
                        steps = [
                            { element: '#dashboard-title', popover: { title: 'Admin Dashboard', description: 'Welcome to your command center. You have full control over the system here.', side: 'bottom' } },
                            { element: '#stats-cards', popover: { title: 'Quick Stats', description: 'Real-time overview of properties, tenants, and financials.', side: 'bottom' } },
                            { element: '#revenue-section', popover: { title: 'Revenue Tracking', description: 'Monitor monthly income performance at a glance.', side: 'top' } },
                            { element: '#recent-payments-section', popover: { title: 'Recent Activity', description: 'See the latest payments as they come in.', side: 'right' } },
                            { element: '#overdue-rents-section', popover: { title: 'Attention Needed', description: 'Track overdue payments and take action.', side: 'left' } },
                            ...commonSteps
                        ];
                    } else if (userRole === 'manager') {
                        steps = [
                            { element: '#dashboard-title', popover: { title: 'Manager Dashboard', description: 'Welcome! Manage your day-to-day operations here.', side: 'bottom' } },
                            { element: '#stats-cards', popover: { title: 'Overview', description: 'Check the status of your assigned units and tenants.', side: 'bottom' } },
                            { element: '#overdue-rents-section', popover: { title: 'Collections', description: 'Focus on these overdue accounts.', side: 'left' } },
                            ...commonSteps
                        ];
                    } else if (userRole === 'tenant') {
                         steps = [
                            { element: '#dashboard-title', popover: { title: 'Tenant Portal', description: 'Welcome home! This is your personal dashboard.', side: 'bottom' } },
                            { element: '#lease-card', popover: { title: 'Lease Details', description: 'View your rent amount, due dates, and unit info.', side: 'right' } },
                            { element: '#payment-history', popover: { title: 'Payment History', description: 'Track your past payments and download receipts.', side: 'left' } },
                            ...commonSteps
                        ];
                    }
                }

                // Filter out steps where element doesn't exist on current page
                const activeSteps = steps.filter(step => document.querySelector(step.element));

                if (activeSteps.length > 0) {
                    const tour = driver({
                        showProgress: true,
                        steps: activeSteps,
                        onDestroyed: () => {
                            // Only set "seen" for dashboard tour to avoid annoying popups on every page
                            if(currentRoute === 'dashboard' && !localStorage.getItem('tour_seen')) {
                                localStorage.setItem('tour_seen', 'true');
                            }
                        },
                    });
                    tour.drive();
                }
            };

            // Auto-start on first visit (only on dashboard)
            if (window.location.pathname.endsWith('dashboard') && !localStorage.getItem('tour_seen') && "{{ Auth::check() }}" === "1") {
                // Small delay to ensure render
                setTimeout(startTour, 1000);
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebarMenu');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        /**
         * Generic Table Search Function
         * @param {string} inputId - ID of the search input field
         * @param {string} tableId - ID of the table to search
         */
        function setupTableSearch(inputId, tableId) {
            const input = document.getElementById(inputId);
            const table = document.getElementById(tableId);
            
            if (!input || !table) return;

            input.addEventListener('keyup', function() {
                const filter = input.value.toLowerCase();
                const tbody = table.querySelector('tbody');
                if (!tbody) return;
                
                const dataRows = tbody.getElementsByTagName('tr');
                let visibleCount = 0;
                let noResultsRow = tbody.querySelector('.no-results-search-row');

                for (let i = 0; i < dataRows.length; i++) {
                    const row = dataRows[i];
                    
                    // Skip the "no results" row itself if it exists
                    if (row.classList.contains('no-results-search-row')) continue;

                    const text = row.textContent || row.innerText;
                    if (text.toLowerCase().indexOf(filter) > -1) {
                        row.style.display = "";
                        visibleCount++;
                    } else {
                        row.style.display = "none";
                    }
                }

                // Handle No Results Row
                if (visibleCount === 0 && filter !== '') {
                    if (!noResultsRow) {
                        noResultsRow = document.createElement('tr');
                        noResultsRow.className = 'no-results-search-row';
                        const cell = document.createElement('td');
                        
                        // Calculate colspan
                        let colCount = 0;
                        const headerRow = table.querySelector('thead tr');
                        if (headerRow) colCount = headerRow.cells.length;
                        else colCount = 5; // Fallback

                        cell.colSpan = colCount;
                        cell.className = 'text-center py-4 text-muted';
                        cell.innerHTML = '<i class="bi bi-search fs-3 d-block mb-2"></i>No matching records found';
                        
                        noResultsRow.appendChild(cell);
                        tbody.appendChild(noResultsRow);
                    } else {
                        noResultsRow.style.display = "";
                    }
                } else {
                    if (noResultsRow) {
                        noResultsRow.style.display = "none";
                    }
                }
            });
        }
    </script>
    @stack('scripts')

    <!-- Screensaver Overlay -->
    <style>
        #screensaver {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background-color: #333; /* Fallback color */
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset("assets/images/hero-bg.jpg") }}');
            background-size: cover;
            background-position: center;
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            backdrop-filter: blur(5px);
        }
        #screensaver h1 {
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
    </style>
    <div id="screensaver">
        <div>
            <h1 class="display-3 fw-bold">Okaro & Associates</h1>
            <p class="lead fs-3">Property Management Simplified</p>
            <p class="mt-4 opacity-75">
                <small class="d-none d-lg-inline"><i class="bi bi-mouse me-2"></i>Move mouse or press any key to return</small>
                <small class="d-lg-none">Tap screen to continue</small>
            </p>

        </div>
    </div>

    <!-- Chatbot Widget HTML -->
    <div id="chatbot-widget">
        <button id="chatbot-toggle" onclick="toggleChatbot()">
            <i class="bi bi-chat-dots-fill"></i>
        </button>
    </div>

    <div id="chatbot-window">
        <div id="chatbot-header">
            <span class="fw-bold"><i class="bi bi-robot me-2"></i>Okaro Assistant</span>
            <button class="btn btn-sm text-white" onclick="toggleChatbot()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div id="chatbot-messages">
            <div class="chat-message bot-message">
                Hello! I'm your AI assistant. How can I help you today?
            </div>
        </div>
        <div id="chatbot-input-area">
            <input type="text" id="chatbot-input" placeholder="Type your message..." onkeypress="handleEnter(event)">
            <button id="chatbot-send" onclick="sendMessage()">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>

    <script>
        function toggleChatbot() {
            const window = document.getElementById('chatbot-window');
            if (window.style.display === 'flex') {
                window.style.display = 'none';
            } else {
                window.style.display = 'flex';
                document.getElementById('chatbot-input').focus();
            }
        }

        function handleEnter(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        }

        function sendMessage() {
            const input = document.getElementById('chatbot-input');
            const message = input.value.trim();
            
            if (message) {
                // Add user message
                addMessage(message, 'user');
                input.value = '';

                // Simulate AI response (Ollama integration placeholder)
                showTypingIndicator();
                
                // Use a relative path directly to avoid environment configuration issues
                // Changed from /chatbot/send to /bot/message to avoid 403 blocks
                const endpoint = '/bot/message';
                console.log('Chatbot sending to:', endpoint);

                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: message })
                })
                .then(async response => {
                    const isJson = response.headers.get('content-type')?.includes('application/json');
                    const text = await response.text();
                    
                    if (!response.ok) {
                        throw new Error(`Server Error (${response.status}): ${text.substring(0, 50)}...`);
                    }
                    
                    if (!isJson) {
                         // Fallback: If response is text but 200 OK, maybe it's a raw string?
                         // Check if it looks like the valid response format
                         console.warn("Received non-JSON response:", text);
                         try {
                             return JSON.parse(text);
                         } catch (e) {
                             throw new Error(`Invalid Response Format: ${text.substring(0, 50)}...`);
                         }
                    }
                    
                    return JSON.parse(text);
                })
                .then(data => {
                    removeTypingIndicator();
                    addMessage(data.response, 'bot');
                })
                .catch(error => {
                    removeTypingIndicator();
                    console.error('Chatbot Error:', error);
                    let errMsg = "Sorry, I'm having trouble connecting to the server right now.";
                    
                    // Show specific error details if available
                    if (error.message) {
                        errMsg += ` (${error.message})`;
                    }
                    
                    // Add helpful hint for Failed to fetch
                    if (error.message === 'Failed to fetch') {
                         errMsg += " - Please check your internet connection or if the server URL is correct.";
                    }

                    addMessage(errMsg, 'bot');
                });
            }
        }

        function addMessage(text, sender) {
            const messagesDiv = document.getElementById('chatbot-messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${sender}-message`;
            messageDiv.textContent = text;
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        function showTypingIndicator() {
            const messagesDiv = document.getElementById('chatbot-messages');
            const indicator = document.createElement('div');
            indicator.className = 'chat-message bot-message typing-indicator';
            indicator.id = 'typing-indicator';
            indicator.innerHTML = '<i class="bi bi-three-dots"></i>';
            messagesDiv.appendChild(indicator);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        function removeTypingIndicator() {
            const indicator = document.getElementById('typing-indicator');
            if (indicator) {
                indicator.remove();
            }
        }

        (function() {
            let inactivityTime = function () {
                let time;
                const screensaver = document.getElementById('screensaver');
                
                // Reset timer on activity
                function resetTimer() {
                    if (screensaver.style.display === 'flex') {
                        screensaver.style.display = 'none';
                    }
                    clearTimeout(time);
                    time = setTimeout(showScreensaver, 20000); // 20 seconds
                }

                function showScreensaver() {
                    screensaver.style.display = 'flex';
                }

                // Events to monitor
                const events = ['mousemove', 'keypress', 'touchstart', 'click', 'scroll'];
                events.forEach(function(name) {
                    document.addEventListener(name, resetTimer, true);
                });
                
                resetTimer(); // Start timer on load
            };

            inactivityTime();
        })();
    </script>
</body>
</html>
