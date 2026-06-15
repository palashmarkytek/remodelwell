<div class="form-background mk-register-page">
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-lg-10 col-md-11 col-12">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Contractor Sign Up</h4>
            <small>Complete the workflow wizard below.</small>
          </div>

          <div class="card-body">
            <?php $this->load->view('includes/_messages.php') ?>

            <?php echo form_open(base_url('auth/register'), array('id' => 'contractorSignupForm', 'class' => 'login-form', 'novalidate' => 'novalidate')); ?>

            <!-- UPDATED: Registration wizard now contains the five tabs requested. -->
            <ul class="nav nav-pills mb-3 signup-steps" id="signupSteps">
              <li class="nav-item"><a class="nav-link active" href="javascript:void(0)" data-step-link="1">1. Company <span class="step-status-icon ml-1"></span></a></li>
              <li class="nav-item"><a class="nav-link" href="javascript:void(0)" data-step-link="2">2. Qualification <span class="step-status-icon ml-1"></span></a></li>
              <li class="nav-item"><a class="nav-link" href="javascript:void(0)" data-step-link="3">3. Lead Setup <span class="step-status-icon ml-1"></span></a></li>
              <li class="nav-item"><a class="nav-link" href="javascript:void(0)" data-step-link="4">4. Delivery <span class="step-status-icon ml-1"></span></a></li>
              <li class="nav-item"><a class="nav-link" href="javascript:void(0)" data-step-link="5">5. Agreement <span class="step-status-icon ml-1"></span></a></li>
            </ul>

            <!-- UPDATED: Existing backend field names are preserved through hidden/default fields. -->
            <input type="hidden" name="annual_revenue" value="$1 - 5 Million">
            <input type="hidden" name="lead_type" value="Form Leads">
            <input type="hidden" name="filtering_notes" value="">
            <input type="hidden" name="cannot_work_on" value="">
            <input type="hidden" name="send_to_crm" value="No">
            <input type="hidden" name="crm_details" value="">
            <input type="hidden" name="pricing_estimate" value="">
            <input type="hidden" name="account_funding" value="">
            <input type="hidden" name="billing_start_date" value="<?php echo date('Y-m-d'); ?>">
            <input type="hidden" name="payment_demo_status" value="Demo Payment Pending">
            <input type="hidden" name="payment_information" value="">
            <input type="hidden" name="service_area" id="service_area" value="<?php echo set_value('service_area'); ?>">
            <input type="hidden" name="receive_time" id="receive_time" value="<?php echo set_value('receive_time'); ?>">

            <!-- Step 1: Company -->
            <div class="wizard-step" data-step="1">
              <h5 class="mb-3">Company</h5>
              <div class="step-message alert alert-danger small d-none"></div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="small mb-1">First Name *</label>
                  <input type="text" name="first_name" class="form-control form-control-sm" value="<?php echo set_value('first_name'); ?>" data-msg="First name is required." required>
                </div>
                <div class="form-group col-md-6">
                  <label class="small mb-1">Last Name *</label>
                  <input type="text" name="last_name" class="form-control form-control-sm" value="<?php echo set_value('last_name'); ?>" data-msg="Last name is required." required>
                </div>
                <div class="form-group col-md-6">
                  <label class="small mb-1">Company Name *</label>
                  <input type="text" name="company_name" class="form-control form-control-sm" value="<?php echo set_value('company_name'); ?>" data-msg="Company name is required." required>
                </div>
                <div class="form-group col-md-6">
                  <label class="small mb-1">Email *</label>
                  <input type="email" name="email" class="form-control form-control-sm" value="<?php echo set_value('email'); ?>" data-msg="Valid email is required." required>
                </div>
                <div class="form-group col-md-6">
                  <label class="small mb-1">Phone *</label>
                  <input type="text" name="phone_number" class="form-control form-control-sm" value="<?php echo set_value('phone_number'); ?>" data-msg="Phone number is required." required>
                </div>
              </div>
            </div>

            <!-- Step 2: Qualification -->
            <div class="wizard-step d-none" data-step="2">
              <h5 class="mb-3">Qualification</h5>
              <div class="step-message alert alert-danger small d-none"></div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="small mb-1">Are you currently buying leads or calls? *</label>
                  <select name="currently_buying" id="currently_buying" class="form-control form-control-sm" data-msg="Please select an option." required>
                    <option value="">Select</option>
                    <option value="Yes" <?php echo set_select('currently_buying', 'Yes'); ?>>Yes</option>
                    <option value="No" <?php echo set_select('currently_buying', 'No'); ?>>No</option>
                  </select>
                </div>
                <div class="form-group col-md-6">
                  <label class="small mb-1">Monthly budget on buying leads *</label>
                  <input type="text" name="monthly_budget" class="form-control form-control-sm" placeholder="$5,000 / month" value="<?php echo set_value('monthly_budget'); ?>" data-msg="Monthly budget is required." required>
                </div>

                <!-- UPDATED: Verticals remain connected to active ci_verticals records. -->
                <div class="form-group col-md-12">
                  <label class="small mb-2">What verticals do you want leads for? *</label>
                  <div class="row border rounded p-2 mx-0">
                    <?php if (!empty($verticals)): ?>
                      <?php foreach ($verticals as $vertical): ?>
                        <div class="col-md-4 col-sm-6 mb-2">
                          <div class="form-check">
                            <input class="form-check-input vertical-checkbox" type="radio" name="vertical_id" value="<?php echo (int) $vertical['id']; ?>" id="vertical_<?php echo (int) $vertical['id']; ?>" <?php echo set_radio('vertical_id', $vertical['id']); ?>>
                            <label class="form-check-label small" for="vertical_<?php echo (int) $vertical['id']; ?>"><?php echo html_escape($vertical['vertical_name']); ?></label>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <div class="col-12 text-muted small">No active vertical is available. Please contact the administrator.</div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 3: Lead Setup -->
            <div class="wizard-step d-none" data-step="3">
              <h5 class="mb-3">Lead Setup</h5>
              <div class="step-message alert alert-danger small d-none"></div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="small mb-1">How many leads do you want per week? *</label>
                  <input type="number" name="leads_per_week" class="form-control form-control-sm" min="1" value="<?php echo set_value('leads_per_week'); ?>" data-msg="Leads per week is required." required>
                </div>
                <!-- UPDATED: User can choose either ZIP Code Filter or State Filter. -->
                <?php $selected_filter_type = set_value('filter_type', 'zip'); ?>
                <div class="form-group col-md-12">
                  <label class="small mb-2">Choose Service Area Filter *</label>
                  <div class="border rounded p-2">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input service-filter-type" type="radio" name="filter_type" id="filter_type_zip" value="zip" <?php echo ($selected_filter_type === 'zip') ? 'checked' : ''; ?> required>
                      <label class="form-check-label" for="filter_type_zip">Add ZIP Code Filter</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input service-filter-type" type="radio" name="filter_type" id="filter_type_state" value="state" <?php echo ($selected_filter_type === 'state') ? 'checked' : ''; ?> required>
                      <label class="form-check-label" for="filter_type_state">Add State Filter</label>
                    </div>
                  </div>
                </div>

                <div class="form-group col-md-12 zip-filter-section">
                  <label class="small mb-1">Add ZIP Code Filter *</label>
                  <textarea id="zip_code_filter" name="zip_code_filter" class="form-control form-control-sm" rows="4" placeholder="Example: 12345, 32410" data-msg="At least one ZIP code is required."><?php echo html_escape(set_value('zip_code_filter')); ?></textarea>
                  <small class="text-muted">Enter multiple ZIP codes separated by commas.</small>
                </div>

                <!-- UPDATED: Active states are loaded from ci_usa_states instead of a hardcoded list. -->
                <div class="form-group col-md-12 state-filter-section d-none">
                  <label class="small mb-2">Add State Filter *</label>
                  <div class="row border rounded p-2 mx-0 state-filter-box">
                    <div class="col-12 mb-2 border-bottom pb-2">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select_all_states">
                        <label class="form-check-label font-weight-bold" for="select_all_states">Select All</label>
                      </div>
                    </div>
                    <?php if (!empty($states)): ?>
                      <?php foreach ($states as $state): ?>
                        <?php $abbr = strtoupper(trim($state['state_abbreviation'])); ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                          <div class="form-check">
                            <input class="form-check-input state-checkbox" type="checkbox" name="state_filter[]" id="state_<?php echo html_escape($abbr); ?>" value="<?php echo html_escape($abbr); ?>" <?php echo set_checkbox('state_filter[]', $abbr); ?>>
                            <label class="form-check-label small" for="state_<?php echo html_escape($abbr); ?>"><?php echo html_escape($state['state_name']); ?> (<?php echo html_escape($abbr); ?>)</label>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <div class="col-12 text-muted small">No active states are available. Please contact the administrator.</div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 4: Delivery -->
            <div class="wizard-step d-none" data-step="4">
              <h5 class="mb-3">Delivery</h5>
              <div class="step-message alert alert-danger small d-none"></div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="small mb-1">Where should we send your leads? *</label>
                  <select name="lead_delivery_method" id="lead_delivery_method" class="form-control form-control-sm" data-msg="Lead delivery method is required." required>
                    <option value="">Select</option>
                    <option value="Email" <?php echo set_select('lead_delivery_method', 'Email'); ?>>Email</option>
                    <option value="SMS" <?php echo set_select('lead_delivery_method', 'SMS'); ?>>SMS</option>
                    <option value="Email and SMS" <?php echo set_select('lead_delivery_method', 'Email and SMS'); ?>>Email and SMS</option>
                  </select>
                </div>
                <div class="form-group col-md-6 delivery-email-box">
                  <label class="small mb-1">Lead Delivery Email</label>
                  <input type="email" name="lead_delivery_email" id="lead_delivery_email" class="form-control form-control-sm" value="<?php echo set_value('lead_delivery_email'); ?>">
                </div>
                <div class="form-group col-md-6 delivery-sms-box d-none">
                  <label class="small mb-1">Lead Delivery SMS Number</label>
                  <input type="text" name="lead_delivery_sms" id="lead_delivery_sms" class="form-control form-control-sm" value="<?php echo set_value('lead_delivery_sms'); ?>">
                </div>

                <!-- UPDATED: Day checkboxes include Select All; time defaults cover the full day. -->
                <div class="form-group col-md-12">
                  <div class="d-flex align-items-center mb-2">
                    <label class="small mb-0">When do you want to receive leads? *</label>
                    <div class="form-check ml-3">
                      <input class="form-check-input" type="checkbox" id="select_all_days">
                      <label class="form-check-label small font-weight-bold" for="select_all_days">Select All</label>
                    </div>
                  </div>
                  <div class="border rounded p-3">
                    <div class="row">
                      <?php foreach (array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') as $day): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                          <div class="form-check">
                            <!-- UPDATED: Delivery days now retain submitted values and keep the existing receive_days[] backend field. -->
                            <input type="checkbox" class="form-check-input receive-day" name="receive_days[]" id="day_<?php echo strtolower($day); ?>" value="<?php echo $day; ?>" <?php echo set_checkbox('receive_days[]', $day); ?>>
                            <label class="form-check-label small" for="day_<?php echo strtolower($day); ?>"><?php echo $day; ?></label>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                    <div class="form-row mt-2">
                      <div class="form-group col-md-6 mb-0">
                        <label class="small mb-1">Start Time *</label>
                        <input type="time" id="delivery_start_time" name="start_time" class="form-control form-control-sm" value="<?php echo set_value('start_time', '00:00'); ?>" required>
                      </div>
                      <div class="form-group col-md-6 mb-0">
                        <label class="small mb-1">End Time *</label>
                        <input type="time" id="delivery_end_time" name="end_time" class="form-control form-control-sm" value="<?php echo set_value('end_time', '23:59'); ?>" required>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 5: Agreement -->
            <div class="wizard-step d-none" data-step="5">
              <h5 class="mb-3">Agreement & Complete Sign Up</h5>
              <div class="step-message alert alert-danger small d-none"></div>

              <!-- UPDATED: Agreement note is loaded from the existing general settings data. -->
              <div class="alert alert-light border small agreement-note">
                <?php
                  echo !empty($signup_agreement)
                    ? $signup_agreement
                    : 'By completing signup, you confirm that the information supplied is correct and agree to be contacted for lead delivery setup.';
                ?>
              </div>

              <div class="form-check mb-3">
                <input type="checkbox" name="agreement_accept" value="Yes" class="form-check-input" id="agreement_accept" <?php echo set_checkbox('agreement_accept', 'Yes'); ?> data-msg="Please accept the agreement to complete signup." required>
                <label class="form-check-label" for="agreement_accept">I agree and want to complete sign up.</label>
              </div>
            </div>

            <div class="d-flex justify-content-between mt-3">
              <button type="button" class="btn btn-secondary btn-sm" id="prevStep" disabled>Previous</button>
              <button type="button" class="btn btn-primary btn-sm" id="nextStep">Next</button>
              <input type="submit" name="submit" id="submitSignup" class="btn btn-success btn-sm d-none" disabled value="Complete Sign Up">
            </div>

            <?php echo form_close(); ?>

            <div class="mt-3">
              <a href="<?php echo base_url('auth/login'); ?>">Back to Login</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .mk-register-page .card { border-radius: 8px; }
  .signup-steps .nav-link { font-size: 12px; padding: 6px 9px; margin-right: 4px; margin-bottom: 4px; cursor: pointer; }
  .signup-steps .nav-link.step-valid { border: 1px solid #28a745; }
  .signup-steps .nav-link.step-invalid { border: 1px solid #dc3545; color: #dc3545 !important; }
  .step-status-icon { font-weight: bold; }
  .step-status-icon.valid { color: #28a745; }
  .step-status-icon.invalid { color: #dc3545; }
  .wizard-step { min-height: 280px; }
  .invalid-feedback { display: block; font-size: 12px; }
  .state-filter-box { max-height: 280px; overflow-y: auto; }
  .agreement-note { max-height: 330px; overflow-y: auto; }
</style>

<script>
(function () {
  var currentStep = 1;
  var totalSteps = 5; // UPDATED: Payment tab removed; total is now five.

  function showStep(step) {
    currentStep = parseInt(step, 10);
    $('.wizard-step').addClass('d-none');
    $('.wizard-step[data-step="' + currentStep + '"]').removeClass('d-none');
    $('[data-step-link]').removeClass('active');
    $('[data-step-link="' + currentStep + '"]').addClass('active');
    $('#prevStep').prop('disabled', currentStep === 1);
    $('#nextStep').toggleClass('d-none', currentStep === totalSteps);
    $('#submitSignup').toggleClass('d-none', currentStep !== totalSteps);
  }

  function setStepStatus(step, status) {
    var $link = $('[data-step-link="' + step + '"]');
    var $icon = $link.find('.step-status-icon');
    $link.removeClass('step-valid step-invalid');
    $icon.removeClass('valid invalid').html('');
    if (status === 'valid') {
      $link.addClass('step-valid');
      $icon.addClass('valid').html('&#10004;');
    } else if (status === 'invalid') {
      $link.addClass('step-invalid');
      $icon.addClass('invalid').html('&#10008;');
    }
  }

  function showFieldError($field, message) {
    $field.addClass('is-invalid');
    var $group = $field.closest('.form-group, .form-check');
    $group.find('.invalid-feedback').remove();
    $group.append('<div class="invalid-feedback">' + message + '</div>');
  }

  function clearStepErrors(step) {
    var $step = $('.wizard-step[data-step="' + step + '"]');
    $step.find('.is-invalid').removeClass('is-invalid');
    $step.find('.invalid-feedback').remove();
    $step.find('.step-message').addClass('d-none').html('');
  }

  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  function validateRequired($field, showErrors) {
    var message = $field.data('msg') || 'This field is required.';
    if ($field.attr('type') === 'checkbox' || $field.attr('type') === 'radio') {
      var name = $field.attr('name');
      var checked = name ? $('[name="' + name + '"]:checked').length > 0 : $field.is(':checked');
      if (!checked) {
        if (showErrors) showFieldError($field, message);
        return false;
      }
      return true;
    }
    var value = $.trim($field.val());
    if (value === '') {
      if (showErrors) showFieldError($field, message);
      return false;
    }
    if ($field.attr('type') === 'email' && !isValidEmail(value)) {
      if (showErrors) showFieldError($field, 'Please enter a valid email address.');
      return false;
    }
    return true;
  }

  // UPDATED: Build service_area from only the selected service-area filter.
  function buildServiceArea() {
    var filterType = $('[name="filter_type"]:checked').val();
    var zipCodes = $.trim($('#zip_code_filter').val());
    var states = [];
    $('.state-checkbox:checked').each(function () { states.push($(this).val()); });

    if (filterType === 'state') {
      $('#service_area').val('States: ' + states.join(','));
    } else {
      $('#service_area').val('ZIP Codes: ' + zipCodes);
    }
  }

  // UPDATED: Show and require only the selected ZIP or State filter.
  function toggleServiceAreaFilter() {
    var filterType = $('[name="filter_type"]:checked').val();
    var useState = (filterType === 'state');

    $('.zip-filter-section').toggleClass('d-none', useState);
    $('.state-filter-section').toggleClass('d-none', !useState);
    $('#zip_code_filter').prop('required', !useState);
    $('.state-checkbox').prop('required', false);
  }

  // UPDATED: Build the existing receive_time value from selected days and time pickers.
  function buildReceiveTime() {
    var days = [];
    $('.receive-day:checked').each(function () { days.push($(this).val()); });
    var start = $('#delivery_start_time').val();
    var end = $('#delivery_end_time').val();
    $('#receive_time').val(days.join(', ') + ' | ' + start + ' - ' + end);
  }

  function validateStep(step, showErrors) {
    var valid = true;
    showErrors = (showErrors !== false);
    if (showErrors) clearStepErrors(step);

    var $step = $('.wizard-step[data-step="' + step + '"]');
    var processedGroups = {};
    $step.find('input:visible, select:visible, textarea:visible').each(function () {
      var $field = $(this);
      if (!$field.prop('required')) return;
      var fieldType = $field.attr('type');
      var fieldName = $field.attr('name') || $field.attr('id');
      if ((fieldType === 'radio' || fieldType === 'checkbox') && processedGroups[fieldName]) return;
      processedGroups[fieldName] = true;
      if (!validateRequired($field, showErrors)) valid = false;
    });

    if (step === 2 && $('.vertical-checkbox:checked').length === 0) {
      if (showErrors) showFieldError($('.vertical-checkbox').first(), 'Please select one vertical.');
      valid = false;
    }

    if (step === 3) {
      var leads = parseInt($('[name="leads_per_week"]').val(), 10);
      var filterType = $('[name="filter_type"]:checked').val();

      if (isNaN(leads) || leads <= 0) {
        if (showErrors) showFieldError($('[name="leads_per_week"]'), 'Please enter leads per week greater than 0.');
        valid = false;
      }
      if (!filterType) {
        if (showErrors) showFieldError($('.service-filter-type').first(), 'Please choose ZIP Code Filter or State Filter.');
        valid = false;
      } else if (filterType === 'zip' && $.trim($('#zip_code_filter').val()) === '') {
        if (showErrors) showFieldError($('#zip_code_filter'), 'Please enter at least one ZIP code.');
        valid = false;
      } else if (filterType === 'state' && $('.state-checkbox:checked').length === 0) {
        if (showErrors) showFieldError($('.state-checkbox').first(), 'Please select at least one state.');
        valid = false;
      }
      buildServiceArea();
    }

    if (step === 4) {
      var method = $('#lead_delivery_method').val();
      if ((method === 'Email' || method === 'Email and SMS') && !isValidEmail($.trim($('#lead_delivery_email').val()))) {
        if (showErrors) showFieldError($('#lead_delivery_email'), 'Valid delivery email is required.');
        valid = false;
      }
      if ((method === 'SMS' || method === 'Email and SMS') && $.trim($('#lead_delivery_sms').val()) === '') {
        if (showErrors) showFieldError($('#lead_delivery_sms'), 'Delivery SMS number is required.');
        valid = false;
      }
      if ($('.receive-day:checked').length === 0) {
        if (showErrors) showFieldError($('.receive-day').first(), 'Please select at least one delivery day.');
        valid = false;
      }
      if ($('#delivery_start_time').val() && $('#delivery_end_time').val() && $('#delivery_start_time').val() >= $('#delivery_end_time').val()) {
        if (showErrors) showFieldError($('#delivery_end_time'), 'End time must be later than start time.');
        valid = false;
      }
      buildReceiveTime();
    }

    setStepStatus(step, valid ? 'valid' : 'invalid');
    return valid;
  }

  // UPDATED: Remove only the focused/edited field's red border and validation message.
  $('#contractorSignupForm').on('focus input change', 'input, select, textarea', function () {
    var $field = $(this);
    var fieldType = $field.attr('type');
    var fieldName = $field.attr('name');

    if ((fieldType === 'radio' || fieldType === 'checkbox') && fieldName) {
      var $fields = $('[name="' + fieldName + '"]');
      $fields.removeClass('is-invalid');
      $fields.closest('.form-group, .form-check').find('.invalid-feedback').remove();
    } else {
      $field.removeClass('is-invalid');
      $field.closest('.form-group, .form-check').find('.invalid-feedback').remove();
    }
  });

  // UPDATED: Switch between ZIP and State filters without changing backend field names.
  $('.service-filter-type').on('change', function () {
    toggleServiceAreaFilter();
    buildServiceArea();
  });
  toggleServiceAreaFilter();

  $('#lead_delivery_method').on('change', function () {
    var method = $(this).val();
    $('.delivery-email-box').toggleClass('d-none', !(method === 'Email' || method === 'Email and SMS'));
    $('.delivery-sms-box').toggleClass('d-none', !(method === 'SMS' || method === 'Email and SMS'));
  }).trigger('change');

  $('#nextStep').on('click', function () {
    if (validateStep(currentStep, true) && currentStep < totalSteps) showStep(currentStep + 1);
  });

  $('#prevStep').on('click', function () {
    if (currentStep > 1) showStep(currentStep - 1);
  });

  $('[data-step-link]').on('click', function () {
    var targetStep = parseInt($(this).data('step-link'), 10);
    // UPDATED: User can move to any tab without blocking navigation.
    validateStep(currentStep, false);
    showStep(targetStep);
  });


  // UPDATED: Select or clear all state checkboxes.
  $('#select_all_states').on('change', function () {
    $('.state-checkbox').prop('checked', $(this).is(':checked'));
  });
  $('.state-checkbox').on('change', function () {
    $('#select_all_states').prop('checked', $('.state-checkbox').length === $('.state-checkbox:checked').length);
  });
  // UPDATED: Keep Select All synchronized after server-side validation reload.
  $('#select_all_states').prop('checked', $('.state-checkbox').length > 0 && $('.state-checkbox').length === $('.state-checkbox:checked').length);

  // UPDATED: Select or clear all delivery days and keep Select All synchronized.
  $('#select_all_days').on('change', function () {
    $('.receive-day').prop('checked', $(this).is(':checked'));
  });
  $('.receive-day').on('change', function () {
    $('#select_all_days').prop('checked', $('.receive-day').length > 0 && $('.receive-day').length === $('.receive-day:checked').length);
  });
  $('#select_all_days').prop('checked', $('.receive-day').length > 0 && $('.receive-day').length === $('.receive-day:checked').length);

  // UPDATED: Sign Up remains disabled until the agreement is accepted.
  $('#agreement_accept').on('change', function () {
    $('#submitSignup').prop('disabled', !$(this).is(':checked'));
  }).trigger('change');

  $('#contractorSignupForm').on('submit', function (event) {
    var allValid = true;
    for (var step = 1; step <= totalSteps; step++) {
      if (!validateStep(step, true)) allValid = false;
    }
    buildServiceArea();
    buildReceiveTime();
    if (!allValid) {
      event.preventDefault();
      for (var invalidStep = 1; invalidStep <= totalSteps; invalidStep++) {
        if ($('[data-step-link="' + invalidStep + '"]').hasClass('step-invalid')) {
          showStep(invalidStep);
          break;
        }
      }
    } else {
      $('#submitSignup').prop('disabled', true).val('Please Wait...');
    }
  });

  showStep(1);
})();
</script>
