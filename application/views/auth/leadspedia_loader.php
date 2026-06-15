<!-- UPDATED: First-login Leadspedia processing screen. -->
<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-body text-center py-5">
            <div class="mb-4">
                <i class="fa fa-spinner fa-spin fa-4x text-primary"></i>
            </div>
            <h4 class="mb-2">Setting up your account</h4>
            <p class="text-muted mb-0" id="leadspedia-status">
                Please do not close this page while your Leadspedia account is being created.
            </p>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // UPDATED: Trigger the API only after this authenticated loader page opens.
    $.ajax({
        url: '<?= base_url('auth/process-leadspedia'); ?>',
        type: 'POST',
        // UPDATED: Include the existing CodeIgniter CSRF token in the AJAX request.
        data: {
            '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function (response) {
            $('#leadspedia-status').text(response.message || 'Setup completed. Redirecting...');
            window.location.href = response.redirect_url || '<?= base_url('dashboard'); ?>';
        },
        error: function () {
            // The local login must continue even when an unexpected AJAX error occurs.
            $('#leadspedia-status').text('Setup request finished. Redirecting to dashboard...');
            window.location.href = '<?= base_url('dashboard'); ?>';
        }
    });
});
</script>
