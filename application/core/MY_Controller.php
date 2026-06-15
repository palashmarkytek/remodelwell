<?php

class MY_Controller extends CI_Controller
{
	function __construct()
	{

		parent::__construct();

		$this->load->model('setting_model', 'setting_model');
		//general settings
		$global_data['general_settings'] = $this->setting_model->get_general_settings();
		$this->general_settings = $global_data['general_settings'];
		//set timezone

		date_default_timezone_set($this->general_settings['timezone']);

		$site_language = ($this->general_settings['default_language'] != "") ? $this->general_settings['default_language'] : "english";
		$language = ($this->session->userdata('site_lang') != "") ? $this->session->userdata('site_lang') : $site_language;
		$language = strtolower(get_lang_name_by_id($language));

		$this->config->set_item('language', $language);
		$this->lang->load(array('site'), $language);
	}
}


