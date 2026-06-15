<?php if (!isset($footer)): ?>

 <footer class="main-footer mk-footer">
    <div class="mk-footer-inner">
        <div class="mk-footer-left">
            <?= $this->general_settings['application_name']; ?>
        </div>

        <div class="mk-footer-right">
            <?= $this->general_settings['copyright']; ?>
        </div>
    </div>
</footer>

<?php endif; ?>

</div>
<!-- ./wrapper -->


<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Slimscroll -->
<script src="<?= base_url() ?>assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?= base_url() ?>assets/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url() ?>assets/dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?= base_url() ?>assets/dist/js/demo.js"></script>
<!-- Notify JS -->
<script src="<?= base_url() ?>assets/plugins/notify/notify.min.js"></script>
<!-- DROPZONE -->
<script src="<?= base_url() ?>assets/plugins/dropzone/dropzone.js" type="text/javascript"></script>

<script>
function chnageFinancialYear(value) {
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrf_token_value = '<?php echo $this->security->get_csrf_hash(); ?>';

    var data = {
        year: value
    };
    data[csrf_token_name] = csrf_token_value;

    $.ajax({
        type: "POST",
        url: "<?= base_url('auth/set_year') ?>",
        data: data,
        dataType: "json",
        success: function(obj) {
            if (obj.status === 'success') {
                window.location.reload();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error setting financial year: ", textStatus, errorThrown);
        }
    });
}
</script>

<!-- =========================================
</body> ER AGE ADD KORBEN
========================================= -->

<script>

const mkMenuBtn = document.getElementById('mkMenuBtn');
const mkCloseBtn = document.getElementById('mkCloseBtn');
const mkSidebar = document.getElementById('mkSidebar');
const mkOverlay = document.getElementById('mkOverlay');

/* Sidebar Open */
mkMenuBtn.addEventListener('click', function(){

  mkSidebar.classList.add('active');
  mkOverlay.classList.add('active');

});

/* Sidebar Close */
mkCloseBtn.addEventListener('click', function(){

  mkSidebar.classList.remove('active');
  mkOverlay.classList.remove('active');

});

/* Overlay Close */
mkOverlay.addEventListener('click', function(){

  mkSidebar.classList.remove('active');
  mkOverlay.classList.remove('active');

});

/* Dropdown */
document.querySelectorAll('.mk-dropdown-btn').forEach(function(btn){

  btn.addEventListener('click', function(){

    this.parentElement.classList.toggle('active');

  });

});

</script>
<style>
  .ck-content{
    height: 300px !important;
  }
</style>
</body>

</html>