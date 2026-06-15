<?php defined('BASEPATH') or exit('No direct script access allowed');

class User_verticals extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        auth_check();
        // UPDATED: This page is available only to logged-in advertisers.
        if ((int) $this->session->userdata('admin_role_id') !== 2) {
            redirect(base_url('access_denied'));
        }
        $this->load->model('User_vertical_model', 'user_vertical');
        // UPDATED: Reuse the existing Leadspedia settings and cURL helper methods.
        $this->load->model('auth_model', 'auth_model');
        $this->load->helper('curl');
    }

    public function index()
    {
        $data['title'] = 'My Verticals';
        $data['verticals'] = $this->user_vertical->get_by_admin_id((int) $this->session->userdata('admin_id'));
        display('user_verticals/index', $data);
    }

    // UPDATED: Show contract details with status fetched from Leadspedia in real time.
    public function contract($vertical_id = '')
    {
        $vertical_id = (int) $vertical_id;

        if ($vertical_id <= 0) {
            redirect(base_url('user_verticals'));
            return;
        }

        $contracts = $this->user_vertical->get_contracts_by_vertical_id(
            (int) $this->session->userdata('admin_id'),
            $vertical_id
        );

        // UPDATED: Ownership is checked inside the model before contract data is returned.
        if (empty($contracts)) {
            $this->session->set_flashdata('error', 'Contract record not found for the selected vertical.');
            redirect(base_url('user_verticals'));
            return;
        }

        $credentials = leadspedia_get_api_credentials();
        $payment_details = array(
            'loaded' => false,
            'exists' => false,
            'error' => isset($credentials['message']) ? $credentials['message'] : ''
        );

        if (!empty($credentials['status'])) {
            // UPDATED: Check the default card once at runtime for the advertiser.
            $payment_details = leadspedia_default_payment_details(
                isset($contracts[0]['advertiserID']) ? (int) $contracts[0]['advertiserID'] : 0,
                $credentials['api_key'],
                $credentials['api_secret']
            );
        }

        foreach ($contracts as $key => $contract) {
            $runtime_details = array(
                'loaded' => false,
                'status' => 'Unavailable',
                'is_active' => false,
                'contract_name' => '',
                'error' => isset($credentials['message']) ? $credentials['message'] : ''
            );

            if (!empty($credentials['status'])) {
                // UPDATED: Contract status is never read from local mapping/status columns.
                $runtime_details = leadspedia_contract_runtime_details(
                    isset($contract['contractID']) ? (int) $contract['contractID'] : 0,
                    $credentials['api_key'],
                    $credentials['api_secret']
                );
            }

            $contracts[$key]['runtime_status_loaded'] = !empty($runtime_details['loaded']);
            $contracts[$key]['runtime_contract_status'] = isset($runtime_details['status']) ? $runtime_details['status'] : 'Unavailable';
            $contracts[$key]['runtime_contract_active'] = !empty($runtime_details['is_active']);
            $contracts[$key]['runtime_contract_error'] = isset($runtime_details['error']) ? $runtime_details['error'] : '';
            $contracts[$key]['runtime_contract_name'] = !empty($runtime_details['contract_name'])
                ? $runtime_details['contract_name']
                : (isset($contract['contract_name']) ? $contract['contract_name'] : '-');
        }

        $data['title'] = 'View Contract';
        $data['contracts'] = $contracts;
        $data['vertical'] = $contracts[0];
        $data['default_payment_loaded'] = !empty($payment_details['loaded']);
        $data['has_default_payment'] = !empty($payment_details['exists']);
        $data['default_payment_error'] = isset($payment_details['error']) ? $payment_details['error'] : '';
        display('user_verticals/contract', $data);
    }

    // UPDATED: Activate an inactive contract only after a fresh default-card check.
    public function activate_contract($vertical_id = '')
    {
        $vertical_id = (int) $vertical_id;

        if ($this->input->method(TRUE) !== 'POST' || $vertical_id <= 0) {
            redirect(base_url('user_verticals'));
            return;
        }

        $contracts = $this->user_vertical->get_contracts_by_vertical_id(
            (int) $this->session->userdata('admin_id'),
            $vertical_id
        );

        if (empty($contracts)) {
            $this->session->set_flashdata('error', 'Contract record not found for the selected vertical.');
            redirect(base_url('user_verticals'));
            return;
        }

        $contract = $contracts[0];
        $contract_id = isset($contract['contractID']) ? (int) $contract['contractID'] : 0;
        $advertiser_id = isset($contract['advertiserID']) ? (int) $contract['advertiserID'] : 0;

        if ($contract_id <= 0) {
            $this->session->set_flashdata('error', 'Leadspedia contract ID is not available.');
            redirect(base_url('user_verticals/contract/' . $vertical_id));
            return;
        }

        $credentials = leadspedia_get_api_credentials();
        if (empty($credentials['status'])) {
            $this->session->set_flashdata('error', $credentials['message']);
            redirect(base_url('user_verticals/contract/' . $vertical_id));
            return;
        }

        // UPDATED: Recheck payment immediately before activation; page-load state is not trusted.
        $payment_details = leadspedia_default_payment_details(
            $advertiser_id,
            $credentials['api_key'],
            $credentials['api_secret']
        );

        if (empty($payment_details['loaded'])) {
            $this->session->set_flashdata('error', 'Unable to verify the default payment method. ' . $payment_details['error']);
            redirect(base_url('user_verticals/contract/' . $vertical_id));
            return;
        }

        if (empty($payment_details['exists'])) {
            $this->session->set_flashdata('error', 'Please add a default payment method before activating the contract.');
            redirect(base_url('user_payments'));
            return;
        }

        // UPDATED: Recheck the live contract status before calling enableCredit.do.
        $runtime_details = leadspedia_contract_runtime_details(
            $contract_id,
            $credentials['api_key'],
            $credentials['api_secret']
        );

        if (empty($runtime_details['loaded'])) {
            $this->session->set_flashdata('error', 'Unable to verify the current contract status. ' . $runtime_details['error']);
            redirect(base_url('user_verticals/contract/' . $vertical_id));
            return;
        }

        if (!empty($runtime_details['is_active'])) {
            $this->session->set_flashdata('success', 'Contract is already active.');
            redirect(base_url('user_verticals/contract/' . $vertical_id));
            return;
        }

        $api_response = leadspedia_enable_contract_credit(
            $contract_id,
            $credentials['api_key'],
            $credentials['api_secret']
        );

        if (!leadspedia_api_succeeded($api_response)) {
            $this->session->set_flashdata('error', 'Unable to activate the contract. ' . leadspedia_response_message($api_response));
            redirect(base_url('user_verticals/contract/' . $vertical_id));
            return;
        }

        $this->session->set_flashdata('success', 'Contract has been activated successfully.');
        redirect(base_url('user_verticals/contract/' . $vertical_id));
    }

    public function change_status()
    {
        $map_id = (int) $this->input->post('id');
        $status = ((int) $this->input->post('status') === 1) ? 1 : 0;
        $updated = $this->user_vertical->change_status($map_id, (int) $this->session->userdata('admin_id'), $status);
        $this->output->set_content_type('application/json')->set_output(json_encode(array(
            'status' => (bool) $updated,
            'message' => $updated ? 'Vertical status updated successfully.' : 'Unable to update vertical status.'
        )));
    }
}
