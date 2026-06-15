<?php defined('BASEPATH') or exit('No direct script access allowed');

// UPDATED: Leadspedia Core API base path is maintained in one helper method.
// Pass only the endpoint path (example: advertisers/create.do) from controllers.
if (!function_exists('leadspedia_api_url')) {
    function leadspedia_api_url($endpoint = '')
    {
        $base_url = 'https://api.leadspedia.com/core/v2/';
        return $base_url . ltrim($endpoint, '/');
    }
}

if (!function_exists('leadspedia_curl_post')) {
    /**
     * Reusable Leadspedia POST helper.
     *
     * @param string $endpoint
     * @param array  $payload
     * @param string $api_key
     * @param string $api_secret
     * @return array
     */
    function leadspedia_curl_post($endpoint, $payload = array(), $api_key = '', $api_secret = '')
    {
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => leadspedia_api_url($endpoint),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($payload),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_USERPWD => $api_key . ':' . $api_secret,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => true,
        ));

        $body = curl_exec($ch);
        $error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return array(
            'success' => ($error === '' && $http_code >= 200 && $http_code < 300),
            'http_code' => $http_code,
            'body' => ($body !== false) ? $body : '',
            'error' => $error
        );
    }
}

// UPDATED: Reusable Leadspedia GET helper using separate API Key/API Secret values.
if (!function_exists('leadspedia_curl_get')) {
    function leadspedia_curl_get($endpoint, $query = array(), $api_key = '', $api_secret = '')
    {
        $headers = array('Accept: application/json');

        $url = leadspedia_api_url($endpoint);
        if (!empty($query) && is_array($query)) {
            $url .= '?' . http_build_query($query);
        }

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_USERPWD => $api_key . ':' . $api_secret,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => true,
        ));

        $body = curl_exec($ch);
        $error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return array(
            'success' => ($error === '' && $http_code >= 200 && $http_code < 300),
            'http_code' => $http_code,
            'body' => ($body !== false) ? $body : '',
            'error' => $error
        );
    }
}


// ============================================================================
// UPDATED: Advertiser credit-card API wrappers.
// Controllers pass only the logged-in advertiser ID and validated form data.
// Card details are sent directly to Leadspedia and are not stored locally.
// ============================================================================

if (!function_exists('leadspedia_get_advertiser_credit_cards')) {
    /**
     * Fetch saved payment cards for one Leadspedia advertiser.
     */
    function leadspedia_get_advertiser_credit_cards($advertiser_id, $api_key = '', $api_secret = '', $limit = 100, $start = 0)
    {
        return leadspedia_curl_get(
            'advertisersCreditCards/getAll.do',
            array(
                'limit' => max(1, (int) $limit),
                'start' => max(0, (int) $start),
                'advertiserID' => (int) $advertiser_id
            ),
            $api_key,
            $api_secret
        );
    }
}

if (!function_exists('leadspedia_add_advertiser_credit_card')) {
    /**
     * Add a payment card to one Leadspedia advertiser.
     * IMPORTANT: Do not log or persist the payload because it contains card/CVV data.
     */
    function leadspedia_add_advertiser_credit_card($advertiser_id, $card_data, $api_key = '', $api_secret = '')
    {
        $payload = array(
            // UPDATED: advertiserID associates the new card with the logged-in advertiser.
            'advertiserID' => (int) $advertiser_id,
            'defaultCard' => 'Yes',
            'address' => isset($card_data['address']) ? trim((string) $card_data['address']) : '',
            'country' => 'US',
            'cardNumber' => isset($card_data['cardNumber']) ? (string) $card_data['cardNumber'] : '',
            'city' => isset($card_data['city']) ? trim((string) $card_data['city']) : '',
            'cvv' => isset($card_data['cvv']) ? (string) $card_data['cvv'] : '',
            'expMonth' => isset($card_data['expMonth']) ? (string) $card_data['expMonth'] : '',
            'expYear' => isset($card_data['expYear']) ? (string) $card_data['expYear'] : '',
            'nameOnCard' => isset($card_data['nameOnCard']) ? trim((string) $card_data['nameOnCard']) : '',
            'state' => isset($card_data['state']) ? strtoupper(trim((string) $card_data['state'])) : '',
            'zipCode' => isset($card_data['zipCode']) ? trim((string) $card_data['zipCode']) : ''
        );

        return leadspedia_curl_post(
            'advertisersCreditCards/add.do',
            $payload,
            $api_key,
            $api_secret
        );
    }
}


// ============================================================================
// UPDATED: Real-time contract status, default payment, and activation wrappers.
// These methods use the existing cURL/authentication flow and do not save API
// status or payment information in the local database.
// ============================================================================

if (!function_exists('leadspedia_get_api_credentials')) {
    function leadspedia_get_api_credentials()
    {
        $CI =& get_instance();

        if (!isset($CI->auth_model)) {
            $CI->load->model('auth_model', 'auth_model');
        }

        $settings = $CI->auth_model->get_leadspedia_settings();
        $api_key = isset($settings['leadspedia_api_key']) ? trim((string) $settings['leadspedia_api_key']) : '';
        $api_secret = isset($settings['leadspedia_api_secret']) ? trim((string) $settings['leadspedia_api_secret']) : '';

        return array(
            'status' => ($api_key !== '' && $api_secret !== ''),
            'message' => ($api_key !== '' && $api_secret !== '')
                ? ''
                : 'Leadspedia API Key or API Secret is missing in Admin Settings.',
            'api_key' => $api_key,
            'api_secret' => $api_secret
        );
    }
}

if (!function_exists('leadspedia_get_contract_basic_info')) {
    function leadspedia_get_contract_basic_info($contract_id, $api_key = '', $api_secret = '')
    {
        return leadspedia_curl_get(
            'leadDistributionContracts/getBasicInfo.do',
            array('contractID' => (int) $contract_id),
            $api_key,
            $api_secret
        );
    }
}

if (!function_exists('leadspedia_get_default_advertiser_credit_card')) {
    function leadspedia_get_default_advertiser_credit_card($advertiser_id, $api_key = '', $api_secret = '')
    {
        return leadspedia_curl_get(
            'advertisersCreditCards/getDefault.do',
            array('advertiserID' => (int) $advertiser_id),
            $api_key,
            $api_secret
        );
    }
}

if (!function_exists('leadspedia_enable_contract_credit')) {
    function leadspedia_enable_contract_credit($contract_id, $api_key = '', $api_secret = '')
    {
        return leadspedia_curl_post(
            'leadDistributionContracts/enableCredit.do',
            array(
                'buyerLevel' => 'No',
                'contractID' => (int) $contract_id
            ),
            $api_key,
            $api_secret
        );
    }
}

if (!function_exists('leadspedia_response_data')) {
    function leadspedia_response_data($api_response)
    {
        $decoded = json_decode(isset($api_response['body']) ? (string) $api_response['body'] : '', true);
        if (!is_array($decoded)) {
            return array();
        }

        // UPDATED: Support the supplied top-level data structure and common
        // Leadspedia response.data/response wrappers.
        if (isset($decoded['data']) && is_array($decoded['data'])) {
            return $decoded['data'];
        }
        if (isset($decoded['response']['data']) && is_array($decoded['response']['data'])) {
            return $decoded['response']['data'];
        }
        if (isset($decoded['response']) && is_array($decoded['response'])) {
            return $decoded['response'];
        }

        return $decoded;
    }
}

if (!function_exists('leadspedia_find_value_in_mixed_data')) {
    function leadspedia_find_value_in_mixed_data($data, $keys)
    {
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            if (is_array($decoded)) {
                return leadspedia_find_value_in_mixed_data($decoded, $keys);
            }
            return null;
        }

        if (!is_array($data)) {
            return null;
        }

        $keys = array_map('strtolower', (array) $keys);

        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), $keys, true) && !is_array($value)) {
                return $value;
            }
        }

        foreach ($data as $value) {
            if (is_array($value) || is_string($value)) {
                $found = leadspedia_find_value_in_mixed_data($value, $keys);
                if ($found !== null && $found !== '') {
                    return $found;
                }
            }
        }

        return null;
    }
}

if (!function_exists('leadspedia_contract_runtime_details')) {
    function leadspedia_contract_runtime_details($contract_id, $api_key = '', $api_secret = '')
    {
        $contract_id = (int) $contract_id;
        if ($contract_id <= 0) {
            return array(
                'loaded' => false,
                'status' => 'Unavailable',
                'is_active' => false,
                'contract_name' => '',
                'error' => 'Leadspedia contract ID is not available.'
            );
        }

        $api_response = leadspedia_get_contract_basic_info($contract_id, $api_key, $api_secret);
        if (!leadspedia_api_succeeded($api_response)) {
            return array(
                'loaded' => false,
                'status' => 'Unavailable',
                'is_active' => false,
                'contract_name' => '',
                'error' => leadspedia_response_message($api_response)
            );
        }

        $response_data = leadspedia_response_data($api_response);
        $status = leadspedia_find_value_in_mixed_data($response_data, array('status', 'contractStatus'));
        $contract_name = leadspedia_find_value_in_mixed_data($response_data, array('contractName'));
        $status = trim((string) $status);

        if ($status === '') {
            return array(
                'loaded' => false,
                'status' => 'Unavailable',
                'is_active' => false,
                'contract_name' => trim((string) $contract_name),
                'error' => 'Contract status was not returned by Leadspedia.'
            );
        }

        return array(
            'loaded' => true,
            'status' => $status,
            'is_active' => (strtolower($status) === 'active'),
            'contract_name' => trim((string) $contract_name),
            'error' => ''
        );
    }
}

if (!function_exists('leadspedia_default_payment_details')) {
    function leadspedia_default_payment_details($advertiser_id, $api_key = '', $api_secret = '')
    {
        $advertiser_id = (int) $advertiser_id;
        if ($advertiser_id <= 0) {
            return array(
                'loaded' => false,
                'exists' => false,
                'error' => 'Leadspedia advertiser ID is not available.'
            );
        }

        $api_response = leadspedia_get_default_advertiser_credit_card($advertiser_id, $api_key, $api_secret);
        if (!leadspedia_api_succeeded($api_response)) {
            // UPDATED: A valid "no default card" response means the runtime
            // check completed and the Add Payment button should be displayed.
            $response_message = strtolower(leadspedia_response_message($api_response));
            $no_card_messages = array('not found', 'no credit card', 'no default', 'no data', 'no record', 'does not exist');
            foreach ($no_card_messages as $no_card_message) {
                if (strpos($response_message, $no_card_message) !== false) {
                    return array(
                        'loaded' => true,
                        'exists' => false,
                        'error' => ''
                    );
                }
            }

            return array(
                'loaded' => false,
                'exists' => false,
                'error' => leadspedia_response_message($api_response)
            );
        }

        $response_data = leadspedia_response_data($api_response);
        $credit_card_id = leadspedia_find_id_in_mixed_data($response_data, 'creditCardID');
        $card_number = leadspedia_find_value_in_mixed_data($response_data, array('cardNumber'));

        return array(
            'loaded' => true,
            'exists' => ($credit_card_id > 0 || trim((string) $card_number) !== ''),
            'error' => ''
        );
    }
}

// ============================================================================
// UPDATED: Common Leadspedia workflow functions.
// These functions can now be called from Admin or any other controller/page.
// Database reads/writes remain in their existing models to preserve CI MVC.
// ============================================================================

if (!function_exists('leadspedia_result')) {
    function leadspedia_result($status, $message, $steps = array())
    {
        return array(
            'status' => (bool) $status,
            'message' => $message,
            'steps' => array_values((array) $steps)
        );
    }
}

if (!function_exists('leadspedia_output_json')) {
    function leadspedia_output_json($controller, $result)
    {
        return $controller->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }
}

if (!function_exists('leadspedia_prepare_ci')) {
    function leadspedia_prepare_ci()
    {
        $CI =& get_instance();

        // UPDATED: Load required models only when another controller has not loaded them.
        if (!isset($CI->admin)) {
            $CI->load->model('admin_model', 'admin');
        }
        if (!isset($CI->auth_model)) {
            $CI->load->model('auth_model', 'auth_model');
        }

        return $CI;
    }
}

if (!function_exists('leadspedia_format_response')) {
    function leadspedia_format_response($api_response)
    {
        $result = array(
            'success' => leadspedia_api_succeeded($api_response),
            'http_code' => isset($api_response['http_code']) ? (int) $api_response['http_code'] : 0,
            'body' => isset($api_response['body']) ? $api_response['body'] : ''
        );

        if (!empty($api_response['error'])) {
            $result['curl_error'] = $api_response['error'];
        }

        return $result;
    }
}

if (!function_exists('leadspedia_api_succeeded')) {
    function leadspedia_api_succeeded($api_response)
    {
        if (empty($api_response['success'])) {
            return false;
        }

        $decoded = json_decode(isset($api_response['body']) ? $api_response['body'] : '', true);
        if (is_array($decoded) && array_key_exists('success', $decoded)) {
            return in_array($decoded['success'], array(true, 1, '1', 'true', 'True', 'TRUE'), true);
        }

        return true;
    }
}

if (!function_exists('leadspedia_extract_id')) {
    function leadspedia_extract_id($response_body, $id_key)
    {
        $decoded = json_decode((string) $response_body, true);
        return is_array($decoded) ? leadspedia_find_id_in_mixed_data($decoded, $id_key) : 0;
    }
}

if (!function_exists('leadspedia_find_id_in_mixed_data')) {
    function leadspedia_find_id_in_mixed_data($data, $id_key)
    {
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            if (is_array($decoded)) {
                return leadspedia_find_id_in_mixed_data($decoded, $id_key);
            }
            return 0;
        }

        if (!is_array($data)) {
            return 0;
        }

        foreach ($data as $key => $value) {
            if (strcasecmp((string) $key, (string) $id_key) === 0 && is_numeric($value)) {
                return (int) $value;
            }

            if (is_array($value) || is_string($value)) {
                $found_id = leadspedia_find_id_in_mixed_data($value, $id_key);
                if ($found_id > 0) {
                    return $found_id;
                }
            }
        }

        return 0;
    }
}

if (!function_exists('leadspedia_to_camel_case')) {
    function leadspedia_to_camel_case($value)
    {
        $value = preg_replace('/[^a-zA-Z0-9]+/', ' ', trim((string) $value));
        $words = preg_split('/\s+/', strtolower($value), -1, PREG_SPLIT_NO_EMPTY);
        return implode('', array_map('ucfirst', $words));
    }
}

if (!function_exists('leadspedia_split_name')) {
    function leadspedia_split_name($full_name)
    {
        $parts = preg_split('/\s+/', trim((string) $full_name), -1, PREG_SPLIT_NO_EMPTY);
        $first_name = !empty($parts) ? array_shift($parts) : '';
        $last_name = !empty($parts) ? implode(' ', $parts) : $first_name;

        return array(
            'first_name' => $first_name,
            'last_name' => $last_name
        );
    }
}

if (!function_exists('leadspedia_numeric_value')) {
    function leadspedia_numeric_value($value)
    {
        $numeric_value = preg_replace('/[^0-9.\-]/', '', (string) $value);
        return ($numeric_value === '' || !is_numeric($numeric_value)) ? 0 : (float) $numeric_value;
    }
}

if (!function_exists('leadspedia_decode_log')) {
    function leadspedia_decode_log($value)
    {
        $decoded = json_decode((string) $value, true);
        return is_array($decoded) ? $decoded : array();
    }
}

if (!function_exists('leadspedia_response_message')) {
    function leadspedia_response_message($api_response)
    {
        if (!empty($api_response['error'])) {
            return $api_response['error'];
        }

        $decoded = json_decode(isset($api_response['body']) ? $api_response['body'] : '', true);
        if (is_array($decoded) && !empty($decoded['message'])) {
            return (string) $decoded['message'];
        }

        return 'HTTP ' . (isset($api_response['http_code']) ? (int) $api_response['http_code'] : 0) . ' response received.';
    }
}

if (!function_exists('leadspedia_step')) {
    function leadspedia_step($label, $status, $message)
    {
        return array(
            'label' => $label,
            'status' => $status,
            'message' => $message
        );
    }
}

if (!function_exists('leadspedia_saved_step_succeeded')) {
    function leadspedia_saved_step_succeeded($response_log, $step_key)
    {
        return isset($response_log[$step_key]['success']) && $response_log[$step_key]['success'] === true;
    }
}

if (!function_exists('leadspedia_save_advertiser_progress')) {
    function leadspedia_save_advertiser_progress($advertiser_id, $request_log, $response_log, $http_code, $status, $extra_data = array())
    {
        $CI = leadspedia_prepare_ci();
        $data = array_merge(array(
            'leadspedia_request' => json_encode($request_log),
            'leadspedia_response' => json_encode($response_log),
            'leadspedia_http_code' => (int) $http_code,
            'leadspedia_status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ), $extra_data);

        return $CI->admin->update_advertiser_leadspedia($advertiser_id, $data);
    }
}

if (!function_exists('leadspedia_save_contract_progress')) {
    function leadspedia_save_contract_progress($advertiser_vertical_map_id, $request_log, $response_log, $http_code, $status, $extra_data = array())
    {
        $CI = leadspedia_prepare_ci();
        $data = array_merge(array(
            'leadspedia_request' => json_encode($request_log),
            'leadspedia_response' => json_encode($response_log),
            'leadspedia_http_code' => (int) $http_code,
            'leadspedia_status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ), $extra_data);

        return $CI->admin->update_vertical_contract_leadspedia($advertiser_vertical_map_id, $data);
    }
}

if (!function_exists('leadspedia_create_user_setup')) {
    /**
     * UPDATED: Common reusable advertiser setup workflow.
     * API order: advertisers/create.do -> advertisers/updateInfo.do -> advertisersContacts/create.do.
     */
    function leadspedia_create_user_setup($admin_id)
    {
        $CI = leadspedia_prepare_ci();
        $admin_id = (int) $admin_id;

        if ($admin_id <= 0) {
            return leadspedia_result(false, 'Invalid request.');
        }

        $advertiser = $CI->admin->get_leadspedia_user_data($admin_id);
        if (empty($advertiser)) {
            return leadspedia_result(false, 'Advertiser record was not found.');
        }

        $settings = $CI->auth_model->get_leadspedia_settings();
        $api_key = isset($settings['leadspedia_api_key']) ? trim((string) $settings['leadspedia_api_key']) : '';
        $api_secret = isset($settings['leadspedia_api_secret']) ? trim((string) $settings['leadspedia_api_secret']) : '';

        if ($api_key === '' || $api_secret === '') {
            return leadspedia_result(false, 'Leadspedia API Key or API Secret is missing in Admin Settings.');
        }

        $request_log = leadspedia_decode_log(isset($advertiser['leadspedia_request']) ? $advertiser['leadspedia_request'] : '');
        $response_log = leadspedia_decode_log(isset($advertiser['leadspedia_response']) ? $advertiser['leadspedia_response'] : '');
        $steps = array();
        $last_http_code = 0;
        $leadspedia_advertiser_id = isset($advertiser['advertiserID']) ? (int) $advertiser['advertiserID'] : 0;
        $leadspedia_contact_id = isset($advertiser['contactID']) ? (int) $advertiser['contactID'] : 0;

        // UPDATED: Recover IDs from earlier saved responses so retries resume safely.
        if ($leadspedia_advertiser_id <= 0) {
            $leadspedia_advertiser_id = leadspedia_find_id_in_mixed_data($response_log, 'advertiserID');
            if ($leadspedia_advertiser_id > 0) {
                $CI->admin->update_advertiser_leadspedia($advertiser['advertiser_id'], array(
                    'advertiserID' => $leadspedia_advertiser_id,
                    'leadspedia_status' => 'processing',
                    'updated_at' => date('Y-m-d H:i:s')
                ));
            }
        }

        if ($leadspedia_contact_id <= 0) {
            $leadspedia_contact_id = leadspedia_find_id_in_mixed_data($response_log, 'contactID');
            if ($leadspedia_contact_id > 0) {
                $CI->admin->update_advertiser_leadspedia($advertiser['advertiser_id'], array(
                    'contactID' => $leadspedia_contact_id,
                    'leadspedia_status' => 'processing',
                    'updated_at' => date('Y-m-d H:i:s')
                ));
            }
        }

        if (
            $leadspedia_advertiser_id > 0
            && $leadspedia_contact_id > 0
            && leadspedia_saved_step_succeeded($response_log, 'advertiser_update_info')
        ) {
            return leadspedia_result(true, 'Leadspedia user setup is already complete. Resuming vertical contract setup.', array());
        }

        $CI->admin->update_advertiser_leadspedia($advertiser['advertiser_id'], array(
            'leadspedia_status' => 'processing',
            'updated_at' => date('Y-m-d H:i:s')
        ));

        if ($leadspedia_advertiser_id <= 0) {
            $create_payload = array(
                'accountManagerID' => (int) (isset($settings['leadspedia_account_manager_id']) ? $settings['leadspedia_account_manager_id'] : 0),
                'advertiserName' => leadspedia_to_camel_case($advertiser['company']),
                'status' => 'Active'
            );

            $request_log['advertiser_create'] = array(
                'endpoint' => 'advertisers/create.do',
                'payload' => $create_payload
            );

            $create_response = leadspedia_curl_post('advertisers/create.do', $create_payload, $api_key, $api_secret);
            $response_log['advertiser_create'] = leadspedia_format_response($create_response);
            $last_http_code = (int) $create_response['http_code'];
            $leadspedia_advertiser_id = leadspedia_extract_id($create_response['body'], 'advertiserID');

            if (!leadspedia_api_succeeded($create_response) || $leadspedia_advertiser_id <= 0) {
                $steps[] = leadspedia_step('Create advertiser', 'error', leadspedia_response_message($create_response));
                leadspedia_save_advertiser_progress($advertiser['advertiser_id'], $request_log, $response_log, $last_http_code, 'failed');
                return leadspedia_result(false, 'Leadspedia advertiser creation failed.', $steps);
            }

            $steps[] = leadspedia_step('Create advertiser', 'success', 'Advertiser created successfully.');
            leadspedia_save_advertiser_progress(
                $advertiser['advertiser_id'],
                $request_log,
                $response_log,
                $last_http_code,
                'processing',
                array('advertiserID' => $leadspedia_advertiser_id)
            );
        } else {
            $steps[] = leadspedia_step('Create advertiser', 'skipped', 'Existing advertiserID ' . $leadspedia_advertiser_id . ' will be used.');
        }

        if (!leadspedia_saved_step_succeeded($response_log, 'advertiser_update_info')) {
            $update_payload = array(
                'advertiserName' => trim((string) $advertiser['name']),
                'externalCRMID' => (string) $advertiser['display_id'],
                'source' => 'Contractor Portal',
                'advertiserID' => $leadspedia_advertiser_id
            );

            $request_log['advertiser_update_info'] = array(
                'endpoint' => 'advertisers/updateInfo.do',
                'payload' => $update_payload
            );

            $update_response = leadspedia_curl_post('advertisers/updateInfo.do', $update_payload, $api_key, $api_secret);
            $response_log['advertiser_update_info'] = leadspedia_format_response($update_response);
            $last_http_code = (int) $update_response['http_code'];

            if (!leadspedia_api_succeeded($update_response)) {
                $steps[] = leadspedia_step('Update advertiser information', 'error', leadspedia_response_message($update_response));
                leadspedia_save_advertiser_progress(
                    $advertiser['advertiser_id'],
                    $request_log,
                    $response_log,
                    $last_http_code,
                    'failed',
                    array('advertiserID' => $leadspedia_advertiser_id)
                );
                return leadspedia_result(false, 'Leadspedia advertiser information update failed.', $steps);
            }

            $steps[] = leadspedia_step('Update advertiser information', 'success', 'Advertiser information updated successfully.');
        } else {
            $steps[] = leadspedia_step('Update advertiser information', 'skipped', 'Advertiser information was already updated.');
        }

        if ($leadspedia_contact_id <= 0) {
            $name_parts = leadspedia_split_name($advertiser['name']);
            $contact_payload = array(
                'phoneNumber' => trim((string) $advertiser['mobile_no']),
                'advertiserID' => $leadspedia_advertiser_id,
                'emailAddress' => trim((string) $advertiser['email']),
                'firstName' => $name_parts['first_name'],
                'lastName' => $name_parts['last_name'],
                'password' => leadspedia_to_camel_case($advertiser['name']) . '_' . trim((string) $advertiser['mobile_no'])
            );

            $request_log['advertiser_contact_create'] = array(
                'endpoint' => 'advertisersContacts/create.do',
                'payload' => $contact_payload
            );

            $contact_response = leadspedia_curl_post('advertisersContacts/create.do', $contact_payload, $api_key, $api_secret);
            $response_log['advertiser_contact_create'] = leadspedia_format_response($contact_response);
            $last_http_code = (int) $contact_response['http_code'];
            $leadspedia_contact_id = leadspedia_extract_id($contact_response['body'], 'contactID');

            if (!leadspedia_api_succeeded($contact_response) || $leadspedia_contact_id <= 0) {
                $steps[] = leadspedia_step('Create advertiser contact', 'error', leadspedia_response_message($contact_response));
                leadspedia_save_advertiser_progress(
                    $advertiser['advertiser_id'],
                    $request_log,
                    $response_log,
                    $last_http_code,
                    'failed',
                    array('advertiserID' => $leadspedia_advertiser_id)
                );
                return leadspedia_result(false, 'Leadspedia advertiser contact creation failed.', $steps);
            }

            $steps[] = leadspedia_step('Create advertiser contact', 'success', 'Advertiser contact created successfully.');
        } else {
            $steps[] = leadspedia_step('Create advertiser contact', 'skipped', 'Existing contactID ' . $leadspedia_contact_id . ' will be used.');
        }

        leadspedia_save_advertiser_progress(
            $advertiser['advertiser_id'],
            $request_log,
            $response_log,
            $last_http_code,
            'user_success',
            array(
                'advertiserID' => $leadspedia_advertiser_id,
                'contactID' => $leadspedia_contact_id
            )
        );

        return leadspedia_result(true, 'Leadspedia user setup completed successfully.', $steps);
    }
}

if (!function_exists('leadspedia_create_contract_setup')) {
    /**
     * UPDATED: Common reusable vertical contract, filter, and schedule workflow.
     * Successful steps are reused and retry resumes from the first failed step.
     */
    function leadspedia_create_contract_setup($admin_id)
    {
        $CI = leadspedia_prepare_ci();
        $admin_id = (int) $admin_id;

        if ($admin_id <= 0) {
            return leadspedia_result(false, 'Invalid request.');
        }

        $contract = $CI->admin->get_leadspedia_contract_data($admin_id);
        if (empty($contract)) {
            return leadspedia_result(false, 'Assigned vertical contract was not found.');
        }

        if (empty($contract['advertiserID'])) {
            return leadspedia_result(false, 'Create the Leadspedia advertiser before creating the contract.');
        }

        $settings = $CI->auth_model->get_leadspedia_settings();
        $api_key = isset($settings['leadspedia_api_key']) ? trim((string) $settings['leadspedia_api_key']) : '';
        $api_secret = isset($settings['leadspedia_api_secret']) ? trim((string) $settings['leadspedia_api_secret']) : '';

        if ($api_key === '' || $api_secret === '') {
            return leadspedia_result(false, 'Leadspedia API Key or API Secret is missing in Admin Settings.');
        }

        $request_log = leadspedia_decode_log(isset($contract['leadspedia_request']) ? $contract['leadspedia_request'] : '');
        $response_log = leadspedia_decode_log(isset($contract['leadspedia_response']) ? $contract['leadspedia_response'] : '');
        $steps = array();
        $last_http_code = 0;
        $contract_id = isset($contract['contractID']) ? (int) $contract['contractID'] : 0;

        if ($contract_id <= 0) {
            $contract_id = leadspedia_find_id_in_mixed_data($response_log, 'contractID');
            if ($contract_id > 0) {
                $CI->admin->update_vertical_contract_leadspedia($contract['advertiser_vertical_map_id'], array(
                    'contractID' => $contract_id,
                    'leadspedia_status' => 'processing',
                    'updated_at' => date('Y-m-d H:i:s')
                ));
            }
        }

        $CI->admin->update_vertical_contract_leadspedia($contract['advertiser_vertical_map_id'], array(
            'leadspedia_status' => 'processing',
            'updated_at' => date('Y-m-d H:i:s')
        ));

        if (empty($contract['leadspedia_vertical_id'])) {
            $steps[] = leadspedia_step('Create vertical contract', 'error', 'Leadspedia Vertical ID is missing for the assigned vertical.');
            leadspedia_save_contract_progress($contract['advertiser_vertical_map_id'], $request_log, $response_log, 0, 'failed');
            return leadspedia_result(false, 'Leadspedia vertical contract creation failed.', $steps);
        }

        if ($contract_id <= 0) {
            $contract_payload = array(
                'advertiserID' => (int) $contract['advertiserID'],
                'verticalID' => (int) $contract['leadspedia_vertical_id'],
                'contractName' => trim((string) $contract['display_id']) . '_' . trim((string) $contract['vertical_name']),
                'defaultPrice' => 1,
                'revenueModel' => 'Fixed'
            );

            $request_log['contract_create'] = array(
                'endpoint' => 'leadDistributionContracts/create.do',
                'payload' => $contract_payload
            );

            $contract_response = leadspedia_curl_post('leadDistributionContracts/create.do', $contract_payload, $api_key, $api_secret);
            $response_log['contract_create'] = leadspedia_format_response($contract_response);
            $last_http_code = (int) $contract_response['http_code'];
            $contract_id = leadspedia_extract_id($contract_response['body'], 'contractID');

            if (!leadspedia_api_succeeded($contract_response) || $contract_id <= 0) {
                $steps[] = leadspedia_step('Create vertical contract', 'error', leadspedia_response_message($contract_response));
                leadspedia_save_contract_progress($contract['advertiser_vertical_map_id'], $request_log, $response_log, $last_http_code, 'failed');
                return leadspedia_result(false, 'Leadspedia vertical contract creation failed.', $steps);
            }

            $steps[] = leadspedia_step('Create vertical contract', 'success', 'Vertical contract created successfully.');
            leadspedia_save_contract_progress(
                $contract['advertiser_vertical_map_id'],
                $request_log,
                $response_log,
                $last_http_code,
                'processing',
                array('contractID' => $contract_id)
            );
        } else {
            $steps[] = leadspedia_step('Create vertical contract', 'skipped', 'Existing contractID ' . $contract_id . ' will be used.');
        }

        $zip_codes = trim((string) $contract['zip_codes']);
        if ($zip_codes !== '' && !leadspedia_saved_step_succeeded($response_log, 'zip_filter')) {
            $zip_payload = array(
                'date' => date('Y-m-d'),
                'value' => $zip_codes,
                'contractID' => $contract_id,
                'fieldID' => 1369,
                'operator' => 'Equals'
            );

            $request_log['zip_filter'] = array(
                'endpoint' => 'leadDistributionContracts/addFilter.do',
                'payload' => $zip_payload
            );

            $zip_response = leadspedia_curl_post('leadDistributionContracts/addFilter.do', $zip_payload, $api_key, $api_secret);
            $response_log['zip_filter'] = leadspedia_format_response($zip_response);
            $last_http_code = (int) $zip_response['http_code'];

            if (!leadspedia_api_succeeded($zip_response)) {
                $steps[] = leadspedia_step('Add ZIP code filter', 'error', leadspedia_response_message($zip_response));
                leadspedia_save_contract_progress(
                    $contract['advertiser_vertical_map_id'],
                    $request_log,
                    $response_log,
                    $last_http_code,
                    'failed',
                    array('contractID' => $contract_id)
                );
                return leadspedia_result(false, 'Leadspedia ZIP code filter creation failed.', $steps);
            }

            $steps[] = leadspedia_step('Add ZIP code filter', 'success', 'ZIP code filter added successfully.');
        } elseif ($zip_codes === '') {
            $steps[] = leadspedia_step('Add ZIP code filter', 'skipped', 'No ZIP code filter was provided.');
        } else {
            $steps[] = leadspedia_step('Add ZIP code filter', 'skipped', 'ZIP code filter was already added.');
        }

        $state_abbreviations = trim((string) $contract['state_abbreviations']);
        if ($state_abbreviations !== '' && !leadspedia_saved_step_succeeded($response_log, 'state_filter')) {
            $state_payload = array(
                'date' => date('Y-m-d'),
                'value' => $state_abbreviations,
                'contractID' => $contract_id,
                'fieldID' => 1368,
                'operator' => 'Equals'
            );

            $request_log['state_filter'] = array(
                'endpoint' => 'leadDistributionContracts/addFilter.do',
                'payload' => $state_payload
            );

            $state_response = leadspedia_curl_post('leadDistributionContracts/addFilter.do', $state_payload, $api_key, $api_secret);
            $response_log['state_filter'] = leadspedia_format_response($state_response);
            $last_http_code = (int) $state_response['http_code'];

            if (!leadspedia_api_succeeded($state_response)) {
                $steps[] = leadspedia_step('Add state filter', 'error', leadspedia_response_message($state_response));
                leadspedia_save_contract_progress(
                    $contract['advertiser_vertical_map_id'],
                    $request_log,
                    $response_log,
                    $last_http_code,
                    'failed',
                    array('contractID' => $contract_id)
                );
                return leadspedia_result(false, 'Leadspedia state filter creation failed.', $steps);
            }

            $steps[] = leadspedia_step('Add state filter', 'success', 'State filter added successfully.');
        } elseif ($state_abbreviations === '') {
            $steps[] = leadspedia_step('Add state filter', 'skipped', 'No state filter was provided.');
        } else {
            $steps[] = leadspedia_step('Add state filter', 'skipped', 'State filter was already added.');
        }

        $delivery_days = array_filter(array_map('trim', explode(',', strtolower((string) $contract['delivery_days']))));
        $schedule_payload = array(
            'Friday' => in_array('friday', $delivery_days, true) ? 'Yes' : 'No',
            'Monday' => in_array('monday', $delivery_days, true) ? 'Yes' : 'No',
            'Saturday' => in_array('saturday', $delivery_days, true) ? 'Yes' : 'No',
            'Sunday' => in_array('sunday', $delivery_days, true) ? 'Yes' : 'No',
            'Thursday' => in_array('thursday', $delivery_days, true) ? 'Yes' : 'No',
            'Tuesday' => in_array('tuesday', $delivery_days, true) ? 'Yes' : 'No',
            'Wednesday' => in_array('wednesday', $delivery_days, true) ? 'Yes' : 'No',
            'cap' => (int) $contract['leads_per_week'],
            'contractID' => $contract_id,
            'endTime' => $contract['end_time'],
            'startTime' => $contract['start_time'],
            'price' => (float) $contract['price'],
            'revenueCap' => leadspedia_numeric_value($contract['monthly_budget']),
            'type' => 'Exclusive'
        );

        if (!leadspedia_saved_step_succeeded($response_log, 'contract_schedule')) {
            $request_log['contract_schedule'] = array(
                'endpoint' => 'leadDistributionContracts/createSchedule.do',
                'payload' => $schedule_payload
            );

            $schedule_response = leadspedia_curl_post('leadDistributionContracts/createSchedule.do', $schedule_payload, $api_key, $api_secret);
            $response_log['contract_schedule'] = leadspedia_format_response($schedule_response);
            $last_http_code = (int) $schedule_response['http_code'];

            if (!leadspedia_api_succeeded($schedule_response)) {
                $steps[] = leadspedia_step('Create delivery schedule', 'error', leadspedia_response_message($schedule_response));
                leadspedia_save_contract_progress(
                    $contract['advertiser_vertical_map_id'],
                    $request_log,
                    $response_log,
                    $last_http_code,
                    'failed',
                    array('contractID' => $contract_id)
                );
                return leadspedia_result(false, 'Leadspedia delivery schedule creation failed.', $steps);
            }

            $steps[] = leadspedia_step('Create delivery schedule', 'success', 'Delivery schedule created successfully.');
        } else {
            $steps[] = leadspedia_step('Create delivery schedule', 'skipped', 'Delivery schedule was already created.');
        }

        leadspedia_save_contract_progress(
            $contract['advertiser_vertical_map_id'],
            $request_log,
            $response_log,
            $last_http_code,
            'success',
            array('contractID' => $contract_id)
        );

        $CI->admin->update_advertiser_leadspedia($contract['advertiser_id'], array(
            'leadspedia_status' => 'success',
            'updated_at' => date('Y-m-d H:i:s')
        ));

        return leadspedia_result(true, 'Leadspedia contract setup completed successfully.', $steps);
    }
}
