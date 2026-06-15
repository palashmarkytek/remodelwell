<?php
class Admin_roles_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	//-----------------------------------------------------
	function get_role_by_id($id)
	{
		$this->db->from('ci_admin_roles');
		$this->db->where('admin_role_id', $id);
		$query = $this->db->get();
		return $query->row_array();
	}

	//-----------------------------------------------------
	function get_all()
	{
		$this->db->from('ci_admin_roles');
		$this->db->where('deleted_at', NULL);
		$query = $this->db->get();
		return $query->result_array();
	}

	//-----------------------------------------------------
	function insert()
	{
		$this->db->set('admin_role_title', $this->input->post('admin_role_title'));
		$this->db->set('admin_role_status', $this->input->post('admin_role_status'));
		$this->db->set('admin_role_created_on', date('Y-m-d h:i:sa'));
		$this->db->insert('ci_admin_roles');
	}

	//-----------------------------------------------------
	function update()
	{
		$this->db->set('admin_role_title', $this->input->post('admin_role_title'));
		$this->db->set('admin_role_status', $this->input->post('admin_role_status'));
		$this->db->set('admin_role_modified_on', date('Y-m-d h:i:sa'));
		$this->db->where('admin_role_id', $this->input->post('admin_role_id'));
		$this->db->update('ci_admin_roles');
	}

	//-----------------------------------------------------
	function change_status()
	{
		$this->db->set('admin_role_status', $this->input->post('status'));
		$this->db->where('admin_role_id', $this->input->post('id'));
		$this->db->update('ci_admin_roles');
	}

	//-----------------------------------------------------
	function delete($id)
	{
		$this->db->set('deleted_at', today_date());
		$this->db->where('admin_role_id', $id);
		$this->db->update('ci_admin_roles');
	}

	//-----------------------------------------------------
	function get_modules()
	{
		$this->db->from('module');
		$this->db->order_by('sort_order', 'asc');
		$query = $this->db->get();
		return $query->result_array();
	}

	//-----------------------------------------------------
/**
 * set_access()
 *
 * EXISTING:
 * - module_access (controller/operation)
 *
 * UPDATED:
 * - sub_module_access (sub_module_id + operation)
 *
 * RULE:
 * If a sub-module operation is enabled:
 * 1) store in sub_module_access (role + submodule + op)
 * 2) ensure module_access exists for that controller + op (so RBAC passes)
 *
 * If disabled:
 * 1) remove from sub_module_access
 * 2) remove module_access ONLY if no other sub-module still grants same controller+op
 */
function set_access()
{
    $is_sub_module  = (bool) $this->input->post('is_sub_module');
    $status         = (int) $this->input->post('status');
    $admin_role_id  = (int) $this->input->post('admin_role_id');

    // -----------------------------
    // ✅ SUB-MODULE OPERATION MODE
    // -----------------------------
    if ($is_sub_module === true) {

        $sub_module_id = (int) $this->input->post('sub_module_id');
        $operation     = trim((string) $this->input->post('operation'));

        if ($sub_module_id <= 0 || $admin_role_id <= 0 || $operation === '') {
            return;
        }

        // Fetch sub-module row to detect controller from link
        $sub = $this->db->where('id', $sub_module_id)->get('sub_module')->row_array();
        if (!$sub) {
            return;
        }

        // Detect controller from sub_module.link
        // Examples:
        //  "purchase/received"  => controller="purchase"
        //  "admin_roles/access" => controller="admin_roles"
        $link = trim($sub['link'] ?? '');
        $controller = '';
        if ($link !== '') {
            $parts = explode('/', $link);
            $controller = strtolower(trim($parts[0] ?? ''));
        }

        if ($status === 1) {

            // 1) ✅ Grant sub_module_access (role + submodule + operation)
            $exists = $this->db->where('admin_role_id', $admin_role_id)
                ->where('sub_module_id', $sub_module_id)
                ->where('operation', $operation)
                ->get('sub_module_access')
                ->num_rows();

            if ((int)$exists === 0) {
                $this->db->insert('sub_module_access', [
                    'admin_role_id' => $admin_role_id,
                    'sub_module_id' => $sub_module_id,
                    'operation'     => $operation,
                ]);
            }

            // 2) ✅ Ensure module_access exists for controller+operation
            // (so RBAC check_operation_access() passes)
            if ($controller !== '') {
                $mExists = $this->db->where('admin_role_id', $admin_role_id)
                    ->where('module', $controller)
                    ->where('operation', $operation)
                    ->get('module_access')
                    ->num_rows();

                if ((int)$mExists === 0) {
                    $this->db->insert('module_access', [
                        'admin_role_id' => $admin_role_id,
                        'module'        => $controller,
                        'operation'     => $operation,
                    ]);
                }
            }

        } else {

            // 1) ✅ Revoke sub_module_access (role + submodule + op)
            $this->db->where('admin_role_id', $admin_role_id)
                ->where('sub_module_id', $sub_module_id)
                ->where('operation', $operation)
                ->delete('sub_module_access');

            // 2) ✅ Remove module_access ONLY if no other sub-module still grants this controller+op
            if ($controller !== '') {

                // ✅ FIX: Correct CI where() usage with SQL function
                // If ANY other submodule still grants same controller+operation, do NOT delete module_access
                $still_needed = $this->db->select('sma.id')
                    ->from('sub_module_access sma')
                    ->join('sub_module sm', 'sm.id = sma.sub_module_id', 'inner')
                    ->where('sma.admin_role_id', $admin_role_id)
                    ->where('sma.operation', $operation)
                    ->where("LOWER(SUBSTRING_INDEX(sm.link,'/',1)) = " . $this->db->escape($controller), null, false)
                    ->limit(1)
                    ->get()
                    ->num_rows();

                if ((int)$still_needed === 0) {
                    $this->db->where('admin_role_id', $admin_role_id)
                        ->where('module', $controller)
                        ->where('operation', $operation)
                        ->delete('module_access');
                }
            }
        }

        return;
    }

    // -----------------------------
    // ✅ EXISTING MODULE/OPERATION MODE (UNCHANGED)
    // -----------------------------
    if ($status == 1) {
        $this->db->insert('module_access', [
            'admin_role_id' => (int)$this->input->post('admin_role_id'),
            'module'        => trim((string)$this->input->post('module')),
            'operation'     => trim((string)$this->input->post('operation')),
        ]);
    } else {
        $this->db->where('admin_role_id', (int)$this->input->post('admin_role_id'))
            ->where('module', trim((string)$this->input->post('module')))
            ->where('operation', trim((string)$this->input->post('operation')))
            ->delete('module_access');
    }
}



	//-----------------------------------------------------
	function get_access($admin_role_id)
	{
		$this->db->from('module_access');
		$this->db->where('admin_role_id', $admin_role_id);
		$query = $this->db->get();
		$data = array();
		foreach ($query->result_array() as $v) {
			$data[] = $v['module'] . '/' . $v['operation'];
		}
		return $data;
	}

	//-----------------------------------------------------
	// NEW: Return all sub-modules (for the permission screen)
	function get_all_sub_modules()
	{
		$this->db->select('*');
		$this->db->from('sub_module');
		$this->db->order_by('parent', 'asc');
		$this->db->order_by('sort_order', 'asc');
		$query = $this->db->get();
		return $query->result_array();
	}

	//-----------------------------------------------------
	// NEW: Return list of sub_module_id that this role has access to
function get_sub_module_access($admin_role_id)
{
    $this->db->from('sub_module_access');
    $this->db->where('admin_role_id', $admin_role_id);
    $query = $this->db->get();

    $keys = [];
    foreach ($query->result_array() as $row) {
        $sid = (int)$row['sub_module_id'];
        $op  = trim($row['operation']);
        if ($sid > 0 && $op !== '') {
            $keys[] = $sid . '/' . $op;
        }
    }
    return $keys;
}


	/* SIDE MENU & SUB MODULE */

	//-----------------------------------------------------
	function get_all_module()
	{
		$this->db->select('*');
		$this->db->order_by('sort_order', 'asc');
		$query = $this->db->get('module');
		return $query->result_array();
	}

	//-----------------------------------------------------
	function add_module($data)
	{
		$this->db->insert('module', $data);
		return true;
	}

	//---------------------------------------------------
	public function edit_module($data, $id)
	{
		$this->db->where('module_id', $id);
		$this->db->update('module', $data);
		return true;
	}

	//-----------------------------------------------------
	function delete_module($id)
	{
		$this->db->where('module_id', $id);
		$this->db->delete('module');
	}

	//-----------------------------------------------------
	function get_module_by_id($id)
	{
		$this->db->from('module');
		$this->db->where('module_id', $id);
		$query = $this->db->get();
		return $query->row_array();
	}

	/*------------------------------
		Sub Module / Sub Menu  
	------------------------------*/

	//-----------------------------------------------------
	function add_sub_module($data)
	{
		$this->db->insert('sub_module', $data);
		return $this->db->insert_id();
	}

	//-----------------------------------------------------
	function get_sub_module_by_id($id)
	{
		$this->db->from('sub_module');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row_array();
	}

	//-----------------------------------------------------
	function get_sub_module_by_module($id)
	{
		$this->db->select('*');
		$this->db->where('parent', $id);
		$this->db->order_by('sort_order', 'asc');
		$query = $this->db->get('sub_module');
		return $query->result_array();
	}

	//----------------------------------------------------
	function edit_sub_module($data, $id)
	{
		$this->db->where('id', $id);
		$this->db->update('sub_module', $data);
		return true;
	}

	//-----------------------------------------------------
	function delete_sub_module($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('sub_module');
		return true;
	}
}
?>