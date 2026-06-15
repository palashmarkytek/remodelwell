<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (!function_exists('auth_check')) {
    function auth_check()
    {
        // Get a reference to the controller object
        $ci = &get_instance();
        if (!$ci->session->has_userdata('is_admin_login')) {
            redirect('auth/login', 'refresh');
        }
    }
}

if (!function_exists('get_general_settings')) {
    function get_general_settings()
    {
        $ci = &get_instance();
        $ci->load->model('admin/setting_model');
        return $ci->setting_model->get_general_settings();
    }
}

if (!function_exists('get_sidebar_sub_menu')) {
    function get_sidebar_sub_menu($parent_id)
    {
        $ci = &get_instance();
        $ci->db->select('*');
        $ci->db->where('parent', $parent_id);
        $ci->db->order_by('sort_order', 'asc');
        return $ci->db->get('sub_module')->result_array();
    }
}

if (!function_exists('get_sidebar_menu')) {
    function get_sidebar_menu()
    {
        $ci = &get_instance();
        $ci->db->select('*');
        $ci->db->order_by('sort_order', 'asc');
        return $ci->db->get('module')->result_array();
    }
}

if (!function_exists('old')) {
    function old($field)
    {
        $ci = &get_instance();
        return html_escape($ci->session->flashdata('form_data')[$field]);
    }
}

if (!function_exists('post')) {
    function post($filed, $update = [])
    {
        $ci = &get_instance();
        if (empty($update) || $ci->input->post('submit')) {
            return ($ci->input->post($filed)) ? $ci->input->post($filed) : '';
        } else {
            return ($ci->input->post($filed)) ? $ci->input->post($filed) : $update[$filed];
        }
    }
}

if (!function_exists('today_date')) {
    function today_date()
    {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('pr')) {
    function pr($input)
    {
        echo '<pre>';
        print_r($input);
        echo '</pre>';
    }
}

if (!function_exists('display')) {
    function display($path, $data)
    {
        $ci = &get_instance();
        $ci->load->view('includes/_header', $data);
        $ci->load->view($path, $data);
        return $ci->load->view('includes/_footer', $data);
    }
}

if (!function_exists('generateEmployeeId')) {
    function generateEmployeeId()
    {
        $prefix = "RWELL";
        $ci = &get_instance();
        $ci->db->select('admin_id');
        $ci->db->from('ci_admin');
        $ci->db->order_by('admin_id', 'DESC');
        $ci->db->limit(1);
        $query = $ci->db->get();
        return $prefix . str_pad($query->row()->admin_id, 6, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('createFolder')) {
    function createFolder($folder_name)
    {
        $folderPath = FCPATH . 'documents/' . $folder_name;
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0755, true);
        }
    }
}

if (!function_exists('getFinancialYear')) {
    function getFinancialYear($date = null)
    {
        $dateObject = $date ? new DateTime($date) : new DateTime();

        // Extract the month and year.
        $month = (int) $dateObject->format('m');
        $year = (int) $dateObject->format('Y');

        // For a fiscal year from April 1 to March 31:
        // - Dates in January (01) to March (03) fall in the fiscal year that started the previous calendar year.
        // - Dates in April (04) to December (12) fall in the fiscal year that starts in the current calendar year.
        if ($month < 4) {
            $startYear = $year - 1;
        } else {
            $startYear = $year;
        }

        // The fiscal year ends one year after it starts.
        $endYear = $startYear + 1;

        return sprintf("%d-%d", $startYear, $endYear);
    }
}
