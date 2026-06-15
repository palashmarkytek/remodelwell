<div class="form-background">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8 col-12">
        <div class="card shadow-sm">
          <div class="card-header bg-success text-white">
            <h4 class="mb-0">Sign Up Completed</h4>
          </div>
          <div class="card-body">
            <div class="alert alert-success">
              Your account was created successfully. Save these login details securely.
            </div>

            <div class="form-group">
              <label class="small mb-1">Login Username</label>
              <input type="text" class="form-control" value="<?php echo html_escape($credentials['email']); ?>" readonly>
            </div>

            <div class="form-group">
              <label class="small mb-1">Password</label>
              <div class="input-group">
                <input type="text" id="generatedPassword" class="form-control" value="<?php echo html_escape($credentials['password']); ?>" readonly>
                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-secondary" id="copyPassword">Copy</button>
                </div>
              </div>
              <small class="text-danger">This password is displayed only once. Please copy and store it safely.</small>
            </div>

            <a href="<?php echo base_url('auth/login'); ?>" class="btn btn-primary btn-block">Continue to Login</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  $('#copyPassword').on('click', function () {
    var passwordField = document.getElementById('generatedPassword');
    passwordField.select();
    passwordField.setSelectionRange(0, 99999);
    document.execCommand('copy');
    $(this).text('Copied');
  });
})();
</script>
