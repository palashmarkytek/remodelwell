<?php

class RBAC
{
    private $module_access = [];
    private $sub_module_access = []; // NOW: array of strings "subId/op"
    private $obj;

    // Small runtime caches
    private $controller_is_module_cache = []; // controller => bool
    private $controller_submodule_ids_cache = []; // controller => [sub_module_ids]

    public function __construct()
    {
        $this->obj = &get_instance();

        $this->module_access = $this->obj->session->userdata('module_access');
        $this->sub_module_access = $this->obj->session->userdata('sub_module_access');

        // backward compatibility
        $this->obj->module_access = $this->module_access;
        $this->obj->sub_module_access = $this->sub_module_access;
        $this->obj->is_supper = $this->obj->session->userdata('is_supper');
    }

    /**
     * Build access maps in session (call after login and after permission changes).
     */
    public function set_access_in_session()
    {
        $admin_id = (int) $this->obj->session->userdata('admin_id');
        if ($admin_id <= 0) {
            return;
        }

        $this->obj->db->select('admin_role_id, is_supper');
        $this->obj->db->from('ci_admin');
        $this->obj->db->where('admin_id', $admin_id);
        $row = $this->obj->db->get()->row_array();
        if (!$row) {
            return;
        }

        $admin_role_id = (int) ($row['admin_role_id'] ?? 0);
        $is_supper = (int) ($row['is_supper'] ?? 0);

        $this->obj->session->set_userdata('admin_role_id', $admin_role_id);
        $this->obj->session->set_userdata('is_supper', $is_supper);
        $this->obj->is_supper = $is_supper;

        // =============================
        // 1) Module access map
        // =============================
        $moduleAccessMap = [];

        if ($admin_role_id === 1 || $is_supper === 1) {
            // Super admin => all modules + all operations
            $this->obj->db->from('module');
            $query = $this->obj->db->get();

            foreach ($query->result_array() as $v) {
                $controller = $v['controller_name'];

                if (!isset($moduleAccessMap[$controller])) {
                    $moduleAccessMap[$controller] = [];
                }

                // common ops
                $moduleAccessMap[$controller]['index'] = '';
                $moduleAccessMap[$controller]['view'] = '';
                $moduleAccessMap[$controller]['add'] = '';
                $moduleAccessMap[$controller]['edit'] = '';
                $moduleAccessMap[$controller]['delete'] = '';
                $moduleAccessMap[$controller]['access'] = '';
                $moduleAccessMap[$controller]['change_status'] = '';

                // custom ops from DB
                if (!empty($v['operation'])) {
                    $ops = explode('|', $v['operation']);
                    foreach ($ops as $op) {
                        $op = trim($op);
                        if ($op !== '') {
                            $moduleAccessMap[$controller][$op] = '';
                        }
                    }
                }
            }
        } else {
            // Normal role => from module_access
            $this->obj->db->from('module_access');
            $this->obj->db->where('admin_role_id', $admin_role_id);
            $query = $this->obj->db->get();

            foreach ($query->result_array() as $v) {
                $module = $v['module'];    // controller_name
                $operation = $v['operation']; // add/edit/delete/access/change_status etc.

                if (!isset($moduleAccessMap[$module])) {
                    $moduleAccessMap[$module] = [];
                }
                $moduleAccessMap[$module][$operation] = '';
            }
        }

        // =============================
        // 2) Sub-module access list (OPERATION WISE)
        // Session format: ["12/add","12/edit",...]
        // =============================
        $subModuleKeys = [];

        if ($admin_role_id === 1 || $is_supper === 1) {
            // Super admin => allow all sub-modules with all ops defined in sub_module.operation
            $this->obj->db->select('id, operation');
            $this->obj->db->from('sub_module');
            $qSub = $this->obj->db->get();

            foreach ($qSub->result_array() as $r) {
                $sid = (int) $r['id'];
                if ($sid <= 0) {
                    continue;
                }

                $opsStr = trim((string) ($r['operation'] ?? ''));
                if ($opsStr === '') {
                    $opsStr = 'add|edit|delete|access|change_status';
                }
                $ops = array_filter(array_map('trim', explode('|', $opsStr)));

                foreach ($ops as $op) {
                    $subModuleKeys[] = $sid . '/' . $op;
                }
            }
        } else {
            // Normal role => from sub_module_access (role + sub_module + operation)
            $this->obj->db->select('sub_module_id, operation');
            $this->obj->db->from('sub_module_access');
            $this->obj->db->where('admin_role_id', $admin_role_id);
            $qSub = $this->obj->db->get();

            foreach ($qSub->result_array() as $r) {
                $sid = (int) ($r['sub_module_id'] ?? 0);
                $op = trim((string) ($r['operation'] ?? ''));
                if ($sid > 0 && $op !== '') {
                    $subModuleKeys[] = $sid . '/' . $op;
                }
            }
        }

        $subModuleKeys = array_values(array_unique($subModuleKeys));

        // Save to session
        $this->obj->session->set_userdata('module_access', $moduleAccessMap);
        $this->obj->session->set_userdata('sub_module_access', $subModuleKeys);

        $this->module_access = $moduleAccessMap;
        $this->sub_module_access = $subModuleKeys;

        $this->obj->module_access = $this->module_access;
        $this->obj->sub_module_access = $this->sub_module_access;
    }

    /**
     * Checks if a controller exists as a MODULE in table `module`.
     */
    private function is_module_controller($controller)
    {
        $controller = trim((string) $controller);
        if ($controller === '') {
            return false;
        }

        if (array_key_exists($controller, $this->controller_is_module_cache)) {
            return $this->controller_is_module_cache[$controller];
        }

        $this->obj->db->select('module_id');
        $this->obj->db->from('module');
        $this->obj->db->where('controller_name', $controller);
        $row = $this->obj->db->get()->row_array();

        $isModule = $row ? true : false;
        $this->controller_is_module_cache[$controller] = $isModule;
        return $isModule;
    }

    /**
     * Map controller methods to base operations (same idea as your code).
     * Used for sub-module controller permission check too.
     */
    private function map_method_to_base_op($method)
    {
        $method = trim((string) $method);
        if ($method === '') {
            return null;
        }

        // view-like
        if (
            $method === 'index' ||
            strpos($method, '_index') !== false ||
            strpos($method, 'list') !== false ||
            strpos($method, 'view') !== false ||
            strpos($method, 'filter') !== false || // ✅ ADD THIS
            strpos($method, 'search') !== false    // ✅ OPTIONAL
        ) {
            return 'view';
        }

        // delete-like
        if (strpos($method, 'delete') !== false || strpos($method, 'remove') !== false) {
            return 'delete';
        }

        // status-like
        if ($method === 'change_status' || strpos($method, 'status') !== false) {
            return 'change_status';
        }

        // edit-like
        if (strpos($method, 'edit') !== false || strpos($method, 'update') !== false) {
            return 'edit';
        }

        // add/create/payment-like
        if (
            strpos($method, 'add') !== false ||
            strpos($method, 'create') !== false ||
            strpos($method, 'payment') !== false
        ) {
            return 'add';
        }

        return null;
    }

    /**
     * For controllers that are NOT in `module`, treat them as "sub-module controllers"
     * if sub_module.link contains:
     *   - "controller" OR "controller/anything"
     *
     * UPDATED: requires OPERATION access, not just sub_module_id.
     */
    private function has_submodule_controller_access($controller, $methodForOp = '')
    {
        $controller = trim((string) $controller);
        if ($controller === '') {
            return false;
        }

        $allowed = $this->sub_module_access;
        if (empty($allowed) || !is_array($allowed)) {
            return false;
        }

        // Determine operation we should check for this method
        $op = $this->map_method_to_base_op($methodForOp);
        if ($op === null) {
            // fallback: try exact method name as operation (in case you store custom ops)
            $op = trim((string) $methodForOp);
        }
        if ($op === '') {
            return false;
        }

        // Cache DB results per request
        if (!array_key_exists($controller, $this->controller_submodule_ids_cache)) {

            $this->obj->db->select('id');
            $this->obj->db->from('sub_module');

            $trimController = trim($controller, '/');

            $this->obj->db->group_start();
            $this->obj->db->where(
                "TRIM(BOTH '/' FROM `link`) = " . $this->obj->db->escape($trimController),
                null,
                false
            );
            $this->obj->db->or_where(
                "TRIM(BOTH '/' FROM `link`) LIKE " . $this->obj->db->escape($trimController . '/%'),
                null,
                false
            );
            $this->obj->db->group_end();

            $rows = $this->obj->db->get()->result_array();
            $ids = [];
            foreach ($rows as $r) {
                $ids[] = (int) $r['id'];
            }
            $this->controller_submodule_ids_cache[$controller] = $ids;
        }

        $ids = $this->controller_submodule_ids_cache[$controller];
        if (empty($ids)) {
            return false;
        }

        // If user has access to ANY matching sub_module id for THIS operation => allow
        foreach ($ids as $sid) {
            if ($this->has_sub_module_op_access((int) $sid, $op)) {
                return true;
            }
        }

        return false;
    }

    public function check_operation_access()
    {
        if ($this->obj->is_supper) {
            return 1;
        }

        $operation = $this->obj->router->fetch_method();

        if (!$this->check_operation_permission($operation)) {
            $back_to = $this->obj->functions->encode($_SERVER['REQUEST_URI']);
            redirect('access_denied/index/' . $back_to);
        }

        return 1;
    }

    public function check_module_access()
    {
        if ($this->obj->is_supper) {
            return 1;
        }

        $module = $this->obj->router->fetch_class();

        if (!$this->check_module_permission($module)) {
            $back_to = $this->obj->functions->encode($_SERVER['REQUEST_URI']);
            redirect('access_denied/index/' . $back_to);
        }

        return 1;
    }
    public function check_module_permission($module)
    {
        if ($this->obj->is_supper) {
            return 1;
        }

        $module = trim((string) $module);
        if ($module === '') {
            return 0;
        }

        /**
         * RULE (6):
         * If controller is NOT a module controller, allow module access if it is a sub-module controller
         * AND user has some operation access to it (safe).
         */
        if (!$this->is_module_controller($module)) {
            $method = $this->obj->router->fetch_method();
            return $this->has_submodule_controller_access($module, $method) ? 1 : 0;
        }

        /**
         * RULE (5):
         * If controller is module => allow entering module if role has ANY access for it.
         */
        if (!empty($this->module_access) && is_array($this->module_access)) {
            if (isset($this->module_access[$module]) && is_array($this->module_access[$module])) {
                if (!empty($this->module_access[$module])) {
                    return 1;
                }
            }
        }

        /**
         * Secondary fallback: if role has ANY allowed sub_module under this module (any op)
         * allow module access (submenu-driven UI).
         */
        $this->obj->db->select('module_id');
        $this->obj->db->from('module');
        $this->obj->db->where('controller_name', $module);
        $modRow = $this->obj->db->get()->row_array();
        if (!$modRow) {
            return 0;
        }

        $module_id = (int) $modRow['module_id'];

        $this->obj->db->select('id');
        $this->obj->db->from('sub_module');
        $this->obj->db->where('parent', $module_id);
        $subs = $this->obj->db->get()->result_array();
        if (empty($subs)) {
            return 0;
        }

        foreach ($subs as $s) {
            $sid = (int) $s['id'];
            // if ANY op exists in session list for this sid => allow module
            if ($this->has_any_submodule_access($sid)) {
                return 1;
            }
        }

        return 0;
    }

    /**
     * Operation permission check.
     *
     * RULE (5): If controller is module => enforce method-based access.
     * RULE (6): If controller is sub-module controller => allow method only if sub-module op is allowed.
     */
    public function check_operation_permission($operation)
    {
        if ($this->obj->is_supper) {
            return 1;
        }

        $operation = trim((string) $operation);
        if ($operation === '') {
            return 0;
        }

        $module = $this->obj->router->fetch_class();
        $method = trim($operation, '/');

        /**
         * RULE (6):
         * If controller is NOT a module controller but is a sub-module controller allowed for this method,
         * allow.
         */
        if (!$this->is_module_controller($module)) {
            return $this->has_submodule_controller_access($module, $method) ? 1 : 0;
        }

        // Segment 3 ID heuristic (same as your code)
        $idParam = (int) $this->obj->uri->segment(3);

        // -----------------------------------------
        // 1) Module based permission (NORMALIZED)
        // -----------------------------------------
        if (!empty($this->module_access) && is_array($this->module_access)) {
            if (isset($this->module_access[$module]) && is_array($this->module_access[$module])) {

                // 1) exact method match (rare but supported)
                if (isset($this->module_access[$module][$method])) {
                    return 1;
                }
            }
        }

        // -----------------------------------------
        // 2) Sub-module based permission (OPERATION WISE)
        // Supports link = controller/method OR method-only
        // -----------------------------------------
        $this->obj->db->select('module_id');
        $this->obj->db->from('module');
        $this->obj->db->where('controller_name', $module);
        $modRow = $this->obj->db->get()->row_array();
        if (!$modRow) {
            return 0;
        }

        $module_id = (int) $modRow['module_id'];

        $this->obj->db->select('id');
        $this->obj->db->from('sub_module');
        $this->obj->db->where('parent', $module_id);

        $this->obj->db->where(
            "FIND_IN_SET(" . $this->obj->db->escape($method) . ", REPLACE(`operation`, '|', ',')) > 0",
            null,
            false
        );

        $subRow = $this->obj->db->get()->row_array();

        if (!$subRow) {
            return 0;
        }
        return $this->check_sub_module_permission((int) $subRow['id'], $method);
    }

    /**
     * NEW SIGNATURE (backward compatible):
     * - If $operation is provided, check "subId/op" in session/DB.
     * - If $operation is not provided, checks if ANY operation exists for that subId.
     */
    public function check_sub_module_permission($sub_module_id, $operation = '')
    {
        if ($this->obj->is_supper) {
            return 1;
        }

        $sub_module_id = (int) $sub_module_id;
        if ($sub_module_id <= 0) {
            return 0;
        }

        $operation = trim((string) $operation);

        // 1) session cache
        if (!empty($this->sub_module_access) && is_array($this->sub_module_access)) {
            if ($operation === '') {
                return $this->has_any_submodule_access($sub_module_id) ? 1 : 0;
            }
            return $this->has_sub_module_op_access($sub_module_id, $operation) ? 1 : 0;
        }

        // 2) DB fallback
        $admin_role_id = (int) $this->obj->session->userdata('admin_role_id');
        if ($admin_role_id <= 0) {
            return 0;
        }

        $this->obj->db->from('sub_module_access');
        $this->obj->db->where('admin_role_id', $admin_role_id);
        $this->obj->db->where('sub_module_id', $sub_module_id);

        if ($operation !== '') {
            $this->obj->db->where('operation', $operation);
        }

        return ($this->obj->db->get()->num_rows() > 0) ? 1 : 0;
    }

    /**
     * Helper: check if user has ANY op access for sub_module_id in session list.
     */
    private function has_any_submodule_access($sub_module_id)
    {
        $sub_module_id = (int) $sub_module_id;
        if ($sub_module_id <= 0) {
            return false;
        }

        if (empty($this->sub_module_access) || !is_array($this->sub_module_access)) {
            return false;
        }

        $prefix = $sub_module_id . '/';
        foreach ($this->sub_module_access as $k) {
            if (strpos((string) $k, $prefix) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Helper: check if user has sub_module_id + operation in session list.
     */
    private function has_sub_module_op_access($sub_module_id, $operation)
    {
        $sub_module_id = (int) $sub_module_id;
        $operation = trim((string) $operation);
        if ($sub_module_id <= 0 || $operation === '') {
            return false;
        }

        $key = $sub_module_id . '/' . $operation;
        return in_array($key, $this->sub_module_access, true);
    }
}
