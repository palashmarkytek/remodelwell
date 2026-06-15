<?php defined('BASEPATH') or exit('No direct script access allowed');

class User_vertical_model extends CI_Model
{
    public function get_by_admin_id($admin_id)
    {
        // UPDATED: Include the local vertical ID only for the secure contract detail URL.
        return $this->db->select('m.advertiser_vertical_map_id, m.is_active, v.id AS local_vertical_id, v.vertical_id, v.vertical_name, v.status, c.leads_per_week, c.zip_codes, c.state_abbreviations, c.lead_delivery_method, c.lead_delivery_sms, c.lead_delivery_email, c.delivery_days, c.start_time, c.end_time')
            ->from('ci_advertiser_vertical_map m')
            ->join('ci_advertisers a', 'a.advertiser_id = m.advertiser_id')
            ->join('ci_verticals v', 'v.id = m.vertical_id')
            ->join('ci_vertical_contract_map c', 'c.advertiser_vertical_map_id = m.advertiser_vertical_map_id', 'left')
            ->where('a.admin_id', (int) $admin_id)
            ->order_by('v.vertical_name', 'ASC')
            ->get()->result_array();
    }

    // UPDATED: Return all locally stored contract information for one advertiser-owned vertical.
    public function get_contracts_by_vertical_id($admin_id, $vertical_id)
    {
        $admin_id = (int) $admin_id;
        $vertical_id = (int) $vertical_id;

        if ($admin_id <= 0 || $vertical_id <= 0) {
            return array();
        }

        $this->db->select(
            "TRIM(CONCAT(TRIM(COALESCE(ad.display_id, '')), '_', TRIM(COALESCE(v.vertical_name, '')))) AS contract_name, " .
            'm.advertiser_vertical_map_id, m.is_active AS mapping_is_active, ' .
            'a.advertiser_id, a.advertiserID, a.monthly_budget, ' .
            'ad.display_id, ad.name AS advertiser_name, ad.company AS company_name, ' .
            'v.id AS local_vertical_id, v.vertical_id AS leadspedia_vertical_id, ' .
            'v.vertical_name, v.price, v.status AS vertical_status, c.*',
            false
        );
        $this->db->from('ci_advertiser_vertical_map m');
        $this->db->join('ci_advertisers a', 'a.advertiser_id = m.advertiser_id', 'inner');
        $this->db->join('ci_admin ad', 'ad.admin_id = a.admin_id', 'inner');
        $this->db->join('ci_verticals v', 'v.id = m.vertical_id', 'inner');
        $this->db->join('ci_vertical_contract_map c', 'c.advertiser_vertical_map_id = m.advertiser_vertical_map_id', 'inner');
        $this->db->where('a.admin_id', $admin_id);
        $this->db->where('m.vertical_id', $vertical_id);
        $this->db->order_by('c.created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function change_status($map_id, $admin_id, $status)
    {
        // UPDATED: Ownership join prevents an advertiser from changing another advertiser's mapping.
        $row = $this->db->select('m.advertiser_vertical_map_id')
            ->from('ci_advertiser_vertical_map m')
            ->join('ci_advertisers a', 'a.advertiser_id = m.advertiser_id')
            ->where('m.advertiser_vertical_map_id', (int) $map_id)
            ->where('a.admin_id', (int) $admin_id)
            ->get()->row_array();
        if (empty($row)) return false;
        return $this->db->where('advertiser_vertical_map_id', (int) $map_id)
            ->update('ci_advertiser_vertical_map', array('is_active' => $status));
    }
}
