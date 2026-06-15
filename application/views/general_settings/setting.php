<!-- Content Wrapper. Contains page content -->
<div class="mk-dashboard">
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="card card-default color-palette-bo">
            <div class="card-header">
              <div class="d-inline-block">
                  <h3 class="card-title"> <i class="fa fa-plus"></i>
                  <?= trans('general_settings') ?> </h3>
              </div>
            </div>
            <div class="card-body">   
                 <!-- For Messages -->
                <?php $this->load->view('includes/_messages.php') ?>

                <?php echo form_open_multipart(base_url('general_settings/add')); ?>	
                <!-- Nav tabs -->
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#main" role="tab" aria-controls="main" aria-selected="true"><?= trans('general_setting') ?></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#email" role="tab" aria-controls="email" aria-selected="false"><?= trans('email_setting') ?></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="pills-leadspedia-tab" data-toggle="pill" href="#leadspedia" role="tab" aria-controls="leadspedia" aria-selected="false">Leadspedia Setting</a>
                  </li>
                  <!-- UPDATED: Registration agreement is managed from its own settings tab. -->
                  <li class="nav-item">
                    <a class="nav-link" id="pills-agreement-tab" data-toggle="pill" href="#signup-agreement" role="tab" aria-controls="signup-agreement" aria-selected="false">Signup Agreement</a>
                  </li>
                </ul>

                 <!-- Tab panes -->
                <div class="tab-content">

                    <!-- General Setting -->
                    <div role="tabpanel" class="tab-pane active" id="main">
                        <div class="form-group">
                            <label class="control-label"><?= trans('favicon') ?> (25*25)</label><br/>
                            <?php if(!empty($general_settings['favicon'])): ?>
                               <p><img src="<?= base_url($general_settings['favicon']); ?>" class="favicon"></p>
                           <?php endif; ?>
                           <input type="file" name="favicon" accept=".png, .jpg, .jpeg, .gif, .svg">
                           <p><small class="text-success"><?= trans('allowed_types') ?>: gif, jpg, png, jpeg</small></p>
                           <input type="hidden" name="old_favicon" value="<?php echo html_escape($general_settings['favicon']); ?>">
                       </div>
                       <div class="form-group">
                           <label class="control-label"><?= trans('logo') ?></label><br/>
                           <?php if(!empty($general_settings['logo'])): ?>
                               <p><img src="<?= base_url($general_settings['logo']); ?>" class="logo" width="150"></p>
                           <?php endif; ?>
                           <input type="file" name="logo" accept=".png, .jpg, .jpeg, .gif, .svg">
                           <p><small class="text-success"><?= trans('allowed_types') ?>: gif, jpg, png, jpeg</small></p>
                           <input type="hidden" name="old_logo" value="<?php echo html_escape($general_settings['logo']); ?>">
                       </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('application_name') ?></label>
                            <input type="text" class="form-control" name="application_name" placeholder="application name" value="<?php echo html_escape($general_settings['application_name']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('defult_leaves_no') ?></label>
                            <input type="text" class="form-control" name="defult_leaves_no" placeholder="application name" value="<?php echo html_escape($general_settings['defult_leaves_no']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('timezone') ?></label>
                            <input type="text" class="form-control" name="timezone" placeholder="timezone"
                            value="<?php echo html_escape($general_settings['timezone']); ?>">
                            <a href="http://php.net/manual/en/timezones.php" target="_blank"><?= trans('timezone') ?></a>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('default_language') ?></label>
                            <?php 
                                $options = array_column($languages, 'name','id');
                                echo form_dropdown('language',$options,$general_settings['default_language'],'class="form-control"');
                            ?>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Contact Number</label>
                            <input type="text" class="form-control" name="contact_number" placeholder="Contact Number"
                            value="<?php echo html_escape($general_settings['contact_number']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('copyright') ?></label>
                            <input type="text" class="form-control" name="copyright"
                            placeholder="Copyright"
                            value="<?php echo html_escape($general_settings['copyright']); ?>">
                        </div>
                    </div>

                    <!-- Leadspedia Setting -->
                    <div role="tabpanel" class="tab-pane" id="leadspedia">
                        <div class="alert alert-info small">
                            These values are used when contractor signup data is posted to Leadspedia from the /auth/register workflow.
                        </div>
                        <div class="form-group">
                            <label class="control-label">Leadspedia Account Manager ID</label>
                            <input type="text" class="form-control" name="leadspedia_account_manager_id" placeholder="Account Manager ID" value="<?php echo html_escape(isset($general_settings['leadspedia_account_manager_id']) ? $general_settings['leadspedia_account_manager_id'] : ''); ?>">
                        </div>
                        <!-- UPDATED: Basic Auth Token replaced with separate API credentials. -->
                        <div class="form-group">
                            <label class="control-label">Leadspedia API Key</label>
                            <input type="text" class="form-control" name="leadspedia_api_key" placeholder="API Key" value="<?php echo html_escape(isset($general_settings['leadspedia_api_key']) ? $general_settings['leadspedia_api_key'] : ''); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Leadspedia API Secret</label>
                            <input type="password" class="form-control" name="leadspedia_api_secret" placeholder="API Secret" value="<?php echo html_escape(isset($general_settings['leadspedia_api_secret']) ? $general_settings['leadspedia_api_secret'] : ''); ?>">
                        </div>
                    </div>

                    <!-- UPDATED: Agreement shown on /auth/register final tab. -->
                    <div role="tabpanel" class="tab-pane" id="signup-agreement">
                        <div class="form-group">
                            <label class="control-label">Agreement &amp; Complete Sign Up</label>
                            <textarea class="form-control" name="signup_agreement" rows="12" placeholder="Enter registration agreement text"><?php echo html_escape(isset($general_settings['signup_agreement']) ? $general_settings['signup_agreement'] : ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Email Setting -->
                    <div role="tabpanel" class="tab-pane" id="email">
                        <div class="form-group">
                            <label class="control-label"><?= trans('email_from') ?></label>
                            <input type="text" class="form-control" name="email_from" placeholder= "no-reply@domain.com" value="<?php echo html_escape($general_settings['email_from']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('smtp_host') ?></label>
                            <input type="text" class="form-control" name="smtp_host" placeholder="SMTP Host" value="<?php echo html_escape($general_settings['smtp_host']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('smtp_port') ?></label>
                            <input type="text" class="form-control" name="smtp_port" placeholder="SMTP Port" value="<?php echo html_escape($general_settings['smtp_port']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('smtp_user') ?></label>
                            <input type="text" class="form-control" name="smtp_user" placeholder="SMTP Email" value="<?php echo html_escape($general_settings['smtp_user']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?= trans('smtp_password') ?></label>
                            <input type="password" class="form-control" name="smtp_pass" placeholder="SMTP Password" value="<?php echo html_escape($general_settings['smtp_pass']); ?>">
                        </div>
                    </div>

                </div>

                <div class="box-footer">
                    <input type="submit" name="submit" value="<?= trans('save_changes') ?>" class="btn btn-primary pull-right">
                </div>	
                <?php echo form_close(); ?>
            </div>
        </div>
    </section>
</div>
</div>

<script>
    $("#setting").addClass('active');
    $('#myTabs a').click(function (e) {
     e.preventDefault()
     $(this).tab('show')
 })
</script>
