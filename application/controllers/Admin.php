<?php defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		auth_check(); // check login auth
		$this->rbac->check_module_access();

		$this->load->model('admin_model', 'admin');
		$this->load->model('Activity_model', 'activity_model');
		// UPDATED: Reuse the existing advertiser and Leadspedia settings methods.
		$this->load->model('auth_model', 'auth_model');
		// UPDATED: All reusable Leadspedia API/workflow functions are centralized in curl_helper.php.
		$this->load->helper('curl');
	}

	function index($type = '')
	{
		$this->session->set_userdata('filter_type', $type);
		$this->session->set_userdata('filter_keyword', '');
		$this->session->set_userdata('filter_status', '');

		$data['admin_roles'] = $this->admin->get_admin_roles();
		$data['title'] = 'Advertisers List';

		$this->load->view('includes/_header');
		$this->load->view('admin/index', $data);
		$this->load->view('includes/_footer');
	}

	function filterdata()
	{
		$this->session->set_userdata('filter_type', $this->input->post('type'));
		$this->session->set_userdata('filter_status', $this->input->post('status'));
		$this->session->set_userdata('filter_keyword', $this->input->post('keyword'));
	}

	function list_data()
	{
		$this->rbac->check_operation_access();
		$data['info'] = $this->admin->get_all();
		$this->load->view('admin/list', $data);
	}

	function change_status()
	{
		$this->rbac->check_operation_access(); // check opration permission
		$this->admin->change_status();
	}

	function add()
	{
		// UPDATED: Advertisers are created only from the public registration page.
		redirect(base_url('admin'));
	}

	function edit($id = "")
	{
		$update = (!$id) ? redirect('admin') : $this->admin->get_admin_by_id($id);
		$this->rbac->check_operation_access();

		if (empty($update) || (int) $update['admin_role_id'] === 1) {
			redirect('admin');
		}

		// UPDATED: /admin/edit/:id now follows the new registration data flow.
		// Company data comes from ci_admin, qualification from ci_advertisers,
		// and the selected vertical/contract comes from the two mapping tables.
		$data['title'] = 'Update Advertiser';
		$data['admin_roles'] = $this->admin->get_admin_roles();
		$data['id'] = $id;
		$data['update'] = $update;
		$data['verticals'] = $this->admin->get_active_verticals();
		$data['states'] = $this->admin->get_active_usa_states();
		$data['contract'] = $this->admin->get_advertiser_contract(isset($update['advertiser_id']) ? $update['advertiser_id'] : 0);

		// UPDATED: ci_admin stores one full name, while the form uses first/last name.
		$name_parts = preg_split('/\s+/', trim(isset($update['name']) ? $update['name'] : ''), 2);
		$data['update']['first_name'] = isset($name_parts[0]) ? $name_parts[0] : '';
		$data['update']['last_name'] = isset($name_parts[1]) ? $name_parts[1] : '';
		$data['update']['company_name'] = isset($update['company']) ? $update['company'] : '';
		$data['update']['phone_number'] = isset($update['mobile_no']) ? $update['mobile_no'] : '';

		if ($this->input->method(TRUE) === 'POST') {
			$this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
			$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
			$this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
			$this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_check_email[' . $update['email'] . ']');
			$this->form_validation->set_rules('admin_role_id', 'Role', 'trim|required');
			$this->form_validation->set_rules('currently_buying', 'Currently Buying Leads or Calls', 'trim|required');
			$this->form_validation->set_rules('monthly_budget', 'Monthly Budget', 'trim|required');
			// UPDATED: Registration allows only one vertical, so edit also uses one radio value.
			$this->form_validation->set_rules('vertical_id', 'Vertical', 'trim|required|integer');
			$this->form_validation->set_rules('leads_per_week', 'Leads Per Week', 'trim|required|integer|greater_than[0]');
			$this->form_validation->set_rules('zip_code_filter', 'ZIP Code Filter', 'trim|required');
			$this->form_validation->set_rules('state_filter[]', 'State Filter', 'required');
			$this->form_validation->set_rules('lead_delivery_method', 'Lead Delivery Method', 'trim|required');
			$this->form_validation->set_rules('receive_days[]', 'Receive Days', 'required');
			$this->form_validation->set_rules('start_time', 'Start Time', 'trim|required');
			$this->form_validation->set_rules('end_time', 'End Time', 'trim|required');

			if (in_array(post('lead_delivery_method'), array('Email', 'Email and SMS'), true)) {
				$this->form_validation->set_rules('lead_delivery_email', 'Lead Delivery Email', 'trim|required|valid_email');
			}
			if (in_array(post('lead_delivery_method'), array('SMS', 'Email and SMS'), true)) {
				$this->form_validation->set_rules('lead_delivery_sms', 'Lead Delivery SMS Number', 'trim|required');
			}

			if ($this->form_validation->run() === FALSE) {
				$this->session->set_flashdata('errors', validation_errors());
				display('admin/edit', $data);
				return;
			}

			$admin_data = array(
				'name' => trim(post('first_name') . ' ' . post('last_name')),
				'email' => post('email'),
				'mobile_no' => post('phone_number'),
				'company' => post('company_name'),
				'admin_role_id' => post('admin_role_id'),
				'source_id' => trim((string) post('source_id')),
				'updated_at' => today_date()
			);

			// UPDATED: Only the remaining advertiser qualification fields are updated.
			$advertiser_data = array(
				'currently_buying' => post('currently_buying'),
				'monthly_budget' => post('monthly_budget'),
				'updated_at' => date('Y-m-d H:i:s')
			);

			$state_filter = $this->input->post('state_filter');
			$receive_days = $this->input->post('receive_days');
			$state_filter = is_array($state_filter) ? $state_filter : array();
			$receive_days = is_array($receive_days) ? $receive_days : array();

			// UPDATED: Checkbox values are stored as comma-separated text in the contract row.
			$state_filter = array_values(array_unique(array_filter(array_map(function ($state) {
				return strtoupper(trim($state));
			}, $state_filter))));
			$receive_days = array_values(array_unique(array_filter(array_map('trim', $receive_days))));

			$contract_data = array(
				'leads_per_week' => (int) post('leads_per_week'),
				'zip_codes' => trim((string) post('zip_code_filter')),
				'state_abbreviations' => implode(',', $state_filter),
				'lead_delivery_method' => post('lead_delivery_method'),
				'lead_delivery_sms' => trim((string) post('lead_delivery_sms')),
				'lead_delivery_email' => trim((string) post('lead_delivery_email')),
				'delivery_days' => implode(',', $receive_days),
				'start_time' => post('start_time'),
				'end_time' => post('end_time'),
				'updated_at' => date('Y-m-d H:i:s')
			);

			$result = $this->admin->update_advertiser_profile(
				$this->security->xss_clean($admin_data),
				$this->security->xss_clean($advertiser_data),
				$id,
				(int) post('vertical_id'),
				$this->security->xss_clean($contract_data)
			);

			if ($result) {
				$this->activity_model->add_log(5);
				$this->session->set_flashdata('success', 'Advertiser has been updated successfully!');
				redirect(base_url('admin'));
			}

			$this->session->set_flashdata('error', 'Unable to update advertiser. Please try again.');
		}

		display('admin/edit', $data);
	}

	public function mapping($admin_id = '')
	{
		$this->rbac->check_operation_access();
		$advertiser = (!$admin_id) ? redirect('admin') : $this->admin->get_admin_by_id($admin_id);

		// UPDATED: Mapping page is only for a valid advertiser linked with ci_advertisers.
		if (empty($advertiser) || empty($advertiser['advertiser_id'])) {
			$this->session->set_flashdata('error', 'Advertiser record not found.');
			redirect(base_url('admin'));
			return;
		}

		$data['title'] = 'Assigned Verticals';
		$data['advertiser'] = $advertiser;

		// UPDATED: Show only verticals currently assigned to this advertiser.
		$data['verticals'] = $this->admin->get_assigned_verticals($advertiser['advertiser_id']);

		display('admin/mapping', $data);
	}

	// UPDATED: Display all contract details for one advertiser and assigned vertical.
	public function mapping_contract($admin_id = '', $vertical_id = '')
	{
		// UPDATED: Reuse the existing mapping permission; no new RBAC operation is required.
		if (!$this->rbac->check_operation_permission('mapping')) {
			$back_to = $this->functions->encode($_SERVER['REQUEST_URI']);
			redirect('access_denied/index/' . $back_to);
			return;
		}

		$admin_id = (int) $admin_id;
		$vertical_id = (int) $vertical_id;
		$advertiser = ($admin_id > 0) ? $this->admin->get_admin_by_id($admin_id) : array();

		if (empty($advertiser) || empty($advertiser['advertiser_id']) || $vertical_id <= 0) {
			$this->session->set_flashdata('error', 'Advertiser or vertical record not found.');
			redirect(base_url('admin'));
			return;
		}

		$contracts = $this->admin->get_advertiser_vertical_contracts(
			$advertiser['advertiser_id'],
			$vertical_id
		);

		if (empty($contracts)) {
			$this->session->set_flashdata('error', 'Contract record not found for the selected vertical.');
			redirect(base_url('admin/mapping/' . $admin_id));
			return;
		}

		// UPDATED: Fetch each contract status directly from Leadspedia at page load.
		$credentials = leadspedia_get_api_credentials();
		foreach ($contracts as $key => $contract) {
			$runtime_details = array(
				'loaded' => false,
				'status' => 'Unavailable',
				'is_active' => false,
				'contract_name' => '',
				'error' => isset($credentials['message']) ? $credentials['message'] : ''
			);

			if (!empty($credentials['status'])) {
				$runtime_details = leadspedia_contract_runtime_details(
					isset($contract['contractID']) ? (int) $contract['contractID'] : 0,
					$credentials['api_key'],
					$credentials['api_secret']
				);
			}

			$contracts[$key]['runtime_status_loaded'] = !empty($runtime_details['loaded']);
			$contracts[$key]['runtime_contract_status'] = isset($runtime_details['status']) ? $runtime_details['status'] : 'Unavailable';
			$contracts[$key]['runtime_contract_active'] = !empty($runtime_details['is_active']);
			$contracts[$key]['runtime_contract_error'] = isset($runtime_details['error']) ? $runtime_details['error'] : '';
			$contracts[$key]['runtime_contract_name'] = !empty($runtime_details['contract_name'])
				? $runtime_details['contract_name']
				: (isset($contract['contract_name']) ? $contract['contract_name'] : '-');
		}

		$data['title'] = 'View Contract';
		$data['advertiser'] = $advertiser;
		$data['contracts'] = $contracts;
		$data['vertical'] = $contracts[0];
		display('admin/mapping_contract', $data);
	}

	// UPDATED: Remove one assigned vertical from the selected advertiser.
	public function remove_vertical_mapping($admin_id = '', $vertical_id = '')
	{
		$this->rbac->check_operation_access();

		// UPDATED: Removal is allowed only through POST so the action remains CSRF protected.
		if ($this->input->method(TRUE) !== 'POST' || empty($admin_id) || empty($vertical_id)) {
			redirect(base_url('admin'));
			return;
		}

		$advertiser = $this->admin->get_admin_by_id($admin_id);
		if (empty($advertiser) || empty($advertiser['advertiser_id'])) {
			$this->session->set_flashdata('error', 'Advertiser record not found.');
			redirect(base_url('admin'));
			return;
		}

		$result = $this->admin->remove_advertiser_vertical(
			$advertiser['advertiser_id'],
			$vertical_id
		);

		if ($result) {
			$this->session->set_flashdata('success', 'Assigned vertical has been removed successfully.');
		} else {
			$this->session->set_flashdata('error', 'Unable to remove the assigned vertical.');
		}

		redirect(base_url('admin/mapping/' . $admin_id));
	}

	public function save_mapping($admin_id = '')
	{
		$this->rbac->check_operation_access();
		$advertiser = (!$admin_id) ? redirect('admin') : $this->admin->get_admin_by_id($admin_id);

		// UPDATED: Validate advertiser before synchronizing vertical assignments.
		if (empty($advertiser) || empty($advertiser['advertiser_id'])) {
			$this->session->set_flashdata('error', 'Advertiser record not found.');
			redirect(base_url('admin'));
			return;
		}

		// UPDATED: Checked vertical IDs are inserted; unchecked vertical IDs are removed.
		$result = $this->admin->sync_advertiser_vertical_mapping(
			$advertiser['advertiser_id'],
			(array) $this->input->post('vertical_ids')
		);

		if ($result) {
			$this->session->set_flashdata('success', 'Advertiser vertical mapping has been updated successfully.');
		} else {
			$this->session->set_flashdata('error', 'Unable to update advertiser vertical mapping.');
		}

		redirect(base_url('admin/mapping/' . $admin_id));
	}

	public function delete_mapping($mapping_id = '', $admin_id = '')
	{
		$this->rbac->check_operation_access();
		if (!$mapping_id || !$admin_id) {
			redirect('admin');
		}

		$this->admin->delete_source_offer_mapping($mapping_id, $admin_id);
		$this->session->set_flashdata('success', 'Mapping has been deleted successfully.');
		redirect(base_url('admin/mapping/' . $admin_id));
	}

	public function check_email($new_email, $current_email)
	{
		if ($new_email !== $current_email) {
			if ($this->admin->is_email_unique($new_email)) {
				return TRUE;
			} else {
				$this->form_validation->set_message('check_email', 'Email already in our system. Please add a unique email.');
				return FALSE;
			}
		}
		return TRUE;
	}



	// UPDATED: Keep only the existing route/controller responsibility here.
	// The complete reusable Leadspedia advertiser workflow is in curl_helper.php.
	public function create_leadspedia_user($admin_id = '')
	{
		$this->rbac->check_operation_access();

		if ($this->input->method(TRUE) !== 'POST' || empty($admin_id)) {
			return leadspedia_output_json($this, leadspedia_result(false, 'Invalid request.'));
		}

		return leadspedia_output_json(
			$this,
			leadspedia_create_user_setup((int) $admin_id)
		);
	}

	// UPDATED: Keep only the existing route/controller responsibility here.
	// Contract, filter, schedule, retry, and progress logic is in curl_helper.php.
	public function create_leadspedia_contract($admin_id = '')
	{
		$this->rbac->check_operation_access();

		if ($this->input->method(TRUE) !== 'POST' || empty($admin_id)) {
			return leadspedia_output_json($this, leadspedia_result(false, 'Invalid request.'));
		}

		return leadspedia_output_json(
			$this,
			leadspedia_create_contract_setup((int) $admin_id)
		);
	}

	function delete($id = '')
	{
		$this->rbac->check_operation_access(); // check opration permission
		$this->admin->delete($id);
		$this->activity_model->add_log(6);
		$this->session->set_flashdata('success', 'User has been Deleted Successfully.');
		redirect('admin');
	}
}
