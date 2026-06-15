<!-- UPDATED: Contract information now opens on a separate page, matching /user_verticals. -->
<div class="mk-dashboard"><div class="content-wrapper"><section class="content">
<div class="card mb-2"><div class="card-body py-2 d-flex align-items-center"><div>
<h5 class="mb-0"><i class="fa fa-list"></i> <?php echo $title; ?></h5>
<small class="text-muted">Advertiser: <?php echo html_escape($advertiser['name']); ?><?php if (!empty($advertiser['company_name'])): ?> - <?php echo html_escape($advertiser['company_name']); ?><?php endif; ?></small>
</div><a href="<?php echo base_url('admin'); ?>" class="btn btn-success btn-sm ml-auto"><i class="fa fa-list"></i> Advertiser List</a></div></div>
<div class="card"><div class="card-body p-2"><?php $this->load->view('includes/_messages.php'); ?><div class="table-responsive">
<table class="table table-bordered table-hover table-sm mb-0"><thead class="thead-light small"><tr>
<th width="70">#</th><th>Vertical ID</th><th>Vertical Name</th><th width="100" class="text-center">Status</th><th width="120" class="text-center">View Contract</th>
</tr></thead><tbody class="small">
<?php if (!empty($verticals)): foreach ($verticals as $key => $vertical): ?>
<tr>
<td><?= $key + 1 ?></td>
<td><?= html_escape($vertical['leadspedia_vertical_id'] ?: '-') ?></td>
<td><?= html_escape($vertical['vertical_name']) ?></td>
<td class="text-center"><?php if (!empty($vertical['mapping_is_active'])): ?><span class="badge badge-success">Assigned</span><?php else: ?><span class="badge badge-secondary">Inactive</span><?php endif; ?></td>
<td class="text-center">
  <!-- UPDATED: Keep contract details under the existing advertiser mapping URL. -->
  <a href="<?= base_url('admin/mapping/' . (int) $advertiser['admin_id'] . '/contract/' . (int) $vertical['vertical_id']) ?>"
     class="btn btn-info btn-sm" title="View Contract">
    <i class="fa fa-eye"></i>
  </a>
</td>
</tr>
<?php endforeach; else: ?><tr><td colspan="5" class="text-center py-3">No vertical is assigned to this advertiser.</td></tr><?php endif; ?>
</tbody></table></div></div></div></section></div></div>
