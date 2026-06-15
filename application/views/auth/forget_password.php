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
        <p class="login-box-msg">Forgot your password?</p>
        <p class="text-muted text-center">
          Enter your registered email address. We will send you a secure password reset link.
        </p>

        <?php $this->load->view('includes/_messages.php') ?>

        <!-- UPDATED: Form now posts to the requested auth/forget-password route. -->
        <?php echo form_open(base_url('auth/forget-password'), 'class="login-form"'); ?>
          <div class="form-group has-feedback">
            <input type="email" name="email" id="email" class="form-control"
              value="<?= html_escape(set_value('email')); ?>"
              placeholder="<?= trans('email') ?>" required>
          </div>

          <div class="row">
            <div class="col-12">
              <input type="submit" name="submit" id="submit"
                class="btn btn-primary btn-block btn-flat" value="Send Reset Link">
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
