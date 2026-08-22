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

  <!-- Custom Color Theme: Teal #0D9488 + White Navbar #FFFFFF + Dark Slate Sidebar #0F172A -->
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    
    body, .main-sidebar, .nav-link, h1, h2, h3, h4, h5, h6 {
      font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    /* 1. CLEAN WHITE NAVBAR (#FFFFFF) WITH DARK SLATE TEXT (#1E293B) & TEAL ACCENT */
    .main-header.navbar {
      background-color: #FFFFFF !important;
      border-bottom: 3px solid #0D9488 !important;
      box-shadow: 0 4px 15px rgba(15, 23, 42, 0.08) !important;
      padding: 0.5rem 1rem !important;
    }

    .main-header.navbar .nav-link {
      color: #1E293B !important;
      font-weight: 600 !important;
      border-radius: 8px !important;
      padding: 0.5rem 0.85rem !important;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
      margin: 0 2px !important;
    }

    .main-header.navbar .nav-link:hover {
      background-color: #F1F5F9 !important;
      color: #0D9488 !important;
      transform: translateY(-1px) !important;
      box-shadow: 0 2px 8px rgba(13, 148, 136, 0.15) !important;
    }

    .main-header.navbar .user-menu .nav-link:hover {
      background-color: rgba(13, 148, 136, 0.1) !important;
      color: #0D9488 !important;
    }

    /* 2. DARK SLATE SIDEBAR (#0F172A / #1E293B) */
    .main-sidebar {
      background: linear-gradient(180deg, #0F172A 0%, #1E293B 100%) !important;
      box-shadow: 4px 0 25px rgba(15, 23, 42, 0.3) !important;
      border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    /* Brand Logo Area (#0D9488 Solid Teal) */
    .main-sidebar .brand-link {
      background-color: #0D9488 !important;
      border-bottom: 2px solid rgba(255, 255, 255, 0.15) !important;
      padding: 0.85rem 1rem !important;
      transition: all 0.3s ease !important;
    }

    .main-sidebar .brand-link:hover {
      background-color: #0F766E !important;
      box-shadow: inset 0 0 15px rgba(255, 255, 255, 0.2) !important;
    }

    .main-sidebar .brand-link .brand-text {
      color: #FFFFFF !important;
      font-weight: 800 !important;
      letter-spacing: 0.5px !important;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
    }

    /* User Profile Box on Sidebar */
    .sidebar .user-panel {
      border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
      padding: 0.85rem 0.5rem !important;
      margin-bottom: 0.85rem !important;
      background: rgba(255, 255, 255, 0.04) !important;
      border-radius: 12px !important;
    }

    .sidebar .user-panel .info a {
      color: #FFFFFF !important;
      font-weight: 600 !important;
      transition: color 0.2s ease !important;
    }

    .sidebar .user-panel .info a:hover {
      color: #2DD4BF !important;
    }

    /* Sidebar Navigation Links */
    .sidebar .nav-sidebar .nav-item {
      margin-bottom: 3px !important;
    }

    .sidebar .nav-sidebar .nav-link {
      color: #CBD5E1 !important;
      font-weight: 500 !important;
      border-radius: 10px !important;
      padding: 0.65rem 0.9rem !important;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
      border-left: 3px solid transparent !important;
    }

    /* Sidebar Nav Link HOVER State */
    .sidebar .nav-sidebar .nav-link:hover:not(.active) {
      background: rgba(13, 148, 136, 0.18) !important;
      color: #2DD4BF !important;
      transform: translateX(4px) !important;
      border-left-color: #2DD4BF !important;
      box-shadow: 0 4px 12px rgba(13, 148, 136, 0.2) !important;
    }

    .sidebar .nav-sidebar .nav-link:hover:not(.active) i {
      transform: scale(1.15) rotate(-3deg) !important;
      transition: transform 0.2s ease !important;
      color: #2DD4BF !important;
    }

    /* Sidebar Nav Link ACTIVE State (#0D9488 Solid Teal Emerald) */
    .sidebar .nav-sidebar .nav-link.active {
      background-color: #0D9488 !important;
      color: #FFFFFF !important;
      font-weight: 700 !important;
      box-shadow: 0 4px 18px rgba(13, 148, 136, 0.45) !important;
      border-left-color: #FACC15 !important;
    }

    .sidebar .nav-sidebar .nav-link.active i {
      color: #FFFFFF !important;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
    }

    /* Sidebar Category Headers */
    .sidebar .nav-header {
      color: #94A3B8 !important;
      font-weight: 800 !important;
      letter-spacing: 1.2px !important;
      font-size: 0.72rem !important;
      text-transform: uppercase !important;
      padding: 1rem 0.9rem 0.4rem 0.9rem !important;
    }

    /* 3. BUTTON PRIMARY & GENERATE SISTER COLOR (#0D9488 Teal) */
    .btn-primary, .btn-info {
      background-color: #0D9488 !important;
      border-color: #0D9488 !important;
      color: #FFFFFF !important;
    }

    .btn-primary:hover, .btn-info:hover {
      background-color: #0F766E !important;
      border-color: #0F766E !important;
      color: #FFFFFF !important;
      box-shadow: 0 4px 12px rgba(13, 148, 136, 0.35) !important;
    }

    .btn-primary:focus, .btn-info:focus, .btn-primary:active, .btn-info:active {
      background-color: #115E59 !important;
      border-color: #115E59 !important;
      box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.35) !important;
    }

    .btn-outline-primary, .btn-outline-info {
      color: #0D9488 !important;
      border-color: #0D9488 !important;
    }

    .btn-outline-primary:hover, .btn-outline-info:hover {
      background-color: #0D9488 !important;
      color: #FFFFFF !important;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
