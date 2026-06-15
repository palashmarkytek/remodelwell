<!-- Content Wrapper. Contains page content -->
<div class="mk-dashboard">
<div class="content-wrapper">
  <section class="content">
    <div class="card card-default">
      <div class="card-header">
        <div class="d-inline-block">
          <h3 class="card-title">
            <i class="fa fa-plus"></i> Add New Sub Module
          </h3>
        </div>

        <?php $parent_menu = (int)$this->uri->segment(3); ?>

        <div class="d-inline-block float-right">
          <a href="<?= base_url('admin_roles/sub_module/' . $parent_menu); ?>" class="btn btn-success">
            <i class="fa fa-list"></i> Sub Module List
          </a>
        </div>
      </div>

      <div class="card-body">

        <?php $this->load->view('includes/_messages.php'); ?>

        <?php echo form_open(base_url('admin_roles/sub_module_add'), 'class="form-horizontal"'); ?>

          <!-- Sub Module Name -->
          <div class="form-group">
            <label for="module_name" class="control-label">
              Sub Module Name <span class="text-danger">*</span>
            </label>

            <input
              type="text"
              name="module_name"
              id="module_name"
              class="form-control"
              value="<?= set_value('module_name'); ?>"
              required
            >
            <small>Language index as per your language file</small>
            <?= form_error('module_name', '<small class="text-danger">', '</small>'); ?>
          </div>

          <!-- Link -->
          <div class="form-group">
            <label for="operation" class="control-label">Link</label>

            <input
              type="text"
              name="operation"
              id="operation"
              class="form-control"
              placeholder="eg. purchase/received"
              value="<?= set_value('operation'); ?>"
            >
            <small class="text-muted">Controller/action format</small>
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
              placeholder="eg. add|edit|delete|access|change_status"
              value="<?= set_value('operations', 'add|edit|delete|access|change_status'); ?>"
              required
            >
            <small class="text-muted">
              Pipe (|) separated operations used for permissions
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
              value="<?= set_value('sort_order'); ?>"
              min="0"
            >
            <?= form_error('sort_order', '<small class="text-danger">', '</small>'); ?>
          </div>

          <input type="hidden" name="parent_module" value="<?= $parent_menu; ?>">

          <div class="form-group">
            <input type="submit" name="submit" value="Add Sub Module" class="btn btn-primary pull-right">
          </div>

        <?php echo form_close(); ?>

      </div>
    </div>
  </section>
</div>
</div>
