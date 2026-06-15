<!-- UPDATED: Advertiser contract page with real-time Leadspedia status and activation controls. -->
<div class="content-wrapper">
  <section class="content">
    <?php $this->load->view('includes/_messages.php') ?>

    <div class="card mb-2">
      <div class="card-body py-2 d-flex align-items-center">
        <div>
          <h5 class="mb-0"><i class="fa fa-file-text-o"></i> <?= html_escape($title) ?></h5>
          <small class="text-muted">
            Vertical: <?= html_escape(isset($vertical['vertical_name']) ? $vertical['vertical_name'] : '-') ?>
          </small>
        </div>
        <a href="<?= base_url('user_verticals') ?>" class="btn btn-success btn-sm ml-auto">
          <i class="fa fa-arrow-left"></i> My Verticals
        </a>
      </div>
    </div>

    <?php foreach ($contracts as $contract): ?>
      <?php
        // UPDATED: Contract name and status come from the live API when available.
        $contract_name = !empty($contract['runtime_contract_name'])
          ? $contract['runtime_contract_name']
          : (isset($contract['contract_name']) ? $contract['contract_name'] : '-');
        $contract_status = !empty($contract['runtime_contract_status'])
          ? $contract['runtime_contract_status']
          : 'Unavailable';
        $normalized_status = strtolower(str_replace(array(' ', '_', '-'), '', $contract_status));
        $status_badge = 'badge-secondary';

        if (!empty($contract['runtime_status_loaded'])) {
          if ($normalized_status === 'active') {
            $status_badge = 'badge-success';
          } elseif ($normalized_status === 'paused') {
            $status_badge = 'badge-warning';
          } elseif ($normalized_status === 'inactive') {
            $status_badge = 'badge-danger';
          }
        }
      ?>

      <div class="card mb-3">
        <div class="card-header py-2 d-flex align-items-center">
          <h6 class="mb-0"><i class="fa fa-file-text-o"></i> <?= html_escape($contract_name) ?></h6>

          <?php if (!empty($contract['runtime_status_loaded']) && empty($contract['runtime_contract_active'])): ?>
            <?php if (!empty($default_payment_loaded) && !empty($has_default_payment)): ?>
              <!-- UPDATED: Activation is a CSRF-protected POST and is revalidated in the controller. -->
              <?= form_open(
                'user_verticals/activate_contract/' . (int) $contract['local_vertical_id'],
                array('class' => 'ml-auto mb-0', 'onsubmit' => "return confirm('Are you sure you want to activate this contract?');")
              ) ?>
                <button type="submit" class="btn btn-primary btn-sm">
                  <i class="fa fa-check-circle"></i> Click to Activate Contract
                </button>
              <?= form_close() ?>
            <?php elseif (!empty($default_payment_loaded)): ?>
              <!-- UPDATED: No default card exists, so direct the advertiser to the payment page. -->
              <a href="<?= base_url('user_payments') ?>" class="btn btn-primary btn-sm ml-auto">
                <i class="fa fa-credit-card"></i> Add Payment
              </a>
            <?php endif; ?>
          <?php endif; ?>
        </div>

        <div class="card-body p-2">
          <?php if (empty($contract['runtime_status_loaded'])): ?>
            <div class="alert alert-warning py-2 mb-3">
              <i class="fa fa-exclamation-triangle"></i>
              Unable to load the current contract status from Leadspedia.
              <?php if (!empty($contract['runtime_contract_error'])): ?>
                <?= html_escape($contract['runtime_contract_error']) ?>
              <?php endif; ?>
            </div>
          <?php elseif (empty($contract['runtime_contract_active']) && empty($default_payment_loaded)): ?>
            <div class="alert alert-warning py-2 mb-3">
              <i class="fa fa-exclamation-triangle"></i>
              Unable to verify the default payment method.
              <?php if (!empty($default_payment_error)): ?>
                <?= html_escape($default_payment_error) ?>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <div class="table-responsive">
            <table class="table table-bordered table-sm mb-3">
              <tbody>
                <tr><th width="240">Contract Name</th><td><?= html_escape($contract_name) ?></td></tr>
                <tr><th>Contract ID</th><td><?= !empty($contract['contractID']) ? (int) $contract['contractID'] : '-' ?></td></tr>
                <tr>
                  <th>Contract Status</th>
                  <td>
                    <span class="badge <?= $status_badge ?>"><?= html_escape($contract_status) ?></span>
                    <small class="text-muted ml-1">Live from Leadspedia</small>
                  </td>
                </tr>
                <tr><th>Vertical ID</th><td><?= html_escape(isset($contract['leadspedia_vertical_id']) ? $contract['leadspedia_vertical_id'] : '-') ?></td></tr>
                <tr><th>Vertical Name</th><td><?= html_escape(isset($contract['vertical_name']) ? $contract['vertical_name'] : '-') ?></td></tr>
              </tbody>
            </table>
          </div>

          <h6 class="border-bottom pb-2">Contract and Lead Setup</h6>
          <div class="table-responsive">
            <table class="table table-bordered table-sm mb-3">
              <tbody>
                <tr><th width="240">Price</th><td><?= isset($contract['price']) ? number_format((float) $contract['price'], 2) : '-' ?></td></tr>
                <tr><th>Monthly Budget</th><td><?= isset($contract['monthly_budget']) ? number_format((float) $contract['monthly_budget'], 2) : '-' ?></td></tr>
                <tr><th>Leads Per Week</th><td><?= isset($contract['leads_per_week']) ? (int) $contract['leads_per_week'] : '-' ?></td></tr>
                <tr><th>ZIP Codes</th><td><?= nl2br(html_escape(!empty($contract['zip_codes']) ? $contract['zip_codes'] : '-')) ?></td></tr>
                <tr><th>States</th><td><?= html_escape(!empty($contract['state_abbreviations']) ? $contract['state_abbreviations'] : '-') ?></td></tr>
              </tbody>
            </table>
          </div>

          <h6 class="border-bottom pb-2">Delivery Information</h6>
          <div class="table-responsive">
            <table class="table table-bordered table-sm mb-3">
              <tbody>
                <tr><th width="240">Delivery Method</th><td><?= html_escape(!empty($contract['lead_delivery_method']) ? $contract['lead_delivery_method'] : '-') ?></td></tr>
                <tr><th>Delivery Email</th><td><?= html_escape(!empty($contract['lead_delivery_email']) ? $contract['lead_delivery_email'] : '-') ?></td></tr>
                <tr><th>Delivery SMS</th><td><?= html_escape(!empty($contract['lead_delivery_sms']) ? $contract['lead_delivery_sms'] : '-') ?></td></tr>
                <tr><th>Delivery Days</th><td><?= html_escape(!empty($contract['delivery_days']) ? $contract['delivery_days'] : '-') ?></td></tr>
                <tr><th>Start Time</th><td><?= html_escape(!empty($contract['start_time']) ? $contract['start_time'] : '-') ?></td></tr>
                <tr><th>End Time</th><td><?= html_escape(!empty($contract['end_time']) ? $contract['end_time'] : '-') ?></td></tr>
              </tbody>
            </table>
          </div>

          <h6 class="border-bottom pb-2">System Information</h6>
          <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
              <tbody>
                <tr><th width="240">Advertiser Vertical Map ID</th><td><?= isset($contract['advertiser_vertical_map_id']) ? (int) $contract['advertiser_vertical_map_id'] : '-' ?></td></tr>
                <tr><th>Leadspedia Advertiser ID</th><td><?= !empty($contract['advertiserID']) ? (int) $contract['advertiserID'] : '-' ?></td></tr>
                <tr><th>Created At</th><td><?= html_escape(!empty($contract['created_at']) ? $contract['created_at'] : '-') ?></td></tr>
                <tr><th>Updated At</th><td><?= html_escape(!empty($contract['updated_at']) ? $contract['updated_at'] : '-') ?></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </section>
</div>
