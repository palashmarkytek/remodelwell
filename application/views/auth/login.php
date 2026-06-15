<div class="form-background mk-login-page">
  <div class="login-box">
    <div class="login-logo">
      <h2>
        <a href="<?php echo base_url('admin'); ?>">
          <img src="<?php echo base_url('assets/img/logo.png') ?>">
        </a>
      </h2>
    </div>

    <div class="card">
      <div class="card-body login-card-body">

        <p class="login-box-msg">
          <?php echo trans('signin_to_start_your_session') ?>
        </p>

        <?php $this->load->view('includes/_messages.php') ?>

        <?php echo form_open(base_url('auth/login'), 'class="login-form"'); ?>

        <div class="form-group has-feedback">
          <input type="text" name="email" id="email" class="form-control" placeholder="<?php echo trans('email') ?>">
        </div>

        <div class="form-group has-feedback">
          <input type="password" name="password" id="password" class="form-control"
            placeholder="<?php echo trans('password') ?>">
        </div>

        <div class="row align-items-center">

          <div class="col-12 col-md-7">
            <div class="checkbox icheck mk-remember">
              <label>
                <input type="checkbox"> <?php echo trans('remember_me') ?>
              </label>
            </div>
          </div>

          <div class="col-12 col-md-5">
            <input type="submit" name="submit" id="submit" class="btn btn-primary btn-block btn-flat"
              value="<?php echo trans('signin') ?>">
          </div>
          <div class="col-12 mt-3 d-flex justify-content-between flex-wrap">
            <p class="mb-0">
              <a href="<?php echo base_url('auth/register') ?>">Create Account</a>
            </p>

            <!-- UPDATED: Added forgot-password link without changing the existing login form flow. -->
            <p class="mb-0">
              <a href="<?php echo base_url('auth/forget-password') ?>">Forgot Password?</a>
            </p>
          </div>
        </div>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>