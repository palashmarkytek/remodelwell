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

            <!-- Wizard Progress: users can click any tab. Status icons are added after validation. -->
            <ul class="nav nav-pills mb-3 signup-steps" id="signupSteps">
              <li class="nav-item"><a class="nav-link active" href="javascript:void(0)" data-step-link="1">1. Company <span class="step-status-icon ml-1"></span></a></li>
              <li class="nav-item"><a class="nav-link" href="javascript:void(0)" data-step-link="2">2. Qualification <span class="step-status-icon ml-1"></span></a></li>
              <li class="nav-item"><a class="nav-link" href="javascript:void(0)" data-step-link="3">3. Lead Setup <span class="step-status-icon ml-1"></span></a></li>
              <li class="nav-item"><a class="nav-link" href="javascript:void(0)" data-step-link="4">4. Delivery <span class="step-status-icon ml-1"></span></a></li>
              <li class="nav-item"><a class="nav-link" href="javascript:void(0)" data-step-link="5">5. Payment <span class="step-status-icon ml-1"></span></a></li>
              <li class="nav-item"><a class="nav-link" href="javascript:void(0)" data-step-link="6">6. Agreement <span class="step-status-icon ml-1"></span></a></li>
            </ul>

            <!-- Step 1 -->
            <div class="wizard-step" data-step="1">
              <h5 class="mb-3">Company Information</h5>
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
                  <label class="small mb-1">Phone Number *</label>
                  <input type="text" name="phone_number" class="form-control form-control-sm" value="<?php echo set_value('phone_number'); ?>" data-msg="Phone number is required." required>
                </div>
                <div class="col-md-12">
                  <div class="border rounded p-2 mb-2 bg-light">
                    <h6 class="mb-2">Login Information</h6>
                    <div class="form-row">
                      <div class="form-group col-md-12">
                        <label class="small mb-1">Email *</label>
                        <input type="email" name="email" class="form-control form-control-sm" value="<?php echo set_value('email'); ?>" data-msg="Valid email is required." required>
                        <small class="text-muted">Your login password will be generated securely after you complete sign up.</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="wizard-step d-none" data-step="2">
              <h5 class="mb-3">
                Qualification
                <a href="https://calendly.com/remodelwell" target="_blank" class="qualification-help" data-toggle="tooltip" title="Need help? Schedule a call with RemodelWell.">
                  <i class="fa fa-question-circle"></i>
                </a>
              </h5>
              <div class="step-message alert alert-danger small d-none"></div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="small mb-1">Are you currently buying leads or calls? *</label>
                  <select name="currently_buying" id="currently_buying" class="form-control form-control-sm" data-msg="Please select if you are currently buying leads or calls." required>
                    <option value="">Select</option>
                    <option value="Yes" <?php echo set_select('currently_buying', 'Yes'); ?>>Yes</option>
                    <option value="No" <?php echo set_select('currently_buying', 'No'); ?>>No</option>
                  </select>
                </div>
                <div class="form-group col-md-6 annual-revenue-box d-none">
                  <label class="small mb-1">If No, what is your company annual revenue? *</label>
                  <select name="annual_revenue" id="annual_revenue" class="form-control form-control-sm" data-msg="Annual revenue is required when you are not currently buying leads/calls.">
                    <option value="">Select</option>
                    <option value="Just Starting" <?php echo set_select('annual_revenue', 'Just Starting'); ?>>Just Starting</option>
                    <option value="Less than $1 Million" <?php echo set_select('annual_revenue', 'Less than $1 Million'); ?>>Less than $1 Million</option>
                    <option value="$1 - 5 Million" <?php echo set_select('annual_revenue', '$1 - 5 Million'); ?>>$1 - 5 Million</option>
                    <option value="$5 Million+" <?php echo set_select('annual_revenue', '$5 Million+'); ?>>$5 Million+</option>
                  </select>
                  <small class="text-danger revenue-message d-none">We are currently only able to serve companies over $1 Million in annual revenue.</small>
                </div>
                <div class="form-group col-md-6">
                  <label class="small mb-1">Monthly budget on buying leads *</label>
                  <input type="text" name="monthly_budget" class="form-control form-control-sm" placeholder="$5,000 / month" value="<?php echo set_value('monthly_budget'); ?>" data-msg="Monthly budget is required." required>
                </div>
                <!-- UPDATED: Services are selected from active ci_verticals records. -->
                <div class="form-group col-md-12">
                  <label class="small mb-2">What verticals do you want leads for? *</label>
                  <div class="row border rounded p-2 mx-0">
                    <?php if (!empty($verticals)): ?>
                      <?php foreach ($verticals as $vertical): ?>
                        <div class="col-md-4 col-sm-6 mb-2">
                          <div class="form-check">
                            <input class="form-check-input vertical-checkbox" type="checkbox" name="vertical_ids[]" value="<?php echo (int) $vertical['id']; ?>" id="vertical_<?php echo (int) $vertical['id']; ?>" <?php echo set_checkbox('vertical_ids[]', $vertical['id']); ?>>
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

            <!-- Step 3 -->
            <div class="wizard-step d-none" data-step="3">
              <h5 class="mb-3">Lead Setup</h5>
              <div class="step-message alert alert-danger small d-none"></div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="small mb-1">What type of leads do you want? *</label>
                  <select name="lead_type" id="lead_type" class="form-control form-control-sm" data-msg="Lead type is required." required>
                    <option value="">Select</option>
                    <option value="Form Leads" <?php echo set_select('lead_type', 'Form Leads'); ?>>Form Leads</option>
                    <option value="Phone Calls Only" <?php echo set_select('lead_type', 'Phone Calls Only'); ?>>Phone Calls Only</option>
                    <option value="Both Leads and Calls" <?php echo set_select('lead_type', 'Both Leads and Calls'); ?>>Both Leads and Calls</option>
                  </select>
                </div>
                <div class="form-group col-md-6">
                  <label class="small mb-1">How many leads do you want per week? *</label>
                  <input type="number" name="leads_per_week" class="form-control form-control-sm" min="1" value="<?php echo set_value('leads_per_week'); ?>" data-msg="Leads per week is required." required>
                </div>
                <div class="form-group col-md-12">
                  <label class="small mb-1">Filtering - repairs, number of windows, etc.</label>
                  <textarea name="filtering_notes" class="form-control form-control-sm" rows="3"><?php echo set_value('filtering_notes'); ?></textarea>
                </div>
                <div class="form-group col-md-6">
                  <label class="small mb-1">What can’t you work on?</label>
                  <input type="text" name="cannot_work_on" class="form-control form-control-sm" placeholder="Mobile homes, apartments, etc." value="<?php echo set_value('cannot_work_on'); ?>">
                </div>
                <div class="form-group col-md-6">
                  <label class="small mb-1">Where is your service area? *</label>
                  <!-- UPDATED: Google Places search appends selected states, counties, and ZIP codes into service_area. -->
                  <input type="text" id="service_area_search" class="form-control form-control-sm mb-2" placeholder="Search state, county or ZIP code">
                  <textarea name="service_area" id="service_area" class="form-control form-control-sm" rows="3" placeholder="Selected service areas" data-msg="Service area is required." required><?php echo set_value('service_area'); ?></textarea>
                </div>
              </div>
            </div>

            <!-- Step 4 -->
            <div class="wizard-step d-none" data-step="4">
              <h5 class="mb-3">Lead Delivery</h5>
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
                <div class="form-group col-md-6">
                  <label class="small mb-1">Lead Delivery Email <span class="text-danger">*</span></label>
                  <input type="email" name="lead_delivery_email" id="lead_delivery_email" class="form-control form-control-sm" value="<?php echo set_value('lead_delivery_email'); ?>" data-msg="Valid delivery email is required for Email delivery.">
                </div>
                <div class="form-group col-md-6">
                  <label class="small mb-1">Lead Delivery SMS Number</label>
                  <input type="text" name="lead_delivery_sms" id="lead_delivery_sms" class="form-control form-control-sm" value="<?php echo set_value('lead_delivery_sms'); ?>" data-msg="Delivery SMS number is required for SMS delivery.">
                </div>
                <div class="form-group col-md-6">
                  <label class="small mb-1">Do you want leads sent to your CRM?</label>
                  <select name="send_to_crm" id="send_to_crm" class="form-control form-control-sm">
                    <option value="No" <?php echo set_select('send_to_crm', 'No', true); ?>>No</option>
                    <option value="Yes" <?php echo set_select('send_to_crm', 'Yes'); ?>>Yes</option>
                  </select>
                </div>
                <div class="form-group col-md-12 crm-details-box d-none">
                  <label class="small mb-1">CRM Details *</label>
                  <textarea name="crm_details" id="crm_details" class="form-control form-control-sm" rows="3" data-msg="CRM details are required when CRM delivery is selected."><?php echo set_value('crm_details'); ?></textarea>
                </div>
                <div class="form-group col-md-12">
                  <label class="small mb-1">When do you want to receive leads? *</label>
                  <input type="text" name="receive_time" class="form-control form-control-sm" placeholder="Business hours, 24/7, weekdays only" value="<?php echo set_value('receive_time'); ?>" data-msg="Receive time is required." required>
                </div>
              </div>
            </div>

            <!-- Step 5 -->
            <div class="wizard-step d-none" data-step="5">
              <h5 class="mb-3">Pricing, Funding & Demo Payment</h5>
              <div class="step-message alert alert-danger small d-none"></div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="small mb-1">Pricing with estimates</label>
                  <input type="text" name="pricing_estimate" class="form-control form-control-sm" placeholder="Example: $XX per lead/call" value="<?php echo set_value('pricing_estimate'); ?>">
                </div>
                <div class="form-group col-md-6">
                  <label class="small mb-1">Account Funding</label>
                  <input type="text" name="account_funding" class="form-control form-control-sm" placeholder="Example: Initial deposit / prepaid balance" value="<?php echo set_value('account_funding'); ?>">
                </div>
                <div class="form-group col-md-6">
                  <label class="small mb-1">Billing Start Date *</label>
                  <input type="date" name="billing_start_date" class="form-control form-control-sm" value="<?php echo set_value('billing_start_date'); ?>" data-msg="Billing start date is required." required>
                </div>
                <div class="form-group col-md-6">
                  <label class="small mb-1">Payment Demo Status</label>
                  <select name="payment_demo_status" class="form-control form-control-sm">
                    <option value="Demo Payment Pending" <?php echo set_select('payment_demo_status', 'Demo Payment Pending', true); ?>>Demo Payment Pending</option>
                    <option value="Demo Payment Completed" <?php echo set_select('payment_demo_status', 'Demo Payment Completed'); ?>>Demo Payment Completed</option>
                  </select>
                  <small class="text-muted">Demo step only. Real payment gateway can be added later.</small>
                </div>
                <div class="form-group col-md-12">
                  <label class="small mb-1">Payment Information</label>
                  <textarea name="payment_information" class="form-control form-control-sm" rows="3" placeholder="Demo payment note only. Do not collect card data here."><?php echo set_value('payment_information'); ?></textarea>
                </div>
              </div>
            </div>

            <!-- Step 6 -->
            <div class="wizard-step d-none" data-step="6">
              <h5 class="mb-3">Agreement & Complete Sign Up</h5>
              <div class="step-message alert alert-danger small d-none"></div>
              <div class="alert alert-light border small">
                By completing signup, the contractor confirms the information is correct and agrees to be contacted by RemodelWell for lead delivery setup.
              </div>
              <div class="form-check mb-3">
                <input type="checkbox" name="agreement_accept" value="Yes" class="form-check-input" id="agreement_accept" data-msg="Please accept the agreement to complete signup." required>
                <label class="form-check-label" for="agreement_accept">I agree and want to complete sign up.</label>
              </div>
              <div class="alert alert-info small">
                After submission, data will be saved in ci_admin and ci_advertisers. Your generated login credentials will be shown on the next page.
              </div>
            </div>

            <div class="d-flex justify-content-between mt-3">
              <button type="button" class="btn btn-secondary btn-sm" id="prevStep" disabled>Previous</button>
              <button type="button" class="btn btn-primary btn-sm" id="nextStep">Next</button>
              <input type="submit" name="submit" id="submitSignup" class="btn btn-success btn-sm d-none" value="Complete Sign Up">
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
  .signup-steps .nav-link.step-invalid { border: 1px solid #dc3545; }
  .step-status-icon { font-weight: bold; }
  .step-status-icon.valid { color: #28a745; }
  .step-status-icon.invalid { color: #dc3545; }
  .wizard-step { min-height: 280px; }
  .qualification-help { color: #fff; background: #17a2b8; border-radius: 50%; padding: 1px 5px; font-size: 12px; vertical-align: middle; }
  .qualification-help:hover { color: #fff; text-decoration: none; }
  .invalid-feedback { display: block; font-size: 12px; }
</style>

<script>
(function () {
  var currentStep = 1;
  var totalSteps = 6;

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
      $icon.addClass('valid').html('&#10004;'); // green tick
    }

    if (status === 'invalid') {
      $link.addClass('step-invalid');
      $icon.addClass('invalid').html('&#10008;'); // red cross
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

  function showStepMessage(step, message) {
    $('.wizard-step[data-step="' + step + '"] .step-message').removeClass('d-none').html(message);
  }

  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  function validateRequired($field, showErrors) {
    var value = $.trim($field.val());
    var message = $field.data('msg') || 'This field is required.';
    showErrors = (showErrors !== false);

    if ($field.attr('type') === 'checkbox') {
      if (!$field.is(':checked')) {
        if (showErrors) showFieldError($field, message);
        return false;
      }
      return true;
    }

    if (value === '') {
      if (showErrors) showFieldError($field, message);
      return false;
    }

    if ($field.attr('type') === 'email' && !isValidEmail(value)) {
      if (showErrors) showFieldError($field, message);
      return false;
    }

    return true;
  }

  function validateStep(step, showErrors) {
    var valid = true;
    showErrors = (showErrors !== false);

    if (showErrors) {
      clearStepErrors(step);
    }

    var $step = $('.wizard-step[data-step="' + step + '"]');
    $step.find('input:visible, select:visible, textarea:visible').each(function () {
      var $field = $(this);
      if ($field.prop('required') && !validateRequired($field, showErrors)) {
        valid = false;
      }
    });

    // Step-specific validation and messages.
    if (step === 2) {
      var currentlyBuying = $('#currently_buying').val();
      var annualRevenue = $('#annual_revenue').val();

      if (currentlyBuying === 'No') {
        if (annualRevenue === '') {
          if (showErrors) showFieldError($('#annual_revenue'), 'Annual revenue is required.');
          valid = false;
        }
        if (annualRevenue === 'Just Starting' || annualRevenue === 'Less than $1 Million') {
          if (showErrors) {
            showFieldError($('#annual_revenue'), 'We are currently only able to serve companies over $1 Million in annual revenue.');
            showStepMessage(step, 'This signup cannot continue because the selected annual revenue is below the current qualification requirement.');
          }
          valid = false;
        }
      }
    }

    if (step === 3) {
      var leadsPerWeek = parseInt($('[name="leads_per_week"]').val(), 10);
      if (isNaN(leadsPerWeek) || leadsPerWeek <= 0) {
        if (showErrors) showFieldError($('[name="leads_per_week"]'), 'Please enter leads per week greater than 0.');
        valid = false;
      }
    }

    if (step === 4) {
      var deliveryMethod = $('#lead_delivery_method').val();
      if ((deliveryMethod === 'Email' || deliveryMethod === 'Email and SMS') && !isValidEmail($.trim($('#lead_delivery_email').val()))) {
        if (showErrors) showFieldError($('#lead_delivery_email'), 'Valid delivery email is required for Email delivery.');
        valid = false;
      }
      if ((deliveryMethod === 'SMS' || deliveryMethod === 'Email and SMS') && $.trim($('#lead_delivery_sms').val()) === '') {
        if (showErrors) showFieldError($('#lead_delivery_sms'), 'Delivery SMS number is required for SMS delivery.');
        valid = false;
      }
      if ($('#send_to_crm').val() === 'Yes' && $.trim($('#crm_details').val()) === '') {
        if (showErrors) showFieldError($('#crm_details'), 'CRM details are required when CRM delivery is selected.');
        valid = false;
      }
    }

    if (!valid && showErrors) {
      showStepMessage(step, 'Please fix the highlighted fields before continuing.');
    }

    setStepStatus(step, valid ? 'valid' : 'invalid');
    return valid;
  }

  function updateConditionalFields() {
    var currentlyBuying = $('#currently_buying').val();
    $('.annual-revenue-box').toggleClass('d-none', currentlyBuying !== 'No');
    $('#annual_revenue').prop('required', currentlyBuying === 'No');

    var annualRevenue = $('#annual_revenue').val();
    $('.revenue-message').toggleClass('d-none', !(annualRevenue === 'Just Starting' || annualRevenue === 'Less than $1 Million'));

    var sendToCrm = $('#send_to_crm').val() === 'Yes';
    $('.crm-details-box').toggleClass('d-none', !sendToCrm);
    $('#crm_details').prop('required', sendToCrm);
  }

  function validateAllSteps(showErrors) {
    var firstInvalidStep = null;

    updateConditionalFields();
    for (var step = 1; step <= totalSteps; step++) {
      var stepValid = validateStep(step, showErrors);
      if (!stepValid && firstInvalidStep === null) {
        firstInvalidStep = step;
      }
    }

    return firstInvalidStep;
  }

  $('#nextStep').on('click', function () {
    updateConditionalFields();
    if (validateStep(currentStep) && currentStep < totalSteps) {
      showStep(currentStep + 1);
    }
  });

  $('#prevStep').on('click', function () {
    if (currentStep > 1) {
      showStep(currentStep - 1);
    }
  });

  $('[data-step-link]').on('click', function () {
    // Users can move to any tab by clicking the tab name.
    validateStep(currentStep, false);
    showStep(parseInt($(this).attr('data-step-link'), 10));
  });

  $('#currently_buying, #annual_revenue, #send_to_crm, #lead_delivery_method').on('change', function () {
    updateConditionalFields();
    validateStep(currentStep, false);
  });

  $('#contractorSignupForm input, #contractorSignupForm select, #contractorSignupForm textarea').on('blur change', function () {
    updateConditionalFields();
    validateStep(currentStep, false);
  });

  $('#contractorSignupForm').on('submit', function (e) {
    var firstInvalidStep = validateAllSteps(true);

    if (firstInvalidStep !== null) {
      e.preventDefault();
      showStep(firstInvalidStep);
      return false;
    }

    $('#submitSignup').prop('disabled', true).val('Submitting...');
    return true;
  });

  if ($.fn.tooltip) {
    $('[data-toggle="tooltip"]').tooltip();
  }

  updateConditionalFields();
  showStep(currentStep);
})();
</script>

<!-- UPDATED: Google Places Autocomplete for state/county/ZIP selections. Configure google_places_api_key in ci_general_settings. -->
<?php if (!empty($this->general_settings['google_places_api_key'])): ?>
<script>
function initServiceAreaAutocomplete() {
  var input = document.getElementById('service_area_search');
  var target = document.getElementById('service_area');
  if (!input || !target || !window.google || !google.maps || !google.maps.places) return;
  var autocomplete = new google.maps.places.Autocomplete(input, {types: ['(regions)'], componentRestrictions: {country: 'us'}});
  autocomplete.addListener('place_changed', function () {
    var place = autocomplete.getPlace();
    var value = place.formatted_address || place.name || input.value;
    if (!value) return;
    var current = target.value.split(',').map(function(v){ return v.trim(); }).filter(Boolean);
    if (current.indexOf(value) === -1) current.push(value);
    target.value = current.join(', ');
    input.value = '';
    target.dispatchEvent(new Event('change'));
  });
}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= html_escape($this->general_settings['google_places_api_key']) ?>&libraries=places&callback=initServiceAreaAutocomplete"></script>
<?php endif; ?>
