<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css">

<?php
/* ===========================================================
   Modules List (UI aligned with Unit list.php)
   - NO change in existing logic/loops/routes/Datatable init
   - Compact table, small fonts, aligned actions
   =========================================================== */
?>
<div class="mk-dashboard">
  <div class="content-wrapper">
    <section class="content">

      <!-- For Messages -->
      <?php $this->load->view('includes/_messages.php'); ?>

      <div class="card mb-2">
        <div class="card-body py-2 d-flex align-items-center">
          <h5 class="mb-0">
            <i class="fa fa-list"></i> <?= $title ?>
          </h5>

          <a href="<?= base_url('admin_roles/module_add'); ?>" class="btn btn-success btn-sm ml-auto">
            <i class="fa fa-plus"></i> <?= trans('add_new_module') ?>
          </a>
        </div>

        <div class="card-body p-2">
          <div class="table-responsive">
          <table id="module_list" class="table table-bordered table-hover table-sm mb-0">
            <thead class="thead-light small">
              <tr>
                <th style="width:60px;">ID</th>
                <th><?= trans('module_name') ?></th>
                <th><?= trans('controller_name') ?></th>
                <th style="width:140px;"><?= trans('fa_icon') ?></th>
                <th style="width:140px;"><?= trans('operations') ?></th>
                <th style="width:110px; text-align:center;"><?= trans('sub_module') ?></th>
                <th style="width:120px; text-align:right;"><?= trans('action') ?></th>
              </tr>
            </thead>

            <tbody class="small">
              <?php if (!empty($records)): ?>
                <?php foreach ($records as $record): ?>
                  <?php
                    $moduleId   = isset($record['module_id']) ? (int)$record['module_id'] : 0;
                    $moduleName = isset($record['module_name']) ? $record['module_name'] : '';
                    $controller = isset($record['controller_name']) ? $record['controller_name'] : '';
                    $faIcon     = isset($record['fa_icon']) ? $record['fa_icon'] : '';
                    $operation  = isset($record['operation']) ? $record['operation'] : '';
                  ?>
                  <tr>
                    <td class="align-middle"><?= $moduleId; ?></td>
                    <td class="align-middle"><?= trans($moduleName); ?></td>
                    <td class="align-middle"><?= html_escape($controller); ?></td>
                    <td class="align-middle"><code><?= html_escape($faIcon); ?></code></td>
                    <td class="align-middle"><?= html_escape($operation); ?></td>

                    <td class="align-middle text-center">
                      <a href="<?= base_url('admin_roles/sub_module/' . $moduleId) ?>"
                         class="btn btn-info btn-sm"
                         data-toggle="tooltip"
                         title="<?= trans('sub_module') ?>">
                        <i class="fa fa-sliders"></i>
                      </a>
                    </td>

                    <td class="align-middle text-right">
                      <a href="<?= site_url("admin_roles/module_edit/" . $moduleId); ?>"
                         class="btn btn-warning btn-sm mr-1"
                         data-toggle="tooltip"
                         title="Edit">
                        <i class="fa fa-edit"></i>
                      </a>

                      <a href="<?= site_url("admin_roles/module_delete/" . $moduleId); ?>"
                         onclick="return confirm('Are you sure you want to delete?')"
                         class="btn btn-danger btn-sm"
                         data-toggle="tooltip"
                         title="Delete">
                        <i class="fa fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center small">No record found.</td>
                </tr>
              <?php endif; ?>
            </tbody>

          </table>
        </div>
        </div>
      </div>

    </section>
  </div>
</div>

<!-- DataTables -->
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>

<script>
  $(function () {
    // Prevent "Cannot reinitialise DataTable"
    if ($.fn.DataTable.isDataTable('#module_list')) {
      $('#module_list').DataTable().destroy();
    }

    $('#module_list').DataTable({
      "dom": 'lrtip',
      "pageLength": 100,
      "autoWidth": false,
      "lengthChange": false,
      "scrollCollapse": true,
      "order": [[1,'asc']]
    });
  });
</script>
