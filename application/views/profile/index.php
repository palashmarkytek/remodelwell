<!-- Content Wrapper. Contains page content -->
<div class="mk-dashboard">
  <div class="content-wrapper">
    <section class="content">

      <div class="card">
        <!-- Header (like Unit edit.php) -->
        <div class="card-body py-2 d-flex align-items-center">
          <h5 class="mb-0">
            <i class="fa fa-pencil"></i> <?= trans('profile') ?>
          </h5>

          <a href="<?= base_url('profile/change_pwd'); ?>"
             class="btn btn-success btn-sm py-0 px-1 ml-auto">
            <i class="fa fa-list"></i> <?= trans('change_password') ?>
          </a>
        </div>

        <div class="card-body p-2">
          <!-- Messages -->
          <?php $this->load->view('includes/_messages.php') ?>

          <?php echo form_open(base_url('profile'), 'class="form-horizontal"' )?>

            <!-- Name -->
            <div class="form-row">
              <div class="form-group col-12 col-md-4">
                <label for="name" class="small mb-1"><?= trans('name') ?></label>
                <input type="text"
                       name="name"
                       value="<?= $admin['name']; ?>"
                       class="form-control form-control-sm"
                       id="name"
                       placeholder="">
              </div>
              <div class="form-group col-12 col-md-4">
                <label for="email" class="small mb-1"><?= trans('email') ?></label>
                <input type="email"
                       readonly
                       name="email"
                       value="<?= $admin['email']; ?>"
                       class="form-control form-control-sm"
                       id="email"
                       placeholder="">
              </div>

              <div class="form-group col-12 col-md-4">
                <label for="mobile_no" class="small mb-1"><?= trans('mobile_no') ?></label>
                <input type="number"
                       name="mobile_no"
                       value="<?= $admin['mobile_no']; ?>"
                       class="form-control form-control-sm"
                       id="mobile_no"
                       placeholder="">
              </div>
            </div>

            <!-- UPDATED: Leadspedia details are informational and cannot be edited. -->
            <?php if (!empty($leadspedia)): ?>
              <hr>
              <h6 class="mb-3"><i class="fa fa-external-link"></i> Leadspedia Details</h6>

              <div class="form-row">
                <div class="form-group col-12 col-md-4">
                  <label class="small mb-1">Company</label>
                  <input type="text" readonly class="form-control form-control-sm"
                         value="<?= html_escape($leadspedia['company_name']); ?>">
                </div>
                <div class="form-group col-12 col-md-4">
                  <label class="small mb-1">API Status</label>
                  <input type="text" readonly class="form-control form-control-sm"
                         value="<?= html_escape(ucfirst($leadspedia['leadspedia_status'])); ?>">
                </div>
                <div class="form-group col-12 col-md-4">
                  <label class="small mb-1">HTTP Code</label>
                  <input type="text" readonly class="form-control form-control-sm"
                         value="<?= html_escape($leadspedia['leadspedia_http_code']); ?>">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-12 col-md-6">
                  <label class="small mb-1">Leadspedia Request</label>
                  <textarea readonly rows="6" class="form-control form-control-sm"><?= html_escape($leadspedia['leadspedia_request']); ?></textarea>
                </div>
                <div class="form-group col-12 col-md-6">
                  <label class="small mb-1">Leadspedia Response</label>
                  <textarea readonly rows="6" class="form-control form-control-sm"><?= html_escape($leadspedia['leadspedia_response']); ?></textarea>
                </div>
              </div>
            <?php endif; ?>

            <!-- Submit -->
            <div class="form-row">
              <div class="form-group col-12 text-right">
                <input type="submit"
                       name="submit"
                       value="<?= trans('update_profile') ?>"
                       class="btn btn-info btn-sm py-0 px-1">
              </div>
            </div>

          <?php echo form_close(); ?>
        </div>

      </div>

    </section>
  </div>
</div>