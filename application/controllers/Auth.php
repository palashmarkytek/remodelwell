<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Auth Controller
|--------------------------------------------------------------------------
| Existing controller structure is kept. Login and Register flows are moved
| to traits so future auth workflows can be maintained page-wise.
*/
require_once APPPATH . 'controllers/auth/LoginTrait.php';
require_once APPPATH . 'controllers/auth/RegisterTrait.php';
// UPDATED: Existing auth controller now includes the complete forgot-password flow.
require_once APPPATH . 'controllers/auth/ForgotPasswordTrait.php';

class Auth extends MY_Controller
{
    use LoginTrait, RegisterTrait, ForgotPasswordTrait;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('mailer');
        $this->load->model('auth_model', 'auth_model');
    }

    //--------------------------------------------------------------
    public function index()
    {
        if ($this->session->has_userdata('is_admin_login')) {
            redirect('dashboard');
        } else {
            redirect('auth/login');
        }
    }

    //-----------------------------------------------------------------------
    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url('auth/login'), 'refresh');
    }

    public function set_year()
    {
        // Retrieve the 'year' from POST data.
        $year = $this->input->post('year');

        // Check if a year was provided.
        if ($year) {
            // Set the provided year in the session.
            $this->session->set_userdata('default_session', $year);

            // Prepare a success response.
            $response = [
                'status' => 'success',
                'message' => 'Year set successfully',
            ];
        } else {
            // Prepare an error response if 'year' was not provided.
            $response = [
                'status' => 'error',
                'message' => 'Year not provided',
            ];
        }

        // Set the Content-Type header to JSON and output the JSON response.
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
?>
