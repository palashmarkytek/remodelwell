<!-- Content Wrapper. Contains page content -->
<div class="mk-dashboard">
    <div class="content-wrapper">
      <section class="content">

        <div class="card">
          <!-- Header (like Unit edit.php) -->
          <div class="card-body py-2 d-flex align-items-center">
            <h5 class="mb-0">
              <i class="fa fa-pencil"></i> <?= trans('change_password') ?>
            </h5>
          </div>

          <div class="card-body p-2">
            <!-- Messages -->
            <?php $this->load->view('includes/_messages.php') ?>

            <?php echo form_open(base_url('profile/change_pwd'), 'class="form-horizontal" id="changePasswordForm"'); ?>

              <!-- Current Password -->
              <div class="form-row">
                <div class="form-group col-12 col-md-4">
                  <label for="cpassword" class="small mb-1">Current Password</label>
                  <input type="password"
                         name="cpassword"
                         class="form-control form-control-sm"
                         id="cpassword"
                         required>
                </div>


                <div class="form-group col-12 col-md-4">
                  <label for="password" class="small mb-1">New Password</label>
                  <input type="password"
                         name="password"
                         class="form-control form-control-sm"
                         id="password"
                         required>

                  <small id="passwordHelp" class="form-text text-muted small">
                    Password must be at least 8 characters, include uppercase, lowercase, number, and a special character.
                  </small>

                  <div id="passwordStrength" class="mt-1 font-weight-bold small"></div>
                </div>

                <div class="form-group col-12 col-md-4">
                  <label for="confirm_pwd" class="small mb-1">Confirm Password</label>
                  <input type="password"
                         name="confirm_pwd"
                         class="form-control form-control-sm"
                         id="confirm_pwd"
                         required>
                  <div id="matchMsg" class="mt-1 small"></div>
                </div>
              </div>


              <!-- Submit -->
              <div class="form-row">
                <div class="form-group col-12 text-right">
                  <button type="submit"
                          name="submit"
                          class="btn btn-info btn-sm py-0 px-1"
                          value="<?= trans('change_password') ?>">
                    <?= trans('change_password') ?>
                  </button>
                </div>
              </div>

            <?php echo form_close(); ?>
          </div>

        </div>

      </section>
    </div>
</div>

<script>
/* ===========================================================
   Password Strength + Match Validation (kept as-is)
   =========================================================== */
$(function () {
    const strengthDisplay = $('#passwordStrength');
    const matchMsg = $('#matchMsg');

    function checkStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;

        if (strength <= 2) {
            strengthDisplay.text("Weak password").css("color", "red");
        } else if (strength <= 4) {
            strengthDisplay.text("Medium strength").css("color", "orange");
        } else {
            strengthDisplay.text("Strong password").css("color", "green");
        }
    }

    $('#password').on('keyup', function () {
        const password = $(this).val();
        checkStrength(password);
    });

    $('#confirm_pwd').on('keyup', function () {
        const confirm = $(this).val();
        const original = $('#password').val();
        if (confirm !== original) {
            matchMsg.text("Passwords do not match").css("color", "red");
        } else {
            matchMsg.text("Passwords match").css("color", "green");
        }
    });

    $('#changePasswordForm').on('submit', function (e) {
        const pwd = $('#password').val();
        const confirm = $('#confirm_pwd').val();
        if (pwd !== confirm) {
            e.preventDefault();
            matchMsg.text("Passwords do not match").css("color", "red");
        }
    });
});
</script>