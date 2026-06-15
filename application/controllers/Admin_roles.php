<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Roles Controller
 * - Keeps constructor + loads model
 * - Uses traits for:
 *   1) PermissionsTrait  (roles + permissions)
 *   2) ModuleTrait       (module + submodule CRUD)
 */

// ✅ Traits (page-wise split)
require_once APPPATH . 'controllers/admin_roles/PermissionsTrait.php';
require_once APPPATH . 'controllers/admin_roles/ModuleTrait.php';

class Admin_roles extends MY_Controller
{
    // ✅ Attach traits
    use PermissionsTrait;
    use ModuleTrait;

    public function __construct()
    {
        parent::__construct();

        // ✅ Security: user must be logged in
        auth_check();

        // ✅ RBAC: user must have module access
        $this->rbac->check_module_access();

        // ✅ Required model
        $this->load->model('admin_roles_model', 'admin_roles');
    }
}
