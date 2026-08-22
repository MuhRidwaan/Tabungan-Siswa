<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Dashboard</title>

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" media="print" onload="this.media='all'">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('plugins/fontawesome-free/css/all.min.css') ?>">

  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" media="print" onload="this.media='all'">

  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="<?= base_url('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">

  <!-- iCheck -->
  <link rel="stylesheet" href="<?= base_url('plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">

  <!-- JQVMap -->
  <link rel="stylesheet" href="<?= base_url('plugins/jqvmap/jqvmap.min.css') ?>">

  <!-- AdminLTE Theme -->
  <link rel="stylesheet" href="<?= base_url('dist/css/adminlte.min.css') ?>">

  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?= base_url('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">

  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?= base_url('plugins/daterangepicker/daterangepicker.css') ?>">

  <!-- Summernote -->
  <link rel="stylesheet" href="<?= base_url('plugins/summernote/summernote-bs4.min.css') ?>">

  <!-- Select2 -->
  <link rel="stylesheet" href="<?= base_url('plugins/select2/css/select2.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">

  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <!-- Custom Color Theme for Vibrant Blue & Red Navbar & Sidebar -->
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    
    body, .main-sidebar, .nav-link, h1, h2, h3, h4, h5, h6 {
      font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    /* 1. VIBRANT ROYAL BLUE & CRIMSON RED NAVBAR */
    .main-header.navbar {
      background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1e40af 100%) !important;
      border-bottom: 3px solid #ef4444 !important;
      box-shadow: 0 4px 20px rgba(220, 38, 38, 0.25) !important;
      padding: 0.5rem 1rem !important;
    }

    .main-header.navbar .nav-link {
      color: #f8fafc !important;
      font-weight: 600 !important;
      border-radius: 8px !important;
      padding: 0.5rem 0.85rem !important;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
      margin: 0 2px !important;
    }

    .main-header.navbar .nav-link:hover {
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.3) 0%, rgba(220, 38, 38, 0.4) 100%) !important;
      color: #ffffff !important;
      transform: translateY(-1.5px) !important;
      box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4) !important;
    }

    .main-header.navbar .user-menu .nav-link:hover {
      background: linear-gradient(135deg, rgba(37, 99, 235, 0.3) 0%, rgba(220, 38, 38, 0.3) 100%) !important;
    }

    /* 2. VIBRANT ROYAL BLUE & CRIMSON RED SIDEBAR */
    .main-sidebar {
      background: linear-gradient(180deg, #0f172a 0%, #172554 60%, #0f172a 100%) !important;
      box-shadow: 4px 0 25px rgba(15, 23, 42, 0.4) !important;
      border-right: 1px solid rgba(239, 68, 68, 0.2) !important;
    }

    /* Brand Logo Area (Blue to Crimson Red Gradient) */
    .main-sidebar .brand-link {
      background: linear-gradient(135deg, #1d4ed8 0%, #dc2626 100%) !important;
      border-bottom: 2px solid rgba(255, 255, 255, 0.2) !important;
      padding: 0.85rem 1rem !important;
      transition: all 0.3s ease !important;
    }

    .main-sidebar .brand-link:hover {
      background: linear-gradient(135deg, #1e40af 0%, #b91c1c 100%) !important;
      box-shadow: inset 0 0 15px rgba(255, 255, 255, 0.25) !important;
    }

    .main-sidebar .brand-link .brand-text {
      color: #ffffff !important;
      font-weight: 800 !important;
      letter-spacing: 0.5px !important;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4) !important;
    }

    /* User Profile Box on Sidebar */
    .sidebar .user-panel {
      border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
      padding: 0.85rem 0.5rem !important;
      margin-bottom: 0.85rem !important;
      background: rgba(255, 255, 255, 0.04) !important;
      border-radius: 12px !important;
    }

    .sidebar .user-panel .info a {
      color: #ffffff !important;
      font-weight: 600 !important;
      transition: color 0.2s ease !important;
    }

    .sidebar .user-panel .info a:hover {
      color: #f87171 !important;
    }

    /* Sidebar Navigation Links */
    .sidebar .nav-sidebar .nav-item {
      margin-bottom: 3px !important;
    }

    .sidebar .nav-sidebar .nav-link {
      color: #cbd5e1 !important;
      font-weight: 500 !important;
      border-radius: 10px !important;
      padding: 0.65rem 0.9rem !important;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
      border-left: 3px solid transparent !important;
    }

    /* Sidebar Nav Link HOVER State (Blue-Crimson Gradient Glow & Crimson Red Indicator) */
    .sidebar .nav-sidebar .nav-link:hover:not(.active) {
      background: linear-gradient(90deg, rgba(30, 64, 175, 0.35) 0%, rgba(220, 38, 38, 0.25) 100%) !important;
      color: #ffffff !important;
      transform: translateX(4px) !important;
      border-left-color: #ef4444 !important;
      box-shadow: 0 4px 14px rgba(220, 38, 38, 0.25) !important;
    }

    .sidebar .nav-sidebar .nav-link:hover:not(.active) i {
      transform: scale(1.18) rotate(-3deg) !important;
      transition: transform 0.2s ease !important;
      color: #f87171 !important;
    }

    /* Sidebar Nav Link ACTIVE State (Royal Blue to Crimson Red Gradient with Gold Border) */
    .sidebar .nav-sidebar .nav-link.active {
      background: linear-gradient(135deg, #2563eb 0%, #dc2626 100%) !important;
      color: #ffffff !important;
      font-weight: 700 !important;
      box-shadow: 0 4px 18px rgba(220, 38, 38, 0.45) !important;
      border-left-color: #facc15 !important;
    }

    .sidebar .nav-sidebar .nav-link.active i {
      color: #ffffff !important;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4) !important;
    }

    /* Sidebar Category Headers */
    .sidebar .nav-header {
      color: #94a3b8 !important;
      font-weight: 800 !important;
      letter-spacing: 1.2px !important;
      font-size: 0.72rem !important;
      text-transform: uppercase !important;
      padding: 1rem 0.9rem 0.4rem 0.9rem !important;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
