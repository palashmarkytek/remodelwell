<?php defined('BASEPATH') or exit('No direct script access allowed');

class Admin_model extends CI_Model
{
	public function get_user_detail()
	{
		$id = $this->session->userdata('admin_id');
		$query = $this->db->get_where('ci_admin', array('admin_id' => $id));
		return $result = $query->row_array();
	}


	// UPDATED: Company name was moved to ci_admin; keep the existing profile response shape.
	public function get_leadspedia_detail()
	{
		$id = $this->session->userdata('admin_id');
		$this->db->select('a.advertiser_id, ad.company AS company_name, a.leadspedia_request, a.leadspedia_response, a.leadspedia_http_code, a.leadspedia_status, a.updated_at');
		$this->db->from('ci_advertisers a');
		$this->db->join('ci_admin ad', 'ad.admin_id = a.admin_id', 'left');
		$this->db->where('a.admin_id', $id);
		return $this->db->get()->row_array();
	}

	public function update_user($data)
	{
		$id = $this->session->userdata('admin_id');
		$this->db->where('admin_id', $id);
		$this->db->update('ci_admin', $data);
		return true;
	}

	public function change_pwd($data, $id)
	{
		$this->db->where('admin_id', $id);
		$this->db->update('ci_admin', $data);
		return true;
	}

	function get_admin_roles()
	{
		$this->db->from('ci_admin_roles');
		$this->db->where('admin_role_status', 1);
		// UPDATED: Super Admin role is not available in Advertiser management.
		$this->db->where('admin_role_id !=', 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	function get_admin_by_id($id)
	{
		// UPDATED: Select only fields that still exist after the advertiser table cleanup.
		// Company/contact details come from ci_admin; qualification comes from ci_advertisers.
		$this->db->select('ci_admin.*, ci_admin.company AS company_name, ci_admin_roles.admin_role_title, ci_advertisers.advertiser_id, ci_advertisers.currently_buying, ci_advertisers.monthly_budget, ci_advertisers.agreement_accept, ci_advertisers.leadspedia_status');
		$this->db->from('ci_admin');
		$this->db->join('ci_admin_roles', 'ci_admin_roles.admin_role_id = ci_admin.admin_role_id');
		$this->db->join('ci_advertisers', 'ci_advertisers.admin_id = ci_admin.admin_id', 'left');
		$this->db->where('ci_admin.admin_id', $id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function get_all()
	{
		// UPDATED: Include Leadspedia status so the listing can hide the create button after success.
		$this->db->select('ci_admin.*, ci_admin_roles.admin_role_title, ci_advertisers.advertiser_id, ci_advertisers.leadspedia_status');
		$this->db->from('ci_admin');
		$this->db->join('ci_admin_roles', 'ci_admin_roles.admin_role_id=ci_admin.admin_role_id');
		$this->db->join('ci_advertisers', 'ci_advertisers.admin_id=ci_admin.admin_id', 'left');

		if ($this->session->userdata('filter_type') != '')
			$this->db->where('ci_admin.admin_role_id', $this->session->userdata('filter_type'));

		if ($this->session->userdata('filter_status') != '')
			$this->db->where('ci_admin.is_active', $this->session->userdata('filter_status'));

		$filterData = $this->session->userdata('filter_keyword');
		if ($filterData != '') {
			$this->db->group_start();
			$this->db->like('ci_admin_roles.admin_role_title', $filterData, 'both', false);
			$this->db->or_like('ci_admin.name', $filterData, 'both', false);
			$this->db->or_like('ci_admin.email', $filterData, 'both', false);
			$this->db->or_like('ci_admin.mobile_no', $filterData, 'both', false);
			$this->db->or_like('ci_admin.source_id', $filterData, 'both', false);
			$this->db->group_end();
		}
		$this->db->where('ci_admin.deleted_at', NULL);
		// UPDATED: Hide every Super Admin account; show all other users.
		$this->db->where('ci_admin.admin_role_id !=', 1);
		$this->db->order_by('ci_admin.admin_id', 'desc');

		$query = $this->db->get();
		$module = array();
		if ($query->num_rows() > 0) {
			$module = $query->result_array();
		}
		return $module;
	}

	public function add_admin($data)
	{
		$this->db->insert('ci_admin', $data);
		return true;
	}

	public function edit_admin($data, $id)
	{
		$this->db->where('admin_id', $id);
		$this->db->update('ci_admin', $data);
		return true;
	}

	// UPDATED: Update the new advertiser registration flow in one transaction.
	// ci_admin = company/contact, ci_advertisers = qualification,
	// ci_advertiser_vertical_map = one selected vertical,
	// ci_vertical_contract_map = lead setup and delivery settings.
	public function update_advertiser_profile($admin_data, $advertiser_data, $admin_id, $vertical_id = 0, $contract_data = array())
	{
		$admin_id = (int) $admin_id;
		$vertical_id = (int) $vertical_id;

		if ($admin_id <= 0 || $vertical_id <= 0) {
			return false;
		}

		$this->db->trans_begin();

		$this->db->where('admin_id', $admin_id);
		$this->db->update('ci_admin', $admin_data);

		$advertiser = $this->db
			->select('advertiser_id')
			->where('admin_id', $admin_id)
			->get('ci_advertisers')
			->row_array();

		if (!empty($advertiser)) {
			$advertiser_id = (int) $advertiser['advertiser_id'];
			$this->db->where('advertiser_id', $advertiser_id);
			$this->db->update('ci_advertisers', $advertiser_data);
		} else {
			// UPDATED: Preserve the existing signup relationship if an older admin has no advertiser row.
			$advertiser_data['admin_id'] = $admin_id;
			$advertiser_data['agreement_accept'] = 'Yes';
			$advertiser_data['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert('ci_advertisers', $advertiser_data);
			$advertiser_id = (int) $this->db->insert_id();
		}

		// UPDATED: Keep the existing mapping row when the vertical is unchanged.
		$selected_mapping = $this->db
			->select('advertiser_vertical_map_id')
			->where('advertiser_id', $advertiser_id)
			->where('vertical_id', $vertical_id)
			->get('ci_advertiser_vertical_map')
			->row_array();

		if (!empty($selected_mapping)) {
			$advertiser_vertical_map_id = (int) $selected_mapping['advertiser_vertical_map_id'];

			// Remove any older extra assignments; contract rows cascade automatically.
			$this->db->where('advertiser_id', $advertiser_id);
			$this->db->where('advertiser_vertical_map_id !=', $advertiser_vertical_map_id);
			$this->db->delete('ci_advertiser_vertical_map');

			$this->db->where('advertiser_vertical_map_id', $advertiser_vertical_map_id);
			$this->db->update('ci_advertiser_vertical_map', array('is_active' => 1));
		} else {
			// UPDATED: A changed radio selection replaces the previous one-to-one mapping.
			$this->db->where('advertiser_id', $advertiser_id);
			$this->db->delete('ci_advertiser_vertical_map');

			$this->db->insert('ci_advertiser_vertical_map', array(
				'advertiser_id' => $advertiser_id,
				'vertical_id' => $vertical_id,
				'is_active' => 1,
				'created_at' => date('Y-m-d H:i:s')
			));
			$advertiser_vertical_map_id = (int) $this->db->insert_id();
		}

		$existing_contract = $this->db
			->select('advertiser_vertical_map_id')
			->where('advertiser_vertical_map_id', $advertiser_vertical_map_id)
			->get('ci_vertical_contract_map')
			->row_array();

		if (!empty($existing_contract)) {
			$this->db->where('advertiser_vertical_map_id', $advertiser_vertical_map_id);
			$this->db->update('ci_vertical_contract_map', $contract_data);
		} else {
			$contract_data['advertiser_vertical_map_id'] = $advertiser_vertical_map_id;
			$contract_data['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert('ci_vertical_contract_map', $contract_data);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return false;
		}

		$this->db->trans_commit();
		return true;
	}


	// UPDATED: Active vertical options used by advertiser edit.
	public function get_active_verticals()
	{
		$this->db->where('is_active', 1);
		$this->db->order_by('vertical_name', 'asc');
		return $this->db->get('ci_verticals')->result_array();
	}


	// UPDATED: Load the one selected vertical and its one-to-one contract row.
	public function get_advertiser_contract($advertiser_id)
	{
		$advertiser_id = (int) $advertiser_id;
		if ($advertiser_id <= 0) {
			return array();
		}

		$this->db->select('m.advertiser_vertical_map_id, m.vertical_id, m.is_active AS mapping_is_active, c.leads_per_week, c.zip_codes, c.state_abbreviations, c.lead_delivery_method, c.lead_delivery_sms, c.lead_delivery_email, c.delivery_days, c.start_time, c.end_time');
		$this->db->from('ci_advertiser_vertical_map m');
		$this->db->join('ci_vertical_contract_map c', 'c.advertiser_vertical_map_id = m.advertiser_vertical_map_id', 'left');
		$this->db->where('m.advertiser_id', $advertiser_id);
		$this->db->order_by('m.advertiser_vertical_map_id', 'asc');
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}

	// UPDATED: USA states are loaded for checkbox editing and Select All.
	public function get_active_usa_states()
	{
		$this->db->where('is_active', 1);
		$this->db->order_by('state_name', 'asc');
		return $this->db->get('ci_usa_states')->result_array();
	}

	// UPDATED: Return only verticals already assigned to the selected advertiser.
	public function get_assigned_verticals($advertiser_id)
	{
		if ((int) $advertiser_id <= 0) {
			return array();
		}

		// UPDATED: Include the Leadspedia vertical ID for display and keep the local ID for the contract URL.
		$this->db->select('ci_advertiser_vertical_map.advertiser_vertical_map_id, ci_advertiser_vertical_map.vertical_id, ci_advertiser_vertical_map.is_active AS mapping_is_active, ci_verticals.vertical_id AS leadspedia_vertical_id, ci_verticals.vertical_name, ci_verticals.group_name, ci_verticals.total_offers, c.leads_per_week, c.zip_codes, c.state_abbreviations, c.lead_delivery_method, c.lead_delivery_sms, c.lead_delivery_email, c.delivery_days, c.start_time, c.end_time');
		$this->db->from('ci_advertiser_vertical_map');
		$this->db->join('ci_verticals', 'ci_verticals.id = ci_advertiser_vertical_map.vertical_id', 'inner');
		$this->db->join('ci_vertical_contract_map c', 'c.advertiser_vertical_map_id = ci_advertiser_vertical_map.advertiser_vertical_map_id', 'left');
		$this->db->where('ci_advertiser_vertical_map.advertiser_id', (int) $advertiser_id);
		$this->db->order_by('ci_verticals.vertical_name', 'asc');
		return $this->db->get()->result_array();
	}

	// UPDATED: Return all contract information for the selected advertiser and local vertical ID.
	public function get_advertiser_vertical_contracts($advertiser_id, $vertical_id)
	{
		$advertiser_id = (int) $advertiser_id;
		$vertical_id = (int) $vertical_id;

		if ($advertiser_id <= 0 || $vertical_id <= 0) {
			return array();
		}

		$this->db->select(
			"TRIM(CONCAT(TRIM(COALESCE(ad.display_id, '')), '_', TRIM(COALESCE(v.vertical_name, '')))) AS contract_name, " .
			'm.advertiser_vertical_map_id, m.is_active AS mapping_is_active, ' .
			'a.advertiser_id, a.advertiserID, a.monthly_budget, ' .
			'ad.admin_id, ad.display_id, ad.name AS advertiser_name, ad.company AS company_name, ' .
			'v.id AS local_vertical_id, v.vertical_id AS leadspedia_vertical_id, ' .
			'v.vertical_name, v.price, v.status AS vertical_status, c.*',
			false
		);
		$this->db->from('ci_advertiser_vertical_map m');
		$this->db->join('ci_advertisers a', 'a.advertiser_id = m.advertiser_id', 'inner');
		$this->db->join('ci_admin ad', 'ad.admin_id = a.admin_id', 'inner');
		$this->db->join('ci_verticals v', 'v.id = m.vertical_id', 'inner');
		$this->db->join('ci_vertical_contract_map c', 'c.advertiser_vertical_map_id = m.advertiser_vertical_map_id', 'inner');
		$this->db->where('m.advertiser_id', $advertiser_id);
		$this->db->where('m.vertical_id', $vertical_id);
		$this->db->order_by('c.created_at', 'DESC');
		return $this->db->get()->result_array();
	}

	// UPDATED: Remove only the requested advertiser-to-vertical assignment.
	public function remove_advertiser_vertical($advertiser_id, $vertical_id)
	{
		$advertiser_id = (int) $advertiser_id;
		$vertical_id = (int) $vertical_id;

		if ($advertiser_id <= 0 || $vertical_id <= 0) {
			return false;
		}

		$this->db->where('advertiser_id', $advertiser_id);
		$this->db->where('vertical_id', $vertical_id);
		$this->db->delete('ci_advertiser_vertical_map');

		return $this->db->affected_rows() > 0;
	}

	// UPDATED: Existing selections used to pre-check advertiser edit checkboxes.
	public function get_advertiser_vertical_ids($advertiser_id)
	{
		if (!$advertiser_id) {
			return array();
		}
		$rows = $this->db->select('vertical_id')->where('advertiser_id', (int) $advertiser_id)->get('ci_advertiser_vertical_map')->result_array();
		return array_map('intval', array_column($rows, 'vertical_id'));
	}


	/**
	 * UPDATED: Synchronize vertical assignments from /admin/mapping/:id.
	 * Existing application structure is preserved: unchecked rows are removed,
	 * existing rows remain unchanged, and only new rows are inserted.
	 */
	public function sync_advertiser_vertical_mapping($advertiser_id, $vertical_ids = array())
	{
		$advertiser_id = (int) $advertiser_id;
		$vertical_ids = array_values(array_unique(array_filter(array_map('intval', (array) $vertical_ids))));

		if ($advertiser_id <= 0) {
			return false;
		}

		$this->db->trans_start();

		if (!empty($vertical_ids)) {
			// UPDATED: Remove only verticals unchecked for this advertiser.
			$this->db->where('advertiser_id', $advertiser_id);
			$this->db->where_not_in('vertical_id', $vertical_ids);
			$this->db->delete('ci_advertiser_vertical_map');

			$existing_rows = $this->db
				->select('vertical_id')
				->where('advertiser_id', $advertiser_id)
				->get('ci_advertiser_vertical_map')
				->result_array();

			$existing_ids = array_map('intval', array_column($existing_rows, 'vertical_id'));

			foreach ($vertical_ids as $vertical_id) {
				if (!in_array($vertical_id, $existing_ids, true)) {
					$this->db->insert('ci_advertiser_vertical_map', array(
						'advertiser_id' => $advertiser_id,
						'vertical_id' => $vertical_id,
						'is_active' => 1,
						'created_at' => date('Y-m-d H:i:s')
					));
				}
			}
		} else {
			// UPDATED: No checked vertical means remove all assignments for this advertiser.
			$this->db->where('advertiser_id', $advertiser_id);
			$this->db->delete('ci_advertiser_vertical_map');
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function change_status()
	{
		$this->db->set('is_active', $this->input->post('status'));
		$this->db->where('admin_id', $this->input->post('id'));
		$this->db->update('ci_admin');
	}

	function delete($id)
	{
		$this->db->set('deleted_at', today_date());
		$this->db->where('admin_id', $id);
		$this->db->update('ci_admin');
	}

	public function is_email_unique($email)
	{
		$this->db->where('email', $email);
		$query = $this->db->get('ci_admin');
		return $query->num_rows() === 0;
	}

	/**
	 * Offer ids are loaded from the buyer offer mapping table and grouped by
	 * buyer so the mapping screen can show Buyer heading with offer ids below it.
	 *
	 * IMPORTANT:
	 * Revenue type and amount are associate/vendor-specific, so they are read
	 * from ci_associate_offer_map for the selected admin only. This prevents one
	 * associate's revenue update from appearing on another associate's mapping.
	 */
	public function get_offer_id_list_grouped($admin_id)
	{
		$this->db->select('b.buyer_id, b.name as buyer_name, o.buyer_offer_map_id, o.network_offer_id, o.offer_name, m.revenue_type, m.revenue_amount');
		$this->db->from('ci_buyer_offer_map o');
		$this->db->join('ci_buyers b', 'b.buyer_id = o.buyer_id', 'left');
		$this->db->join('ci_associate_offer_map m', 'm.buyer_offer_map_id = o.buyer_offer_map_id AND m.admin_id = ' . (int) $admin_id, 'left');
		$this->db->where('o.deleted_at', NULL);
		$this->db->order_by('b.name', 'asc');
		$this->db->order_by('o.network_offer_id', 'asc');

		$query = $this->db->get();

		$groups = array();

		foreach ($query->result_array() as $row) {
			$buyer_id = $row['buyer_id'];

			if (!isset($groups[$buyer_id])) {
				$groups[$buyer_id] = array(
					'buyer_id' => $buyer_id,
					'buyer_name' => $row['buyer_name'],
					'offers' => array(),
				);
			}

			$groups[$buyer_id]['offers'][] = array(
				'buyer_offer_map_id' => $row['buyer_offer_map_id'],
				'network_offer_id' => $row['network_offer_id'],
				'offer_name' => $row['offer_name'],

				// Revenue fields are saved per associate/vendor mapping row.
				'revenue_type' => $row['revenue_type'],
				'revenue_amount' => $row['revenue_amount'],
			);
		}

		return $groups;
	}

	/**
	 * Returns selected offer ids for one associate so the checkbox list can
	 * show already mapped offers as checked.
	 */
	public function get_mapped_offer_ids($admin_id)
	{
		$this->db->select('buyer_offer_map_id');
		$this->db->from('ci_associate_offer_map');
		$this->db->where('admin_id', $admin_id);
		$query = $this->db->get();

		$ids = array();
		foreach ($query->result_array() as $row) {
			$ids[] = $row['buyer_offer_map_id'];
		}
		return $ids;
	}

	/**
	 * Synchronizes mapping rows from the checkbox form.
	 * Checked offer ids are inserted/updated for this associate only, and
	 * unchecked offer ids are removed only for this associate.
	 */
	public function sync_source_offer_mapping($admin_id, $source_id, $offer_ids, $revenue_types = array(), $revenue_amounts = array())
	{
		$selected_ids = array_unique(array_filter((array) $offer_ids));
		$existing_ids = $this->get_mapped_offer_ids($admin_id);

		$to_insert = array_diff($selected_ids, $existing_ids);
		$to_delete = array_diff($existing_ids, $selected_ids);

		// Validate revenue details for each selected offer before database update.
		// Revenue values are saved in ci_associate_offer_map, not ci_buyer_offer_map,
		// because the same buyer offer can have different payout settings per vendor.
		$validated_revenue = array();

		foreach ($selected_ids as $buyer_offer_map_id) {
			$revenue_type = isset($revenue_types[$buyer_offer_map_id]) ? trim($revenue_types[$buyer_offer_map_id]) : '';
			$revenue_amount = isset($revenue_amounts[$buyer_offer_map_id]) ? trim($revenue_amounts[$buyer_offer_map_id]) : '';

			if ($revenue_type !== '0' && $revenue_type !== '1') {
				return array('status' => false, 'message' => 'Please select revenue type for all selected offers.');
			}

			if ($revenue_amount === '' || !is_numeric($revenue_amount) || (int) $revenue_amount < 0) {
				return array('status' => false, 'message' => 'Please enter valid revenue amount for all selected offers.');
			}

			$validated_revenue[$buyer_offer_map_id] = array(
				'revenue_type' => $revenue_type,
				'revenue_amount' => (int) $revenue_amount,
			);
		}

		if (!empty($to_delete)) {
			$this->db->where('admin_id', $admin_id);
			$this->db->where_in('buyer_offer_map_id', $to_delete);
			$this->db->delete('ci_associate_offer_map');
		}

		foreach ($selected_ids as $buyer_offer_map_id) {
			$mapping_data = array(
				'source_id' => $source_id,
				'revenue_type' => $validated_revenue[$buyer_offer_map_id]['revenue_type'],
				'revenue_amount' => $validated_revenue[$buyer_offer_map_id]['revenue_amount'],
				'updated_at' => today_date(),
			);

			if (in_array($buyer_offer_map_id, $to_insert)) {
				// New mapping row for this associate/vendor.
				$mapping_data['admin_id'] = $admin_id;
				$mapping_data['buyer_offer_map_id'] = $buyer_offer_map_id;
				$mapping_data['created_at'] = today_date();

				$this->db->insert('ci_associate_offer_map', $mapping_data);
			} else {
				// Existing mapping row update is scoped by admin_id + offer id only.
				$this->db->where('admin_id', $admin_id);
				$this->db->where('buyer_offer_map_id', $buyer_offer_map_id);
				$this->db->update('ci_associate_offer_map', $mapping_data);
			}
		}

		return array(
			'status' => true,
			'message' => 'Offer mapping updated successfully.',
		);
	}

	/**
	 * Returns Source Id vs Offer Id mapping list for one associate.
	 */
	public function get_source_offer_mapping_list($admin_id)
	{
		$this->db->select('m.associate_offer_map_id, m.source_id, m.created_at, o.network_offer_id, o.offer_name');
		$this->db->from('ci_associate_offer_map m');
		$this->db->join('ci_buyer_offer_map o', 'o.buyer_offer_map_id = m.buyer_offer_map_id', 'left');
		$this->db->where('m.admin_id', $admin_id);
		$this->db->order_by('m.associate_offer_map_id', 'desc');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * Saves a new Source Id vs Offer Id mapping. Duplicate mapping combinations are blocked.
	 */
	public function save_source_offer_mapping($data)
	{
		$this->db->from('ci_associate_offer_map');
		$this->db->where('admin_id', $data['admin_id']);
		$this->db->where('source_id', $data['source_id']);
		$this->db->where('buyer_offer_map_id', $data['buyer_offer_map_id']);
		$duplicate = $this->db->get()->row_array();

		if (!empty($duplicate)) {
			return array('status' => false, 'message' => 'This Source Id vs Offer Id mapping already exists.');
		}

		$this->db->insert('ci_associate_offer_map', $data);
		return array('status' => true, 'message' => 'Mapping has been added successfully.');
	}

	/**
	 * Deletes one associate mapping row only.
	 */
	public function delete_source_offer_mapping($mapping_id, $admin_id)
	{
		$this->db->where('associate_offer_map_id', $mapping_id);
		$this->db->where('admin_id', $admin_id);
		$this->db->delete('ci_associate_offer_map');
		return true;
	}
	// UPDATED: Load ci_admin and ci_advertisers fields required by the
	// Leadspedia advertiser, advertiser info, and contact API calls.
	public function get_leadspedia_user_data($admin_id)
	{
		$this->db->select('ad.admin_id, ad.display_id, ad.name, ad.email, ad.mobile_no, ad.company, a.advertiser_id, a.advertiserID, a.contactID, a.leadspedia_request, a.leadspedia_response, a.leadspedia_http_code, a.leadspedia_status');
		$this->db->from('ci_admin ad');
		$this->db->join('ci_advertisers a', 'a.admin_id = ad.admin_id', 'inner');
		$this->db->where('ad.admin_id', (int) $admin_id);
		$this->db->where('ad.deleted_at', NULL);
		return $this->db->get()->row_array();
	}

	// UPDATED: Load the single assigned vertical and its contract values for
	// Leadspedia contract, filters, and schedule creation.
	public function get_leadspedia_contract_data($admin_id)
	{
		// UPDATED: Keep m.vertical_id as the local ci_verticals.id foreign key and
		// expose v.vertical_id separately because Leadspedia create contract requires verticalID.
		$this->db->select('ad.admin_id, ad.display_id, a.advertiser_id, a.advertiserID, a.monthly_budget, m.advertiser_vertical_map_id, m.vertical_id, v.vertical_id AS leadspedia_vertical_id, v.vertical_name, v.price, c.contractID, c.leads_per_week, c.zip_codes, c.state_abbreviations, c.delivery_days, c.start_time, c.end_time, c.leadspedia_request, c.leadspedia_response, c.leadspedia_http_code, c.leadspedia_status');
		$this->db->from('ci_admin ad');
		$this->db->join('ci_advertisers a', 'a.admin_id = ad.admin_id', 'inner');
		$this->db->join('ci_advertiser_vertical_map m', 'm.advertiser_id = a.advertiser_id', 'inner');
		$this->db->join('ci_verticals v', 'v.id = m.vertical_id', 'inner');
		$this->db->join('ci_vertical_contract_map c', 'c.advertiser_vertical_map_id = m.advertiser_vertical_map_id', 'inner');
		$this->db->where('ad.admin_id', (int) $admin_id);
		$this->db->where('m.is_active', 1);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}

	// UPDATED: Save Leadspedia IDs, request/response logs, and status without
	// changing the existing advertiser registration fields.
	public function update_advertiser_leadspedia($advertiser_id, $data)
	{
		$this->db->where('advertiser_id', (int) $advertiser_id);
		$this->db->update('ci_advertisers', $data);
		return $this->db->affected_rows() >= 0;
	}

	// UPDATED: Save Leadspedia contract ID, all contract API logs, and status
	// against the existing one-to-one advertiser vertical contract row.
	public function update_vertical_contract_leadspedia($advertiser_vertical_map_id, $data)
	{
		$this->db->where('advertiser_vertical_map_id', (int) $advertiser_vertical_map_id);
		$this->db->update('ci_vertical_contract_map', $data);
		return $this->db->affected_rows() >= 0;
	}

}
?>