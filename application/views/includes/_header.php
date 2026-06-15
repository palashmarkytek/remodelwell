<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= isset($title) ? $title . ' - ' : '' ?> <?= $this->general_settings['application_name']; ?></title>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel=icon href="<?= base_url($this->general_settings['favicon']) ?>" sizes="20x20" type="image/png">

  <link rel="stylesheet" href="<?= base_url('assets/plugins/font-awesome/css/font-awesome.min.css') ?>">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/dist/css/adminlte.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/plugins/iCheck/flat/blue.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/plugins/morris/morris.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/plugins/jvectormap/jquery-jvectormap-1.2.2.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/plugins/datepicker/datepicker3.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/plugins/daterangepicker/daterangepicker-bs3.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/plugins/dropzone/dropzone.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/custom.css') ?>">

  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  <!-- jQuery -->
  <script src="<?= base_url('assets/plugins/jquery/jquery.min.js') ?>"></script>

  <!-- Bootstrap JS (REQUIRED for modal) -->
  <script src="<?= base_url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</head>

<body class="hold-transition sidebar-mini <?= (isset($bg_cover)) ? 'bg-cover' : '' ?>">



  <?php if ($this->router->fetch_class() != 'auth'): ?>

    <!-- =========================================
MOBILE HEADER
========================================== -->

    <div class="mk-mobile-header">

      <div class="mk-mobile-left">

        <!-- Menu Button -->
        <button class="mk-menu-btn" id="mkMenuBtn">
          <span></span>
          <span></span>
          <span></span>
        </button>

        <!-- Logo -->
        <a href="<?= base_url() ?>" class="mk-mobile-logo">
          <img src="<?= base_url($this->general_settings['logo']) ?>">
        </a>

      </div>

      <!-- Logout -->
      <a href="<?= base_url('auth/logout') ?>" class="mk-mobile-logout">
        <i class="fa fa-power-off"></i>
      </a>

    </div>

    <!-- =========================================
SIDEBAR
========================================== -->

    <div class="mk-mobile-sidebar" id="mkSidebar">

      <!-- Top -->
      <div class="mk-sidebar-top">

        <a href="<?= base_url() ?>" class="mk-sidebar-logo">
          <img src="<?= base_url($this->general_settings['logo']) ?>">
        </a>

        <button class="mk-close-btn" id="mkCloseBtn">
          &times;
        </button>

      </div>

      <!-- Menu -->
      <ul class="mk-mobile-menu">

        <?php
        $menu = get_sidebar_menu();
        $current_path = trim($this->uri->uri_string(), '/');

        foreach ($menu as $nav):

          if (!$this->rbac->check_module_permission($nav['controller_name'])) {
            continue;
          }

          $all_sub_menu = get_sidebar_sub_menu($nav['module_id']);
          $sub_menu = [];

          if (!empty($all_sub_menu)) {

            foreach ($all_sub_menu as $sn) {

              $sid = (int) ($sn['id'] ?? 0);

              if (
                $this->rbac->check_sub_module_permission($sid, 'view') ||
                $this->rbac->check_sub_module_permission($sid, 'access')
              ) {
                $sub_menu[] = $sn;
              }

            }

          }

          $has_submenu = !empty($sub_menu);
          ?>

          <!-- Parent -->
          <li class="mk-mobile-item">

            <?php if ($has_submenu): ?>

              <!-- Dropdown -->
              <button class="mk-dropdown-btn">

                <div class="mk-menu-left">
                  <i class="fa <?= html_escape($nav['fa_icon']) ?>"></i>
                  <span><?= trans($nav['module_name']) ?></span>
                </div>

                <i class="fa fa-angle-down mk-dd-arrow"></i>

              </button>

              <!-- Submenu -->
              <ul class="mk-submenu">

                <?php foreach ($sub_menu as $sub_nav):

                  $raw_link = trim((string) ($sub_nav['link'] ?? ''), '/');

                  if ($raw_link !== '') {

                    if (strpos($raw_link, '/') !== false) {
                      $target_path = $raw_link;
                    } else {
                      $target_path = trim($nav['controller_name'] . '/' . $raw_link, '/');
                    }

                  } else {

                    $target_path = trim($nav['controller_name']);

                  }

                  ?>

                  <li>
                    <a href="<?= base_url($target_path) ?>">
                      <?= trans($sub_nav['name']) ?>
                    </a>
                  </li>

                <?php endforeach; ?>

              </ul>

            <?php else: ?>

              <!-- Normal Link -->
              <a href="<?= base_url($nav['controller_name']) ?>" class="mk-single-link">

                <div class="mk-menu-left">
                  <i class="fa <?= html_escape($nav['fa_icon']) ?>"></i>
                  <span><?= trans($nav['module_name']) ?></span>
                </div>

              </a>

            <?php endif; ?>

          </li>

        <?php endforeach; ?>

      </ul>

    </div>

    <!-- =========================================
OVERLAY
========================================== -->

    <div class="mk-sidebar-overlay" id="mkOverlay"></div>

  <?php endif; ?>

  <!-- Main Wrapper Start -->
  <div class="wrapper">

    <!-- Navbar -->
    <?php if (!isset($navbar)): ?>

      <nav class="main-header navbar navbar-expand mk-topbar">

        <?php
        defined('BASEPATH') OR exit('No direct script access allowed');

        $cur_tab = $this->uri->segment(1) ?: 'dashboard';
        $current_path = trim($this->uri->uri_string(), '/');

        date_default_timezone_set('Asia/Kolkata');
        $Hour = date('G');

        if ($Hour >= 5 && $Hour <= 11) {
          $greetings = "Good Morning";
        } else if ($Hour >= 12 && $Hour <= 18) {
          $greetings = "Good Afternoon";
        } else {
          $greetings = "Good Evening";
        }
        ?>

        <a href="<?= base_url() ?>dashboard" class="mk-brand">
          <img src="<?= base_url($this->general_settings['logo']) ?>">
        </a>

        <div class="mk-nav-wrap">

          <ul class="navbar-nav mk-menu">

            <?php
            $menu = get_sidebar_menu();

            foreach ($menu as $nav):

              if (!$this->rbac->check_module_permission($nav['controller_name'])) {
                continue;
              }

              $all_sub_menu = get_sidebar_sub_menu($nav['module_id']);
              $sub_menu = [];

              if (!empty($all_sub_menu)) {
                foreach ($all_sub_menu as $sn) {
                  $sid = (int) ($sn['id'] ?? 0);

                  if (
                    $this->rbac->check_sub_module_permission($sid, 'view') ||
                    $this->rbac->check_sub_module_permission($sid, 'access')
                  ) {
                    $sub_menu[] = $sn;
                  }
                }
              }

              $has_submenu = !empty($sub_menu);
              $is_parent_active = ($cur_tab === $nav['controller_name']);

              $admin_role_id = (int) $this->session->userdata('admin_role_id');
              $controller_name = trim((string) $nav['controller_name']);

              $hidden_for_super_admin = [
                'user_verticals',
                'user_payments',
              ];

              if ($admin_role_id !== 1 || !in_array($controller_name, $hidden_for_super_admin, true)):
                ?>
                <li class="nav-item dropdown">

                  <a href="<?= base_url($nav['controller_name']) ?>" class="nav-link <?= $is_parent_active ? 'active' : '' ?>"
                    <?= $has_submenu ? 'data-toggle="dropdown"' : '' ?>>

                    <i class="fa <?= html_escape($nav['fa_icon']) ?>"></i>
                    <span><?= trans($nav['module_name']) ?></span>

                    <?php if ($has_submenu): ?>
                      <i class="fa fa-angle-down mk-arrow"></i>
                    <?php endif; ?>

                  </a>

                  <?php if ($has_submenu): ?>
                    <div class="dropdown-menu mk-dropdown">

                      <?php foreach ($sub_menu as $sub_nav):

                        $raw_link = trim((string) ($sub_nav['link'] ?? ''), '/');

                        if ($raw_link !== '') {
                          if (strpos($raw_link, '/') !== false) {
                            $target_path = $raw_link;
                          } else {
                            $target_path = trim($nav['controller_name'] . '/' . $raw_link, '/');
                          }
                        } else {
                          $target_path = trim($nav['controller_name']);
                        }

                        $is_sub_active = ($target_path !== '' && $current_path === $target_path);
                        ?>

                        <a href="<?= base_url($target_path) ?>" class="dropdown-item <?= $is_sub_active ? 'active' : '' ?>">
                          <?= trans($sub_nav['name']) ?>
                        </a>

                      <?php endforeach; ?>

                    </div>
                  <?php endif; ?>

                </li>
              <?php endif; ?>
            <?php endforeach; ?>

          </ul>

          <div class="mk-right">

            <div class="mk-user">
              <?= html_escape($greetings . ' ' . ucwords($this->session->userdata('name'))) ?>
            </div>

            <div class="mk-datetime">
              <div id="clock" class="mk-time"></div>
              <div class="mk-date"><?= date('jS F, Y', time()) ?></div>
            </div>

            <a href="<?= base_url('auth/logout') ?>" class="mk-logout">
              <i class="fa fa-power-off"></i>
              <span><?= trans('logout') ?></span>
            </a>

          </div>

        </div>

      </nav>

    <?php endif; ?>
    <!-- /.navbar -->