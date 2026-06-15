<div class="mk-dashboard">
<div class="content-wrapper">
  <section class="content">

    <div class="card">
      <div class="card-body py-2 d-flex align-items-center">
        <h5 class="mb-0">
          <i class="fa fa-plus"></i> <?= $title ?>
        </h5>

        <a href="<?= base_url('admin'); ?>" class="btn btn-success btn-sm py-0 px-1 ml-auto">
          <i class="fa fa-list"></i> Associates List
        </a>
      </div>

      <div class="card-body p-2">
        <?php $this->load->view('includes/_messages.php') ?>

        <?php echo form_open(base_url('admin/add'), 'class="form-horizontal"'); ?>

          <div class="form-row">
            <div class="form-group col-12 col-md-6">
              <label for="admin_role_id" class="small mb-1"><?= trans('select_admin_role') ?> <span class="text-danger">*</span></label>
              <select name="admin_role_id" class="form-control form-control-sm" id="admin_role_id">
                <option value="">Select Associate Type</option>
                <?php foreach ($admin_roles as $role): ?>
                  <option value="<?= $role['admin_role_id']; ?>" <?= ($role['admin_role_id'] == $insert['admin_role_id']) ? 'selected' : ''; ?>>
                    <?= $role['admin_role_title']; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>


          </div>

          <div class="form-row">
                        <div class="form-group col-12 col-md-6 vendor-source-field">
              <label for="source_id" class="small mb-1">Source Id <span class="text-danger vendor-source-required" style="display:none;">*</span></label>
              <input type="text"
                     value="<?= $insert['source_id']; ?>"
                     name="source_id"
                     class="form-control form-control-sm"
                     id="source_id"
                     placeholder="">
            </div>
            <div class="form-group col-12 col-md-6">
              <label for="name" class="small mb-1"><?= trans('name') ?> <span class="text-danger">*</span></label>
              <input type="text" value="<?= $insert['name']; ?>" name="name" class="form-control form-control-sm" id="name" placeholder="">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-12 col-md-6">
              <label for="mobile_no" class="small mb-1"><?= trans('mobile_no') ?>(What's App Number) <span class="text-danger">*</span></label>
              <input type="number" value="<?= $insert['mobile_no']; ?>" name="mobile_no" class="form-control form-control-sm" id="mobile_no" placeholder="">
            </div>

            <div class="form-group col-12 col-md-6">
              <label for="additional_mobile_no" class="small mb-1">Additional Mobile Number</label>
              <input type="text" value="<?= $insert['additional_mobile_no']; ?>" name="additional_mobile_no" class="form-control form-control-sm" id="additional_mobile_no" placeholder="">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-12 col-md-6">
              <label for="email" class="small mb-1"><?= trans('email') ?> <span class="text-danger">*</span></label>
              <input type="email" value="<?= $insert['email']; ?>" name="email" class="form-control form-control-sm" id="email" placeholder="">
            </div>

            <div class="form-group col-12 col-md-6">
              <label for="address" class="small mb-1">Address <span class="text-danger">*</span></label>
              <textarea name="address" class="form-control form-control-sm" id="address" rows="2"><?= $insert['address']; ?></textarea>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-12 col-md-6">
              <label for="password" class="small mb-1"><?= trans('password') ?> <span class="text-danger">*</span></label>
              <input type="password" value="<?= $insert['password']; ?>" name="password" class="form-control form-control-sm" id="password" placeholder="">
            </div>

            <div class="form-group col-12 col-md-6">
              <label for="cpassword" class="small mb-1">Confirm <?= trans('password') ?> <span class="text-danger">*</span></label>
              <input type="password" value="" name="cpassword" class="form-control form-control-sm" id="cpassword" placeholder="">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-12 text-right">
              <input type="submit" name="submit" value="<?= $title ?>" class="btn btn-primary btn-sm py-0 px-1">
            </div>
          </div>

        <?php echo form_close(); ?>
      </div>
    </div>

  </section>
</div>
</div>

<script>
  // Source Id is enabled and required only for Vendor role (admin_role_id = 2).
  function toggleVendorSourceField() {
    var isVendor = $('#admin_role_id').val() === '2';
    $('#source_id').prop('disabled', !isVendor).prop('required', isVendor);
    $('.vendor-source-required').toggle(isVendor);
    if (!isVendor) {
      $('#source_id').val('');
    }
  }

  $(document).ready(function () {
    toggleVendorSourceField();
    $('#admin_role_id').on('change', function () {
      toggleVendorSourceField();
    });
  });
</script>
