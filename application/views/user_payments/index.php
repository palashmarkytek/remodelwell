<!-- UPDATED: Advertiser payment-method listing and add-payment modal. -->
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css">

<?php
$payment_form_data = isset($payment_form_data) && is_array($payment_form_data) ? $payment_form_data : array();
$old_payment_value = function ($field) use ($payment_form_data) {
    return isset($payment_form_data[$field]) ? html_escape($payment_form_data[$field]) : '';
};
?>

<div class="content-wrapper">
  <section class="content">
    <?php $this->load->view('includes/_messages.php') ?>

    <?php if (!empty($api_error)): ?>
      <div class="alert alert-danger alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <p class="mb-0"><i class="icon fa fa-times"></i> <?= html_escape($api_error) ?></p>
      </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header d-flex align-items-center">
        <h3 class="card-title mb-0"><i class="fa fa-credit-card"></i> User Payments</h3>

        <!-- UPDATED: Opens the add-payment form without changing the existing page layout. -->
        <button type="button"
                class="btn btn-primary btn-sm ml-auto"
                data-toggle="modal"
                data-target="#addPaymentModal"
                <?= empty($can_add_payment) ? 'disabled' : '' ?>>
          <i class="fa fa-plus"></i> Add Payment
        </button>
      </div>

      <div class="card-body p-2">
        <div class="table-responsive">
          <table id="user-payment-list" class="table table-bordered table-striped table-sm mb-0">
            <thead>
              <tr>
                <th width="70">#</th>
                <th>Card Number</th>
                <th>Card Brand</th>
                <th width="120" class="text-center">Default</th>
                <th width="120" class="text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($payments as $key => $payment): ?>
                <?php
                $is_default = strcasecmp((string) $payment['isDefault'], 'Yes') === 0;
                $is_active = strcasecmp((string) $payment['status'], 'Active') === 0;
                ?>
                <tr>
                  <td><?= $key + 1 ?></td>
                  <td>•••• <?= html_escape($payment['cardNumber'] ?: '-') ?></td>
                  <td><?= html_escape($payment['cardBrand'] ?: '-') ?></td>
                  <td class="text-center">
                    <span class="badge <?= $is_default ? 'badge-success' : 'badge-secondary' ?>">
                      <?= html_escape($payment['isDefault'] ?: 'No') ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <span class="badge <?= $is_active ? 'badge-success' : 'badge-secondary' ?>">
                      <?= html_escape($payment['status'] ?: '-') ?>
                    </span>
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

<!-- UPDATED: Add Payment modal. Card number and CVV are never repopulated. -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <?php echo form_open(base_url('user_payments/add'), 'id="add-payment-form" autocomplete="off" novalidate'); ?>
        <div class="modal-header">
          <h5 class="modal-title" id="addPaymentModalLabel"><i class="fa fa-credit-card"></i> Add Payment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-12 col-md-6">
              <label for="nameOnCard" class="small mb-1">Name on Card <span class="text-danger">*</span></label>
              <input type="text" name="nameOnCard" id="nameOnCard" class="form-control form-control-sm"
                     maxlength="150" autocomplete="cc-name" value="<?= $old_payment_value('nameOnCard') ?>" required>
              <div class="invalid-feedback">Please enter the name shown on the card.</div>
            </div>

            <div class="form-group col-12 col-md-6">
              <label for="cardNumber" class="small mb-1">Card Number <span class="text-danger">*</span></label>
              <input type="text" name="cardNumber" id="cardNumber" class="form-control form-control-sm"
                     maxlength="23" inputmode="numeric" autocomplete="cc-number"
                     placeholder="Card number" required>
              <div class="invalid-feedback">Please enter a valid card number.</div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-12 col-md-4">
              <label for="cvv" class="small mb-1">CVV <span class="text-danger">*</span></label>
              <input type="password" name="cvv" id="cvv" class="form-control form-control-sm"
                     maxlength="4" inputmode="numeric" autocomplete="cc-csc"
                     placeholder="3 or 4 digits" required>
              <div class="invalid-feedback">CVV must contain 3 or 4 digits.</div>
            </div>

            <div class="form-group col-6 col-md-4">
              <label for="expMonth" class="small mb-1">Expiration Month <span class="text-danger">*</span></label>
              <input type="text" name="expMonth" id="expMonth" class="form-control form-control-sm"
                     maxlength="2" inputmode="numeric" autocomplete="cc-exp-month"
                     placeholder="MM" value="<?= $old_payment_value('expMonth') ?>" required>
              <div class="invalid-feedback">Use a month from 01 to 12.</div>
            </div>

            <div class="form-group col-6 col-md-4">
              <label for="expYear" class="small mb-1">Expiration Year <span class="text-danger">*</span></label>
              <input type="text" name="expYear" id="expYear" class="form-control form-control-sm"
                     maxlength="4" inputmode="numeric" autocomplete="cc-exp-year"
                     placeholder="YYYY" value="<?= $old_payment_value('expYear') ?>" required>
              <div class="invalid-feedback">Use a valid future year in YYYY format.</div>
            </div>
          </div>

          <div class="form-group">
            <label for="address" class="small mb-1">Billing Address <span class="text-danger">*</span></label>
            <input type="text" name="address" id="address" class="form-control form-control-sm"
                   maxlength="255" autocomplete="address-line1" value="<?= $old_payment_value('address') ?>" required>
            <div class="invalid-feedback">Please enter the billing address.</div>
          </div>

          <div class="form-row">
            <div class="form-group col-12 col-md-5">
              <label for="city" class="small mb-1">City <span class="text-danger">*</span></label>
              <input type="text" name="city" id="city" class="form-control form-control-sm"
                     maxlength="100" autocomplete="address-level2" value="<?= $old_payment_value('city') ?>" required>
              <div class="invalid-feedback">Please enter the city.</div>
            </div>

            <div class="form-group col-6 col-md-3">
              <label for="state" class="small mb-1">State / Region <span class="text-danger">*</span></label>
              <input type="text" name="state" id="state" class="form-control form-control-sm text-uppercase"
                     maxlength="2" autocomplete="address-level1" placeholder="CA"
                     value="<?= $old_payment_value('state') ?>" required>
              <div class="invalid-feedback">Enter a 2-character state or region code.</div>
            </div>

            <div class="form-group col-6 col-md-4">
              <label for="zipCode" class="small mb-1">ZIP Code <span class="text-danger">*</span></label>
              <input type="text" name="zipCode" id="zipCode" class="form-control form-control-sm"
                     maxlength="10" inputmode="numeric" autocomplete="postal-code"
                     placeholder="12345" value="<?= $old_payment_value('zipCode') ?>" required>
              <div class="invalid-feedback">Enter a valid 5-digit or ZIP+4 code.</div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
          <button type="submit" id="add-payment-submit" class="btn btn-primary btn-sm">
            <i class="fa fa-save"></i> Save Payment
          </button>
        </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>
<script>
$(function () {
  $('#user-payment-list').DataTable({
    dom: 'lrtip',
    pageLength: 100,
    autoWidth: false,
    lengthChange: false,
    order: [[2, 'asc']]
  });

  // UPDATED: Keep card-related inputs numeric and normalize the state code.
  $('#cardNumber').on('input', function () {
    var digits = this.value.replace(/\D/g, '').substring(0, 19);
    this.value = digits.replace(/(.{4})/g, '$1 ').trim();
  });

  $('#cvv, #expMonth, #expYear').on('input', function () {
    this.value = this.value.replace(/\D/g, '');
  });

  $('#state').on('input', function () {
    this.value = this.value.replace(/[^a-zA-Z]/g, '').toUpperCase().substring(0, 2);
  });

  $('#zipCode').on('input', function () {
    this.value = this.value.replace(/[^0-9-]/g, '').substring(0, 10);
  });

  function passesLuhn(cardNumber) {
    var sum = 0;
    var alternate = false;

    for (var i = cardNumber.length - 1; i >= 0; i--) {
      var digit = parseInt(cardNumber.charAt(i), 10);
      if (alternate) {
        digit *= 2;
        if (digit > 9) digit -= 9;
      }
      sum += digit;
      alternate = !alternate;
    }

    return sum > 0 && sum % 10 === 0;
  }

  // UPDATED: Client-side validation mirrors server-side rules before submission.
  $('#add-payment-form').on('submit', function (event) {
    var form = this;
    var cardNumber = $('#cardNumber').val().replace(/\D/g, '');
    var cvv = $('#cvv').val();
    var month = $('#expMonth').val();
    var year = $('#expYear').val();
    var state = $('#state').val();
    var zipCode = $('#zipCode').val();
    var now = new Date();
    var currentValue = now.getFullYear() * 100 + (now.getMonth() + 1);
    var expiryValue = parseInt(year || '0', 10) * 100 + parseInt(month || '0', 10);

    $('#cardNumber').get(0).setCustomValidity(
      /^\d{13,19}$/.test(cardNumber) && passesLuhn(cardNumber) ? '' : 'Invalid card number.'
    );
    $('#cvv').get(0).setCustomValidity(/^\d{3,4}$/.test(cvv) ? '' : 'Invalid CVV.');
    $('#expMonth').get(0).setCustomValidity(/^(0[1-9]|1[0-2])$/.test(month) ? '' : 'Invalid month.');
    $('#expYear').get(0).setCustomValidity(/^\d{4}$/.test(year) && expiryValue >= currentValue ? '' : 'Invalid expiration date.');
    $('#state').get(0).setCustomValidity(/^[A-Z]{2}$/.test(state) ? '' : 'Invalid state code.');
    $('#zipCode').get(0).setCustomValidity(/^\d{5}(-\d{4})?$/.test(zipCode) ? '' : 'Invalid ZIP code.');

    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
      $(form).addClass('was-validated');
      return;
    }

    // UPDATED: Submit a digits-only card number to the existing controller endpoint.
    $('#cardNumber').val(cardNumber);
    $('#add-payment-submit').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
  });

  <?php if (!empty($open_payment_modal) && !empty($can_add_payment)): ?>
    $('#addPaymentModal').modal('show');
  <?php endif; ?>
});
</script>
