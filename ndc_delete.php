<?php 
    require_once('../../../wp-load.php');

$delete_id='';
$delete_id = $_GET['del'];

global $wpdb;
$table = $wpdb->prefix . 'ndc_form_details';

$wpdb->delete($table, ['id' => $delete_id]);

$form_plugin_url = admin_url('admin.php?page=NDC_FORM');

header("location: $form_plugin_url");