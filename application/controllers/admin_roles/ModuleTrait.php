<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ModuleTrait
 * -----------
 * Contains:
 * - Module CRUD
 * - Sub-module CRUD
 * - Keeps your same validations, redirects and view loading
 */
trait ModuleTrait
{
    /* SIDE MENU & SUB MENU */

    //-----------------------------------------------------------
    /**
     * Module list page
     */
    public function module_list()
    {
        $this->rbac->check_operation_access();
        $data['title']   = trans('module_setting');
        $data['records'] = $this->admin_roles->get_all_module();

        $this->load->view('includes/_header');
        $this->load->view('admin_roles/module_list', $data);
        $this->load->view('includes/_footer');
    }

    //-----------------------------------------------------------
    /**
     * Add new module
     */
    public function module_add()
    {
        $this->rbac->check_operation_access();

        if ($this->input->post('submit')) {
            // ✅ Validation rules
            $this->form_validation->set_rules('module_name', 'Module Name', 'trim|required');
            $this->form_validation->set_rules('controller_name', 'Controller Name', 'trim|required');
            $this->form_validation->set_rules('fa_icon', 'fa_icon', 'trim');
            $this->form_validation->set_rules('operation', 'Operation', 'trim');
            $this->form_validation->set_rules('sort_order', 'Sort Order', 'trim');

            if ($this->form_validation->run() == FALSE) {
                $data = array('errors' => validation_errors());
                $this->session->set_flashdata('errors', $data['errors']);
                redirect(base_url('admin_roles/module_add'), 'refresh');
            } else {
                // ✅ Clean & insert
                $data = array(
                    'module_name'      => $this->input->post('module_name'),
                    'controller_name'  => $this->input->post('controller_name'),
                    'fa_icon'          => $this->input->post('fa_icon'),
                    'operation'        => $this->input->post('operation'),
                    'sort_order'       => $this->input->post('sort_order'),
                );

                $data   = $this->security->xss_clean($data);
                $result = $this->admin_roles->add_module($data);

                if ($result) {
                    $this->session->set_flashdata('success', 'Module has been added successfully!');
                    redirect(base_url('admin_roles/module_list'));
                }
            }
        } else {
            $data['title'] = trans('add_new_module');

            $this->load->view('includes/_header');
            $this->load->view('admin_roles/module_add', $data);
            $this->load->view('includes/_footer');
        }
    }

    //-----------------------------------------------------------
    /**
     * Edit module
     */
    public function module_edit($id = 0)
    {
        $this->rbac->check_operation_access();

        if ($this->input->post('submit')) {
            // ✅ Validation rules
            $this->form_validation->set_rules('module_name', 'Module Name', 'trim|required');
            $this->form_validation->set_rules('controller_name', 'Controller Name', 'trim|required');
            $this->form_validation->set_rules('fa_icon', 'fa_icon', 'trim');
            $this->form_validation->set_rules('operation', 'Operation', 'trim');
            $this->form_validation->set_rules('sort_order', 'Sort Order', 'trim');

            if ($this->form_validation->run() == FALSE) {
                $data = array('errors' => validation_errors());
                $this->session->set_flashdata('errors', $data['errors']);
                redirect(base_url('admin_roles/module_edit/' . $id), 'refresh');
            } else {
                // ✅ Clean & update
                $data = array(
                    'module_name'      => $this->input->post('module_name'),
                    'controller_name'  => $this->input->post('controller_name'),
                    'fa_icon'          => $this->input->post('fa_icon'),
                    'operation'        => $this->input->post('operation'),
                    'sort_order'       => $this->input->post('sort_order'),
                );

                $data   = $this->security->xss_clean($data);
                $result = $this->admin_roles->edit_module($data, $id);

                if ($result) {
                    $this->session->set_flashdata('success', 'Module has been Updated successfully!');
                    redirect(base_url('admin_roles/module_list'));
                }
            }
        } else {
            $data['title']  = trans('update_module');
            $data['module'] = $this->admin_roles->get_module_by_id($id);

            $this->load->view('includes/_header');
            $this->load->view('admin_roles/module_edit', $data);
            $this->load->view('includes/_footer');
        }
    }

    //------------------------------------------------------------
    /**
     * Delete module
     */
    public function module_delete($id = '')
    {
        $this->rbac->check_operation_access();

        $this->admin_roles->delete_module($id);
        $this->session->set_flashdata('msg', 'Module has been Deleted Successfully.');
        redirect('admin_roles/module_list');
    }

    /*-------------------------
        Sub Module / Sub Menu
    -------------------------*/

    //-----------------------------------------------------------
    /**
     * List sub-modules by module_id (parent module)
     */
    public function sub_module($module_id = NULL)
    {
        $this->rbac->check_operation_access();
        $data['title']   = '';
        $data['records'] = $this->admin_roles->get_sub_module_by_module($module_id);

        $this->load->view('includes/_header');
        $this->load->view('admin_roles/sub_module_list', $data);
        $this->load->view('includes/_footer');
    }

    //-----------------------------------------------------------
    /**
     * Add sub-module
     * - Saves operations list in `operation` column (as per your implementation)
     */
    public function sub_module_add()
    {
        $this->rbac->check_operation_access();

        if ($this->input->post('submit')) {

            $this->form_validation->set_rules('module_name', 'sub_module Name', 'trim|required');
            $this->form_validation->set_rules('operation', 'Link', 'trim');
            $this->form_validation->set_rules('operations', 'Operations', 'trim|required'); // ✅ NEW
            $this->form_validation->set_rules('sort_order', 'Sort Order', 'trim');
            $this->form_validation->set_rules('parent_module', 'Parent Module', 'trim|required');

            $parent = $this->input->post('parent_module');

            if ($this->form_validation->run() == FALSE) {
                $data = array('errors' => validation_errors());
                $this->session->set_flashdata('errors', $data['errors']);
                redirect(base_url('admin_roles/sub_module_add/' . $parent), 'refresh');
            } else {

                $data = array(
                    'parent'     => $parent,
                    'name'       => $this->input->post('module_name'),
                    'link'       => $this->input->post('operation'),
                    'operation'  => $this->input->post('operations'), // ✅ NEW (save operations list)
                    'sort_order' => $this->input->post('sort_order'),
                );

                $data   = $this->security->xss_clean($data);
                $result = $this->admin_roles->add_sub_module($data);

                if ($result) {
                    $this->session->set_flashdata('success', 'Sub Module has been added successfully!');
                    redirect(base_url('admin_roles/sub_module/' . $parent));
                }
            }
        } else {
            $this->load->view('includes/_header');
            $this->load->view('admin_roles/sub_module_add');
            $this->load->view('includes/_footer');
        }
    }

    //-----------------------------------------------------------
    /**
     * Edit sub-module
     */
    public function sub_module_edit($id = 0)
    {
        $this->rbac->check_operation_access();

        if ($this->input->post('submit')) {
            $this->form_validation->set_rules('module_name', 'sub_module Name', 'trim|required');
            $this->form_validation->set_rules('operation', 'Link', 'trim');
            $this->form_validation->set_rules('operations', 'Operations', 'trim|required'); // ✅ NEW
            $this->form_validation->set_rules('sort_order', 'Sort Order', 'trim');

            if ($this->form_validation->run() == FALSE) {
                $data = array('errors' => validation_errors());
                $this->session->set_flashdata('errors', $data['errors']);
                redirect(base_url('admin_roles/sub_module_edit/' . $id), 'refresh');
            } else {
                // ⚠ Keeping your original structure (even if naming seems odd)
                $parent = $this->input->post('module_name');

                $data = array(
                    'parent'     => $parent,
                    'name'       => $this->input->post('sub_module_name'),
                    'link'       => $this->input->post('operation'),
                    'operation'  => $this->input->post('operations'), // ✅ NEW
                    'sort_order' => $this->input->post('sort_order'),
                );

                $data   = $this->security->xss_clean($data);
                $result = $this->admin_roles->edit_sub_module($data, $id);

                if ($result) {
                    $this->session->set_flashdata('success', 'sub module has been Updated successfully!');
                    redirect(base_url('admin_roles/sub_module/' . $parent));
                }
            }
        } else {
            $data['title']  = '';
            $data['module'] = $this->admin_roles->get_sub_module_by_id($id);

            $this->load->view('includes/_header');
            $this->load->view('admin_roles/sub_module_edit', $data);
            $this->load->view('includes/_footer');
        }
    }

    //------------------------------------------------------------
    /**
     * Delete sub-module
     */
    public function sub_module_delete($id = 0, $parent = 0)
    {
        $this->rbac->check_operation_access();

        $this->admin_roles->delete_sub_module($id);

        $this->session->set_flashdata('msg', 'Sub Menu has been Deleted Successfully.');
        redirect('admin_roles/sub_module/' . $parent);
    }
}
