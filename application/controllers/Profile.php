<?php defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends MY_Controller
{

	public function __construct()
	{

		parent::__construct();
		auth_check(); // check login auth
		$this->load->model('admin_model', 'admin_model');
	}

	//-------------------------------------------------------------------------
	public function index()
	{

		if ($this->input->post('submit')) {
			$data = array(
				'name' => $this->input->post('name'),
				'mobile_no' => $this->input->post('mobile_no'),
				'updated_at' => today_date(),
			);
			$data = $this->security->xss_clean($data);
			$result = $this->admin_model->update_user($data);
			if ($result) {
				$this->session->set_flashdata('success', 'Profile has been Updated Successfully!');
				redirect(base_url('profile'), 'refresh');
			}
		} else {

			$data['title'] = 'Admin Profile';
			$data['admin'] = $this->admin_model->get_user_detail();

			// UPDATED: Load the linked Leadspedia result for read-only profile display.
			$data['leadspedia'] = $this->admin_model->get_leadspedia_detail();

			$this->load->view('includes/_header');
			$this->load->view('profile/index', $data);
			$this->load->view('includes/_footer');
		}
	}

	//-------------------------------------------------------------------------
	public function change_pwd()
	{
		$this->rbac->check_operation_access();
		$id = $this->session->userdata('admin_id');

		if ($this->input->post('submit')) {
			$this->form_validation->set_rules('password', 'Password', 'trim|required|callback_valid_password');
			$this->form_validation->set_rules('confirm_pwd', 'Confirm Password', 'trim|required|matches[password]');

			if ($this->form_validation->run() == FALSE) {

				$data = array('errors' => validation_errors());
				$this->session->set_flashdata('errors', $data['errors']);
				redirect(base_url('profile/change_pwd'), 'refresh');
			} else {
				$data = array(
					'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT)
				);
				$data = $this->security->xss_clean($data);

				$result = $this->admin_model->change_pwd($data, $id);
				if ($result) {
					$this->session->set_flashdata('success', 'Password has been changed successfully!');
					redirect(base_url('profile/change_pwd'));
				}
			}
		} else {
			$data['title'] = 'Change Password';
			$data['user'] = $this->admin_model->get_user_detail();

			$this->load->view('includes/_header');
			$this->load->view('profile/change_pwd', $data);
			$this->load->view('includes/_footer');
		}
	}

	public function valid_password($password)
	{
		$password = trim($password);

		if (strlen($password) < 8) {
			$this->form_validation->set_message('valid_password', 'Password must be at least 8 characters.');
			return false;
		}

		if (!preg_match('#[A-Z]#', $password)) {
			$this->form_validation->set_message('valid_password', 'Password must include at least one uppercase letter.');
			return false;
		}

		if (!preg_match('#[a-z]#', $password)) {
			$this->form_validation->set_message('valid_password', 'Password must include at least one lowercase letter.');
			return false;
		}

		if (!preg_match('#[0-9]#', $password)) {
			$this->form_validation->set_message('valid_password', 'Password must include at least one number.');
			return false;
		}

		if (!preg_match('#[\W_]#', $password)) {
			$this->form_validation->set_message('valid_password', 'Password must include at least one special character.');
			return false;
		}

		return true;
	}


}

?>