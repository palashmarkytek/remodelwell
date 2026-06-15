<?php defined('BASEPATH') or exit('No direct script access allowed');

class User_payments extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        auth_check();

        // UPDATED: Payment methods are available only to logged-in advertisers.
        if ((int) $this->session->userdata('admin_role_id') !== 2) {
            redirect(base_url('access_denied'));
        }

        $this->load->model('User_payment_model', 'user_payment');
        $this->load->model('Setting_model', 'setting');
        $this->load->helper('curl');
    }

    public function index()
    {
        // UPDATED: New advertiser payment-method listing page.
        $data['title'] = 'User Payments';
        $data['payments'] = array();
        $data['api_error'] = '';
        $data['can_add_payment'] = false;
        $data['open_payment_modal'] = (bool) $this->session->flashdata('open_payment_modal');
        $data['payment_form_data'] = (array) $this->session->flashdata('payment_form_data');

        $advertiser = $this->_get_logged_in_advertiser();
        if (empty($advertiser) || (int) $advertiser['advertiserID'] <= 0) {
            $data['api_error'] = 'Leadspedia advertiser ID is not available for this account.';
            return display('user_payments/index', $data);
        }

        $credentials = $this->_get_api_credentials();
        if (!$credentials['status']) {
            $data['api_error'] = $credentials['message'];
            return display('user_payments/index', $data);
        }

        $data['can_add_payment'] = true;

        // UPDATED: Fetch up to 100 cards from the requested Leadspedia endpoint.
        $api_response = leadspedia_get_advertiser_credit_cards(
            (int) $advertiser['advertiserID'],
            $credentials['api_key'],
            $credentials['api_secret'],
            100,
            0
        );

        if (!leadspedia_api_succeeded($api_response)) {
            $data['api_error'] = 'Unable to load payment methods. ' . leadspedia_response_message($api_response);
            return display('user_payments/index', $data);
        }

        $decoded = json_decode(isset($api_response['body']) ? $api_response['body'] : '', true);
        if (!is_array($decoded)) {
            $data['api_error'] = 'Invalid JSON response received from Leadspedia.';
            return display('user_payments/index', $data);
        }

        $data['payments'] = $this->_extract_payment_rows($decoded);
        return display('user_payments/index', $data);
    }

    public function add()
    {
        // UPDATED: Add-card requests are accepted only as POST submissions.
        if ($this->input->method(TRUE) !== 'POST') {
            redirect(base_url('user_payments'));
        }

        $advertiser = $this->_get_logged_in_advertiser();
        if (empty($advertiser) || (int) $advertiser['advertiserID'] <= 0) {
            $this->session->set_flashdata('error', 'Leadspedia advertiser ID is not available for this account.');
            redirect(base_url('user_payments'));
        }

        $credentials = $this->_get_api_credentials();
        if (!$credentials['status']) {
            $this->session->set_flashdata('error', $credentials['message']);
            redirect(base_url('user_payments'));
        }

        $card_data = $this->_payment_form_data();
        $validation_errors = $this->_validate_payment_form($card_data);

        if (!empty($validation_errors)) {
            // UPDATED: Preserve only non-sensitive billing fields after validation failure.
            // Card number and CVV are intentionally never stored in flash/session data.
            $this->_set_payment_form_error(implode('<br>', array_map('html_escape', $validation_errors)), $card_data);
            redirect(base_url('user_payments'));
        }

        // UPDATED: Send validated card data directly to Leadspedia. No local DB insert is performed.
        $api_response = leadspedia_add_advertiser_credit_card(
            (int) $advertiser['advertiserID'],
            $card_data,
            $credentials['api_key'],
            $credentials['api_secret']
        );

        if (!leadspedia_api_succeeded($api_response)) {
            $message = leadspedia_response_message($api_response);
            $this->_set_payment_form_error('Unable to add payment method. ' . html_escape($message), $card_data);
            redirect(base_url('user_payments'));
        }

        $this->session->set_flashdata('success', 'Payment method has been added successfully.');
        redirect(base_url('user_payments'));
    }

    private function _get_logged_in_advertiser()
    {
        return $this->user_payment->get_advertiser_by_admin_id(
            (int) $this->session->userdata('admin_id')
        );
    }

    private function _get_api_credentials()
    {
        $settings = $this->setting->get_general_settings();
        $api_key = trim((string) ($settings['leadspedia_api_key'] ?? ''));
        $api_secret = trim((string) ($settings['leadspedia_api_secret'] ?? ''));

        if ($api_key === '' || $api_secret === '') {
            return array(
                'status' => false,
                'message' => 'Leadspedia API Key or API Secret is missing in General Settings.',
                'api_key' => '',
                'api_secret' => ''
            );
        }

        return array(
            'status' => true,
            'message' => '',
            'api_key' => $api_key,
            'api_secret' => $api_secret
        );
    }

    private function _payment_form_data()
    {
        // UPDATED: Normalize the card number before validation/API submission.
        $card_number = preg_replace('/[\s-]+/', '', (string) $this->input->post('cardNumber', true));

        return array(
            'address' => trim((string) $this->input->post('address', true)),
            'cardNumber' => $card_number,
            'cvv' => trim((string) $this->input->post('cvv', true)),
            'city' => trim((string) $this->input->post('city', true)),
            'expMonth' => str_pad(trim((string) $this->input->post('expMonth', true)), 2, '0', STR_PAD_LEFT),
            'expYear' => trim((string) $this->input->post('expYear', true)),
            'nameOnCard' => trim((string) $this->input->post('nameOnCard', true)),
            'state' => strtoupper(trim((string) $this->input->post('state', true))),
            'zipCode' => trim((string) $this->input->post('zipCode', true))
        );
    }

    private function _validate_payment_form($data)
    {
        $errors = array();

        if ($data['nameOnCard'] === '') {
            $errors[] = 'Name on card is required.';
        }
        if ($data['address'] === '') {
            $errors[] = 'Address is required.';
        }
        if ($data['city'] === '') {
            $errors[] = 'City is required.';
        }
        if (!preg_match('/^[A-Z]{2}$/', $data['state'])) {
            $errors[] = 'State must be a 2-character US state or region code.';
        }
        if (!preg_match('/^\d{5}(?:-\d{4})?$/', $data['zipCode'])) {
            $errors[] = 'ZIP code must be 5 digits or ZIP+4 format.';
        }
        if (!preg_match('/^\d{13,19}$/', $data['cardNumber']) || !$this->_passes_luhn_check($data['cardNumber'])) {
            $errors[] = 'Please enter a valid card number.';
        }
        if (!preg_match('/^\d{3,4}$/', $data['cvv'])) {
            $errors[] = 'CVV must contain 3 or 4 digits.';
        }
        if (!preg_match('/^(0[1-9]|1[0-2])$/', $data['expMonth'])) {
            $errors[] = 'Expiration month must be between 01 and 12.';
        }
        if (!preg_match('/^\d{4}$/', $data['expYear'])) {
            $errors[] = 'Expiration year must use YYYY format.';
        }

        if (
            preg_match('/^(0[1-9]|1[0-2])$/', $data['expMonth'])
            && preg_match('/^\d{4}$/', $data['expYear'])
        ) {
            $expiry_value = ((int) $data['expYear'] * 100) + (int) $data['expMonth'];
            $current_value = ((int) date('Y') * 100) + (int) date('m');

            if ($expiry_value < $current_value) {
                $errors[] = 'The card expiration date has already passed.';
            }
        }

        return $errors;
    }

    private function _passes_luhn_check($card_number)
    {
        $sum = 0;
        $alternate = false;

        for ($index = strlen($card_number) - 1; $index >= 0; $index--) {
            $digit = (int) $card_number[$index];

            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
            $alternate = !$alternate;
        }

        return $sum > 0 && ($sum % 10) === 0;
    }

    private function _extract_payment_rows($decoded)
    {
        // UPDATED: Support both the supplied top-level data response and
        // Leadspedia's common response.data wrapper without changing page logic.
        $candidates = array();

        if (isset($decoded['data']) && is_array($decoded['data'])) {
            $candidates[] = $decoded['data'];
        }
        if (isset($decoded['response']['data']) && is_array($decoded['response']['data'])) {
            $candidates[] = $decoded['response']['data'];
        }
        if (isset($decoded['response']) && is_array($decoded['response'])) {
            $candidates[] = $decoded['response'];
        }

        foreach ($candidates as $rows) {
            if (empty($rows)) {
                return array();
            }

            if (isset($rows[0]) && is_array($rows[0])) {
                $payments = array();

                foreach ($rows as $row) {
                    $payments[] = array(
                        'cardNumber' => isset($row['cardNumber']) ? (string) $row['cardNumber'] : '',
                        'cardBrand' => isset($row['cardBrand']) ? (string) $row['cardBrand'] : '',
                        'isDefault' => isset($row['isDefault']) ? (string) $row['isDefault'] : '',
                        'status' => isset($row['status']) ? (string) $row['status'] : ''
                    );
                }

                return $payments;
            }
        }

        return array();
    }

    private function _set_payment_form_error($message, $card_data)
    {
        $this->session->set_flashdata('error', $message);
        $this->session->set_flashdata('open_payment_modal', true);
        $this->session->set_flashdata('payment_form_data', array(
            'address' => $card_data['address'],
            'city' => $card_data['city'],
            'expMonth' => $card_data['expMonth'],
            'expYear' => $card_data['expYear'],
            'nameOnCard' => $card_data['nameOnCard'],
            'state' => $card_data['state'],
            'zipCode' => $card_data['zipCode']
        ));
    }
}
