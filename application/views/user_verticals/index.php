<!-- UPDATED: Removed Leadspedia Status, renamed Action to Status, and added View Contract. -->
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css">
<div class="content-wrapper">
  <section class="content">
    <?php $this->load->view('includes/_messages.php') ?>
    <div class="card">
      <div class="card-header"><h3 class="card-title"><i class="fa fa-list"></i> My Verticals</h3></div>
      <div class="card-body p-2">
        <div class="table-responsive">
          <table id="user-vertical-list" class="table table-bordered table-striped table-sm mb-0">
            <thead>
              <tr>
                <th width="70">#</th>
                <th>Vertical ID</th>
                <th>Vertical Name</th>
                <th width="100" class="text-center">Status</th>
                <th width="120" class="text-center">View Contract</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($verticals as $key => $vertical): ?>
              <tr>
                <td><?= $key + 1 ?></td>
                <td><?= html_escape($vertical['vertical_id']) ?></td>
                <td><?= html_escape($vertical['vertical_name']) ?></td>
                <td class="text-center">
                  <label class="switch-xs mb-0" title="Active / Inactive">
                    <input type="checkbox" class="user-vertical-status" data-id="<?= (int) $vertical['advertiser_vertical_map_id'] ?>" <?= ((int) $vertical['is_active'] === 1) ? 'checked' : '' ?>>
                    <span class="slider-xs"></span>
                  </label>
                </td>
                <td class="text-center">
                  <!-- UPDATED: Open the contract detail page for this advertiser-owned vertical. -->
                  <a href="<?= base_url('user_verticals/contract/' . (int) $vertical['local_vertical_id']) ?>"
                     class="btn btn-info btn-sm" title="View Contract">
                    <i class="fa fa-eye"></i>
                  </a>
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
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>
<script>
$(function(){
  $('#user-vertical-list').DataTable({dom:'lrtip', pageLength:100, autoWidth:false, lengthChange:false, order:[[2,'asc']]});
  $('body').on('change','.user-vertical-status',function(){
    var $toggle=$(this), status=$toggle.is(':checked')?1:0;
    $toggle.prop('disabled',true);
    $.ajax({url:'<?= base_url('user_verticals/change_status') ?>',type:'POST',dataType:'json',data:{
      '<?= $this->security->get_csrf_token_name(); ?>':'<?= $this->security->get_csrf_hash(); ?>',id:$toggle.data('id'),status:status
    }}).done(function(r){
      if(r.status){$.notify(r.message,'success');}else{$toggle.prop('checked',!status);$.notify(r.message,'error');}
    }).fail(function(){$toggle.prop('checked',!status);$.notify('Unable to update vertical status.','error');})
      .always(function(){$toggle.prop('disabled',false);});
  });
});
</script>
