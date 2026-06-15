<div class="datalist">
  <div class="table-responsive">
  <table id="associate_list" class="table table-bordered table-hover table-sm mb-0">
    <thead class="thead-light small">
      <tr>
        <th style="width:60px;">Id</th>
        <th>Advertiser</th>
        <th><?= trans('email') ?></th>
        <th style="width:100px; text-align:center;"><?= trans('status') ?></th>
        <th style="width:190px; text-align:right;"><?= trans('action') ?></th>
      </tr>
    </thead>

    <tbody class="small">
      <?php if (count($info) > 0) { ?>
        <?php foreach ($info as $row): ?>
          <tr>
            <td class="align-middle"><?= $row['display_id'] ?></td>

            <td class="align-middle">
              <div class="mb-1"><?= $row['name'] ?> (<small class="text-muted"><?= $row['admin_role_title'] ?></small>)
              </div>
              <?php if (!empty($row['source_id'])): ?>
                <small class="text-muted">Source Id: <?= $row['source_id'] ?></small>
              <?php endif; ?>
            </td>

            <td class="align-middle"><?= $row['email'] ?></td>

            <td class="align-middle text-center">
              <?php if (!in_array($row['admin_id'], array(1))): ?>
                <label class="switch-xs mb-0">
                  <input type="checkbox" class="tgl_checkbox" data-id="<?= $row['admin_id'] ?>" <?= ($row['is_active'] == 1) ? 'checked' : '' ?>>
                  <span class="slider-xs"></span>
                </label>
              <?php else: ?>
                <span class="badge badge-<?= ((int) $row['is_active'] === 1) ? 'success' : 'secondary' ?> small">
                  <?= ((int) $row['is_active'] === 1) ? 'Active' : 'Inactive' ?>
                </span>
              <?php endif; ?>
            </td>

            <td class="align-middle text-right">
              <!-- UPDATED: Show Leadspedia create button only until API creation succeeds. -->
              <?php if (!empty($row['advertiser_id']) && $row['leadspedia_status'] !== 'success'): ?>
                <button type="button"
                  class="btn btn-success btn-sm mr-1 py-0 px-1 create-leadspedia-user"
                  data-id="<?= $row['admin_id'] ?>"
                  data-toggle="tooltip"
                  title="Create User in Leadspedia">
                  <span class="leadspedia-btn-icon"><i class="fa fa-user-plus"></i></span>
                  <span class="leadspedia-btn-loader d-none"><i class="fa fa-spinner fa-spin"></i></span>
                </button>
              <?php endif; ?>

              <!-- UPDATED: Mapping remains available for each advertiser. -->
              <?php if (!in_array($row['admin_id'], array(1))): ?>
                <a href="<?= base_url('admin/mapping/' . $row['admin_id']); ?>" class="btn btn-info btn-sm mr-1 py-0 px-1"
                  data-toggle="tooltip" title="Mapping">
                  <i class="fa fa-random"></i>
                </a>
              <?php endif; ?>
              
              <a href="<?= base_url('admin/edit/' . $row['admin_id']); ?>" class="btn btn-warning btn-sm mr-1 py-0 px-1"
                data-toggle="tooltip" title="Edit">
                <i class="fa fa-edit"></i>
              </a>

              <?php if (!in_array($row['admin_id'], array(1))): ?>
                <a href="<?= base_url('admin/delete/' . $row['admin_id']); ?>"
                  onclick="return confirm('Are you sure to delete?')" class="btn btn-danger btn-sm py-0 px-1"
                  data-toggle="tooltip" title="Delete">
                  <i class="fa fa-trash"></i>
                </a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php } else { ?>
        <tr>
          <td colspan="5" class="text-center small">No record found.</td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
</div>