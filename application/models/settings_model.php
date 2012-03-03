<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Settings_model extends CI_Model {

	//Prints
	function getPrintSettings() {
		$sql_get_print_settings = 'SELECT * FROM settings WHERE setting_group = ' . "'file-printing ';";
		$query_get_print_settings = $this->db->query($sql_get_print_settings);
		if ($query_get_print_settings->num_rows() > 0) {
			foreach ($query_get_print_settings->result() as $print_setting) {
				$print_settings[$print_setting->setting_key]['setting_value'] = $print_setting->setting_value;
				$print_settings[$print_setting->setting_key]['setting_label'] = $print_setting->setting_label;
			}
			return $print_settings;
		}
	}
	
	function getGCPStatus() {
		$sql_get_gcp_status = 'SELECT * FROM settings WHERE setting_key = ' . "'gcp'" . ';';
		$query_get_gcp_status = $this->db->query($sql_get_gcp_status);
		if ($query_get_gcp_status->num_rows() > 0) {
			foreach ($query_get_gcp_status->result() as $gcp) {
				$gcp_status['setting_label'] = $gcp->setting_label;
				$gcp_status['setting_key'] = $gcp->setting_key;
				$gcp_status['setting_value'] = $gcp->setting_value;
			}
			return $gcp_status;
		}
	}
	
	function getPrinterToUse() {
		$sql_get_printer_to_use = 'SELECT * FROM settings WHERE setting_key = ' . "'printer';";
		$query_get_printer_to_use = $this->db->query($sql_get_printer_to_use);
		if ($query_get_printer_to_use->num_rows() == 1) {
			foreach ($query_get_printer_to_use->result() as $printer_to_use) {
				$printer['setting_value'] = $printer_to_use->setting_value;
			}
			return $printer;
		}
	}
	
	function getTimekeeping() {
		$sql_get_timekeeping = 'SELECT * FROM settings WHERE setting_key = ' . "'timekeeping';";
		$query_get_timekeeping = $this->db->query($sql_get_timekeeping);
		if ($query_get_timekeeping->num_rows() == 1) {
			foreach ($query_get_timekeeping->result() as $timekeeping) {
				$timekeeping_used['setting_label'] = $timekeeping->setting_label;
				$timekeeping_used['setting_key'] = $timekeeping->setting_key;
				$timekeeping_used['setting_value'] = $timekeeping->setting_value;
			}
			return $timekeeping_used;
		}
	}
	
	function updateSettingValue($setting_key, $setting_value) {
		$sql_update_setting = 'UPDATE settings SET setting_value = ' . "'$setting_value'" . ' WHERE setting_key = ' . "'$setting_key'";
		$query_update_setting = $this->db->query($sql_update_setting);
	}
}
?>