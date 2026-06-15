<?php defined('BASEPATH') or exit('No direct script access allowed');

class General_settings extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        auth_check(); // check login auth
        $this->rbac->check_module_access();

        $this->load->model('setting_model', 'setting_model');
    }

    //-------------------------------------------------------------------------
    // General Setting View
    public function index()
    {

        $data['general_settings'] = $this->setting_model->get_general_settings();
        $data['languages'] = $this->setting_model->get_all_languages();

        $data['title'] = 'General Setting';

        $this->load->view('includes/_header', $data);
        $this->load->view('general_settings/setting', $data);
        $this->load->view('includes/_footer');
    }
    public function engagement()
    {
        $data['acknowledgement'] = $this->setting_model->get_acknowledgement();
        $data['title'] = 'Engagement';

        $this->load->view('includes/_header', $data);
        $this->load->view('general_settings/engagement', $data);
        $this->load->view('includes/_footer');
    }

    //-------------------------------------------------------------------------
    public function update_acknowledgement()
    {
        $this->rbac->check_operation_access(); // check opration permission

        $data = array(
            'acknowledgement_letter' => post('acknowledgement_letter'),
            'acknowledgement_text' => post('acknowledgement_text'),
            'regards' => post('regards'),
        );
        $data = $this->security->xss_clean($data);
        $result = $this->setting_model->update_general_setting($data);
        if ($result) {
            $this->session->set_flashdata('success', 'Acknowledgement has been changed Successfully!');
            redirect(base_url('general_settings/engagement'), 'refresh');
        }
    }
    public function add()
    {
        $this->rbac->check_operation_access(); // check opration permission

        $data = array(
            'application_name' => post('application_name'),
            'timezone' => post('timezone'),
            'contact_number' => post('contact_number'),
            'default_language' => post('language'),
            'copyright' => post('copyright'),
            'email_from' => post('email_from'),
            'smtp_host' => post('smtp_host'),
            'smtp_port' => post('smtp_port'),
            'smtp_user' => post('smtp_user'),
            'smtp_pass' => post('smtp_pass'),
            'defult_leaves_no' => post('defult_leaves_no'),
            // UPDATED: Store Leadspedia API Key and API Secret separately.
            // Existing Account Manager ID and registration flow remain unchanged.
            'leadspedia_account_manager_id' => post('leadspedia_account_manager_id'),
            'leadspedia_api_key' => post('leadspedia_api_key'),
            'leadspedia_api_secret' => post('leadspedia_api_secret'),
            // UPDATED: Agreement text displayed on /auth/register.
            'signup_agreement' => post('signup_agreement'),

            // UPDATED: Google Maps/Places setting removed.
            // Do not include google_places_api_key because the column was removed
            // from ci_general_settings.

            'created_date' => today_date(),
            'updated_date' => today_date(),
        );

        $old_logo = post('old_logo');
        $old_favicon = post('old_favicon');

        $path = "assets/img/";

        if (!empty($_FILES['logo']['name'])) {
            $this->functions->delete_file($old_logo);

            $result = $this->functions->file_insert($path, 'logo', 'image', '9097152');
            if ($result['status'] == 1) {
                $data['logo'] = $path . $result['msg'];
            } else {
                $this->session->set_flashdata('error', $result['msg']);
                redirect(base_url('general_settings'), 'refresh');
            }
        }

        // favicon
        if (!empty($_FILES['favicon']['name'])) {
            $this->functions->delete_file($old_favicon);

            $result = $this->functions->file_insert($path, 'favicon', 'image', '197152');
            if ($result['status'] == 1) {
                $data['favicon'] = $path . $result['msg'];
            } else {
                $this->session->set_flashdata('error', $result['msg']);
                redirect(base_url('general_settings'), 'refresh');
            }
        }

        $data = $this->security->xss_clean($data);
        $result = $this->setting_model->update_general_setting($data);
        if ($result) {
            $this->session->set_flashdata('success', 'Setting has been changed Successfully!');
            redirect(base_url('general_settings'), 'refresh');
        }
    }

    /*--------------------------
    Email Template Settings
    --------------------------*/

    // ------------------------------------------------------------
    public function email_templates()
    {
        $this->rbac->check_operation_access(); // check opration permission
        if (post()) {
            $this->form_validation->set_rules('subject', 'Email Subject', 'trim|required');
            $this->form_validation->set_rules('content', 'Email Body', 'trim|required');
            if ($this->form_validation->run() == false) {
                echo validation_errors();
            } else {

                $id = post('id');

                $data = array(
                    'subject' => post('subject'),
                    'body' => post('content'),
                    'last_update' => today_date(),
                );
                $data = $this->security->xss_clean($data);
                $result = $this->setting_model->update_email_template($data, $id);
                if ($result) {
                    echo "true";
                }
            }
        } else {
            $data['title'] = '';
            $data['templates'] = $this->setting_model->get_email_templates();

            $this->load->view('includes/_header');
            $this->load->view('general_settings/email_templates/templates_list', $data);
            $this->load->view('includes/_footer');
        }
    }

    // ------------------------------------------------------------
    // Get Email Template & Related variables via Ajax by ID
    public function get_email_template_content_by_id()
    {
        $id = post('template_id');

        $data['template'] = $this->setting_model->get_email_template_content_by_id($id);

        $variables = $this->setting_model->get_email_template_variables_by_id($id);

        $data['variables'] = implode(',', array_column($variables, 'variable_name'));

        echo json_encode($data);
    }

    //---------------------------------------------------------------
    //
    public function email_preview()
    {
        if (post('content')) {
            $data['content'] = post('content');
            $data['head'] = post('head');
            $data['title'] = 'Send Email to Subscribers';
            echo $this->load->view('general_settings/email_templates/email_preview', $data, true);
        }
    }

}
