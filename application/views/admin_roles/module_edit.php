<!-- Content Wrapper. Contains page content -->
<div class="mk-dashboard">
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <div class="card card-default">
      <div class="card-header">
        <div class="d-inline-block">
          <h3 class="card-title">
            <i class="fa fa-pencil"></i> <?= $title ?>
          </h3>
        </div>
        <div class="d-inline-block float-right">
          <a href="<?= base_url('admin_roles/module_list'); ?>" class="btn btn-success">
            <i class="fa fa-list"></i> <?= trans('module_list') ?>
          </a>
        </div>
      </div>

      <div class="card-body">

        <!-- For Messages -->
        <?php $this->load->view('includes/_messages.php'); ?>

        <?php
          $module_id       = isset($module['module_id']) ? (int)$module['module_id'] : 0;
          $module_name     = isset($module['module_name']) ? $module['module_name'] : '';
          $controller_name = isset($module['controller_name']) ? $module['controller_name'] : '';
          $fa_icon         = isset($module['fa_icon']) ? $module['fa_icon'] : '';
          $operation       = isset($module['operation']) ? $module['operation'] : '';
          $sort_order      = isset($module['sort_order']) ? $module['sort_order'] : '';
        ?>

        <?php echo form_open(base_url('admin_roles/module_edit/' . $module_id), 'class="form-horizontal"'); ?>

          <!-- Module Name -->
          <div class="form-group">
            <label for="module_name" class="control-label">
              <?= trans('module_name') ?> <span class="text-danger">*</span>
            </label>

            <input
              type="text"
              name="module_name"
              class="form-control"
              id="module_name"
              value="<?= set_value('module_name', html_escape($module_name)); ?>"
              required
            >
            <small><?= trans('lang_index_message') ?></small>
            <?= form_error('module_name', '<small class="text-danger">', '</small>'); ?>
          </div>

          <!-- Controller Name -->
          <div class="form-group">
            <label for="controller_name" class="control-label">
              <?= trans('controller_name') ?> <span class="text-danger">*</span>
            </label>

            <input
              type="text"
              name="controller_name"
              class="form-control"
              id="controller_name"
              value="<?= set_value('controller_name', html_escape($controller_name)); ?>"
              required
            >
            <?= form_error('controller_name', '<small class="text-danger">', '</small>'); ?>
          </div>

          <!-- Font Awesome Icon -->
          <div class="form-group">
            <label for="fa_icon" class="control-label"><?= trans('fa_icon') ?></label>

            <input
              type="text"
              name="fa_icon"
              class="form-control"
              id="fa_icon"
              value="<?= set_value('fa_icon', html_escape($fa_icon)); ?>"
            >
            <?= form_error('fa_icon', '<small class="text-danger">', '</small>'); ?>
          </div>

          <!-- Operations -->
          <div class="form-group">
            <label for="operation" class="control-label"><?= trans('operations') ?></label>

            <input
              type="text"
              name="operation"
              class="form-control"
              id="operation"
              placeholder="eg. add|edit|delete|access|change_status"
              value="<?= set_value('operation', html_escape($operation)); ?>"
            >
            <small class="text-muted">
              Use pipe separated operations. Example: <code>add|edit|delete</code>
            </small>
            <?= form_error('operation', '<small class="text-danger">', '</small>'); ?>
          </div>

          <!-- Sort Order -->
          <div class="form-group">
            <label for="sort_order" class="control-label"><?= trans('sort_order') ?></label>

            <input
              type="number"
              name="sort_order"
              class="form-control"
              id="sort_order"
              value="<?= set_value('sort_order', html_escape($sort_order)); ?>"
              min="0"
              step="1"
            >
            <?= form_error('sort_order', '<small class="text-danger">', '</small>'); ?>
          </div>

          <!-- Submit -->
          <div class="form-group">
            <input
              type="submit"
              name="submit"
              value="<?= trans('update_module') ?>"
              class="btn btn-primary pull-right"
            >
          </div>

        <?php echo form_close(); ?>

      </div>
    </div>
  </section>
</div>
</div>