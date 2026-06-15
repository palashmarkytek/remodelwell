<?php
/* ===========================================================
   Admin Roles List (UI update only)
   - NO change in data/logic/conditions/JS behavior
   - Only layout + classes to match slick modern style (settings/item)
   =========================================================== */
?>
<div class="mk-dashboard">
<div class="content-wrapper">
	<section class="content">

		<div class="card">
			<!-- Modern header bar -->
			<div class="card-body py-2 d-flex align-items-center">
				<h5 class="mb-0">
					<i class="fa fa-list"></i> <?= $title ?>
				</h5>

				<a href="<?= base_url('admin_roles/add'); ?>" class="btn btn-success btn-sm ml-auto">
					<i class="fa fa-plus"></i> <?= trans('add_new_role') ?>
				</a>
			</div>

			<div class="card-body p-2">

				<!-- ✅ CSRF holders (so JS can update token after each request) -->
				<input type="hidden" id="csrf_name" value="<?= $this->security->get_csrf_token_name(); ?>">
				<input type="hidden" id="csrf_hash" value="<?= $this->security->get_csrf_hash(); ?>">

				<!-- Table (compact like settings/item) -->
				<div class="table-responsive">
				<table id="example2" class="table table-bordered table-hover table-sm mb-0">
					<thead class="thead-light small">
						<tr>
							<th style="width:80px;"><?= trans('id') ?></th>
							<th><?= trans('admin_role') ?></th>
							<th style="width:120px; text-align:center;"><?= trans('status') ?></th>
							<th style="width:120px; text-align:center;"><?= trans('permission') ?></th>
							<th style="width:200px; text-align:right;"><?= trans('action') ?></th>
						</tr>
					</thead>

					<tbody class="small">
						<?php foreach ($records as $record): ?>
							<?php
								$roleId     = (int)$record['admin_role_id'];
								$roleTitle  = isset($record['admin_role_title']) ? $record['admin_role_title'] : '';
								$roleStatus = isset($record['admin_role_status']) ? (int)$record['admin_role_status'] : 0;

								// Protected roles (you already use 1 in UI, delete uses 1..7)
								$isProtectedForToggle = in_array($roleId, array(1), true);
								$isProtectedForDelete = in_array($roleId, array(1,2,3,4,5,6,7), true);
							?>

							<tr>
								<!-- ✅ Show actual id (matches header) -->
								<td class="align-middle"><?= $roleId; ?></td>

								<td class="align-middle font-weight-semibold"><?= html_escape($roleTitle); ?></td>

								<td class="align-middle text-center">
									<?php if (!$isProtectedForToggle): ?>
										<input
											class="tgl tgl-ios tgl_checkbox"
											data-id="<?= $roleId; ?>"
											id="cb_<?= $roleId; ?>"
											type="checkbox"
											<?= ($roleStatus === 1) ? 'checked="checked"' : ''; ?>
										/>
										<label class="tgl-btn" for="cb_<?= $roleId; ?>"></label>
									<?php else: ?>
										<span class="badge badge-secondary px-2 py-1">Protected</span>
									<?php endif; ?>
								</td>

								<td class="align-middle text-center">
									<?php if (!$isProtectedForToggle): ?>
										<a href="<?= site_url("admin_roles/access/" . $roleId); ?>" class="btn btn-info btn-sm">
											<i class="fa fa-sliders"></i>
										</a>
									<?php else: ?>
										<span class="text-muted">—</span>
									<?php endif; ?>
								</td>

								<td class="align-middle text-right">
									<?php if (!$isProtectedForToggle): ?>
										<a href="<?= site_url("admin_roles/edit/" . $roleId); ?>" class="btn btn-warning btn-sm mr-1">
											<i class="fa fa-edit"></i>
										</a>
									<?php endif; ?>

									<?php if (!$isProtectedForDelete): ?>
										<a href="<?= site_url("admin_roles/delete/" . $roleId); ?>"
										   onclick="return confirm('Are you sure you want to delete?')"
										   class="btn btn-danger btn-sm">
											<i class="fa fa-remove"></i>
										</a>
									<?php endif; ?>
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

<script>
/* ===========================================================
   Status toggle (NO change in logic)
   - Kept your CSRF refresh behavior and rollback safety
   =========================================================== */
$("body").on("change", ".tgl_checkbox", function () {

	var $el = $(this);
	var roleId = $el.attr("data-id");
	var newStatus = $el.is(":checked") ? 1 : 0;

	// ✅ always take latest CSRF from hidden inputs
	var csrfName = $("#csrf_name").val();
	var csrfHash = $("#csrf_hash").val();

	var postData = {};
	postData[csrfName] = csrfHash;
	postData.id = roleId;
	postData.status = newStatus;

	$.ajax({
		url: '<?= base_url("admin_roles/change_status") ?>',
		method: 'POST',
		data: postData,
		dataType: 'json',
		success: function(resp){

			// ✅ refresh CSRF for next toggle (CRITICAL if csrf_regenerate=true)
			if (resp && resp.csrf_hash) {
				$("#csrf_hash").val(resp.csrf_hash);
			}

			if (resp && resp.status === 'ok') {
				$.notify("Status Changed Successfully", "success");
			} else {
				// rollback UI if server did not confirm ok
				$el.prop("checked", !newStatus);
				$.notify("Save failed", "error");
				console.log("Unexpected response:", resp);
			}
		},
		error: function(xhr){
			// rollback UI if request failed
			$el.prop("checked", !newStatus);
			$.notify("Save failed (check console/network)", "error");
			console.log("XHR Error:", xhr.status, xhr.responseText);
		}
	});
});
</script>
