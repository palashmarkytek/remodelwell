<?php defined('BASEPATH') or exit('No direct script access allowed');

trait LoginTrait
{
    //--------------------------------------------------------------
    public function login()
    {
        if ($this->input->post('submit')) {

            $this->form_validation->set_rules('email', 'Email', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'errors' => validation_errors()
                );
                $this->session->set_flashdata('error', $data['errors']);
                redirect(base_url('auth/login'), 'refresh');
            } else {
                $data = array(
                    'email' => $this->input->post('email'),
                    'password' => $this->input->post('password')
                );
                $result = $this->auth_model->login($data);
                if ($result) {
                    if ($result['is_verify'] == 0) {
                        $this->session->set_flashdata('error', 'Please verify your email address!');
                        redirect(base_url('auth/login'));
                        exit();
                    }
                    if ($result['is_active'] == 0) {
                        $this->session->set_flashdata('error', 'Account is disabled by Admin!');
                        redirect(base_url('auth/login'));
                        exit();
                    }
                    if ($result['is_admin'] == 1) {
                        $admin_data = array(
                            'admin_id' => $result['admin_id'],
                            'selected_employee' => $result['admin_id'],
                            'display_id' => $result['display_id'],
                            'username' => $result['admin_role_title'],
                            'name' => $result['name'],
                            'email' => $result['email'],
                            'admin_role_id' => $result['admin_role_id'],
                            'admin_role' => $result['admin_role_title'],
                            'is_supper' => $result['is_supper'],
                            'is_admin_login' => TRUE,
                            'selected_date' => date('Y-m-d'),
                            'default_session' => getFinancialYear()
                        );
                        $this->session->set_userdata($admin_data);
                        $this->rbac->set_access_in_session(); // set access in session

                        // UPDATED: Automatic Leadspedia user creation on first login removed.
                        // All authenticated users now continue directly to the dashboard.
                        // Manual Leadspedia creation from the existing admin action remains unchanged.
                        redirect(base_url('dashboard'), 'refresh');
                    }
                } else {
                    $this->session->set_flashdata('errors', 'Invalid Username or Password!');
                    redirect(base_url('auth/login'));
                }
            }
        } else {
            $data['title'] = 'Login';
            $data['navbar'] = false;
            $data['sidebar'] = false;
            $data['footer'] = false;
            $data['bg_cover'] = true;

            $this->load->view('includes/_header', $data);
            $this->load->view('auth/login');
            $this->load->view('includes/_footer', $data);
        }
    }

    //--------------------------------------------------------------
    // UPDATED: First-login Leadspedia creation has been removed.
    // Keep the old route safe by sending logged-in users to dashboard.
    public function leadspedia_loader()
    {
        if (!$this->session->has_userdata('is_admin_login')) {
            redirect(base_url('auth/login'), 'refresh');
        }

        redirect(base_url('dashboard'), 'refresh');
    }

    //--------------------------------------------------------------
    // UPDATED: Disable the old first-login AJAX processor so calling
    // the existing route directly cannot create a Leadspedia user.
    public function process_leadspedia()
    {
        if (!$this->session->has_userdata('is_admin_login')) {
            return $this->_leadspedia_json_response(false, 'Your session has expired.', base_url('auth/login'));
        }

        return $this->_leadspedia_json_response(
            true,
            'Automatic Leadspedia user creation is disabled.',
            base_url('dashboard')
        );
    }

    //--------------------------------------------------------------
    // UPDATED: Normalize the cURL result before saving it in the existing response field.
    private function _format_leadspedia_response($api_response)
    {
        $result = array(
            'success' => (bool) $api_response['success'],
            'http_code' => (int) $api_response['http_code'],
            'body' => $api_response['body']
        );

        if (!empty($api_response['error'])) {
            $result['curl_error'] = $api_response['error'];
        }

        return $result;
    }

    //--------------------------------------------------------------
    // UPDATED: Leadspedia responses may wrap advertiserID inside result/data.
    // Search the decoded response recursively without assuming one wrapper format.
    private function _extract_leadspedia_advertiser_id($response_body)
    {
        $decoded = json_decode($response_body, true);
        if (!is_array($decoded)) {
            return 0;
        }

        return $this->_find_leadspedia_id_recursive($decoded);
    }

    //--------------------------------------------------------------
    private function _find_leadspedia_id_recursive($data)
    {
        foreach ($data as $key => $value) {
            if (strcasecmp((string) $key, 'advertiserID') === 0 && is_numeric($value)) {
                return (int) $value;
            }

            if (is_array($value)) {
                $advertiser_id = $this->_find_leadspedia_id_recursive($value);
                if ($advertiser_id > 0) {
                    return $advertiser_id;
                }
            }
        }

        return 0;
    }

    //--------------------------------------------------------------
    // UPDATED: Common JSON response for the loader AJAX request.
    private function _leadspedia_json_response($status, $message, $redirect_url)
    {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status' => (bool) $status,
                'message' => $message,
                'redirect_url' => $redirect_url
            )));
    }

}
