<div class="form-background mk-login-page">
  <div class="login-box">
    <div class="login-logo">
      <h2>
        <a href="<?= base_url('admin'); ?>">
          <img src="<?= base_url('assets/img/logo.png') ?>">
        </a>
      </h2>
    </div>

    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Create a new password</p>
        <p class="text-muted text-center">
          Use at least 8 characters with uppercase, lowercase, number and special character.
        </p>

        <?php $this->load->view('includes/_messages.php') ?>

        <!-- UPDATED: Reset code remains in the URL and is validated by the controller before save. -->
        <?php echo form_open(base_url('auth/reset-password/' . rawurlencode($reset_code)), 'class="login-form"'); ?>
          <div class="form-group has-feedback">
            <input type="password" name="password" id="password" class="form-control"
              placeholder="New Password" minlength="8" required>
          </div>

          <div class="form-group has-feedback">
            <input type="password" name="confirm_password" id="confirm_password" class="form-control"
              placeholder="Confirm New Password" minlength="8" required>
          </div>

          <div class="row">
            <div class="col-12">
              <input type="submit" name="submit" id="submit"
                class="btn btn-primary btn-block btn-flat" value="Reset Password">
            </div>
          </div>
        <?php echo form_close(); ?>

        <p class="mt-3 mb-0 text-center">
          <a href="<?= base_url('auth/login'); ?>">Back to Login</a>
        </p>
      </div>
    </div>
  </div>
</div>
