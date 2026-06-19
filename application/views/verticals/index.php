<!-- UPDATED: New Leadspedia Vertical listing page using the existing card/table page style. -->
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css">

<div class="mk-dashboard">
<div class="content-wrapper">
  <section class="content">
    <?php $this->load->view('includes/_messages.php') ?>

    <div id="vertical-message"></div>

    <div class="card mb-2">
      <div class="card-body py-2 d-flex align-items-center justify-content-between">
        <h5 class="mb-0"><i class="fa fa-list"></i> Verticals List</h5>

        <!-- UPDATED: Sync button follows the top-right action-button pattern used by other pages. -->
        <button type="button" id="add-vertical-btn" class="btn btn-sm btn-primary">
          <i class="fa fa-plus"></i> Add Vertical
        </button>
      </div>
    </div>

    <div class="card">
      <div class="card-body p-2">
        <div class="table-responsive">
          <table id="vertical-list" class="table table-bordered table-striped table-sm mb-0">
            <thead>
              <tr>
                <th width="70">#</th>
                <th>Vertical ID</th>
                <th>Vertical Name</th>
                <!-- UPDATED: Inline editable local price column. -->
                <th width="140">Price</th>
                <!-- UPDATED: Display Leadspedia group name and offer count. -->
                <th>Group Name</th>
                <th>Total Offers</th>
                <th>Leadspedia Status</th>
                <th>Added On</th>
                <!-- UPDATED: Local active/inactive action, matching other listing pages. -->
                <th width="90" class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($verticals as $key => $vertical): ?>
                <tr>
                  <td><?= $key + 1 ?></td>
                  <td><?= html_escape($vertical['vertical_id']) ?></td>
                  <td><?= html_escape($vertical['vertical_name']) ?></td>
                  <!-- UPDATED: Price becomes an inline input when the pen icon is clicked. -->
                  <td>
                    <span class="vertical-price-wrapper" data-id="<?= (int) $vertical['id'] ?>">
                      <span class="vertical-price-value"><?= number_format((float) ($vertical['price'] ?? 0), 2, '.', '') ?></span>
                      <button type="button" class="btn btn-link btn-sm p-0 ml-1 edit-vertical-price" title="Edit Price">
                        <i class="fa fa-edit"></i>
                      </button>
                    </span>
                  </td>
                  <!-- UPDATED: New Leadspedia metadata columns. -->
                  <td><?= html_escape(!empty($vertical['group_name']) ? $vertical['group_name'] : '-') ?></td>
                  <td><?= isset($vertical['total_offers']) ? (int) $vertical['total_offers'] : 0 ?></td>
                  <td><?= html_escape($vertical['status'] !== '' ? $vertical['status'] : '-') ?></td>
                  <td><?= html_escape($vertical['created_at']) ?></td>
                  <td class="text-center">
                    <!-- UPDATED: Active/inactive switch uses the existing listing-page UI pattern. -->
                    <label class="switch-xs mb-0" title="Active / Inactive">
                      <input type="checkbox" class="vertical-status-toggle"
                        data-id="<?= (int) $vertical['id'] ?>"
                        <?= ((int) $vertical['is_active'] === 1) ? 'checked' : '' ?>>
                      <span class="slider-xs"></span>
                    </label>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>
</div>

<!-- UPDATED: Loader modal remains open while Leadspedia sync is running. -->
<div class="modal fade" id="vertical-loader-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-body text-center py-4">
        <img src="<?= base_url('assets/dist/img/loading.png') ?>" alt="Loading" class="mb-3">
        <h6 class="mb-1">Getting Verticals</h6>
        <p class="text-muted small mb-0">Please do not close this window.</p>
      </div>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>
<script>
$(function () {
  $('#vertical-list').DataTable({
    dom: 'lrtip',
    pageLength: 100,
    autoWidth: false,
    lengthChange: false,
    order: [[2, 'asc']]
  });

  // UPDATED: Fetch, save, close loader, show message, and refresh listing.
  $('#add-vertical-btn').on('click', function () {
    var $button = $(this);
    $button.prop('disabled', true);
    $('#vertical-loader-modal').modal('show');

    $.ajax({
      url: '<?= base_url('verticals/sync') ?>',
      type: 'POST',
      dataType: 'json',
      data: {
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      }
    }).done(function (response) {
      $('#vertical-loader-modal').modal('hide');
      var alertClass = response.status ? 'alert-success' : 'alert-danger';
      $('#vertical-message').html('<div class="alert ' + alertClass + '">' + $('<div>').text(response.message).html() + '</div>');

      if (response.status) {
        setTimeout(function () { window.location.reload(); }, 900);
      }
    }).fail(function () {
      $('#vertical-loader-modal').modal('hide');
      $('#vertical-message').html('<div class="alert alert-danger">Unable to complete the Leadspedia vertical sync.</div>');
    }).always(function () {
      $button.prop('disabled', false);
    });
  });

  // UPDATED: Change only the local active/inactive flag and keep all synced data unchanged.
  $('body').on('change', '.vertical-status-toggle', function () {
    var $toggle = $(this);
    var newStatus = $toggle.is(':checked') ? 1 : 0;

    $toggle.prop('disabled', true);

    $.ajax({
      url: '<?= base_url('verticals/change_status') ?>',
      type: 'POST',
      dataType: 'json',
      data: {
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>',
        id: $toggle.data('id'),
        status: newStatus
      }
    }).done(function (response) {
      if (response.status) {
        $.notify(response.message, 'success');
        return;
      }

      $toggle.prop('checked', !newStatus);
      $.notify(response.message || 'Unable to change vertical status.', 'error');
    }).fail(function () {
      $toggle.prop('checked', !newStatus);
      $.notify('Unable to change vertical status.', 'error');
    }).always(function () {
      $toggle.prop('disabled', false);
    });
  });

  // UPDATED: Convert the price display into an inline input on pen click.
  $('body').on('click', '.edit-vertical-price', function () {
    var $wrapper = $(this).closest('.vertical-price-wrapper');
    var currentPrice = $.trim($wrapper.find('.vertical-price-value').text()) || '0.00';

    if ($wrapper.find('.vertical-price-input').length) {
      return;
    }

    $wrapper.data('old-price', currentPrice).html(
      '<input type="number" min="0" step="0.01" class="form-control form-control-sm vertical-price-input" value="' +
      $('<div>').text(currentPrice).html() + '" style="width:110px; display:inline-block;">'
    );

    $wrapper.find('.vertical-price-input').focus().select();
  });

  // UPDATED: Save the changed price on blur without reloading the page.
  $('body').on('blur', '.vertical-price-input', function () {
    var $input = $(this);
    var $wrapper = $input.closest('.vertical-price-wrapper');
    var oldPrice = $wrapper.data('old-price') || '0.00';
    var newPrice = $.trim($input.val());

    if (newPrice === '' || isNaN(newPrice) || parseFloat(newPrice) < 0) {
      renderVerticalPrice($wrapper, oldPrice);
      $.notify('Please enter a valid price greater than or equal to 0.', 'error');
      return;
    }

    $input.prop('disabled', true);

    $.ajax({
      url: '<?= base_url('verticals/update_price') ?>',
      type: 'POST',
      dataType: 'json',
      data: {
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>',
        id: $wrapper.data('id'),
        price: newPrice
      }
    }).done(function (response) {
      if (response.status) {
        renderVerticalPrice($wrapper, response.price);
        $.notify(response.message, 'success');
        return;
      }

      renderVerticalPrice($wrapper, oldPrice);
      $.notify(response.message || 'Unable to update vertical price.', 'error');
    }).fail(function () {
      renderVerticalPrice($wrapper, oldPrice);
      $.notify('Unable to update vertical price.', 'error');
    });
  });

  // UPDATED: Enter saves through blur; Escape restores the previous price.
  $('body').on('keydown', '.vertical-price-input', function (event) {
    var $wrapper = $(this).closest('.vertical-price-wrapper');

    if (event.key === 'Enter') {
      event.preventDefault();
      $(this).blur();
    } else if (event.key === 'Escape') {
      event.preventDefault();
      renderVerticalPrice($wrapper, $wrapper.data('old-price') || '0.00');
    }
  });

  // UPDATED: Restore the normal price text and pen icon after save/cancel.
  function renderVerticalPrice($wrapper, price) {
    var formattedPrice = parseFloat(price || 0).toFixed(2);
    $wrapper.html(
      '<span class="vertical-price-value">' + formattedPrice + '</span>' +
      '<button type="button" class="btn btn-link btn-sm p-0 ml-1 edit-vertical-price" title="Edit Price">' +
      '<i class="fa fa-pen"></i></button>'
    );
  }

});
</script>
