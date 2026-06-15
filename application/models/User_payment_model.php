<?php defined('BASEPATH') or exit('No direct script access allowed');

class User_payment_model extends CI_Model
{
    public function get_advertiser_by_admin_id($admin_id)
    {
        // UPDATED: Resolve only the Leadspedia advertiser linked to the logged-in user.
        return $this->db
            ->select('advertiser_id, admin_id, advertiserID')
            ->from('ci_advertisers')
            ->where('admin_id', (int) $admin_id)
            ->limit(1)
            ->get()
            ->row_array();
    }
}
