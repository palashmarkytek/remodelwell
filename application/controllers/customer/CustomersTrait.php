<?php defined('BASEPATH') or exit('No direct script access allowed');

trait CustomersTrait
{
    public function filterdata()
    {
        $this->session->set_userdata('filter_status', $this->input->post('status'));
        $this->session->set_userdata('filter_keyword', $this->input->post('keyword'));
    }

    public function list_data()
    {
        $data['info'] = $this->customer->get_all();
        $this->load->view('customer/list', $data);
    }

    public function change_status()
    {
        $this->rbac->check_operation_access();
        $this->customer->change_status();
    }

    public function add()
    {
        $this->rbac->check_operation_access();

        $data['title'] = 'Add Buyes';
        $data['insert']['name'] = post('name');
        $data['insert']['company_name'] = post('company_name');
        $data['insert']['email'] = post('email');
        $data['insert']['phone'] = post('phone');
        $data['insert']['address'] = post('address');
        $data['insert']['api_key'] = post('api_key');

        if ($this->input->post('submit')) {
            $this->form_validation->set_rules('name', 'Name', 'trim|required');
            $this->form_validation->set_rules('api_key', 'API Key', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');

            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata('message_page', 'buyer_list');
                $this->session->set_flashdata('errors', validation_errors());
                display('customer/add', $data);
            } else {
                $data['insert']['display_id'] = generateEmployeeId();
                $data['insert']['is_active'] = 1;
                $data['insert']['created_at'] = today_date();
                $data['insert']['updated_at'] = today_date();

                $data_insert = $this->security->xss_clean($data['insert']);
                $result = $this->customer->add_buyer($data_insert);

                if ($result) {
                    $this->activity_model->add_log(4);
                    $this->session->set_flashdata('message_page', 'buyer_list');
                    $this->session->set_flashdata('success', 'Buyes has been added successfully!');
                    redirect(base_url('customer'));
                }
            }
        } else {
            display('customer/add', $data);
        }
    }

    public function edit($id = "")
    {
        $update = (!$id) ? redirect('customer') : $this->customer->get_buyer_by_id($id);

        $this->rbac->check_operation_access();

        $data['title'] = 'Update Buyes';
        $data['id'] = $id;
        $data['update']['name'] = post('name', $update);
        $data['update']['company_name'] = post('company_name', $update);
        $data['update']['email'] = post('email', $update);
        $data['update']['phone'] = post('phone', $update);
        $data['update']['address'] = post('address', $update);
        $data['update']['api_key'] = post('api_key', $update);
        $data['display_id'] = isset($update['display_id']) ? $update['display_id'] : '';

        if ($this->input->post('submit')) {
            $this->form_validation->set_rules('name', 'Name', 'trim|required');
            $this->form_validation->set_rules('api_key', 'API Key', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');

            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata('message_page', 'buyer_list');
                $this->session->set_flashdata('errors', validation_errors());
                display('customer/edit', $data);
            } else {
                $data['update']['updated_at'] = today_date();

                $update_data = $this->security->xss_clean($data['update']);
                $result = $this->customer->edit_buyer($update_data, $id);

                if ($result) {
                    $this->activity_model->add_log(5);
                    $this->session->set_flashdata('message_page', 'buyer_list');
                    $this->session->set_flashdata('success', 'Buyes has been updated successfully!');
                    redirect(base_url('customer'));
                }
            }
        } else {
            display('customer/edit', $data);
        }
    }

    public function offers($buyer_id = '')
    {
        $buyer = (!$buyer_id) ? redirect('customer') : $this->customer->get_buyer_by_id($buyer_id);

        $data['title'] = 'Offer Id List';
        $data['buyer'] = $buyer;
        $data['offers'] = $this->customer->get_buyer_offer_list($buyer_id);

        display('customer/offers', $data);
    }

    public function sync_offers($buyer_id = '')
    {
        $this->rbac->check_operation_access();

        $buyer = (!$buyer_id) ? redirect('customer') : $this->customer->get_buyer_by_id($buyer_id);

        if (empty($buyer['api_key'])) {
            $this->session->set_flashdata('message_page', 'buyer_offer_list');
            $this->session->set_flashdata('error', 'Buyer API Key not found.');
            redirect(base_url('customer/offers/' . $buyer_id));
        }

        $result = $this->customer->sync_buyer_offers($buyer_id, $buyer['api_key']);

        $this->session->set_flashdata('message_page', 'buyer_offer_list');

        if ($result['status']) {
            $this->session->set_flashdata('success', $result['message']);
        } else {
            $this->session->set_flashdata('error', $result['message']);
        }

        redirect(base_url('customer/offers/' . $buyer_id));
    }

    public function delete_offer($mapping_id = '', $buyer_id = '')
    {
        $this->rbac->check_operation_access();

        if (!$mapping_id || !$buyer_id) {
            redirect(base_url('customer'));
        }

        $this->customer->delete_buyer_offer($mapping_id, $buyer_id);

        $this->session->set_flashdata('message_page', 'buyer_offer_list');
        $this->session->set_flashdata('success', 'Offer has been deleted successfully.');
        redirect(base_url('customer/offers/' . $buyer_id));
    }

    public function delete($id = '')
    {
        $this->rbac->check_operation_access();

        $this->customer->delete($id);

        $this->activity_model->add_log(6);

        $this->session->set_flashdata('message_page', 'buyer_list');
        $this->session->set_flashdata('success', 'Buyes has been Deleted Successfully.');
        redirect('customer');
    }

    public function offers_px()
    {
        $data['title'] = 'PX Stats List';
        // List synced PX publisher stats from local database table.
        $data['offers'] = $this->customer->get_buyer_px_offer_list();

        display('customer/offers_px', $data);
    }
/* application/controllers/customer/CustomersTrait.php */
/* Replace only get_px_publisher_stats_report() method */

public function get_px_publisher_stats_report()
{
    /*
     * PX Publisher Stats Sync
     * -------------------------------------------------------------
     * Existing structure and logic are not removed.
     *
     * This method follows the working PX browser curl:
     * - GET request
     * - Bearer token from modal
     * - PX cookie from modal
     * - ReportType = daily
     * - Grouping = affiliateSubID_1
     * - FromPeriod = selected From Date from sync modal
     * - ToPeriod = selected To Date from sync modal
     * - Insert new rows and update duplicate rows in ci_px_publisher_stats
     */

    $bearer_token = trim($this->input->post('bearer_token'));
    $cookie       = trim($this->input->post('px_cookie'));

    /*
     * Date range selected from syncOffersModal.
     * Default current date is already set on the view, but server-side fallback is also added.
     */
    $from_period = trim($this->input->post('from_period'));
    $to_period   = trim($this->input->post('to_period'));

    /*
     * Clean Bearer token if user pasted:
     * Authorization: Bearer TOKEN
     * or Bearer TOKEN
     */
    $bearer_token = str_replace(array('authorization:', 'Authorization:'), '', $bearer_token);
    $bearer_token = trim($bearer_token);
    $bearer_token = preg_replace('/^Bearer\s+/i', '', $bearer_token);
    $bearer_token = trim($bearer_token, " \t\n\r\0\x0B\"'");

    /*
     * Clean PX cookie if user pasted from Windows CMD curl.
     * Windows curl may contain ^ before special characters.
     */
    $cookie = str_replace('-b ', '', $cookie);
    $cookie = str_replace(array('^"', '"', "'"), '', $cookie);
    $cookie = str_replace('^|', '|', $cookie);
    $cookie = str_replace('^', '', $cookie);
    $cookie = trim($cookie);

    if (empty($bearer_token)) {
        $this->session->set_flashdata('error', 'PX sync failed. Bearer token is required.');
        redirect(base_url('customer/offers_px'));
        return;
    }

    if (empty($cookie)) {
        $this->session->set_flashdata('error', 'PX sync failed. PX cookie is required.');
        redirect(base_url('customer/offers_px'));
        return;
    }

    /*
     * Server-side date validation.
     * This keeps future dates disabled on backend also, even if user changes browser HTML manually.
     */
    $current_date = date('Y-m-d');

    if (empty($from_period)) {
        $from_period = $current_date;
    }

    if (empty($to_period)) {
        $to_period = $current_date;
    }

    if (
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $from_period) ||
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to_period)
    ) {
        $this->session->set_flashdata('error', 'PX sync failed. Invalid date format.');
        redirect(base_url('customer/offers_px'));
        return;
    }

    if (strtotime($from_period) > strtotime($current_date) || strtotime($to_period) > strtotime($current_date)) {
        $this->session->set_flashdata('error', 'PX sync failed. Future date is not allowed.');
        redirect(base_url('customer/offers_px'));
        return;
    }

    if (strtotime($from_period) > strtotime($to_period)) {
        $this->session->set_flashdata('error', 'PX sync failed. From Date cannot be greater than To Date.');
        redirect(base_url('customer/offers_px'));
        return;
    }

    $base_url = 'https://open.px.com/api/publisherstats/report';

    $sorting = array(
        'dateTime' => 'desc'
    );

    /*
     * Selected date range will be passed to PX API filter.
     * These values are used in FromPeriod and ToPeriod of the curl call.
     */
    $filter = array(
    'FromPeriod' => date('Y-m-d', strtotime($from_period . ' -1 day')),
    'ToPeriod'   => date('Y-m-d', strtotime($to_period . ' +1 day'))
);

    /*
     * Build URL manually.
     * PX accepts this format:
     * sorting=%7B%22dateTime%22:%22desc%22%7D
     * filter=%7B%22FromPeriod%22:%222026-05-01%22,%22ToPeriod%22:%222026-05-14%22%7D
     */
    $sorting_json = rawurlencode(json_encode($sorting));
    $filter_json  = rawurlencode(json_encode($filter));

    /*
     * Keep ":" and "," unencoded to match browser curl.
     */
    $sorting_json = str_replace(array('%3A', '%2C'), array(':', ','), $sorting_json);
    $filter_json  = str_replace(array('%3A', '%2C'), array(':', ','), $filter_json);

    $url = $base_url
        . '?count=50'
        . '&page=1'
        . '&sorting=' . $sorting_json
        . '&filter=' . $filter_json
        . '&ReportType=daily'
        . '&Grouping=affiliateSubID_1';

    /*
     * Generate browser-like request IDs.
     * These headers do not need to be exactly same every time.
     */
    $trace_id = bin2hex(random_bytes(16));
    $span_id  = bin2hex(random_bytes(8));

    $headers = array(
        'accept: application/json, text/plain, */*',
        'accept-language: en-IN,en;q=0.9,en-GB;q=0.8,en-US;q=0.7,bn;q=0.6,da;q=0.5,zh-CN;q=0.4,zh;q=0.3',
        'authorization: Bearer ' . $bearer_token,
        'priority: u=1, i',
        'referer: https://open.px.com/',
        'request-context: appId=cid-v1:57fe3f1f-5acb-4f53-9daa-d185f60e0b94',
        'request-id: |' . $trace_id . '.' . $span_id,
        'sec-ch-ua: "Chromium";v="148", "Google Chrome";v="148", "Not/A)Brand";v="99"',
        'sec-ch-ua-mobile: ?0',
        'sec-ch-ua-platform: "Windows"',
        'sec-fetch-dest: empty',
        'sec-fetch-mode: cors',
        'sec-fetch-site: same-origin',
        'traceparent: 00-' . $trace_id . '-' . $span_id . '-01',
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36'
    );

    $ch = curl_init();

    curl_setopt_array($ch, array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTPHEADER     => $headers,

        /*
         * Matches curl -b "cookie..."
         */
        CURLOPT_COOKIE => $cookie,

        /*
         * Allow compressed response if PX returns gzip/br.
         */
        CURLOPT_ENCODING => ''
    ));

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);

        $this->session->set_flashdata('error', 'PX sync failed. Curl Error: ' . $error);
        redirect(base_url('customer/offers_px'));
        return;
    }

    curl_close($ch);

    if ((int) $http_code === 400) {
        $this->session->set_flashdata(
            'error',
            'PX sync failed. HTTP Code: 400. Bad request. URL: ' . $url
        );
        redirect(base_url('customer/offers_px'));
        return;
    }

    if ((int) $http_code === 401) {
        $this->session->set_flashdata(
            'error',
            'PX sync failed. HTTP Code: 401. Invalid/expired Bearer token or PX cookie.'
        );
        redirect(base_url('customer/offers_px'));
        return;
    }

    if ($http_code < 200 || $http_code >= 300) {
        $this->session->set_flashdata(
            'error',
            'PX sync failed. HTTP Code: ' . $http_code
        );
        redirect(base_url('customer/offers_px'));
        return;
    }

    $api_response = json_decode($response, true);

    if (
        empty($api_response) ||
        !isset($api_response['data']) ||
        !is_array($api_response['data'])
    ) {
        $this->session->set_flashdata('error', 'PX sync failed. Invalid API response.');
        redirect(base_url('customer/offers_px'));
        return;
    }

    $inserted_count = 0;
    $updated_count  = 0;
    $skipped_count  = 0;

    foreach ($api_response['data'] as $row) {

        /*
         * Skip Total row because it is summary row.
         */
        if (
            !isset($row['dateTime']) ||
            strtolower(trim($row['dateTime'])) === 'total'
        ) {
            $skipped_count++;
            continue;
        }

        $report_date      = trim($row['dateTime']);
        $affiliate_id     = isset($row['affiliateID']) ? trim($row['affiliateID']) : '';
        $offer_id         = isset($row['offerID']) ? trim($row['offerID']) : '';
        $affiliateSubID_1 = isset($row['affiliateSubID_1']) ? trim($row['affiliateSubID_1']) : '';

        /*
         * Required fields validation.
         */
        if (empty($report_date) || empty($affiliate_id) || empty($offer_id)) {
            $skipped_count++;
            continue;
        }

        /*
         * Prevent duplicate insert:
         * Same report_date + affiliate_id + offer_id + affiliateSubID_1
         * will not insert again.
         */
        $exists = $this->customer->check_px_publisher_stat_exists(
            $report_date,
            $affiliate_id,
            $offer_id,
            $affiliateSubID_1
        );

        /*
         * If duplicate row exists, update existing PX stats record.
         * Duplicate condition:
         * report_date + affiliate_id + offer_id + affiliateSubID_1
         */
        if ($exists) {

            $update_data = array(
                'clicks'      => isset($row['clicks']) ? (int) $row['clicks'] : 0,
                'impressions' => isset($row['impressions']) ? (int) $row['impressions'] : 0,
                'conversions' => isset($row['conversions']) ? (int) $row['conversions'] : 0,
                'cost'        => isset($row['cost']) ? (float) $row['cost'] : 0,
                'updated_at'  => date('Y-m-d H:i:s')
            );

            /*
             * Update existing PX publisher stat row instead of skipping duplicate.
             */
            $updated = $this->customer->update_px_publisher_stat(
                $report_date,
                $affiliate_id,
                $offer_id,
                $affiliateSubID_1,
                $update_data
            );

            if ($updated) {
                $updated_count++;
            } else {
                $skipped_count++;
            }

            continue;
        }

        /*
         * Save only required PX fields.
         */
        $insert_data = array(
            'report_date'      => $report_date,
            'affiliate_id'     => $affiliate_id,
            'offer_id'         => $offer_id,
            'clicks'           => isset($row['clicks']) ? (int) $row['clicks'] : 0,
            'impressions'      => isset($row['impressions']) ? (int) $row['impressions'] : 0,
            'conversions'      => isset($row['conversions']) ? (int) $row['conversions'] : 0,
            'affiliateSubID_1' => $affiliateSubID_1,
            'cost'             => isset($row['cost']) ? (float) $row['cost'] : 0,
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s')
        );

        $insert_id = $this->customer->insert_px_publisher_stat($insert_data);

        if ($insert_id) {
            $inserted_count++;
        } else {
            $skipped_count++;
        }
    }

    $this->session->set_flashdata(
        'success',
        'PX sync completed. ' .
        $inserted_count . ' new row(s) inserted, ' .
        $updated_count . ' existing row(s) updated, ' .
        $skipped_count . ' invalid/summary row(s) skipped.'
    );

    redirect(base_url('customer/offers_px'));
}
    public function get_px_publisher_stats_report1()
    {
        /*
         * PX Publisher Stats Sync
         * -------------------------------------------------------------
         * Existing structure and logic are not removed.
         *
         * This method follows the working PX browser curl:
         * - GET request
         * - Bearer token from modal
         * - PX cookie from modal
         * - ReportType = daily
         * - Grouping = affiliateSubID_1
         * - FromPeriod = first day of current month
         * - ToPeriod = latest completed day / yesterday
         * - Insert only new rows into ci_px_publisher_stats
         */

        $bearer_token = trim($this->input->post('bearer_token'));
        $cookie = trim($this->input->post('px_cookie'));

        /*
         * Clean Bearer token if user pasted:
         * Authorization: Bearer TOKEN
         * or Bearer TOKEN
         */
        $bearer_token = str_replace(array('authorization:', 'Authorization:'), '', $bearer_token);
        $bearer_token = trim($bearer_token);
        $bearer_token = preg_replace('/^Bearer\s+/i', '', $bearer_token);
        $bearer_token = trim($bearer_token, " \t\n\r\0\x0B\"'");

        /*
         * Clean PX cookie if user pasted from Windows CMD curl.
         * Windows curl may contain ^ before special characters.
         */
        $cookie = str_replace('-b ', '', $cookie);
        $cookie = str_replace(array('^"', '"', "'"), '', $cookie);
        $cookie = str_replace('^|', '|', $cookie);
        $cookie = str_replace('^', '', $cookie);
        $cookie = trim($cookie);

        if (empty($bearer_token)) {
            $this->session->set_flashdata('error', 'PX sync failed. Bearer token is required.');
            redirect(base_url('customer/offers_px'));
            return;
        }

        if (empty($cookie)) {
            $this->session->set_flashdata('error', 'PX sync failed. PX cookie is required.');
            redirect(base_url('customer/offers_px'));
            return;
        }

        $base_url = 'https://open.px.com/api/publisherstats/report';

        /*
         * PX working curl is using a range:
         * Example:
         * FromPeriod = 2026-05-01
         * ToPeriod   = 2026-05-14
         *
         * So here we use current month first date to yesterday.
         * PX usually rejects current same-day report with HTTP 400.
         */

        $sorting = array(
            'dateTime' => 'desc'
        );
        // $current_date = date('Y-m-d');
        $filter = array(
            'FromPeriod' => date('Y-m-d', strtotime('-1 day')),
            'ToPeriod' => date('Y-m-d', strtotime('+1 day'))
        );

        /*
         * Build URL manually.
         * PX accepts this format:
         * sorting=%7B%22dateTime%22:%22desc%22%7D
         * filter=%7B%22FromPeriod%22:%222026-05-01%22,%22ToPeriod%22:%222026-05-14%22%7D
         */
        $sorting_json = rawurlencode(json_encode($sorting));
        $filter_json = rawurlencode(json_encode($filter));

        /*
         * Keep ":" and "," unencoded to match browser curl.
         */
        $sorting_json = str_replace(array('%3A', '%2C'), array(':', ','), $sorting_json);
        $filter_json = str_replace(array('%3A', '%2C'), array(':', ','), $filter_json);

        $url = $base_url
            . '?count=50'
            . '&page=1'
            . '&sorting=' . $sorting_json
            . '&filter=' . $filter_json
            . '&ReportType=daily'
            . '&Grouping=affiliateSubID_1';

        /*
         * Generate browser-like request IDs.
         * These headers do not need to be exactly same every time.
         */
        $trace_id = bin2hex(random_bytes(16));
        $span_id = bin2hex(random_bytes(8));

        $headers = array(
            'accept: application/json, text/plain, */*',
            'accept-language: en-IN,en;q=0.9,en-GB;q=0.8,en-US;q=0.7,bn;q=0.6,da;q=0.5,zh-CN;q=0.4,zh;q=0.3',
            'authorization: Bearer ' . $bearer_token,
            'priority: u=1, i',
            'referer: https://open.px.com/',
            'request-context: appId=cid-v1:57fe3f1f-5acb-4f53-9daa-d185f60e0b94',
            'request-id: |' . $trace_id . '.' . $span_id,
            'sec-ch-ua: "Chromium";v="148", "Google Chrome";v="148", "Not/A)Brand";v="99"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"',
            'sec-fetch-dest: empty',
            'sec-fetch-mode: cors',
            'sec-fetch-site: same-origin',
            'traceparent: 00-' . $trace_id . '-' . $span_id . '-01',
            'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36'
        );

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER => $headers,

            /*
             * Matches curl -b "cookie..."
             */
            CURLOPT_COOKIE => $cookie,

            /*
             * Allow compressed response if PX returns gzip/br.
             */
            CURLOPT_ENCODING => ''
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);

            $this->session->set_flashdata('error', 'PX sync failed. Curl Error: ' . $error);
            redirect(base_url('customer/offers_px'));
            return;
        }

        curl_close($ch);

        if ((int) $http_code === 400) {
            $this->session->set_flashdata(
                'error',
                'PX sync failed. HTTP Code: 400. Bad request. URL: ' . $url
            );
            redirect(base_url('customer/offers_px'));
            return;
        }

        if ((int) $http_code === 401) {
            $this->session->set_flashdata(
                'error',
                'PX sync failed. HTTP Code: 401. Invalid/expired Bearer token or PX cookie.'
            );
            redirect(base_url('customer/offers_px'));
            return;
        }

        if ($http_code < 200 || $http_code >= 300) {
            $this->session->set_flashdata(
                'error',
                'PX sync failed. HTTP Code: ' . $http_code
            );
            redirect(base_url('customer/offers_px'));
            return;
        }

        $api_response = json_decode($response, true);

        if (
            empty($api_response) ||
            !isset($api_response['data']) ||
            !is_array($api_response['data'])
        ) {
            $this->session->set_flashdata('error', 'PX sync failed. Invalid API response.');
            redirect(base_url('customer/offers_px'));
            return;
        }

        $inserted_count = 0;
        $updated_count = 0;
        $skipped_count = 0;

        foreach ($api_response['data'] as $row) {

            /*
             * Skip Total row because it is summary row.
             */
            if (
                !isset($row['dateTime']) ||
                strtolower(trim($row['dateTime'])) === 'total'
            ) {
                $skipped_count++;
                continue;
            }

            $report_date = trim($row['dateTime']);
            $affiliate_id = isset($row['affiliateID']) ? trim($row['affiliateID']) : '';
            $offer_id = isset($row['offerID']) ? trim($row['offerID']) : '';
            $affiliateSubID_1 = isset($row['affiliateSubID_1']) ? trim($row['affiliateSubID_1']) : '';

            /*
             * Required fields validation.
             */
            if (empty($report_date) || empty($affiliate_id) || empty($offer_id)) {
                $skipped_count++;
                continue;
            }

            /*
             * Prevent duplicate insert:
             * Same report_date + affiliate_id + offer_id + affiliateSubID_1
             * will not insert again.
             */
            $exists = $this->customer->check_px_publisher_stat_exists(
                $report_date,
                $affiliate_id,
                $offer_id,
                $affiliateSubID_1
            );

            /*
             * If duplicate row exists, update existing PX stats record.
             * Duplicate condition:
             * report_date + affiliate_id + offer_id + affiliateSubID_1
             */
            if ($exists) {

                $update_data = array(
                    'clicks' => isset($row['clicks']) ? (int) $row['clicks'] : 0,
                    'impressions' => isset($row['impressions']) ? (int) $row['impressions'] : 0,
                    'conversions' => isset($row['conversions']) ? (int) $row['conversions'] : 0,
                    'cost' => isset($row['cost']) ? (float) $row['cost'] : 0,
                    'updated_at' => date('Y-m-d H:i:s')
                );

                /*
                 * Update existing PX publisher stat row instead of skipping duplicate.
                 */
                $updated = $this->customer->update_px_publisher_stat(
                    $report_date,
                    $affiliate_id,
                    $offer_id,
                    $affiliateSubID_1,
                    $update_data
                );

                if ($updated) {
                    $updated_count++;
                } else {
                    $skipped_count++;
                }

                continue;
            }

            /*
             * Save only required PX fields.
             */
            $insert_data = array(
                'report_date' => $report_date,
                'affiliate_id' => $affiliate_id,
                'offer_id' => $offer_id,
                'clicks' => isset($row['clicks']) ? (int) $row['clicks'] : 0,
                'impressions' => isset($row['impressions']) ? (int) $row['impressions'] : 0,
                'conversions' => isset($row['conversions']) ? (int) $row['conversions'] : 0,
                'affiliateSubID_1' => $affiliateSubID_1,
                'cost' => isset($row['cost']) ? (float) $row['cost'] : 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            $insert_id = $this->customer->insert_px_publisher_stat($insert_data);

            if ($insert_id) {
                $inserted_count++;
            } else {
                $skipped_count++;
            }
        }

        $this->session->set_flashdata(
            'success',
            'PX sync completed. ' .
            $inserted_count . ' new row(s) inserted, ' .
            $updated_count . ' existing row(s) updated, ' .
            $skipped_count . ' invalid/summary row(s) skipped.'
        );

        redirect(base_url('customer/offers_px'));
    }

}