<?php

require_once('../../../wp-load.php');
session_start();

global $wpdb;
$generate = $_GET['eee'];

$wpdb->update(
    'wp_ndc_form_details',
    array(
        'status' => '1',
    ),
    array('generate_number' => $generate)
);

$_SESSION['submit_file'] = 1; 

header("location: https://wp.bdmonster.com/lider-invest/ndc-form/");