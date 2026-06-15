<div class="mk-dashboard">
<div class="content-wrapper">
  <section class="content">

    <div class="card">
      <!-- Header (like Unit edit.php) -->
      <div class="card-body py-2 d-flex align-items-center">
        <h5 class="mb-0">
          <i class="fa fa-plus"></i> <?= $title ?>
        </h5>
      </div>

      <div class="card-body p-2">

        <!-- Messages -->
        <?php $this->load->view('includes/_messages.php') ?>

        <?php echo form_open_multipart(base_url('general_settings/update_acknowledgement')); ?>

          <!-- Textarea 1 -->
          <div class="form-row">
            <div class="form-group col-12">
              <label for="acknowledgement_letter" class="small mb-1">Engagement Letter</label>
              <textarea id="acknowledgement_letter"
                        name="acknowledgement_letter"
                        class="form-control form-control-sm"
                        rows="18"></textarea>
              <small class="text-muted small">Engagement letter template</small>
            </div>
          </div>

          <!-- Textarea 2 -->
          <div class="form-row">
            <div class="form-group col-12">
              <label for="acknowledgement_text" class="small mb-1">Engagement Rules</label>
              <textarea id="acknowledgement_text"
                        name="acknowledgement_text"
                        class="form-control form-control-sm"
                        rows="18"><?= $acknowledgement['acknowledgement_text'] ?></textarea>
              <small class="text-muted small">Add engagement rule</small>
            </div>
          </div>

           <!-- Textarea 2 -->
          <div class="form-row">
            <div class="form-group col-12">
              <label for="regards" class="small mb-1">Regards</label>
              <textarea id="regards"
                        name="regards"
                        class="form-control form-control-sm"
                        rows="18"><?= $acknowledgement['regards'] ?></textarea>
              <small class="text-muted small">Add regards</small>
            </div>
          </div>

          <!-- Submit -->
          <div class="form-row">
            <div class="form-group col-12 text-right">
              <input type="submit"
                     name="submit"
                     value="Update Changes"
                     class="btn btn-primary btn-sm py-0 px-1">
            </div>
          </div>

        <?php echo form_close(); ?>

      </div>
    </div>

  </section>
</div>
</div>

<!-- CKEditor 5 -->
<script src="<?= base_url() ?>assets/editor/ckeditor.js"></script>

<script>
/* ===========================================================
   CKEditor: Engagement Letter (kept as-is)
   =========================================================== */
ClassicEditor
  .create(document.querySelector('#acknowledgement_letter'), {
    toolbar: {
      items: [
        'undo', 'redo', '|',
        'selectAll', '|',
        'heading', '|',
        'bold', 'italic', '|',
        'insertTable', '|',
        'bulletedList', 'numberedList', '|'
      ],
      shouldNotGroupWhenFull: true
    },
    fontFamily: { supportAllValues: true },
    fontSize: { options: [10, 12, 14, 'default', 18, 20, 22], supportAllValues: true },
    heading: {
      options: [
        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
        { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
        { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
      ]
    },
    initialData: '<?= $acknowledgement['acknowledgement_letter'] ?>',
    placeholder: 'Type or paste your content here!',
    table: { contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'] }
  })
  .catch(error => { console.error(error); });
</script>

<script>
/* ===========================================================
   CKEditor: Engagement Rules (kept as-is)
   =========================================================== */
ClassicEditor
  .create(document.querySelector('#acknowledgement_text'), {
    toolbar: {
      items: [
        'undo', 'redo', '|',
        'selectAll', '|',
        'heading', '|',
        'bold', 'italic', '|',
        'insertTable', '|',
        'bulletedList', 'numberedList', '|'
      ],
      shouldNotGroupWhenFull: true
    },
    fontFamily: { supportAllValues: true },
    fontSize: { options: [10, 12, 14, 'default', 18, 20, 22], supportAllValues: true },
    heading: {
      options: [
        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
        { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
        { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
      ]
    },
    initialData: '<?= $acknowledgement['acknowledgement_text'] ?>',
    placeholder: 'Type or paste your content here!',
    table: { contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'] }
  })
  .catch(error => { console.error(error); });
</script>

<script>
/* ===========================================================
   CKEditor: regards (kept as-is)
   =========================================================== */
ClassicEditor
  .create(document.querySelector('#regards'), {
    toolbar: {
      items: [
        'undo', 'redo', '|',
        'selectAll', '|',
        'heading', '|',
        'bold', 'italic', '|',
        'insertTable', '|',
        'bulletedList', 'numberedList', '|'
      ],
      shouldNotGroupWhenFull: true
    },
    fontFamily: { supportAllValues: true },
    fontSize: { options: [10, 12, 14, 'default', 18, 20, 22], supportAllValues: true },
    heading: {
      options: [
        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
        { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
        { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
      ]
    },
    initialData: '<?= $acknowledgement['regards'] ?>',
    placeholder: 'Type or paste your content here!',
    table: { contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'] }
  })
  .catch(error => { console.error(error); });
</script>