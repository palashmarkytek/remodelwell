<?php defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends My_Controller
{
    public function __construct()
    {
        parent::__construct();
        auth_check(); // check login auth
        $this->rbac->check_module_access();

        if ($this->uri->segment(3) != '') {
            $this->rbac->check_operation_access();
        }

        $this->load->model('dashboard_model', 'dashboard');
    }

    //--------------------------------------------------------------------------

    public function index()
    {
        $data['title'] = 'Dashboard';
        $data['is_super_admin'] = ((int) $this->session->userdata('admin_role_id') === 1 || (int) $this->session->userdata('is_supper') === 1);
        $data['is_vendor_login'] = ((int) $this->session->userdata('admin_role_id') === 2);

        $this->load->view('includes/_header', $data);
        $this->load->view('dashboard/index', $data);
        $this->load->view('includes/_footer');
    }


}