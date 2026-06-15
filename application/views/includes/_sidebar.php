<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$cur_tab = $this->uri->segment(1) ?: 'dashboard';

// Full current path like: "purchase/received"
$current_path = trim($this->uri->uri_string(), '/');
?>
<!-- Main Sidebar Container (compact + small text) -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand (compact) -->
  <a href="<?= base_url() ?>" class="brand-link py-1" style="text-align: center;">
    <b><?= $this->general_settings['application_name']; ?></b>
  </a>
  <?php
  // Simple greeting based on current time in IST
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

  <!-- Sidebar -->
  <div class="sidebar">

    <!-- compact user panel -->
    <div class="user-panel d-flex align-items-center mt-2 pb-1 mb-1">
      <div class="info w-100 text-center">
        <small class="d-block text-white text-truncate text-sm">
          <?= html_escape($greetings . ' ' . ucwords($this->session->userdata('name'))) ?>
        </small>
      </div>
    </div>

    <!-- Sidebar Menu (compact + small text) -->
    <nav class="mt-1">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <?php
        // Fetch all top-level modules (sidebar parents)
        $menu = get_sidebar_menu();

        foreach ($menu as $nav):

          // 1) Check module (controller) permission first
          if (!$this->rbac->check_module_permission($nav['controller_name'])) {
            continue;
          }

          // 2) Fetch all sub-modules for this module_id
          $all_sub_menu = get_sidebar_sub_menu($nav['module_id']);

          // 3) Filter sub-menus by RBAC (operation-wise)
          //    ✅ show sub-menu only if role has VIEW or ACCESS for that sub-module
          $sub_menu = [];
          if (!empty($all_sub_menu)) {
            foreach ($all_sub_menu as $sn) {
              $sid = (int) ($sn['id'] ?? 0);
              if ($sid <= 0)
                continue;

              // IMPORTANT: operation-wise permission
              if (
                $this->rbac->check_sub_module_permission($sid, 'view') ||
                $this->rbac->check_sub_module_permission($sid, 'access')
              ) {
                $sub_menu[] = $sn;
              }
            }
          }

          $has_submenu = !empty($sub_menu);

          // active state detection
          $is_parent_active = ($cur_tab === $nav['controller_name']);

          // If any sub-menu matches current path, set parent active
          if ($has_submenu) {
            foreach ($sub_menu as $sn) {

              $raw_link = trim((string) ($sn['link'] ?? ''), '/');
              if ($raw_link === '')
                continue;

              // ✅ Support:
              // - "received" -> "purchase/received"
              // - "purchase/received" -> "purchase/received"
              if (strpos($raw_link, '/') !== false) {
                $target_path = $raw_link;
              } else {
                $target_path = trim($nav['controller_name'] . '/' . $raw_link, '/');
              }

              if ($current_path === $target_path) {
                $is_parent_active = true;
                break;
              }
            }
          }

          $li_classes = 'nav-item';
          if ($has_submenu)
            $li_classes .= ' has-treeview';
          if ($is_parent_active)
            $li_classes .= ' menu-open';
          ?>
          <li id="<?= html_escape($nav['controller_name']) ?>" class="<?= $li_classes ?>">
            <a href="<?= base_url($nav['controller_name']) ?>"
              class="nav-link <?= $is_parent_active ? 'active' : '' ?> py-1 px-2">
              <i class="nav-icon fa <?= html_escape($nav['fa_icon']) ?> fa-fw"></i>
              <p class="mb-0 small text-sm">
                <?= trans($nav['module_name']) ?>
                <?= $has_submenu ? '<i class="right fa fa-angle-left"></i>' : '' ?>
              </p>
            </a>

            <?php if ($has_submenu): ?>
              <ul class="nav nav-treeview">
                <?php foreach ($sub_menu as $sub_nav):

                  $raw_link = trim((string) ($sub_nav['link'] ?? ''), '/');

                  // ✅ Build final target path safely
                  if ($raw_link !== '') {
                    if (strpos($raw_link, '/') !== false) {
                      $target_path = $raw_link; // already full route
                    } else {
                      $target_path = trim($nav['controller_name'] . '/' . $raw_link, '/');
                    }
                  } else {
                    $target_path = trim($nav['controller_name']);
                  }

                  $is_sub_active = ($target_path !== '' && $current_path === $target_path);
                  ?>
                  <li class="nav-item">
                    <a href="<?= $target_path !== '' ? base_url($target_path) : 'javascript:void(0);' ?>"
                      class="nav-link <?= $is_sub_active ? 'active' : '' ?> py-1 pl-4">
                      <i class="fa fa-circle-o nav-icon fa-fw"></i>
                      <p class="mb-0 small text-sm"><?= trans($sub_nav['name']) ?></p>@@
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>

        <!-- UPDATED: My Verticals is shown only for logged-in advertisers. -->
        <?php if ((int) $this->session->userdata('admin_role_id') === 2): ?>
          <li class="nav-item">
            <a href="<?= base_url('user_verticals') ?>" class="nav-link <?= $current_path === 'user_verticals' ? 'active' : '' ?> py-1 px-2">
              <i class="nav-icon fa fa-list fa-fw"></i>
              <p class="mb-0 small text-sm">My Verticals</p>
            </a>
          </li>
        <?php endif; ?>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>

<script>
  // Ensure active highlighting (no-op if server already marked)
  (function () {
    try {
      var cur = <?= json_encode($cur_tab) ?>;
      if (cur) {
        var el = document.getElementById(cur);
        if (el) {
          el.classList.add('menu-open');
          var a = el.querySelector('> a');
          if (a) a.classList.add('active');
        }
      }
    } catch (e) { }
  })();
</script>