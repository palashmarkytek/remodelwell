<?php defined('BASEPATH') or exit('No direct script access allowed');

trait RegisterTrait
{
    //--------------------------------------------------------------
    // Contractor signup wizard. Data is saved only at final submit.
    // The same submission also creates a Vendor login in ci_admin.
    public function register()
    {
        // Process the final wizard submission by request method.
        // The submit button is disabled by JavaScript after validation, so browsers do not include
        // its name/value in POST; checking post('submit') would skip the save operation.
        if ($this->input->method(TRUE) === 'POST') {

            $this->_validate_contractor_signup();

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect(base_url('auth/register'), 'refresh');
            }

            // Revenue qualification rule from workflow: serve only companies over $1M annual revenue.
            if (post('currently_buying') == 'No' && in_array(post('annual_revenue'), array('Just Starting', 'Less than $1 Million'))) {
                $this->session->set_flashdata('error', 'We are currently only able to serve companies over $1 Million in annual revenue.');
                redirect(base_url('auth/register'), 'refresh');
            }

            $admin_data = $this->_prepare_vendor_admin_insert_data();
            $advertiser_data = $this->_prepare_advertiser_insert_data();
            // UPDATED: Selected active vertical ids are saved in the advertiser mapping table.
            $selected_vertical_id = (int) $this->input->post('vertical_id');
            // UPDATED: Lead setup and delivery data is saved in ci_vertical_contract_map.
            $contract_data = $this->_prepare_vertical_contract_insert_data();
            $generated_password = $admin_data['generated_password'];
            unset($admin_data['generated_password']);

            // Save login data in ci_admin and extra workflow data in ci_advertisers.
            $save_result = $this->auth_model->create_vendor_advertiser_signup($admin_data, $advertiser_data, $selected_vertical_id, $contract_data);

            if (!$save_result || empty($save_result['advertiser_id'])) {
                $this->session->set_flashdata('error', 'Signup could not be saved. Please try again.');
                redirect(base_url('auth/register'), 'refresh');
            }

            // Keep the generated plain password only for the next page.
            // The database stores only the secure password hash.
            $this->session->set_flashdata('new_user_credentials', array(
                'email' => post('email'),
                'password' => $generated_password
            ));

            redirect(base_url('auth/new-user'), 'refresh');
        }

        $data['title'] = 'Contractor Signup';
        $data['navbar'] = false;
        $data['sidebar'] = false;
        $data['footer'] = false;
        $data['bg_cover'] = true;
        // UPDATED: Qualification tab loads only active local verticals.
        $data['verticals'] = $this->auth_model->get_active_verticals();
        // UPDATED: Registration state filters are loaded from ci_usa_states.
        $data['states'] = $this->auth_model->get_active_usa_states();
        // UPDATED: Agreement content is maintained from /general_settings.
        $data['signup_agreement'] = isset($this->general_settings['signup_agreement']) ? $this->general_settings['signup_agreement'] : '';

        $this->load->view('includes/_header', $data);
        $this->load->view('auth/register', $data);
        $this->load->view('includes/_footer', $data);
    }

    //--------------------------------------------------------------
    private function _validate_contractor_signup()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[ci_admin.email]', array('is_unique' => 'Email already exists. Please login or use another email.'));
        $this->form_validation->set_rules('currently_buying', 'Currently Buying Leads or Calls', 'trim|required');
        $this->form_validation->set_rules('monthly_budget', 'Monthly Budget', 'trim|required');
        // UPDATED: Services Wanted text field replaced by required vertical checkboxes.
        $this->form_validation->set_rules('vertical_id', 'Vertical', 'required|integer');
        $this->form_validation->set_rules('leads_per_week', 'Leads Per Week', 'trim|required|integer|greater_than[0]');
        // UPDATED: User chooses either ZIP Code Filter or State Filter.
        $this->form_validation->set_rules('filter_type', 'Filter Type', 'trim|required|in_list[zip,state]');
        if (post('filter_type') === 'zip') {
            $this->form_validation->set_rules('zip_code_filter', 'ZIP Code Filter', 'trim|required');
        } elseif (post('filter_type') === 'state') {
            $this->form_validation->set_rules('state_filter[]', 'State Filter', 'required');
        }
        $this->form_validation->set_rules('receive_days[]', 'Receive Days', 'required');
        $this->form_validation->set_rules('start_time', 'Start Time', 'trim|required');
        $this->form_validation->set_rules('end_time', 'End Time', 'trim|required');
        $this->form_validation->set_rules('lead_delivery_method', 'Lead Delivery Method', 'trim|required');
        $this->form_validation->set_rules('agreement_accept', 'Agreement', 'trim|required');

        if (post('currently_buying') == 'No') {
            $this->form_validation->set_rules('annual_revenue', 'Annual Revenue', 'trim|required');
        }

        if (post('lead_delivery_method') == 'Email' || post('lead_delivery_method') == 'Email and SMS') {
            $this->form_validation->set_rules('lead_delivery_email', 'Lead Delivery Email', 'trim|required|valid_email');
        }

        if (post('lead_delivery_method') == 'SMS' || post('lead_delivery_method') == 'Email and SMS') {
            $this->form_validation->set_rules('lead_delivery_sms', 'Lead Delivery SMS Number', 'trim|required');
        }

        if (post('send_to_crm') == 'Yes') {
            $this->form_validation->set_rules('crm_details', 'CRM Details', 'trim|required');
        }
    }

    //--------------------------------------------------------------
    private function _prepare_vendor_admin_insert_data()
    {
        $full_name = trim(post('first_name') . ' ' . post('last_name'));
        $generated_password = $this->_generate_strong_password();

        return array(
            'display_id' => generateEmployeeId(),
            'admin_role_id' => 2, // Vendor role. This allows normal vendor login/menu access through existing RBAC.
            'name' => $full_name,
            'doj' => date('Y-m-d'),
            'email' => post('email'),
            'address' => post('service_area'),
            'mobile_no' => post('phone_number'),
            'additional_mobile_no' => '',
            'password' => password_hash($generated_password, PASSWORD_BCRYPT),
            'last_login' => '0000-00-00 00:00:00',
            'is_verify' => 1,
            'is_admin' => 1,
            'is_active' => 1,
            'is_supper' => 0,
            'token' => '',
            'password_reset_code' => '',
            'leaves_no' => isset($this->general_settings['defult_leaves_no']) ? $this->general_settings['defult_leaves_no'] : 0,
            'company_address' => post('service_area'),
            'company' => post('company_name'),
            // Source id is generated from company name for vendor mapping usage. Admin can edit later if needed.
            'source_id' => $this->_generate_source_id(post('company_name')),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            // Temporary controller-only value; removed before the database insert.
            'generated_password' => $generated_password
        );
    }

    //--------------------------------------------------------------
    // UPDATED: ci_advertisers now stores only advertiser-specific
    // qualification and agreement fields. Company/contact information
    // is saved in ci_admin, while lead setup/delivery is saved in
    // ci_vertical_contract_map.
    private function _prepare_advertiser_insert_data()
    {
        return array(
            'currently_buying' => post('currently_buying'),
            'monthly_budget' => post('monthly_budget'),
            'agreement_accept' => post('agreement_accept'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
    }


    //--------------------------------------------------------------
    // UPDATED: Prepare the selected vertical's lead setup and delivery
    // details for insertion into ci_vertical_contract_map.
    private function _prepare_vertical_contract_insert_data()
    {
        $filter_type = trim((string) post('filter_type'));
        $state_filter = $this->input->post('state_filter');
        $receive_days = $this->input->post('receive_days');

        // Keep existing form compatibility and save checkbox selections
        // as comma-separated values in the contract table.
        if (!is_array($state_filter)) {
            $state_filter = array();
        }

        if (!is_array($receive_days)) {
            $receive_days = array();
        }

        $state_filter = array_values(array_filter(array_map('trim', $state_filter)));
        $receive_days = array_values(array_filter(array_map('trim', $receive_days)));

        // UPDATED: Save only the filter selected on registration. The existing
        // zip_codes and state_abbreviations columns remain unchanged.
        $zip_codes = ($filter_type === 'zip') ? trim((string) post('zip_code_filter')) : '';
        if ($filter_type !== 'state') {
            $state_filter = array();
        }

        return array(
            'leads_per_week' => (int) post('leads_per_week'),
            'zip_codes' => $zip_codes,
            'state_abbreviations' => implode(',', $state_filter),
            'lead_delivery_method' => trim((string) post('lead_delivery_method')),
            'lead_delivery_sms' => trim((string) post('lead_delivery_sms')),
            'lead_delivery_email' => trim((string) post('lead_delivery_email')),
            'delivery_days' => implode(',', $receive_days),
            'start_time' => post('start_time') ? post('start_time') : null,
            'end_time' => post('end_time') ? post('end_time') : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
    }

    //--------------------------------------------------------------
    // UPDATED: Leadspedia advertisers/create.do accepts only these documented fields.
    private function _prepare_leadspedia_create_payload($data, $settings)
    {
        return array(
            'advertiserName' => trim($data['company_name']),
            'accountManagerID' => (int) (isset($settings['leadspedia_account_manager_id']) ? $settings['leadspedia_account_manager_id'] : 0),
            'status' => 'Active'
        );
    }

    //--------------------------------------------------------------
    // UPDATED: Prepare only documented advertisers/updateInfo.do fields for
    // which the current registration flow has meaningful local values.
    private function _prepare_leadspedia_update_payload($data, $leadspedia_advertiser_id)
    {
        return array(
            'advertiserID' => (int) $leadspedia_advertiser_id,
            'advertiserName' => trim($data['company_name']),
            'alternateID' => (string) $data['advertiser_id'],
            'source' => 'RemodelWell',
            'externalCRMID' => (string) $data['admin_id']
        );
    }

    //--------------------------------------------------------------
    private function _build_leadspedia_notes($data)
    {
        return 'Currently Buying: ' . $data['currently_buying'] . "\n"
            . 'Annual Revenue: ' . $data['annual_revenue'] . "\n"
            . 'Monthly Budget: ' . $data['monthly_budget'] . "\n"
            . 'Lead Type: ' . $data['lead_type'] . "\n"
            . 'Filtering: ' . $data['filtering_notes'] . "\n"
            . 'Cannot Work On: ' . $data['cannot_work_on'] . "\n"
            . 'Service Area: ' . $data['service_area'] . "\n"
            . 'Leads Per Week: ' . $data['leads_per_week'] . "\n"
            . 'Delivery Method: ' . $data['lead_delivery_method'] . "\n"
            . 'CRM: ' . $data['send_to_crm'] . "\n"
            . 'Receive Time: ' . $data['receive_time'] . "\n"
            . 'Pricing Estimate: ' . $data['pricing_estimate'] . "\n"
            . 'Account Funding: ' . $data['account_funding'] . "\n"
            . 'Billing Start Date: ' . $data['billing_start_date'];
    }

    //--------------------------------------------------------------
    private function _send_internal_signup_notification($data, $api_response)
    {
        // Per workflow: internal mail is required only for CRM setup or phone-call-only setup.
        $send_to_crm = ($data['send_to_crm'] == 'Yes');
        $phone_call_only = ($data['lead_type'] == 'Phone Calls Only');

        if (!$send_to_crm && !$phone_call_only) {
            return true;
        }

        $reasons = array();
        if ($send_to_crm) {
            $reasons[] = 'CRM delivery setup requested';
        }
        if ($phone_call_only) {
            $reasons[] = 'Phone-call-only lead setup requested';
        }

        $subject = 'New Contractor Signup Setup Required - ' . $data['company_name'];
        $message = "A new contractor signup requires setup help.\n\n"
            . "Reason: " . implode(', ', $reasons) . "\n\n"
            . "Company: " . $data['company_name'] . "\n"
            . "Name: " . $data['first_name'] . ' ' . $data['last_name'] . "\n"
            . "Email: " . $data['email'] . "\n"
            . "Phone: " . $data['phone_number'] . "\n"
            . "Lead Type: " . $data['lead_type'] . "\n"
            . "CRM Required: " . $data['send_to_crm'] . "\n"
            . "CRM Details: " . $data['crm_details'] . "\n"
            . "Leadspedia HTTP Code: " . $api_response['http_code'] . "\n"
            . "Leadspedia Status: " . (($api_response['success']) ? 'success' : 'failed') . "\n";

        // Use existing email_helper.php so SMTP/general_settings credentials are reused.
        // This keeps email sending consistent with the rest of the application.
        $this->load->helper('email');

        return send_email(array(
            'to' => 'kevin@remodelwell.com',
            'cc' => 'tech@remodelwell.com',
            'subject' => $subject,
            'content' => nl2br($message),
            'attachment' => null,
            'client_id' => isset($data['admin_id']) ? $data['admin_id'] : null,
            'project_id' => null,
            'is_project_notofication' => false
        ));
    }

    //--------------------------------------------------------------
    // Server-side password strength validation for signup login password.
    // Rule: minimum 8 chars, uppercase, lowercase, number and special character.
    public function password_strength_check($password)
    {
        if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $password)) {
            return true;
        }

        $this->form_validation->set_message(
            'password_strength_check',
            'Password must be at least 8 characters and include uppercase, lowercase, number and special character.'
        );
        return false;
    }

    //--------------------------------------------------------------
    // Show the newly created login once, immediately after successful signup.
    public function new_user()
    {
        $credentials = $this->session->flashdata('new_user_credentials');

        if (empty($credentials) || empty($credentials['email']) || empty($credentials['password'])) {
            redirect(base_url('auth/register'), 'refresh');
        }

        $data['title'] = 'New User Login';
        $data['navbar'] = false;
        $data['sidebar'] = false;
        $data['footer'] = false;
        $data['bg_cover'] = true;
        $data['credentials'] = $credentials;

        $this->load->view('includes/_header', $data);
        $this->load->view('auth/new_user', $data);
        $this->load->view('includes/_footer', $data);
    }

    //--------------------------------------------------------------
    // Generate a strong 12-character password containing uppercase,
    // lowercase, number and special characters.
    private function _generate_strong_password()
    {
        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lowercase = 'abcdefghijkmnopqrstuvwxyz';
        $numbers = '23456789';
        $special = '!@#$%&*?';
        $all = $uppercase . $lowercase . $numbers . $special;

        $password = $uppercase[random_int(0, strlen($uppercase) - 1)]
            . $lowercase[random_int(0, strlen($lowercase) - 1)]
            . $numbers[random_int(0, strlen($numbers) - 1)]
            . $special[random_int(0, strlen($special) - 1)];

        while (strlen($password) < 12) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($password);
    }

    //--------------------------------------------------------------
    private function _generate_source_id($company_name)
    {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $company_name));
        $slug = substr($slug, 0, 8);

        if ($slug == '') {
            $slug = 'vendor';
        }

        return $slug . rand(100, 999);
    }
}
