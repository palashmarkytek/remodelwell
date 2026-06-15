<!-- bootstrap wysihtml5 - text editor -->
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<div class="content-wrapper">
  <section class="content">

    <!-- Messages -->
    <?php $this->load->view('includes/_messages.php') ?>

    <div class="card mb-2">

      <!-- Header (like Unit index.php) -->
      <div class="card-body py-2 d-flex align-items-center">
        <h5 class="mb-0">
          <i class="fa fa-list"></i>&nbsp; <?= trans('email_template_settings') ?>
        </h5>
      </div>

      <div class="card-body p-2">
        <div class="row">

          <!-- Left: Templates list -->
          <div class="col-12 col-md-3 mb-2 mb-md-0">
            <table class="table table-bordered table-hover table-sm mb-0 text-center templates-table">
              <thead class="thead-light small">
                <tr>
                  <th><?= trans('email_templates') ?></th>
                </tr>
              </thead>
              <tbody class="small">
                <?php foreach($templates as $row): ?>
                  <tr>
                    <td class="btn-template-link cursor-pointer" data-type="<?= $row['id'] ?>">
                      <span class="btn-template-link" data-type="<?= $row['id'] ?>"><?= $row['name'] ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <!-- Right: Template editor -->
          <div class="col-12 col-md-9 template-wrapper">

            <div class="template-body empty-template text-center py-4">
              <p class="mb-0"><?= trans('select_a_template') ?></p>
            </div>

            <!-- form start -->
            <?php echo validation_errors(); ?>
            <?php echo form_open(base_url('general_settings/email_templates'), 'class="form-horizontal template-form"');  ?>

              <div class="template-body non-empty-template hidden">
                <div class="form-row">

                  <div class="form-group col-12">
                    <label class="small mb-1"><?= trans('title') ?></label>
                    <input type="text" name="subject" class="form-control form-control-sm" placeholder="Email Subject">
                  </div>

                  <div class="form-group col-12">
                    <textarea name="content" class="textarea form-control form-control-sm" rows="10"></textarea>
                  </div>

                  <div class="form-group col-12">
                    <label class="small mb-1"><?= trans('variables') ?></label>
                    <input type="text" name="variables" class="form-control form-control-sm" placeholder="Template's Variables" disabled>
                  </div>

                  <div class="form-group col-12 text-right">
                    <input type="hidden" name="template_id">

                    <input type="submit"
                           name="submit"
                           value="<?= trans('save_changes') ?>"
                           class="btn btn-primary btn-sm py-0 px-1">

                    <input type="button"
                           value="<?= trans('preview') ?>"
                           class="btn btn-warning btn-sm py-0 px-1 mr-1"
                           id="btn_preview_email">
                  </div>

                </div>
              </div>

            <?php echo form_close(); ?>

          </div><!-- /.template-wrapper -->

        </div><!-- /.row -->
      </div><!-- /.card-body -->
    </div><!-- /.card -->

  </section>
</div>

<!-- Bootstrap WYSIHTML5 -->
<script src="<?= base_url() ?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

<script>
/* ===========================================================
   Existing JS (kept same logic)
   =========================================================== */
$(function () {
  // bootstrap WYSIHTML5 - text editor
  $('.textarea').wysihtml5({
    toolbar: { fa: true }
  });

  // get email template content
  $('.btn-template-link').on('click', function(){
    $this = $(this);
    $('.empty-template').addClass('hidden');
    $('.non-empty-template').removeClass('hidden');

    $.post('<?=base_url("general_settings/get_email_template_content_by_id")?>',
    {
      '<?= $this->security->get_csrf_token_name(); ?>' : '<?= $this->security->get_csrf_hash(); ?>',
      template_id : $this.data('type'),
    },
    function(data){
      obj = JSON.parse(data);
      template = obj['template'];
      variables = obj['variables'];

      $('input[name=subject]').val(template.subject);
      $('input[name=template_id]').val(template.id);
      $('input[name=variables]').val(variables);
      $('iframe').contents().find('.wysihtml5-editor').html(template.body);
    });
  });

  // update email template content
  $('.template-form').on('submit', function(){
    event.preventDefault();
    $.post('<?=base_url("general_settings/email_templates")?>',
    {
      '<?= $this->security->get_csrf_token_name(); ?>' : '<?= $this->security->get_csrf_hash(); ?>',
      id : $('input[name=template_id]').val(),
      subject : $('input[name=subject]').val(),
      content : $('iframe').contents().find('.wysihtml5-editor').html(),
    },
    function(){
      $.notify("Template Updated Successfully", "success");
    });
  });

  // Preview Email
  $('#btn_preview_email').on('click', function(){
    $.post('<?=base_url("general_settings/email_preview")?>',
    {
      '<?= $this->security->get_csrf_token_name(); ?>' : '<?= $this->security->get_csrf_hash(); ?>',
      head : $('input[name=subject]').val(),
      content : $('.textarea').val(),
    },
    function(data){
      var w = window.open();
      w.document.open();
      w.document.write(data);
      w.document.close();
    });
  });
});
</script>

<script>
  $("#setting").addClass('active');
</script>