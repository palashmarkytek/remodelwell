<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PermissionsTrait
 * ----------------
 * Contains:
 * - Role list/add/edit/delete
 * - Role status change
 * - Access screen and save access (AJAX)
 * - Remote validation for unique role title
 */
trait PermissionsTrait
{
    //-----------------------------------------------------
    /**
     * Roles listing page
     */
    public function permissions_list()
    {
        $this->rbac->check_operation_access();
        $data['title']   = trans('role_and_permissions');
        $data['records'] = $this->admin_roles->get_all();

        $this->load->view('includes/_header');
        $this->load->view('admin_roles/index', $data);
        $this->load->view('includes/_footer');
    }

    //-----------------------------------------------------------
    /**
     * Change role status (AJAX)
     * - Requires operation permission
     * - Returns JSON with fresh CSRF hash (important when csrf_regenerate = TRUE)
     */
    public function change_status()
    {
        $this->rbac->check_operation_access();

        $this->admin_roles->change_status();

        $resp = [
            'status'           => 'ok',
            'csrf_token_name'  => $this->security->get_csrf_token_name(),
            'csrf_hash'        => $this->security->get_csrf_hash(),
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($resp));
        return;
    }

    //------------------------------------------------------------
    /**
     * Delete role (Protected roles can't be deleted)
     * - Uses your same structure + redirects
     * - Keeps soft-delete behavior for other roles (as per model)
     */
    public function delete($id = '')
    {
        // ✅ Check operation permission
        $this->rbac->check_operation_access();

        // ✅ Basic validation
        $id = (int) $id;
        if ($id <= 0) {
            show_404();
        }

        // ✅ System / protected roles (edit list as per your business rule)
        $protected_roles = array(1, 2, 3, 4, 5, 6);

        if (in_array($id, $protected_roles, true)) {
            $this->session->set_flashdata('error', 'You are not allowed to delete this system role.');
            redirect('admin_roles');
            return;
        }

        // ✅ Proceed delete (soft/hard depends on your model)
        $this->admin_roles->delete($id);

        $this->session->set_flashdata('msg', 'Role has been deleted successfully.');
        redirect('admin_roles');
    }

    //-----------------------------------------------------------------
    /**
     * Add new role
     */
    public function add()
    {
        $this->rbac->check_operation_access();

        if ($this->input->post('submit')) {
            $this->admin_roles->insert();
            $this->session->set_flashdata('success', 'Record Added Successfully');
            redirect('admin_roles');
        }

        $data['title'] = trans('add_new_role');

        $this->load->view('includes/_header');
        $this->load->view('admin_roles/add', $data);
        $this->load->view('includes/_footer');
    }

    //--------------------------------------------------
    /**
     * Edit role
     */
    public function edit($id = "")
    {
        $this->rbac->check_operation_access();

        // UPDATED: Keep the role id in the edit URL and validate the submitted record.
        $id = (int) $id;
        if ($id <= 0) {
            redirect('admin_roles');
        }

        $record = $this->admin_roles->get_role_by_id($id);
        if (empty($record)) {
            show_404();
        }

        if ($this->input->method(TRUE) === 'POST') {
            $this->form_validation->set_rules('admin_role_title', 'Admin Role', 'trim|required');
            $this->form_validation->set_rules('admin_role_status', 'Status', 'trim|required|in_list[0,1]');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('errors', validation_errors());
            } else {
                $this->admin_roles->update();
                $this->session->set_flashdata('success', 'Record updated Successfully');
                redirect('admin_roles');
            }
        }

        $data['title']  = trans('edit_role');
        $data['record'] = $record;

        $this->load->view('includes/_header');
        $this->load->view('admin_roles/edit', $data);
        $this->load->view('includes/_footer');
    }

    //--------------------------------------------------
    /**
     * Access page
     * - Shows modules + submodules + access status
     * - Loads:
     *   access               = module-wise access
     *   sub_module_access    = array like ["12/add","12/edit"]
     */
    public function access($id = "")
    {
        $this->rbac->check_operation_access();

        $id = (int) $id;
        if ($id <= 0) {
            show_404();
        }

        $data['title']  = trans('admin_permissions');
        $data['record'] = $this->admin_roles->get_role_by_id($id);

        // ✅ Existing module-wise access
        $data['access'] = $this->admin_roles->get_access($id);

        // ✅ Main Modules
        $data['modules'] = $this->admin_roles->get_modules();

        // ✅ Sub Modules
        $data['sub_modules'] = $this->admin_roles->get_all_sub_modules();

        // ✅ UPDATED: returns array like ["12/add", "12/edit", "15/access"]
        $data['sub_module_access'] = $this->admin_roles->get_sub_module_access($id);

        $this->load->view('includes/_header');
        $this->load->view('admin_roles/access', $data);
        $this->load->view('includes/_footer');
    }

    //-----------------------------------------------------------
    /**
     * Save access (AJAX)
     * - Saves permissions
     * - Refreshes session access immediately
     * - Returns JSON with fresh CSRF hash each time
     */
    public function set_access()
    {
        $this->rbac->check_operation_access();

        // ✅ Save permission changes
        $this->admin_roles->set_access();

        // ✅ Refresh RBAC session access immediately
        $this->rbac->set_access_in_session();

        // ✅ Return new CSRF hash to avoid csrf_regenerate issues
        $resp = [
            'status'           => 'ok',
            'csrf_token_name'  => $this->security->get_csrf_token_name(),
            'csrf_hash'        => $this->security->get_csrf_hash(),
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($resp));
        return;
    }

    //--------------------------------------------------
    /**
     * Remote validation (jQuery Validate etc.)
     * Ensures unique admin_role_title except current id
     */
    public function check_admin_role($id = 0)
    {
        $this->db->from('admin_roles');
        $this->db->where('admin_role_title', $this->input->post('admin_role_title'));
        $this->db->where('admin_role_id !=' . $id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }
}
