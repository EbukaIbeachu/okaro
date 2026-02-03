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
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #fdfbf7; /* Off-white */
            transition: margin-left 0.3s;
            border-top: 5px solid #7c3aed; /* Purple top edge */
        }
        /* Modal Enhancements */
        .modal-content {
            background-color: #fdfbf7;
            border: 1px solid #7c3aed; /* Purple border */
            box-shadow: 0 0 20px rgba(124, 58, 237, 0.15);
        }
        .modal-header {
            border-bottom: 1px solid rgba(124, 58, 237, 0.2);
        }
        .modal-footer {
            border-top: 1px solid rgba(124, 58, 237, 0.2);
        }

        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
            border-right: 2px solid #7c3aed; /* Purple line */
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: all 0.3s;
        }
        .sidebar.collapsed {
            margin-left: -250px;
        }
        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s;
            padding: 20px;
            width: calc(100% - 250px);
        }
        .main-content.expanded {
            margin-left: 0;
            width: 100%;
        }
        .sidebar a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            border-left: 4px solid transparent;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #495057;
            color: white;
            border-left: 4px solid #7c3aed; /* Purple line */
        }
        .sidebar .nav-header {
            padding: 15px;
            font-size: 1.2rem;
            border-bottom: 2px solid #7c3aed; /* Purple line */
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Toggle Button Style */
        .sidebar-toggle {
            background: #7c3aed;
            color: white;
            border: 2px solid #fff;
            padding: 8px 12px;
            border-radius: 50%;
            cursor: pointer;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 110; /* Increased z-index to be above sidebar */
            display: none; /* Hidden by default, shown when collapsed */
            box-shadow: 0 4px 8px rgba(124, 58, 237, 0.4);
            transition: all 0.3s ease;
        }
        .sidebar-toggle:hover {
            background-color: #6d28d9;
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(124, 58, 237, 0.5);
        }
        
        /* Purple Theme Overrides */
        .bg-primary {
            background-color: #7c3aed !important;
        }
        .btn-primary {
            background-color: #7c3aed !important;
            border-color: #7c3aed !important;
        }
        .btn-primary:hover {
            background-color: #6d28d9 !important;
            border-color: #6d28d9 !important;
        }
        .text-primary {
            color: #7c3aed !important;
        }
        .page-item.active .page-link {
            background-color: #7c3aed !important;
            border-color: #7c3aed !important;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
                padding-top: 60px; /* Space for mobile header */
                z-index: 100;
            }
            .sidebar .nav-header {
                display: none; /* Hide duplicate header in sidebar */
            }
            .sidebar.mobile-open {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding-top: 60px; /* Space for toggle button */
            }
            .sidebar-toggle {
                display: block !important;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 99;
                opacity: 0;
                transition: opacity 0.3s;
            }
            .sidebar-overlay.show {
                 display: block;
                 opacity: 1;
             }
             .mobile-header {
                 display: flex;
                 align-items: center;
                 position: fixed;
                 top: 0;
                 left: 0;
                 width: 100%;
                 height: 60px;
                 background-color: #fff;
                 border-bottom: 1px solid #e0e0e0;
                 z-index: 102; /* Above sidebar */
                 padding: 0 15px;
                 box-shadow: 0 2px 4px rgba(0,0,0,0.05);
             }
             .sidebar-toggle {
                position: static !important; /* Reset fixed positioning */
                margin: 0 !important;
                display: block !important;
            }
            #desktopToggleBtn {
                display: none !important;
            }
        }
        
        .bg-purple-light {
            background-color: #a78bfa !important; /* Lighter purple */
            color: white !important;
        }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    
    <div class="container-fluid p-0">
        @auth
        <div class="mobile-header d-md-none">
            <button class="sidebar-toggle" id="sidebarToggleBtn" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <span class="fw-bold fs-5 ms-3 text-primary"><i class="bi bi-building"></i> Okaro & Associates</span>
        </div>
        @endauth

        <div class="row g-0">
            <!-- Sidebar -->
            @auth
            <div class="sidebar" id="sidebarMenu">
                <div class="nav-header">
                    <span><i class="bi bi-building"></i> Okaro & Associates</span>
                    <button class="btn btn-sm text-white d-md-none" onclick="toggleSidebar()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <button class="btn btn-sm text-white d-none d-md-block" onclick="toggleSidebar()">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </div>
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
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
                <div id="desktopHeader" class="d-none align-items-center mb-4 pb-2 border-bottom">
                    <button class="btn btn-primary me-3" onclick="toggleSidebar()">
                        <i class="bi bi-list"></i>
                    </button>
                    <span class="h4 mb-0 text-primary fw-bold">Okaro & Associates</span>
                </div>

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom d-md-none">
                    <h1 class="h2">Okaro</h1>
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
            const mainContent = document.querySelector('.main-content');
            const desktopHeader = document.getElementById('desktopHeader');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (window.innerWidth <= 768) {
                // Mobile Logic
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('show');
            } else {
                // Desktop Logic
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                
                if (sidebar.classList.contains('collapsed')) {
                    if(desktopHeader) {
                        desktopHeader.classList.remove('d-none');
                        desktopHeader.classList.add('d-flex');
                    }
                } else {
                    if(desktopHeader) {
                        desktopHeader.classList.remove('d-flex');
                        desktopHeader.classList.add('d-none');
                    }
                }
            }
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
</body>
</html>
