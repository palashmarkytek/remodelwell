<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css">

<div class="mk-dashboard">
<div class="content-wrapper">
  <section class="content">

    <!-- Messages -->
    <?php $this->load->view('includes/_messages.php') ?>

    <!-- Header + Filters (like Unit index.php) -->
    <div class="card mb-2">
      <div class="card-body py-2 d-flex align-items-center">
        <h5 class="mb-0">
          <i class="fa fa-list"></i> Advertisers List
        </h5>

      </div>

      <div class="card-body py-2">
        <?php echo form_open("/", 'class="filterdata form-inline"') ?>
          <div class="form-row w-100">
            <!-- Role -->
            <div class="form-group col-12 col-md-3 pr-md-1 mb-1">
              <select name="type" class="form-control form-control-sm w-100" onchange="filter_data()">
                <option value="">Advertiser Role</option>
                <?php foreach ($admin_roles as $admin_role): ?>
                  <option value="<?= $admin_role['admin_role_id'] ?>">
                    <?= $admin_role['admin_role_title'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Status -->
            <div class="form-group col-12 col-md-2 pr-md-1 mb-1">
              <select name="status" class="form-control form-control-sm w-100" onchange="filter_data()">
                <option value=""><?= trans('all_status') ?></option>
                <option value="1"><?= trans('active') ?></option>
                <option value="0"><?= trans('inactive') ?></option>
              </select>
            </div>

            <!-- Keyword -->
            <div class="form-group col-12 col-md-4 mb-1">
              <input type="text"
                     name="keyword"
                     class="form-control form-control-sm w-100"
                     placeholder="<?= trans('search_from_here') ?>..."
                     onkeyup="filter_data()" />
            </div>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

  </section>

  <!-- Data Container (like Unit index.php) -->
  <section class="content mt-1">
    <div class="card">
      <div class="card-body p-2">
        <!-- Load Admin list (html view loaded via AJAX) -->
        <div class="data_container"></div>
      </div>
    </div>
  </section>
</div>
</div>


<!-- UPDATED: Leadspedia user and contract creation progress modal. -->
<div class="modal fade" id="leadspediaProgressModal" tabindex="-1" role="dialog" aria-labelledby="leadspediaProgressModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title" id="leadspediaProgressModalLabel">Creating Leadspedia Setup</h5>
        <button type="button" class="close leadspedia-modal-close" data-dismiss="modal" aria-label="Close" disabled>
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-3">
          <div class="spinner-border text-primary" id="leadspedia-main-loader" role="status">
            <span class="sr-only">Loading...</span>
          </div>
          <div class="mt-2 font-weight-bold" id="leadspedia-current-status">Preparing Leadspedia setup...</div>
        </div>

        <!-- UPDATED: Every API progress result is shown below the loader. -->
        <ul class="list-group list-group-flush small" id="leadspedia-progress-list"></ul>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-secondary btn-sm leadspedia-modal-close" data-dismiss="modal" disabled>Close</button>
      </div>
    </div>
  </div>
</div>

<!-- DataTables -->
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>

<script>
  /* ===========================================================
     Advertisers List JS (keeps existing flow)
     - filter_data(): POST filter inputs -> reload list_data -> init DataTable
     - load_records(): initial load -> init DataTable
     - load_table(): DataTable init aligned with Unit index.php settings
     - status toggle: unchanged
     =========================================================== */

  // Debounce timer (same idea as Unit index.php)
  let advertiserTimer;

  //------------------------------------------------------------------
  function filter_data() {
    clearTimeout(advertiserTimer);

    // Show loading while filtering/loading
    $('.data_container').html(
      '<div class="text-center py-3"><img src="<?= base_url('assets/dist/img') ?>/loading.png"/></div>'
    );

    // Debounced reload (UI improvement; same logic/endpoints)
    advertiserTimer = setTimeout(function () {
      $.post('<?= base_url('admin/filterdata') ?>', $('.filterdata').serialize(), function () {
        $('.data_container').load('<?= base_url('admin/list_data') ?>', function () {
          load_table();
        });
      });
    }, 350);
  }

  //------------------------------------------------------------------
  function load_records() {
    // Show loading for initial load
    $('.data_container').html(
      '<div class="text-center py-3"><img src="<?= base_url('assets/dist/img') ?>/loading.png"/></div>'
    );

    // Initial list load
    $('.data_container').load('<?= base_url('admin/list_data') ?>', function () {
      load_table();
    });
  }
  load_records();

  //------------------------------------------------------------------
  function load_table() {
    // NOTE: #associate_list table is assumed to be inside admin/list_data view
    // Align DataTable settings with Unit index.php:
    // dom 'lrtip', pageLength 100, no lengthChange, scrollY 50vh, scrollCollapse, order by 2nd column
    setTimeout(function () {

      // Prevent "Cannot reinitialise DataTable"
      if ($.fn.DataTable.isDataTable('#associate_list')) {
        $('#associate_list').DataTable().destroy();
      }

      $('#associate_list').DataTable({
        "dom": 'lrtip',
        "pageLength": 100,
        "autoWidth": false,
        "lengthChange": false,
        "scrollCollapse": true,
        "order": [[1, 'asc']],
        "stateSave": true // kept from your existing code (no behavior loss)
      });

    }, 500);
  }

  //---------------------------------------------------------------------
  // UPDATED: Create the Leadspedia advertiser/contact first, then create
  // the assigned vertical contract. Progress is displayed in the modal.
  let activeLeadspediaButton = null;

  function resetLeadspediaProgress() {
    $('#leadspedia-progress-list').empty();
    $('#leadspedia-main-loader').removeClass('d-none');
    $('#leadspedia-current-status').removeClass('text-success text-danger').text('Preparing Leadspedia setup...');
    $('.leadspedia-modal-close').prop('disabled', true);
  }

  function setLeadspediaCurrentStatus(message) {
    $('#leadspedia-current-status').removeClass('text-success text-danger').text(message);
  }

  function addLeadspediaProgressStep(step) {
    var iconClass = 'fa-clock-o text-muted';
    if (step.status === 'success') {
      iconClass = 'fa-check-circle text-success';
    } else if (step.status === 'error') {
      iconClass = 'fa-times-circle text-danger';
    } else if (step.status === 'skipped') {
      iconClass = 'fa-forward text-warning';
    }

    var item = $('<li class="list-group-item px-0 py-2 d-flex align-items-start"></li>');
    item.append($('<i class="fa mr-2 mt-1"></i>').addClass(iconClass));

    var content = $('<div></div>');
    content.append($('<div class="font-weight-bold"></div>').text(step.label || 'Leadspedia step'));
    if (step.message) {
      content.append($('<div class="text-muted"></div>').text(step.message));
    }

    item.append(content);
    $('#leadspedia-progress-list').append(item);
  }

  function addLeadspediaResponseSteps(response) {
    if (response && Array.isArray(response.steps)) {
      response.steps.forEach(function (step) {
        addLeadspediaProgressStep(step);
      });
    }
  }

  function resetLeadspediaButton() {
    if (!activeLeadspediaButton) {
      return;
    }

    activeLeadspediaButton.prop('disabled', false);
    activeLeadspediaButton.find('.leadspedia-btn-icon').removeClass('d-none');
    activeLeadspediaButton.find('.leadspedia-btn-loader').addClass('d-none');
  }

  function failLeadspediaSetup(message) {
    $('#leadspedia-main-loader').addClass('d-none');
    $('#leadspedia-current-status').addClass('text-danger').text(message || 'Leadspedia setup failed.');
    $('.leadspedia-modal-close').prop('disabled', false);
    resetLeadspediaButton();
  }

  function completeLeadspediaSetup(message) {
    $('#leadspedia-main-loader').addClass('d-none');
    $('#leadspedia-current-status').addClass('text-success').text(message || 'Leadspedia setup completed successfully.');

    // UPDATED: Close the modal automatically only after the full workflow completes.
    setTimeout(function () {
      $('#leadspediaProgressModal').modal('hide');
      load_records();
      activeLeadspediaButton = null;
    }, 1200);
  }

  function createLeadspediaContract(adminId, requestData) {
    setLeadspediaCurrentStatus('Creating vertical contract, filters, and delivery schedule...');

    $.ajax({
      url: '<?= base_url("admin/create_leadspedia_contract/") ?>' + adminId,
      type: 'POST',
      dataType: 'json',
      data: requestData,
      success: function (response) {
        addLeadspediaResponseSteps(response);

        if (response.status) {
          completeLeadspediaSetup(response.message);
        } else {
          failLeadspediaSetup(response.message);
        }
      },
      error: function (xhr) {
        var message = 'Unable to create the Leadspedia contract. Please try again.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          message = xhr.responseJSON.message;
        }
        failLeadspediaSetup(message);
      }
    });
  }

  $("body").on("click", ".create-leadspedia-user", function () {
    var button = $(this);
    var adminId = button.data('id');

    if (button.prop('disabled')) {
      return;
    }

    activeLeadspediaButton = button;
    button.prop('disabled', true);
    button.find('.leadspedia-btn-icon').addClass('d-none');
    button.find('.leadspedia-btn-loader').removeClass('d-none');

    resetLeadspediaProgress();
    $('#leadspediaProgressModal').modal('show');
    setLeadspediaCurrentStatus('Creating Leadspedia advertiser and contact...');

    // UPDATED: Use the existing CodeIgniter CSRF token for both sequential calls.
    var requestData = {
      '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
    };

    $.ajax({
      url: '<?= base_url("admin/create_leadspedia_user/") ?>' + adminId,
      type: 'POST',
      dataType: 'json',
      data: requestData,
      success: function (response) {
        addLeadspediaResponseSteps(response);

        if (!response.status) {
          failLeadspediaSetup(response.message);
          return;
        }

        // UPDATED: Contract API workflow starts only after user setup succeeds.
        createLeadspediaContract(adminId, requestData);
      },
      error: function (xhr) {
        var message = 'Unable to create the Leadspedia user. Please try again.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          message = xhr.responseJSON.message;
        }
        failLeadspediaSetup(message);
      }
    });
  });

  //---------------------------------------------------------------------
  // Status toggle (unchanged)
  $("body").on("change", ".tgl_checkbox", function () {
    $.post('<?= base_url("admin/change_status") ?>',
      {
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>',
        id: $(this).data('id'),
        status: $(this).is(':checked') ? 1 : 0
      },
      function () {
        $.notify("Status Changed Successfully", "success");
      }
    );
  });
</script>