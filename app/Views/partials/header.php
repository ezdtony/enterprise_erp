<?php
// ========================================================
// HEADER PARCIAL — COMPATIBLE CON BaseController
// ========================================================

// Si por alguna razón no están definidas (mal controlador), las convertimos en arreglos vacíos
$modules  = $modules  ?? ($GLOBALS['modules']  ?? []);
$submenus = $submenus ?? ($GLOBALS['submenus'] ?? []);

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enterprise ERP</title>

    <!-- Favicon y estilos -->
    <link rel="shortcut icon" type="image/png" href="<?= BASE_URL ?>assets/images/logos/favicon.ico" />
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/styles.min.css" />

    <!-- Libs -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0/dist/autoNumeric.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    </script>

</head>

<body>
    <!-- Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper"
        data-layout="vertical" data-navbarbg="skin6"
        data-sidebartype="mini-sidebar" data-sidebar-position="fixed"
        data-header-position="fixed">

        <!-- Sidebar Start -->
        <aside class="left-sidebar">
            <div>
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <a href="<?= BASE_URL ?>home" class="text-nowrap logo-img">
                        <img src="<?= BASE_URL ?>assets/images/logos/logo_astelecom.png" class="my-4" width="120" alt="Enterprise ERP" />
                    </a>
                    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                        <i class="ti ti-x fs-8"></i>
                    </div>
                </div>

                <!-- Sidebar navigation -->
                <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                    <ul id="sidebarnav" class="pt-3">

                        <?php foreach ($modules as $mod): ?>
                            <?php
                            $children = array_filter($submenus, fn($s) => $s->parent_id == $mod->id);
                            $hasChildren = count($children) > 0;

                            // Detectar módulo activo
                            $isActive = ($_SERVER['REQUEST_URI'] ?? '') === ($mod->route ?? '');
                            ?>

                            <li class="sidebar-item <?= $isActive ? 'active bg-primary-light rounded' : '' ?>">

                                <?php if ($hasChildren): ?>
                                    <a class="sidebar-link has-arrow d-flex align-items-center justify-content-between" href="javascript:void(0)" aria-expanded="false">
                                        <span class="d-flex align-items-center">
                                            <i class="<?= $mod->icon ?> me-2"></i>
                                            <span class="hide-menu"><?= htmlspecialchars($mod->name) ?></span>
                                        </span>
                                        <i class="ti ti-chevron-right rotate-icon"></i>
                                    </a>

                                    <ul class="collapse first-level base-level-line ps-4">
                                        <?php foreach ($children as $sub): ?>
                                            <li class="sidebar-item">
                                                <a href="<?= BASE_URL . ltrim($sub->route, '/') ?>" class="sidebar-link d-flex align-items-center py-2">
                                                    <i class="ti ti-circle small-circle me-2"></i>
                                                    <span class="hide-menu"><?= htmlspecialchars($sub->name) ?></span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>

                                <?php else: ?>
                                    <a class="sidebar-link d-flex align-items-center py-2 <?= $isActive ? 'active text-white bg-primary' : '' ?>" href="<?= BASE_URL . ltrim($mod->route ?? '', '/') ?>" aria-expanded="false">
                                        <i class="<?= $mod->icon ?> me-2"></i>
                                        <span class="hide-menu"><?= htmlspecialchars($mod->name) ?></span>
                                    </a>
                                <?php endif; ?>

                            </li>
                        <?php endforeach; ?>

                        <!-- Logout -->
                        <li class="sidebar-item mt-4 border-top pt-3">
                            <a class="sidebar-link text-danger d-flex align-items-center py-2" href="<?= BASE_URL ?>logout" aria-expanded="false">
                                <i class="ti ti-logout me-2"></i>
                                <span class="hide-menu">Logout</span>
                            </a>
                        </li>

                    </ul>
                </nav>
                <!-- End Sidebar navigation -->

            </div>
        </aside>
        <!-- Sidebar End -->

        <!-- Main Wrapper -->
        <div class="body-wrapper">
            <!-- Header Start -->
            <header class="app-header shadow-sm">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <ul class="navbar-nav">
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                            </a>
                        </li>
                    </ul>

                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                            <li class="nav-item dropdown">
                                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?= BASE_URL ?>assets/images/profile/user-1.jpg"
                                        alt="User" width="35" height="35" class="rounded-circle">
                                </a>

                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                                    <div class="message-body">
                                        <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-user fs-6"></i>
                                            <p class="mb-0 fs-3">My Profile</p>
                                        </a>
                                        <a href="<?= BASE_URL ?>auth/logout"
                                            class="btn btn-outline-primary mx-3 mt-2 d-block">
                                            Logout
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

                </nav>
            </header>
            <!-- Header End -->

            <style>
                /* Mejoras visuales */
                .sidebar-link.active {
                    font-weight: 600;
                    background-color: #0d6efd33;
                    /* fondo ligero azul */
                    border-radius: 0.375rem;
                }

                .sidebar-link:hover {
                    background-color: #0d6efd1a;
                    border-radius: 0.375rem;
                }

                .rotate-icon {
                    transition: transform 0.3s ease;
                }

                .sidebar-item .collapse.show+.rotate-icon,
                .sidebar-item .collapse.show~.rotate-icon {
                    transform: rotate(90deg);
                }

                .small-circle {
                    font-size: 0.5rem;
                }

                .ps-4 {
                    padding-left: 1.5rem !important;
                }
            </style>