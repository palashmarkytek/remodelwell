<?php
// UPDATED: /admin/edit/:id follows the new /auth/register data structure.
// Company/contact values come from ci_admin; qualification from ci_advertisers;
// selected vertical and lead contract values come from their mapping tables.
$u = isset($update) && is_array($update) ? $update : array();
$c = isset($contract) && is_array($contract) ? $contract : array();
$is_post = $this->input->method(TRUE) === 'POST';

$value = function ($key, $default = '') use ($u, $c) {
    if (array_key_exists($key, $u)) {
        $default = $u[$key];
    } elseif (array_key_exists($key, $c)) {
        $default = $c[$key];
    }
    return html_escape(set_value($key, $default));
};

$selected_vertical_id = $is_post
    ? (int) $this->input->post('vertical_id')
    : (int) (isset($c['vertical_id']) ? $c['vertical_id'] : 0);

$selected_states = $is_post ? (array) $this->input->post('state_filter') : array_filter(array_map('trim', explode(',', isset($c['state_abbreviations']) ? $c['state_abbreviations'] : '')));
$selected_days = $is_post ? (array) $this->input->post('receive_days') : array_filter(array_map('trim', explode(',', isset($c['delivery_days']) ? $c['delivery_days'] : '')));
$selected_states = array_map('strtoupper', $selected_states);
$delivery_method = set_value('lead_delivery_method', isset($c['lead_delivery_method']) ? $c['lead_delivery_method'] : '');
?>
<div class="mk-dashboard">
<div class="content-wrapper">
<section class="content">
<div class="card">
  <div class="card-body py-2 d-flex align-items-center">
    <h5 class="mb-0"><i class="fa fa-pencil"></i> <?= html_escape($title) ?></h5>
    <a href="<?= base_url('admin'); ?>" class="btn btn-success btn-sm ml-auto"><i class="fa fa-list"></i> Advertisers List</a>
  </div>
  <div class="card-body p-2">
    <?php $this->load->view('includes/_messages.php') ?>
    <?php echo form_open(base_url('admin/edit/' . $id), 'id="advertiserEditForm" novalidate'); ?>

    <!-- UPDATED: Company Information saves only in ci_admin. -->
    <h6 class="border-bottom pb-2">Company Information</h6>
    <div class="form-row">
      <div class="form-group col-md-4">
        <label>First Name <span class="text-danger">*</span></label>
        <input type="text" name="first_name" class="form-control form-control-sm <?= form_error('first_name') ? 'is-invalid' : '' ?>" value="<?= $value('first_name') ?>" required>
        <div class="invalid-feedback"><?= strip_tags(form_error('first_name')) ?></div>
      </div>
      <div class="form-group col-md-4">
        <label>Last Name <span class="text-danger">*</span></label>
        <input type="text" name="last_name" class="form-control form-control-sm <?= form_error('last_name') ? 'is-invalid' : '' ?>" value="<?= $value('last_name') ?>" required>
        <div class="invalid-feedback"><?= strip_tags(form_error('last_name')) ?></div>
      </div>
      <div class="form-group col-md-4">
        <label>Company Name <span class="text-danger">*</span></label>
        <input type="text" name="company_name" class="form-control form-control-sm <?= form_error('company_name') ? 'is-invalid' : '' ?>" value="<?= $value('company_name') ?>" required>
        <div class="invalid-feedback"><?= strip_tags(form_error('company_name')) ?></div>
      </div>
      <div class="form-group col-md-4">
        <label>Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control form-control-sm <?= form_error('email') ? 'is-invalid' : '' ?>" value="<?= $value('email') ?>" required>
        <div class="invalid-feedback"><?= strip_tags(form_error('email')) ?></div>
      </div>
      <div class="form-group col-md-4">
        <label>Phone Number <span class="text-danger">*</span></label>
        <input type="text" name="phone_number" class="form-control form-control-sm <?= form_error('phone_number') ? 'is-invalid' : '' ?>" value="<?= $value('phone_number') ?>" required>
        <div class="invalid-feedback"><?= strip_tags(form_error('phone_number')) ?></div>
      </div>
      <div class="form-group col-md-4">
        <label>Source ID</label>
        <input type="text" name="source_id" class="form-control form-control-sm" value="<?= $value('source_id') ?>">
      </div>
      <div class="form-group col-md-4">
        <label>User Role <span class="text-danger">*</span></label>
        <select name="admin_role_id" class="form-control form-control-sm <?= form_error('admin_role_id') ? 'is-invalid' : '' ?>" required>
          <?php foreach ($admin_roles as $role): ?>
            <option value="<?= (int) $role['admin_role_id'] ?>" <?= ((string) set_value('admin_role_id', isset($u['admin_role_id']) ? $u['admin_role_id'] : '') === (string) $role['admin_role_id']) ? 'selected' : '' ?>><?= html_escape($role['admin_role_title']) ?></option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback"><?= strip_tags(form_error('admin_role_id')) ?></div>
      </div>
      <div class="form-group col-md-4">
        <label>Display ID</label>
        <input type="text" class="form-control form-control-sm" value="<?= html_escape(isset($u['display_id']) ? $u['display_id'] : '') ?>" readonly>
      </div>
      <div class="form-group col-md-4">
        <label>Agreement</label>
        <div class="form-control form-control-sm bg-light">
          <?php if (!empty($u['agreement_accept']) && $u['agreement_accept'] === 'Yes'): ?>
            <span class="text-success"><i class="fa fa-check-circle"></i> Accepted</span>
          <?php else: ?>
            <span class="text-muted">Not recorded</span>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- UPDATED: Only currently_buying and monthly_budget save in ci_advertisers. -->
    <h6 class="border-bottom pb-2 mt-3">Qualification</h6>
    <div class="form-row">
      <div class="form-group col-md-4">
        <label>Currently Buying Leads or Calls? <span class="text-danger">*</span></label>
        <select name="currently_buying" class="form-control form-control-sm <?= form_error('currently_buying') ? 'is-invalid' : '' ?>" required>
          <option value="">Select</option>
          <option value="Yes" <?= set_select('currently_buying', 'Yes', isset($u['currently_buying']) && $u['currently_buying'] === 'Yes') ?>>Yes</option>
          <option value="No" <?= set_select('currently_buying', 'No', isset($u['currently_buying']) && $u['currently_buying'] === 'No') ?>>No</option>
        </select>
        <div class="invalid-feedback"><?= strip_tags(form_error('currently_buying')) ?></div>
      </div>
      <div class="form-group col-md-4">
        <label>Monthly Budget <span class="text-danger">*</span></label>
        <input type="text" name="monthly_budget" class="form-control form-control-sm <?= form_error('monthly_budget') ? 'is-invalid' : '' ?>" value="<?= $value('monthly_budget') ?>" required>
        <div class="invalid-feedback"><?= strip_tags(form_error('monthly_budget')) ?></div>
      </div>

      <!-- UPDATED: One radio-selected vertical is saved in ci_advertiser_vertical_map. -->
      <div class="form-group col-md-12">
        <label>What vertical do you want leads for? <span class="text-danger">*</span></label>
        <div class="row border rounded p-2 mx-0 <?= form_error('vertical_id') ? 'border-danger' : '' ?>">
          <?php if (!empty($verticals)): ?>
            <?php foreach ($verticals as $vertical): ?>
              <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="vertical_id" value="<?= (int) $vertical['id'] ?>" id="vertical_<?= (int) $vertical['id'] ?>" <?= $selected_vertical_id === (int) $vertical['id'] ? 'checked' : '' ?> required>
                  <label class="form-check-label" for="vertical_<?= (int) $vertical['id'] ?>"><?= html_escape($vertical['vertical_name']) ?></label>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-12 text-muted">No active vertical is available.</div>
          <?php endif; ?>
        </div>
        <?php if (form_error('vertical_id')): ?><small class="text-danger field-error"><?= strip_tags(form_error('vertical_id')) ?></small><?php endif; ?>
      </div>
    </div>

    <!-- UPDATED: Lead setup saves in ci_vertical_contract_map. -->
    <h6 class="border-bottom pb-2 mt-3">Lead Setup</h6>
    <div class="form-row">
      <div class="form-group col-md-4">
        <label>How Many Leads Per Week? <span class="text-danger">*</span></label>
        <input type="number" min="1" name="leads_per_week" class="form-control form-control-sm <?= form_error('leads_per_week') ? 'is-invalid' : '' ?>" value="<?= $value('leads_per_week') ?>" required>
        <div class="invalid-feedback"><?= strip_tags(form_error('leads_per_week')) ?></div>
      </div>
      <div class="form-group col-md-8">
        <label>Add ZIP Code Filter <span class="text-danger">*</span></label>
        <!-- UPDATED: ZIP Code Filter is a textarea and keeps submitted values. -->
        <textarea name="zip_code_filter" class="form-control form-control-sm <?= form_error('zip_code_filter') ? 'is-invalid' : '' ?>" rows="4" required><?= html_escape(set_value('zip_code_filter', isset($c['zip_codes']) ? $c['zip_codes'] : '')) ?></textarea>
        <div class="invalid-feedback"><?= strip_tags(form_error('zip_code_filter')) ?></div>
      </div>

      <div class="form-group col-md-12">
        <div class="d-flex align-items-center mb-2">
          <label class="mb-0">Add State Filter <span class="text-danger">*</span></label>
          <div class="form-check ml-3">
            <input type="checkbox" class="form-check-input" id="select_all_states">
            <label class="form-check-label" for="select_all_states">Select All</label>
          </div>
        </div>
        <div class="row border rounded p-2 mx-0 <?= form_error('state_filter[]') ? 'border-danger' : '' ?>" id="stateContainer">
          <?php foreach ($states as $state): ?>
            <?php $abbr = strtoupper($state['state_abbreviation']); ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
              <div class="form-check">
                <input class="form-check-input state-checkbox" type="checkbox" name="state_filter[]" value="<?= html_escape($abbr) ?>" id="state_<?= html_escape($abbr) ?>" <?= in_array($abbr, $selected_states, true) ? 'checked' : '' ?>>
                <label class="form-check-label" for="state_<?= html_escape($abbr) ?>"><?= html_escape($state['state_name']) ?> (<?= html_escape($abbr) ?>)</label>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php if (form_error('state_filter[]')): ?><small class="text-danger field-error"><?= strip_tags(form_error('state_filter[]')) ?></small><?php endif; ?>
      </div>
    </div>

    <!-- UPDATED: Delivery values also save in ci_vertical_contract_map. -->
    <h6 class="border-bottom pb-2 mt-3">Delivery</h6>
    <div class="form-row">
      <div class="form-group col-md-4">
        <label>Where Should We Send Your Leads? <span class="text-danger">*</span></label>
        <select name="lead_delivery_method" id="lead_delivery_method" class="form-control form-control-sm <?= form_error('lead_delivery_method') ? 'is-invalid' : '' ?>" required>
          <option value="">Select</option>
          <?php foreach (array('Email', 'SMS', 'Email and SMS') as $method): ?>
            <option value="<?= $method ?>" <?= $delivery_method === $method ? 'selected' : '' ?>><?= $method ?></option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback"><?= strip_tags(form_error('lead_delivery_method')) ?></div>
      </div>
      <div class="form-group col-md-4 delivery-email-box">
        <label>Lead Delivery Email</label>
        <input type="email" name="lead_delivery_email" id="lead_delivery_email" class="form-control form-control-sm <?= form_error('lead_delivery_email') ? 'is-invalid' : '' ?>" value="<?= $value('lead_delivery_email') ?>">
        <div class="invalid-feedback"><?= strip_tags(form_error('lead_delivery_email')) ?></div>
      </div>
      <div class="form-group col-md-4 delivery-sms-box">
        <label>Lead Delivery SMS Number</label>
        <input type="text" name="lead_delivery_sms" id="lead_delivery_sms" class="form-control form-control-sm <?= form_error('lead_delivery_sms') ? 'is-invalid' : '' ?>" value="<?= $value('lead_delivery_sms') ?>">
        <div class="invalid-feedback"><?= strip_tags(form_error('lead_delivery_sms')) ?></div>
      </div>

      <div class="form-group col-md-12">
        <label>When Do You Want to Receive Leads? <span class="text-danger">*</span></label>
        <div class="row border rounded p-2 mx-0 <?= form_error('receive_days[]') ? 'border-danger' : '' ?>" id="dayContainer">
          <?php foreach (array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') as $day): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
              <div class="form-check">
                <input type="checkbox" class="form-check-input receive-day" name="receive_days[]" id="day_<?= strtolower($day) ?>" value="<?= $day ?>" <?= in_array($day, $selected_days, true) ? 'checked' : '' ?>>
                <label class="form-check-label" for="day_<?= strtolower($day) ?>"><?= $day ?></label>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php if (form_error('receive_days[]')): ?><small class="text-danger field-error"><?= strip_tags(form_error('receive_days[]')) ?></small><?php endif; ?>
      </div>

      <div class="form-group col-md-4">
        <label>Start Time <span class="text-danger">*</span></label>
        <input type="time" name="start_time" class="form-control form-control-sm <?= form_error('start_time') ? 'is-invalid' : '' ?>" value="<?= html_escape(set_value('start_time', isset($c['start_time']) ? substr($c['start_time'], 0, 5) : '')) ?>" required>
        <div class="invalid-feedback"><?= strip_tags(form_error('start_time')) ?></div>
      </div>
      <div class="form-group col-md-4">
        <label>End Time <span class="text-danger">*</span></label>
        <input type="time" name="end_time" class="form-control form-control-sm <?= form_error('end_time') ? 'is-invalid' : '' ?>" value="<?= html_escape(set_value('end_time', isset($c['end_time']) ? substr($c['end_time'], 0, 5) : '')) ?>" required>
        <div class="invalid-feedback"><?= strip_tags(form_error('end_time')) ?></div>
      </div>
    </div>

    <div class="text-right">
      <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Update Advertiser</button>
    </div>
    <?php echo form_close(); ?>
  </div>
</div>
</section>
</div>
</div>

<script>
$(function () {
  // UPDATED: Select All checks/unchecks every USA state and stays synchronized.
  function syncSelectAllStates() {
    var total = $('.state-checkbox').length;
    var checked = $('.state-checkbox:checked').length;
    $('#select_all_states').prop('checked', total > 0 && total === checked);
  }

  $('#select_all_states').on('change', function () {
    $('.state-checkbox').prop('checked', this.checked).trigger('change');
  });
  $('.state-checkbox').on('change', syncSelectAllStates);
  syncSelectAllStates();

  // UPDATED: Require/show only the delivery fields needed by the selected method.
  function toggleDeliveryFields() {
    var method = $('#lead_delivery_method').val();
    var showEmail = method === 'Email' || method === 'Email and SMS';
    var showSms = method === 'SMS' || method === 'Email and SMS';

    $('.delivery-email-box').toggleClass('d-none', !showEmail);
    $('.delivery-sms-box').toggleClass('d-none', !showSms);
    $('#lead_delivery_email').prop('required', showEmail);
    $('#lead_delivery_sms').prop('required', showSms);
  }
  $('#lead_delivery_method').on('change', toggleDeliveryFields);
  toggleDeliveryFields();

  // UPDATED: Remove only the focused/changed field's red border and error message.
  $('#advertiserEditForm').on('focus input change', 'input, select, textarea', function () {
    var $field = $(this);
    $field.removeClass('is-invalid');
    $field.closest('.form-group').find('.invalid-feedback, .field-error').empty().remove();

    if ($field.is('[name="vertical_id"]')) {
      $field.closest('.border').removeClass('border-danger');
    }
    if ($field.hasClass('state-checkbox')) {
      $('#stateContainer').removeClass('border-danger');
      $('#stateContainer').siblings('.field-error').remove();
    }
    if ($field.hasClass('receive-day')) {
      $('#dayContainer').removeClass('border-danger');
      $('#dayContainer').siblings('.field-error').remove();
    }
  });
});
</script>
