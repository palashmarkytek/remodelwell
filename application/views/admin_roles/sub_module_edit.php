<!-- Content Wrapper. Contains page content -->
<div class="mk-dashboard">
<div class="content-wrapper">
  <section class="content">
    <div class="card card-default">
      <div class="card-header">
        <div class="d-inline-block">
          <h3 class="card-title">
            <i class="fa fa-pencil"></i> Edit Sub Module
          </h3>
        </div>

        <div class="d-inline-block float-right">
          <a href="<?= base_url('admin_roles/sub_module/' . (int)$module['parent']); ?>" class="btn btn-success">
            <i class="fa fa-list"></i> Sub Module List
          </a>
        </div>
      </div>

      <div class="card-body">

        <?php $this->load->view('includes/_messages.php'); ?>

        <?php
          $sub_id     = (int)$module['id'];
          $parent_id  = (int)$module['parent'];
          $sub_name   = $module['name'] ?? '';
          $sub_link   = $module['link'] ?? '';
          $ops        = $module['operation'] ?? 'add|edit|delete|access|change_status';
          $sort_order = $module['sort_order'] ?? '';
        ?>

        <?php echo form_open(base_url('admin_roles/sub_module_edit/' . $sub_id), 'class="form-horizontal"'); ?>

          <!-- Parent Module -->
          <div class="form-group">
            <label class="control-label">Module Name</label>

            <?php
              $menu = get_sidebar_menu();
              $options = array_column($menu, 'module_name', 'module_id');
              echo form_dropdown(
                'module_name',
                $options,
                set_value('module_name', $parent_id),
                ['class' => 'form-control select2']
              );
            ?>
            <?= form_error('module_name', '<small class="text-danger">', '</small>'); ?>
          </div>

          <!-- Sub Module Name -->
          <div class="form-group">
            <label for="sub_module_name" class="control-label">
              Sub Module Name <span class="text-danger">*</span>
            </label>

            <input
              type="text"
              name="sub_module_name"
              id="sub_module_name"
              class="form-control"
              value="<?= set_value('sub_module_name', html_escape($sub_name)); ?>"
              required
            >
            <?= form_error('sub_module_name', '<small class="text-danger">', '</small>'); ?>
          </div>

          <!-- Link -->
          <div class="form-group">
            <label for="operation" class="control-label">Link</label>

            <input
              type="text"
              name="operation"
              id="operation"
              class="form-control"
              value="<?= set_value('operation', html_escape($sub_link)); ?>"
              placeholder="eg. purchase/received"
            >
            <?= form_error('operation', '<small class="text-danger">', '</small>'); ?>
          </div>

          <!-- ✅ Operations (NEW) -->
          <div class="form-group">
            <label for="operations" class="control-label">
              Operations <span class="text-danger">*</span>
            </label>

            <input
              type="text"
              name="operations"
              id="operations"
              class="form-control"
              value="<?= set_value('operations', html_escape($ops)); ?>"
              placeholder="add|edit|delete|access|change_status"
              required
            >
            <small class="text-muted">
              Pipe (|) separated permission operations
            </small>
            <?= form_error('operations', '<small class="text-danger">', '</small>'); ?>
          </div>

          <!-- Sort Order -->
          <div class="form-group">
            <label for="sort_order" class="control-label">Sort Order</label>

            <input
              type="number"
              name="sort_order"
              id="sort_order"
              class="form-control"
              value="<?= set_value('sort_order', html_escape($sort_order)); ?>"
              min="0"
            >
            <?= form_error('sort_order', '<small class="text-danger">', '</small>'); ?>
          </div>

          <div class="form-group">
            <input type="submit" name="submit" value="Update Sub Module" class="btn btn-primary pull-right">
          </div>

        <?php echo form_close(); ?>

      </div>
    </div>
  </section>
</div>
</div>
