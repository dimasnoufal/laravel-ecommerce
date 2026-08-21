<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - LaraCommerce</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Chart.js for beautiful charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- jQuery and DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <!-- Flatpickr Date Picker (Modern Calendar Widget) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        :root {
            --primary: #2563EB;
            --primary-hover: #1D4ED8;
            --primary-light: #EFF6FF;
            --sidebar-bg: #FFFFFF;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 80px;
            --header-height: 70px;
            --bg-body: #F8FAFC;
            --card-bg: #FFFFFF;
            --border-color: #E2E8F0;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --text-light: #94A3B8;
            
            /* Status colors */
            --success: #10B981;
            --success-bg: #ECFDF5;
            --warning: #F59E0B;
            --warning-bg: #FFFBEB;
            --danger: #EF4444;
            --danger-bg: #FEF2F2;
            --info: #3B82F6;
            --info-bg: #EFF6FF;

            --radius-xl: 16px;
            --radius-lg: 12px;
            --radius-md: 8px;
            --radius-sm: 6px;

            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.07), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
        }

        /* Dark Mode Theme Variables */
        html.dark {
            --primary: #3B82F6;
            --primary-hover: #60A5FA;
            --primary-light: rgba(59, 130, 246, 0.15);
            --sidebar-bg: #0F172A;
            --bg-body: #020617;
            --card-bg: #0F172A;
            --border-color: #1E293B;
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --text-light: #64748B;

            --success-bg: rgba(16, 185, 129, 0.15);
            --warning-bg: rgba(245, 158, 11, 0.15);
            --danger-bg: rgba(239, 68, 68, 0.15);
            --info-bg: rgba(59, 130, 246, 0.15);

            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.4);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.6);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 40;
            display: flex;
            flex-direction: column;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            gap: 0.875rem;
            border-bottom: 1px solid var(--border-color);
            flex-shrink: 0;
        }

        .brand-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #2563EB, #6366F1);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 6px 12px -2px rgba(37, 99, 235, 0.35);
        }

        .brand-text {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-main);
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.2s ease;
        }

        .sidebar.collapsed .brand-text {
            display: none;
        }

        .sidebar-nav {
            padding: 1.25rem 0.875rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            flex-grow: 1;
        }

        .nav-section-title {
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-light);
            padding: 0.75rem 0.75rem 0.35rem 0.75rem;
            white-space: nowrap;
        }

        .sidebar.collapsed .nav-section-title {
            text-align: center;
            padding: 0.5rem 0;
            font-size: 0.6rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 0.65rem 0.875rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
            position: relative;
            white-space: nowrap;
        }

        .nav-item:hover {
            color: var(--text-main);
            background: var(--bg-body);
        }

        .nav-item.active {
            color: var(--primary);
            background: var(--primary-light);
            font-weight: 700;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            bottom: 20%;
            width: 3.5px;
            background: var(--primary);
            border-radius: 0 4px 4px 0;
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .nav-text {
            flex-grow: 1;
            white-space: nowrap;
        }

        .sidebar.collapsed .nav-text,
        .sidebar.collapsed .nav-badge {
            display: none;
        }

        .sidebar.collapsed .nav-item {
            justify-content: center;
            padding: 0.75rem 0;
        }

        .nav-badge {
            padding: 0.15rem 0.5rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            background: var(--primary-light);
            color: var(--primary);
        }

        .sidebar-footer {
            padding: 1rem 0.875rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        /* Main Content Wrapper */
        .main-wrapper {
            flex-grow: 1;
            flex-shrink: 1;
            flex-basis: 0%;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            min-width: 0;
            width: calc(100% - var(--sidebar-width));
            max-width: calc(100% - var(--sidebar-width));
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed ~ .main-wrapper {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
            max-width: calc(100% - var(--sidebar-collapsed-width));
        }

        /* Header Navbar */
        .topbar {
            height: var(--header-height);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 30;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            backdrop-filter: blur(8px);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .icon-btn {
            background: none;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            width: 38px;
            height: 38px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .icon-btn:hover {
            color: var(--text-main);
            border-color: var(--text-light);
            background: var(--bg-body);
        }

        .search-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-box input {
            background: var(--bg-body);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 0.55rem 1rem 0.55rem 2.4rem;
            font-size: 0.875rem;
            color: var(--text-main);
            width: 260px;
            outline: none;
            transition: all 0.2s ease;
        }

        .search-box input:focus {
            width: 320px;
            border-color: var(--primary);
            background: var(--card-bg);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .search-icon {
            position: absolute;
            left: 0.8rem;
            color: var(--text-light);
            pointer-events: none;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-dropdown {
            position: relative;
            display: flex;
            align-items: center;
        }

        .user-trigger {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.35rem 0.65rem;
            border-radius: var(--radius-md);
            background: none;
            border: none;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .user-trigger:hover {
            background: var(--bg-body);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3B82F6, #10B981);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-info {
            text-align: left;
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .dropdown-menu {
            position: absolute;
            top: 120%;
            right: 0;
            width: 220px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            padding: 0.5rem;
            display: none;
            flex-direction: column;
            z-index: 50;
            animation: fadeIn 0.2s ease;
        }

        .dropdown-menu.show {
            display: flex;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.875rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: var(--radius-md);
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .dropdown-item:hover {
            color: var(--text-main);
            background: var(--bg-body);
        }

        .dropdown-item.danger {
            color: var(--danger);
        }
        .dropdown-item.danger:hover {
            background: var(--danger-bg);
        }

        /* Main Content Container */
        .content-body {
            padding: 2rem;
            flex-grow: 1;
            width: 100%;
            min-width: 0;
            overflow-x: hidden;
        }

        /* ===== TOAST NOTIFICATION SYSTEM (FINOTIC/MODERN STYLE) ===== */
        .toast-container {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            pointer-events: none;
        }

        .toast-card {
            pointer-events: auto;
            width: 380px;
            max-width: calc(100vw - 2rem);
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 1rem 1.25rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
        }

        .toast-card.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast-icon-wrap {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toast-success .toast-icon-wrap {
            background: var(--success-bg);
            color: var(--success);
        }
        .toast-info .toast-icon-wrap {
            background: var(--info-bg);
            color: var(--info);
        }
        .toast-error .toast-icon-wrap {
            background: var(--danger-bg);
            color: var(--danger);
        }

        .toast-body {
            flex-grow: 1;
        }

        .toast-title {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.2rem;
        }

        .toast-message {
            font-size: 0.8125rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .toast-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.65rem;
        }

        .toast-btn {
            font-size: 0.8125rem;
            font-weight: 600;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
        }
        .toast-btn.primary {
            color: var(--primary);
        }
        .toast-btn.secondary {
            color: var(--text-muted);
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .toast-close:hover {
            color: var(--text-main);
        }

        /* ===== PRELOADER / LOADING MODAL SYSTEM ===== */
        .preloader-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(6px);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .preloader-backdrop.show {
            display: flex;
            opacity: 1;
        }

        .preloader-card {
            background: var(--card-bg);
            border-radius: var(--radius-xl);
            padding: 2.25rem 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 320px;
            animation: zoomIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .spinner-ring {
            width: 52px;
            height: 52px;
            border: 4px solid var(--border-color);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.85s linear infinite;
            margin-bottom: 1.25rem;
        }

        .preloader-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.35rem;
        }

        .preloader-subtext {
            font-size: 0.8125rem;
            color: var(--text-muted);
        }

        /* ===== REUSABLE COMPONENT STYLES (KPI, PANEL, STATUS PILL) ===== */
        .kpi-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.25s ease;
        }
        .kpi-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
            border-color: rgba(37, 99, 235, 0.3);
        }
        .kpi-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }
        .kpi-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .kpi-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .kpi-badge.badge-up {
            background: var(--success-bg);
            color: var(--success);
        }
        .kpi-badge.badge-down {
            background: var(--danger-bg);
            color: var(--danger);
        }
        .kpi-value {
            font-size: 1.625rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin-bottom: 0.5rem;
        }
        .kpi-subtext {
            font-size: 0.8125rem;
            color: var(--text-muted);
        }

        /* Panel Card */
        .panel-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            transition: border-color 0.2s ease;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }
        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .panel-title {
            font-size: 1.0625rem;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.01em;
        }
        .panel-content {
            flex-grow: 1;
            width: 100%;
            min-width: 0;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 0.25rem;
        }

        /* Status Pill */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .status-paid {
            background: var(--success-bg);
            color: var(--success);
        }
        .status-processing {
            background: var(--info-bg);
            color: var(--info);
        }
        .status-pending {
            background: var(--warning-bg);
            color: var(--warning);
        }
        .status-cancelled {
            background: var(--danger-bg);
            color: var(--danger);
        }

        /* ===== CUSTOM SLEEK DATATABLES SYSTEM ===== */
        .dataTables_wrapper {
            position: relative;
            font-size: 0.875rem;
            color: var(--text-main);
            width: 100%;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1.25rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .dataTables_wrapper .dataTables_length select {
            background: var(--bg-body);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 0.45rem 0.75rem;
            color: var(--text-main);
            font-weight: 600;
            outline: none;
            cursor: pointer;
            margin: 0 0.35rem;
            transition: all 0.2s ease;
        }

        .dataTables_wrapper .dataTables_length select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .dataTables_wrapper .dataTables_filter input {
            background: var(--bg-body);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 0.5rem 1rem;
            color: var(--text-main);
            outline: none;
            min-width: 240px;
            margin-left: 0.5rem;
            transition: all 0.2s ease;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
            background: var(--card-bg);
        }

        table.dataTable {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            margin-top: 0.5rem !important;
            margin-bottom: 1.25rem !important;
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 1px solid var(--border-color) !important;
        }

        table.dataTable thead th {
            background: var(--bg-body) !important;
            color: var(--text-muted) !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.06em !important;
            padding: 1rem 1.25rem !important;
            border-bottom: 1px solid var(--border-color) !important;
            border-top: none !important;
        }

        table.dataTable tbody td {
            padding: 1rem 1.25rem !important;
            color: var(--text-main);
            border-bottom: 1px solid var(--border-color) !important;
            vertical-align: middle;
            background: var(--card-bg) !important;
            transition: background 0.15s ease;
        }

        table.dataTable tbody tr:hover td {
            background: var(--primary-light) !important;
        }

        table.dataTable tbody tr:last-child td {
            border-bottom: none !important;
        }

        /* Full-Screen Glassmorphism DataTables Loading Backdrop with 3 Horizontal Dots */
        .dataTables_wrapper .dataTables_processing {
            position: fixed !important;
            inset: 0 !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background: rgba(15, 23, 42, 0.55) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            z-index: 99999 !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            color: #FFFFFF !important;
            font-size: 0.9375rem !important;
            font-weight: 600 !important;
            letter-spacing: 0.02em !important;
            text-shadow: 0 2px 8px rgba(0,0,0,0.5) !important;
        }

        .dataTables_wrapper .dataTables_processing[style*="display: none"],
        .dataTables_wrapper .dataTables_processing[style*="display:none"] {
            display: none !important;
        }

        /* Hide duplicate default DataTables inner spinner/dots */
        .dataTables_wrapper .dataTables_processing > div {
            display: none !important;
        }

        /* Single 3 Horizontal Pulsing Wave Dots */
        .dataTables_wrapper .dataTables_processing::before {
            content: '' !important;
            width: 14px !important;
            height: 14px !important;
            background: #3B82F6 !important;
            border-radius: 50% !important;
            border: none !important;
            animation: horizontalDotPulse 1.4s infinite ease-in-out both !important;
            animation-delay: -0.16s !important;
            box-shadow: -24px 0 0 #60A5FA, 24px 0 0 #93C5FD, 0 0 14px rgba(59, 130, 246, 0.8) !important;
            margin-top: 1.25rem !important;
            order: 2 !important;
        }

        /* Pagination & Info */
        .dataTables_wrapper .dataTables_info {
            font-size: 0.8125rem;
            color: var(--text-muted);
            padding-top: 0.75rem;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 0.5rem;
            display: flex;
            gap: 0.35rem;
            align-items: center;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: 1px solid var(--border-color) !important;
            background: var(--card-bg) !important;
            color: var(--text-main) !important;
            border-radius: var(--radius-md) !important;
            padding: 0.4rem 0.75rem !important;
            font-size: 0.8125rem !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--bg-body) !important;
            color: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: var(--primary) !important;
            color: #ffffff !important;
            border-color: var(--primary) !important;
            box-shadow: 0 4px 10px -2px rgba(37, 99, 235, 0.4) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            opacity: 0.4 !important;
            cursor: not-allowed !important;
            background: var(--bg-body) !important;
            border-color: var(--border-color) !important;
            color: var(--text-light) !important;
        }

        /* Reusable UI Components */
        .table-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tbl-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.65rem;
            border-radius: var(--radius-md);
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .tbl-btn-edit {
            background: var(--primary-light);
            color: var(--primary);
            border-color: rgba(37, 99, 235, 0.2);
        }
        .tbl-btn-edit:hover {
            background: var(--primary);
            color: #ffffff;
        }

        .tbl-btn-delete {
            background: var(--danger-bg);
            color: var(--danger);
            border-color: rgba(239, 68, 68, 0.2);
        }
        .tbl-btn-delete:hover {
            background: var(--danger);
            color: #ffffff;
        }

        .brand-avatar-mini {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #2563EB, #6366F1);
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .category-icon-box {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--info-bg);
            color: var(--info);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .code-pill {
            background: var(--bg-body);
            border: 1px solid var(--border-color);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: ui-monospace, monospace;
            font-size: 0.75rem;
            color: var(--primary);
            font-weight: 600;
        }

        /* Custom Tabs Component */
        .nav-tabs-wrapper {
            display: flex;
            gap: 0.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            overflow-x: auto;
        }

        .tab-nav-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.15rem;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
            background: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .tab-nav-link:hover {
            color: var(--text-main);
            background: var(--bg-body);
        }

        .tab-nav-link.active {
            color: var(--primary);
            background: var(--primary-light);
            font-weight: 700;
        }

        .tab-pane {
            display: none;
            animation: fadeIn 0.2s ease;
        }

        .tab-pane.active {
            display: block;
        }

        /* Form Styles for Modals */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.45rem;
        }

        .form-control {
            width: 100%;
            padding: 0.65rem 0.875rem;
            font-size: 0.875rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            background: var(--bg-body);
            color: var(--text-main);
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
            background: var(--card-bg);
        }

        .form-error {
            display: block;
            color: var(--danger);
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.35rem;
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
            border: none;
            padding: 0.65rem 1.25rem;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.1s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--bg-body);
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            padding: 0.65rem 1.25rem;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background: var(--card-bg);
            color: var(--text-main);
            border-color: var(--text-light);
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .sidebar {
                width: var(--sidebar-collapsed-width);
            }
            .sidebar .brand-text,
            .sidebar .nav-text,
            .sidebar .nav-badge,
            .sidebar .nav-section-title {
                display: none;
            }
            .sidebar .nav-item {
                justify-content: center;
                padding: 0.75rem 0;
            }
            .main-wrapper {
                margin-left: var(--sidebar-collapsed-width);
            }
            .search-box input {
                width: 180px;
            }
            .search-box input:focus {
                width: 220px;
            }
        }
        @media (max-width: 640px) {
            .topbar {
                padding: 0 1rem;
            }
            .user-info {
                display: none;
            }
            .content-body {
                padding: 1.25rem 1rem;
            }
        }
    </style>
    @yield('styles')
    @stack('styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="mainSidebar">
        <div class="sidebar-brand">
            <div class="brand-icon-box">
                <i data-lucide="shopping-bag" style="width: 22px; height: 22px;"></i>
            </div>
            <span class="brand-text">E-Commerce</span>
        </div>

        <nav class="sidebar-nav">
            <span class="nav-section-title">Main Menu</span>
            
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="nav-icon"></i>
                <span class="nav-text">Dashboard</span>
            </a>

            <a href="#" class="nav-item" onclick="showToast('info', 'Orders Module', 'Fetching live orders list...'); return false;">
                <i data-lucide="shopping-cart" class="nav-icon"></i>
                <span class="nav-text">Orders</span>
                <span class="nav-badge" style="background: var(--warning-bg); color: var(--warning);">18</span>
            </a>

            <span class="nav-section-title">Master Data</span>

            <a href="{{ route('admin.products.index') }}" class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i data-lucide="package" class="nav-icon"></i>
                <span class="nav-text">Products</span>
            </a>

            <a href="{{ route('admin.categories.index') }}" class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i data-lucide="grid" class="nav-icon"></i>
                <span class="nav-text">Categories</span>
            </a>

            <a href="{{ route('admin.brands.index') }}" class="nav-item {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                <i data-lucide="tag" class="nav-icon"></i>
                <span class="nav-text">Brands</span>
            </a>

            <a href="{{ route('admin.attributes.index') }}" class="nav-item {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}">
                <i data-lucide="sliders" class="nav-icon"></i>
                <span class="nav-text">Attributes</span>
            </a>

            <a href="{{ route('admin.regions.index') }}" class="nav-item {{ request()->routeIs('admin.regions.*') ? 'active' : '' }}">
                <i data-lucide="map-pin" class="nav-icon"></i>
                <span class="nav-text">Regional Data</span>
            </a>

            <span class="nav-section-title">Users</span>

            <a href="#" class="nav-item" onclick="showToast('info', 'Customers', 'Customer relationship view active.'); return false;">
                <i data-lucide="users" class="nav-icon"></i>
                <span class="nav-text">Customers</span>
            </a>

            <span class="nav-section-title">Analytics & Finance</span>

            <a href="#" class="nav-item" onclick="showToast('info', 'Analytics', 'Loading revenue and conversion metrics.'); return false;">
                <i data-lucide="bar-chart-3" class="nav-icon"></i>
                <span class="nav-text">Analytics</span>
            </a>

            <a href="#" class="nav-item" onclick="showToast('info', 'Inventory', 'Stock movements and warehouse status.'); return false;">
                <i data-lucide="layers" class="nav-icon"></i>
                <span class="nav-text">Inventory</span>
            </a>

            <a href="#" class="nav-item" onclick="showToast('info', 'Reports', 'Generating monthly export report...'); return false;">
                <i data-lucide="file-text" class="nav-icon"></i>
                <span class="nav-text">Reports</span>
            </a>

            <span class="nav-section-title">System</span>

            <a href="#" class="nav-item" onclick="showToast('info', 'Settings', 'Admin settings panel.'); return false;">
                <i data-lucide="settings" class="nav-icon"></i>
                <span class="nav-text">Settings</span>
            </a>

            <a href="#" class="nav-item" onclick="showToast('info', 'Help & Support', 'Documentation and live help desk.'); return false;">
                <i data-lucide="help-circle" class="nav-icon"></i>
                <span class="nav-text">Help & Support</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="javascript:void(0)" class="nav-item" id="sidebarToggleFooter" style="justify-content: flex-start;">
                <i data-lucide="panel-left-close" class="nav-icon" id="toggleIcon"></i>
                <span class="nav-text">Collapse Menu</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Shell -->
    <div class="main-wrapper">
        <!-- Top Navigation Bar -->
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="icon-btn" id="sidebarToggleBtn" title="Toggle Sidebar">
                    <i data-lucide="menu" style="width: 20px; height: 20px;"></i>
                </button>

                <div class="search-box">
                    <i data-lucide="search" class="search-icon" style="width: 18px; height: 18px;"></i>
                    <input type="text" placeholder="Search products, orders, customers..." id="globalSearchInput">
                </div>
            </div>

            <div class="topbar-right">
                <!-- Theme Mode Toggle -->
                <button type="button" class="icon-btn" id="themeToggleBtn" title="Toggle Dark/Light Mode">
                    <i data-lucide="moon" id="themeIcon" style="width: 19px; height: 19px;"></i>
                </button>

                <!-- Notifications -->
                <button type="button" class="icon-btn" id="notificationBtn" title="Notifications" onclick="showToast('info', 'Notification Center', 'You have 3 new store orders to review.')">
                    <i data-lucide="bell" style="width: 19px; height: 19px;"></i>
                </button>

                <!-- User Profile Dropdown -->
                <div class="user-dropdown">
                    <button type="button" class="user-trigger" id="userMenuBtn">
                        <div class="user-avatar">
                            {{ substr(auth()->user()->name ?? 'Admin', 0, 1) }}
                        </div>
                        <div class="user-info">
                            <span class="user-name">{{ auth()->user()->name ?? 'Administrator' }}</span>
                            <span class="user-role">{{ auth()->user()->email ?? 'admin@laracommerce.com' }}</span>
                        </div>
                        <i data-lucide="chevron-down" style="width: 16px; height: 16px; color: var(--text-light);"></i>
                    </button>

                    <div class="dropdown-menu" id="userDropdownMenu">
                        <a href="#" class="dropdown-item" onclick="showToast('info', 'Profile', 'Opening account profile settings.'); return false;">
                            <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                            <span>My Profile</span>
                        </a>
                        <a href="#" class="dropdown-item" onclick="showToast('info', 'Preferences', 'Viewing system preferences.'); return false;">
                            <i data-lucide="sliders" style="width: 16px; height: 16px;"></i>
                            <span>Preferences</span>
                        </a>
                        <div style="height: 1px; background: var(--border-color); margin: 0.35rem 0;"></div>
                        <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                            @csrf
                            <button type="submit" class="dropdown-item danger">
                                <i data-lucide="log-out" style="width: 16px; height: 16px;"></i>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Specific Body Content -->
        <main class="content-body">
            @yield('content')
        </main>
    </div>

    <!-- ===== REUSABLE TOAST, PRELOADER & CONFIRM MODAL COMPONENTS ===== -->
    <x-toast-container />
    <x-preloader-modal />
    <x-confirm-delete-modal />

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Sidebar Collapse Logic
        const mainSidebar = document.getElementById('mainSidebar');
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        const sidebarToggleFooter = document.getElementById('sidebarToggleFooter');
        const toggleIcon = document.getElementById('toggleIcon');

        function toggleSidebar() {
            mainSidebar.classList.toggle('collapsed');
            const isCollapsed = mainSidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar_collapsed', isCollapsed ? '1' : '0');
            
            if (toggleIcon) {
                toggleIcon.setAttribute('data-lucide', isCollapsed ? 'panel-left-open' : 'panel-left-close');
                lucide.createIcons();
            }
        }

        if (sidebarToggleBtn) sidebarToggleBtn.addEventListener('click', toggleSidebar);
        if (sidebarToggleFooter) sidebarToggleFooter.addEventListener('click', toggleSidebar);

        if (localStorage.getItem('sidebar_collapsed') === '1') {
            mainSidebar.classList.add('collapsed');
        }

        // Dark/Light Theme Switcher Logic
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeIcon');

        function applyTheme(isDark) {
            if (isDark) {
                document.documentElement.classList.add('dark');
                if (themeIcon) themeIcon.setAttribute('data-lucide', 'sun');
            } else {
                document.documentElement.classList.remove('dark');
                if (themeIcon) themeIcon.setAttribute('data-lucide', 'moon');
            }
            lucide.createIcons();
        }

        const savedTheme = localStorage.getItem('app_theme');
        if (savedTheme === 'dark') {
            applyTheme(true);
        }

        themeToggleBtn.addEventListener('click', () => {
            const isCurrentlyDark = document.documentElement.classList.contains('dark');
            const newThemeDark = !isCurrentlyDark;
            applyTheme(newThemeDark);
            localStorage.setItem('app_theme', newThemeDark ? 'dark' : 'light');
            showToast('info', 'Theme Switched', `Active mode: ${newThemeDark ? 'Dark Theme' : 'Light Theme'}`);
        });

        // User Profile Dropdown Toggle
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdownMenu = document.getElementById('userDropdownMenu');

        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdownMenu.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!userDropdownMenu.contains(e.target) && !userMenuBtn.contains(e.target)) {
                userDropdownMenu.classList.remove('show');
            }
        });

        // ==========================================
        // GLOBAL TOAST NOTIFICATION HELPER FUNCTIONS
        // ==========================================
        window.showToast = function(type, title, message, actionText = null, actionCallback = null) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `toast-card toast-${type}`;

            let iconName = 'check-circle';
            if (type === 'error') iconName = 'alert-circle';
            if (type === 'info') iconName = 'info';

            let actionHtml = '';
            if (actionText) {
                actionHtml = `
                    <div class="toast-actions">
                        <button type="button" class="toast-btn primary" id="toastActionBtn">${actionText}</button>
                        <button type="button" class="toast-btn secondary" onclick="this.closest('.toast-card').remove()">Dismiss</button>
                    </div>
                `;
            }

            toast.innerHTML = `
                <div class="toast-icon-wrap">
                    <i data-lucide="${iconName}" style="width: 20px; height: 20px;"></i>
                </div>
                <div class="toast-body">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                    ${actionHtml}
                </div>
                <button type="button" class="toast-close" onclick="this.closest('.toast-card').remove()">
                    <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                </button>
            `;

            container.appendChild(toast);
            lucide.createIcons();

            // Animate In
            setTimeout(() => {
                toast.classList.add('show');
            }, 50);

            // Bind action callback if supplied
            if (actionText && actionCallback) {
                const actionBtn = toast.querySelector('#toastActionBtn');
                if (actionBtn) {
                    actionBtn.addEventListener('click', () => {
                        actionCallback();
                        toast.remove();
                    });
                }
            }

            // Auto Remove after 4.5 seconds (unless action buttons exist)
            if (!actionText) {
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 350);
                }, 4500);
            }
        };



        // Flash message listeners from Laravel Session
        @if(session('success'))
            showToast('success', 'Saved Successfully', {!! json_encode(session('success')) !!});
        @endif
        @if(session('error'))
            showToast('error', 'Something went wrong', {!! json_encode(session('error')) !!}, 'Retry', () => location.reload());
        @endif
        @if(session('status'))
            showToast('info', 'Notification', {!! json_encode(session('status')) !!});
        @endif
    </script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
