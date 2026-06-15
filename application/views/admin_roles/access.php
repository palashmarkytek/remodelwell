<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">


    <!-- Main content -->
    <div class="mk-role-permission-page">
    <section class="content">
        <div class="card card-default color-palette-bo">
            <div class="card-header">
                <div class="role-permission-header">
                    <div>
                        <h3 class="card-title mb-1">
                            <i class="fa fa-lock"></i>
                            &nbsp; <?= $title ?>
                        </h3>
                        <div class="permission-legend mt-1">
                            <span><span class="legend-dot legend-granted"></span>
                                <?= trans('granted') ?? 'Granted' ?></span>
                            <span><span class="legend-dot legend-denied"></span>
                                <?= trans('denied') ?? 'Denied' ?></span>
                        </div>
                    </div>
                    <div class="text-right mt-2 mt-md-0">
                        <div class="role-permission-chip">
                            <i class="fa fa-user-shield"></i>
                            <span><?= trans('permission_access') ?>:</span>
                            <span><?= strtoupper($record['admin_role_title']) ?></span>
                        </div>
                        <div class="mt-2 text-right">
                            <a href="#" onclick="window.history.go(-1); return false;"
                                class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-reply mr5"></i> <?= trans('back') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            // Group sub-modules by parent module_id (for rendering)
            $subModulesByParent = array();
            if (!empty($sub_modules)) {
                foreach ($sub_modules as $sm) {
                    $parentId = (int) $sm['parent'];
                    if (!isset($subModulesByParent[$parentId])) {
                        $subModulesByParent[$parentId] = array();
                    }
                    $subModulesByParent[$parentId][] = $sm;
                }
            }
            // Allowed sub-module ids for this role
            $subModuleAccessIds = !empty($sub_module_access) ? $sub_module_access : array();
            ?>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">

                        <?php foreach ($modules as $kk => $module): ?>
                            <div class="module-card">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="module-title">
                                            <h5 class="m-0">
                                                <strong class="f-16"><?= trans($module['module_name']) ?></strong>
                                            </h5>
                                        </div>
                                        <div class="module-title-badge">
                                            <i class="fa fa-folder-open"></i>
                                            <?= $module['controller_name']; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-9">
                                        <!-- Module operations -->
                                        <div class="module-operations-label">
                                            <i class="fa fa-sliders"></i>
                                            <?= trans('operations') ?>
                                        </div>

                                        <div class="row mb-2">
                                            <?php foreach (explode("|", $module['operation']) as $k => $operation): ?>
                                                <?php
                                                $operation = trim($operation);
                                                if ($operation === '')
                                                    continue;

                                                $checkboxId = 'cb_' . $kk . $k;
                                                $isChecked = in_array($module['controller_name'] . '/' . $operation, $access);
                                                ?>
                                                <div class="col-md-4 col-sm-6 mb-2">
                                                    <div class="operation-pill">
                                                        <span>
                                                            <input type="checkbox" class="tgl tgl-ios tgl_checkbox"
                                                                data-module="<?= $module['controller_name'] ?>"
                                                                data-operation="<?= $operation; ?>" id="<?= $checkboxId ?>"
                                                                <?php if ($isChecked)
                                                                    echo 'checked="checked"'; ?> />
                                                            <label class="tgl-btn" for="<?= $checkboxId ?>"></label>
                                                        </span>
                                                        <span class="label-text"><?= trans($operation) ?></span>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <!-- Sub-modules under this module -->
                                        <?php if (!empty($subModulesByParent[(int) $module['module_id']])): ?>
                                            <div class="submodule-section-title">
                                                <i class="fa fa-sitemap"></i>
                                                <?= trans('sub_module') ?: 'Sub Modules' ?>
                                            </div>

                                            <div class="row">
                                                <?php foreach ($subModulesByParent[(int) $module['module_id']] as $sub): ?>
                                                    <?php
                                                    $subId = (int) $sub['id'];
                                                    $subOps = trim($sub['operation'] ?? 'add|edit|delete|access|change_status'); // default fallback
                                                    $subOpsArr = array_filter(array_map('trim', explode('|', $subOps)));

                                                    $subCbPrefix = 'sub_' . $kk . '_' . $subId;
                                                    ?>
                                                    <div class="col-md-12 mb-2">
                                                        <div class="submodule-pill"
                                                            style="width:100%;justify-content:space-between;">
                                                            <span class="label-text">
                                                                <strong><?= trans($sub['name']); ?></strong>
                                                                <?php if (!empty($sub['link'])): ?>
                                                                    <small class="text-muted">(<?= $sub['link']; ?>)</small>
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>

                                                        <!-- ✅ Sub-module operations like module operations -->
                                                        <div class="row mt-2">
                                                            <?php foreach ($subOpsArr as $i => $op): ?>
                                                                <?php
                                                                $op = trim($op);
                                                                if ($op === '')
                                                                    continue;

                                                                $subOpCbId = $subCbPrefix . '_' . $i;
                                                                $isChecked = in_array($subId . '/' . $op, $subModuleAccessIds);
                                                                ?>
                                                                <div class="col-md-3 col-sm-6 mb-2">
                                                                    <div class="operation-pill">
                                                                        <span>
                                                                            <input type="checkbox" class="tgl tgl-ios tgl_checkbox"
                                                                                data-submodule-id="<?= $subId; ?>"
                                                                                data-sub-operation="<?= $op; ?>" id="<?= $subOpCbId; ?>"
                                                                                <?php if ($isChecked)
                                                                                    echo 'checked="checked"'; ?> />

                                                                            <label class="tgl-btn" for="<?= $subOpCbId; ?>"></label>
                                                                        </span>
                                                                        <span class="label-text"><?= trans($op) ?></span>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($modules)): ?>
                            <div class="alert alert-info mb-0">
                                <i class="fa fa-info-circle"></i>
                                <?= trans('no_module_found') ?? 'No modules configured yet.' ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
</div>
<input type="hidden" id="csrf_name" value="<?= $this->security->get_csrf_token_name(); ?>">

<input type="hidden" id="csrf_hash" value="<?= $this->security->get_csrf_hash(); ?>">

<script>
    $("body").on("change", ".tgl_checkbox", function () {

        var $el = $(this);

        // ✅ always pull latest CSRF from hidden inputs
        var csrfName = $("#csrf_name").val();
        var csrfHash = $("#csrf_hash").val();

        // ✅ detect submodule fields safely using attr()
        var subId = $el.attr("data-submodule-id");
        var subOp = $el.attr("data-sub-operation");
        var isSubModule = (typeof subId !== "undefined" && subId !== "");

        var postData = {};
        postData[csrfName] = csrfHash;

        postData.admin_role_id = <?= (int) $record['admin_role_id']; ?>;
        postData.status = $el.is(":checked") ? 1 : 0;

        if (isSubModule) {
            postData.is_sub_module = 1;
            postData.sub_module_id = subId;
            postData.operation = subOp;
        } else {
            postData.module = $el.attr("data-module");
            postData.operation = $el.attr("data-operation");
        }

        $.ajax({
            url: "<?= base_url("admin_roles/set_access") ?>",
            method: "POST",
            data: postData,
            dataType: "json",
            success: function (resp) {
                // ✅ refresh CSRF for next request (CRITICAL)
                if (resp && resp.csrf_hash) {
                    $("#csrf_hash").val(resp.csrf_hash);
                }

                if (resp && resp.status === "ok") {
                    $.notify("Status Changed Successfully", "success");
                } else {
                    $.notify("Save failed", "error");
                    console.log("Unexpected response:", resp);
                }
            },
            error: function (xhr) {
                // ✅ rollback toggle UI if request failed
                $el.prop("checked", !$el.prop("checked"));

                console.log("Save failed:", xhr.status, xhr.responseText);
                $.notify("Save failed (check console/network)", "error");
            }
        });

    });
</script>