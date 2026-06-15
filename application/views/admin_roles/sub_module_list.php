<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css">

<?php
/* ===========================================================
   Sub Module Setting (UI update only)
   - NO change in existing logic/loops/routes/Datatable init
   - Updated header + compact table + aligned actions like settings/item
   =========================================================== */
?>
<div class="mk-dashboard">
<div class="content-wrapper">
	<section class="content">
		<!-- For Messages -->
		<?php $this->load->view('includes/_messages.php'); ?>

		<?php $parent_module = (int) $this->uri->segment(3); ?>

		<div class="card">
			<!-- Modern header bar -->
			<div class="card-body py-2 d-flex align-items-center">
				<h5 class="mb-0">
					<i class="fa fa-list"></i> Sub Module Setting
				</h5>

				<a href="<?= base_url('admin_roles/sub_module_add/' . $parent_module); ?>" class="btn btn-success btn-sm ml-auto">
					<i class="fa fa-plus"></i> Add New
				</a>
			</div>

			<div class="card-body p-2">
				<div class="table-responsive">
				<table id="example1" class="table table-bordered table-hover table-sm mb-0">
					<thead class="thead-light small">
						<tr>
							<th style="width:80px;">ID</th>
							<th>Name</th>
							<th>Link</th>
							<th style="width:220px;">Operations</th>
							<th style="width:140px; text-align:right;">Action</th>
						</tr>
					</thead>

					<tbody class="small">
						<?php if (!empty($records)): ?>
							<?php foreach ($records as $record): ?>
								<?php
									$subId    = isset($record['id']) ? (int)$record['id'] : 0;
									$parentId = isset($record['parent']) ? (int)$record['parent'] : $parent_module;
									$name     = isset($record['name']) ? $record['name'] : '';
									$link     = isset($record['link']) ? $record['link'] : '';
									// ✅ new column (fallback if not present)
									$ops      = isset($record['operation']) && $record['operation'] !== ''
										? $record['operation']
										: 'add|edit|delete|access|change_status';
								?>
								<tr>
									<td class="align-middle"><?= $subId; ?></td>
									<td class="align-middle font-weight-semibold"><?= trans($name); ?></td>

									<td class="align-middle">
										<?= html_escape($link); ?>
									</td>

									<td class="align-middle">
										<code><?= html_escape($ops); ?></code>
									</td>

									<td class="align-middle text-right">
										<a href="<?= site_url("admin_roles/sub_module_edit/" . $subId); ?>" class="btn btn-warning btn-sm mr-1">
											<i class="fa fa-edit"></i>
										</a>

										<a href="<?= site_url("admin_roles/sub_module_delete/" . $subId . '/' . $parentId); ?>"
										   onclick="return confirm('Are you sure you want to delete?')"
										   class="btn btn-danger btn-sm">
											<i class="fa fa-remove"></i>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr>
								<td colspan="5" class="text-center small text-muted py-4">
									<?= trans('no_record_found') ?: 'No sub-modules found.'; ?>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>

				</table>
			</div>
			</div>
		</div>
	</section>
	<!-- /.content -->
</div>
</div>

<!-- DataTables -->
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>

<script>
  $(function () {
    // ✅ Prevent re-init errors (NO change)
    if ($.fn.DataTable.isDataTable('#example1')) {
      $('#example1').DataTable().destroy();
    }
    $("#example1").DataTable();
  });
</script>
