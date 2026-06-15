<?php defined('BASEPATH') or exit('No direct script access allowed');

class Verticals extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        auth_check(); // Existing authentication flow remains unchanged.

        // UPDATED: Load only the new Vertical model and existing settings model.
        $this->load->model('Vertical_model', 'vertical');
        $this->load->model('Setting_model', 'setting');
        $this->load->helper('curl');
    }

    public function index()
    {
        // UPDATED: New Leadspedia vertical listing page.
        $data['title'] = 'Verticals List';
        $data['verticals'] = $this->vertical->get_all();

        $this->load->view('includes/_header');
        $this->load->view('verticals/index', $data);
        $this->load->view('includes/_footer');
    }

    public function sync()
    {
        // UPDATED: AJAX-only sync. Existing records are never updated or deleted.
        if ($this->input->method(TRUE) !== 'POST') {
            return $this->_json(false, 'Invalid request method.');
        }

        $settings = $this->setting->get_general_settings();
        $account_manager_id = trim((string) ($settings['leadspedia_account_manager_id'] ?? ''));
        // UPDATED: Read Leadspedia API Key and API Secret separately.
        $api_key = trim((string) ($settings['leadspedia_api_key'] ?? ''));
        $api_secret = trim((string) ($settings['leadspedia_api_secret'] ?? ''));

        if ($account_manager_id === '' || $api_key === '' || $api_secret === '') {
            return $this->_json(false, 'Leadspedia Account Manager ID, API Key or API Secret is missing in General Settings.');
        }

        // UPDATED: Official Leadspedia endpoint: GET /verticals/getAll.do.
        // accountManagerID is read from the existing General Settings record.
        $api_response = leadspedia_curl_get(
            'verticals/getAll.do',
            array(),
            $api_key,
            $api_secret
        );

        if (!$api_response['success']) {
            $message = !empty($api_response['error'])
                ? $api_response['error']
                : 'Leadspedia API returned HTTP ' . (int) $api_response['http_code'] . '.';
            return $this->_json(false, $message);
        }

        $decoded = json_decode($api_response['body'], true);
        if (!is_array($decoded)) {
            return $this->_json(false, 'Invalid JSON response received from Leadspedia.');
        }

        if (
            empty($decoded['success']) ||
            !isset($decoded['response']['data']) ||
            !is_array($decoded['response']['data'])
        ) {
            $api_message = !empty($decoded['message'])
                ? $decoded['message']
                : 'No vertical records were returned by Leadspedia.';

            return $this->_json(false, $api_message);
        }

        $vertical_rows = $this->_extract_vertical_rows($decoded['response']['data']);
        if (empty($vertical_rows)) {
            return $this->_json(false, 'No valid vertical records were returned by Leadspedia.');
        }

        $result = $this->vertical->insert_new_only($vertical_rows);

        return $this->_json(
            true,
            $result['inserted'] > 0
            ? $result['inserted'] . ' new vertical(s) added successfully. Existing records were unchanged.'
            : 'Vertical sync completed. No new records were found; existing records were unchanged.',
            array('inserted' => $result['inserted'], 'total' => $result['total'])
        );
    }

    public function change_status()
    {
        // UPDATED: Change only the local active/inactive state of a saved vertical.
        // Leadspedia data and all existing vertical fields remain unchanged.
        if ($this->input->method(TRUE) !== 'POST') {
            return $this->_json(false, 'Invalid request method.');
        }

        $id = (int) $this->input->post('id');
        $is_active = ((int) $this->input->post('status') === 1) ? 1 : 0;

        if ($id <= 0 || !$this->vertical->exists($id)) {
            return $this->_json(false, 'Vertical record not found.');
        }

        if (!$this->vertical->change_status($id, $is_active)) {
            return $this->_json(false, 'Unable to change vertical status.');
        }

        return $this->_json(true, 'Vertical status changed successfully.');
    }


    public function update_price()
    {
        // UPDATED: Save only the inline-edited vertical price.
        // Existing vertical sync, status and listing logic remain unchanged.
        if ($this->input->method(TRUE) !== 'POST') {
            return $this->_json(false, 'Invalid request method.');
        }

        $id = (int) $this->input->post('id');
        $price = trim((string) $this->input->post('price'));

        if ($id <= 0 || !$this->vertical->exists($id)) {
            return $this->_json(false, 'Vertical record not found.');
        }

        if ($price === '' || !is_numeric($price) || (float) $price < 0) {
            return $this->_json(false, 'Please enter a valid price greater than or equal to 0.');
        }

        $price = number_format((float) $price, 2, '.', '');

        if (!$this->vertical->update_price($id, $price)) {
            return $this->_json(false, 'Unable to update vertical price.');
        }

        return $this->_json(true, 'Vertical price updated successfully.', array('price' => $price));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATED: Convert Leadspedia response rows into existing DB field format
    |--------------------------------------------------------------------------
    | $verticals contains only $decoded['response']['data'].
    */
    private function _extract_vertical_rows($response)
    {
        $candidates = array($response);
        foreach (array('data', 'result', 'verticals', 'records') as $key) {
            if (isset($response[$key]) && is_array($response[$key])) {
                $candidates[] = $response[$key];
            }
        }

        foreach ($candidates as $candidate) {
            if (isset($candidate['verticals']) && is_array($candidate['verticals'])) {
                $candidate = $candidate['verticals'];
            } elseif (isset($candidate['data']) && is_array($candidate['data']) && isset($candidate['data'][0])) {
                $candidate = $candidate['data'];
            }

            if (!isset($candidate[0]) || !is_array($candidate[0])) {
                continue;
            }

            $rows = array();
            foreach ($candidate as $item) {
                $vertical_id = $item['verticalID'] ?? $item['vertical_id'] ?? $item['id'] ?? '';
                $vertical_name = $item['verticalName'] ?? $item['vertical_name'] ?? $item['name'] ?? '';

                if ($vertical_id === '' || trim((string) $vertical_name) === '') {
                    continue;
                }

                $rows[] = array(
                    'vertical_id' => (string) $vertical_id,
                    'vertical_name' => trim((string) $vertical_name),
                    'status' => isset($item['status']) ? (string) $item['status'] : '',

                    // UPDATED: Save additional metadata returned by Leadspedia.
                    'group_id' => isset($item['groupID']) && $item['groupID'] !== ''
                        ? (int) $item['groupID']
                        : null,
                    'group_name' => isset($item['groupName'])
                        ? trim((string) $item['groupName'])
                        : '',
                    'leadspedia_created_on' => !empty($item['createdOn'])
                        ? (string) $item['createdOn']
                        : null,
                    'total_offers' => isset($item['totalOffers'])
                        ? (int) $item['totalOffers']
                        : 0,

                    'raw_data' => json_encode($item)
                );
            }

            if (!empty($rows)) {
                return $rows;
            }
        }

        return array();
    }

    private function _json($status, $message, $extra = array())
    {
        $payload = array_merge(array('status' => (bool) $status, 'message' => $message), $extra);
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
}
