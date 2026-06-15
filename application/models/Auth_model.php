<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model{

	public function login($data){

		$this->db->from('ci_admin');
		$this->db->join('ci_admin_roles','ci_admin_roles.admin_role_id = ci_admin.admin_role_id');
		$this->db->where('ci_admin.email', $data['email']);
		$this->db->where('ci_admin.admin_role_id !=', 5);

		$query = $this->db->get();
		if ($query->num_rows() == 0){
			return false;
		}
		else{
			//Compare the password attempt with the password we have stored.
			$result = $query->row_array();
		    $validPassword = password_verify($data['password'], $result['password']);
		    if($validPassword){
		        return $result = $query->row_array();
		    }
		}
	}

	//--------------------------------------------------------------------
	public function register($data){
		$this->db->insert('ci_admin', $data);
		return true;
	}

	//--------------------------------------------------------------------
	public function email_verification($code){
		$this->db->select('email, token, is_active');
		$this->db->from('ci_admin');
		$this->db->where('token', $code);
		$query = $this->db->get();
		$result= $query->result_array();
		$match = count($result);
		if($match > 0){
			$this->db->where('token', $code);
			$this->db->update('ci_admin', array('is_verify' => 1, 'token'=> ''));
			return true;
		}
		else{
			return false;
			}
	}

	//============ Check User Email ============
    function check_user_mail($email)
    {
    	$result = $this->db->get_where('ci_admin', array('email' => $email));

    	if($result->num_rows() > 0){
    		$result = $result->row_array();
    		return $result;
    	}
    	else {
    		return false;
    	}
    }

    //============ Update Reset Code Function ===================
    public function update_reset_code($reset_code, $user_id){
    	$data = array('password_reset_code' => $reset_code);
    	$this->db->where('admin_id', $user_id);
    	$this->db->update('ci_admin', $data);
    }

    //============ Activation code for Password Reset Function ===================
    public function check_password_reset_code($code){

    	$result = $this->db->get_where('ci_admin',  array('password_reset_code' => $code ));
    	if($result->num_rows() > 0){
    		return true;
    	}
    	else{
    		return false;
    	}
    }
    
    //============ Reset Password ===================
    public function reset_password($id, $new_password){
	    $data = array(
			'password_reset_code' => '',
			'password' => $new_password
	    );
		$this->db->where('password_reset_code', $id);
		$this->db->update('ci_admin', $data);
		return true;
    }

    //--------------------------------------------------------------------
	public function get_admin_detail(){
		$id = $this->session->userdata('admin_id');
		$query = $this->db->get_where('ci_admin', array('admin_id' => $id));
		return $result = $query->row_array();
	}

	//--------------------------------------------------------------------
	public function update_admin($data){
		$id = $this->session->userdata('admin_id');
		$this->db->where('admin_id', $id);
		$this->db->update('ci_admin', $data);
		return true;
	}

	//--------------------------------------------------------------------
	public function change_pwd($data, $id){
		$this->db->where('admin_id', $id);
		$this->db->update('ci_admin', $data);
		return true;
	}


    //--------------------------------------------------------------------
    // Create vendor login and advertiser workflow row in one transaction.
    // Existing login flow remains unchanged because ci_admin is used.
    public function create_vendor_advertiser_signup($admin_data, $advertiser_data, $vertical_id = 0, $contract_data = array())
    {
        $this->db->trans_begin();

        $this->db->insert('ci_admin', $admin_data);
        $admin_id = $this->db->insert_id();

        $advertiser_data['admin_id'] = $admin_id;
        $this->db->insert('ci_advertisers', $advertiser_data);
        $advertiser_id = $this->db->insert_id();

        // UPDATED: Remove only stale mapping rows left behind when
        // ci_advertisers was rebuilt/reset and its AUTO_INCREMENT ids
        // started again. A newly inserted advertiser_id cannot already
        // have a valid mapping, so this prevents uk_advertiser_vertical
        // duplicate errors without changing the normal registration flow.
        $this->db->where('advertiser_id', $advertiser_id);
        $this->db->delete('ci_advertiser_vertical_map');

        // UPDATED: Registration supports one radio-selected vertical only.
        $this->db->insert('ci_advertiser_vertical_map', array(
            'advertiser_id' => $advertiser_id,
            'vertical_id' => (int) $vertical_id,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ));
        $advertiser_vertical_map_id = $this->db->insert_id();

        // UPDATED: Contract data is linked one-to-one using advertiser_vertical_map_id as primary key.
        $contract_data['advertiser_vertical_map_id'] = $advertiser_vertical_map_id;
        $this->db->insert('ci_vertical_contract_map', $contract_data);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return array('admin_id' => $admin_id, 'advertiser_id' => $advertiser_id);
    }

    //--------------------------------------------------------------------
    // Save Leadspedia request/response status for admin follow-up.
    public function update_advertiser_api_response($advertiser_id, $data)
    {
        $this->db->where('advertiser_id', $advertiser_id);
        $this->db->update('ci_advertisers', $data);
        return true;
    }

    //--------------------------------------------------------------------
    // Settings used by Leadspedia advertiser creation API.
    public function get_leadspedia_settings()
    {
        // UPDATED: Read separate Leadspedia API Key and API Secret fields.
        $this->db->select('leadspedia_account_manager_id, leadspedia_api_key, leadspedia_api_secret');
        $this->db->where('id', 1);
        return $this->db->get('ci_general_settings')->row_array();
    }


    //--------------------------------------------------------------------
    // UPDATED: Fetch the registration/Leadspedia row linked to one vendor login.
    public function get_advertiser_by_admin_id($admin_id)
    {
        $this->db->where('admin_id', (int) $admin_id);
        return $this->db->get('ci_advertisers')->row_array();
    }

    //--------------------------------------------------------------------
    // UPDATED: Active verticals displayed as services on public registration.
    public function get_active_verticals()
    {
        $this->db->where('is_active', 1);
        $this->db->order_by('vertical_name', 'asc');
        return $this->db->get('ci_verticals')->result_array();
    }

    //--------------------------------------------------------------------
    // UPDATED: Load active USA states from ci_usa_states for /auth/register.
    // This removes the hardcoded state list from the registration view.
    public function get_active_usa_states()
    {
        $this->db->where('is_active', 1);
        $this->db->order_by('state_name', 'asc');
        return $this->db->get('ci_usa_states')->result_array();
    }

}

?>