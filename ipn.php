<?php

// Instantiate Class and variables
//
include_once('classes/classes.inc.php');

// Initialise var
//
$arg       = (empty($_POST)) ? NULL : $_POST;
$siteId    = (isset($_POST["vads_site_id"])) ? $_POST["vads_site_id"] : NULL;
$signature = (isset($_POST["signature"])) ? $_POST["signature"] : NULL;

// If vads_site_id or databale doesn't exist die 
//
if (empty($siteId)) die("IPN triggered without valid Data");
if (!file_exists(DATABASE_FILE)) die("run InitScript");

// Get key prom parameters and validate signature
//
$iniValues = parse_ini_file("config/key.ini",true);
$id        = array_search($siteId, array_column($iniValues, 'site_id'));
$idKey     = array_keys($iniValues);
$key       = $iniValues[$idKey[$id]]['key_test']; //todo manage PRODUCTION mode too

// Signature check
//
$k = '';
ksort($arg);
foreach ($arg as $param => $val) {
    if(substr($param,0,5) == 'vads_') {
       $k .= $val."+";
    }
}
$hash = sha1($k.$key);

// Signature check result
//
$checked   = ($hash == $signature) ? 'true' : 'false';

// Database instanciation
//
$db = new SQLite3(DATABASE_FILE);

// Prepare and Insert data in the ipn table 
//
$sql = "INSERT INTO ipn (status, vads_site_id, vads_url_check_src, vads_payment_src, vads_shop_name, vads_ctx_mode, vads_trans_uuid, vads_trans_id, vads_order_id, vads_order_info, vads_payment_config, vads_effective_creation_date, vads_operation_type, vads_trans_status, vads_result, vads_extra_result, vads_effective_amount, vads_currency, vads_contract_used, vads_auth_mode, vads_card_brand, vads_card_number, vads_payment_seq, vads_cust_email, vads_capture_delay, vads_presentation_date, vads_warranty_result, vads_risk_control, vads_validation_mode, vads_recurrence_status, vads_identifier_status, vads_identifier, vads_subscription, vads_sub_desc, vads_sub_effect_date, vads_sub_currency, vads_sub_amount, vads_sub_init_amount_number, vads_sub_init_amount, vads_contrib, vads_ext_info_donation, vads_ext_info_donation_recipient, vads_ext_info_donation_recipient_name, vads_ext_info_donation_merchant, full, signature, checked)

  VALUES (
    'NEW', '"
    .SQLite3::escapeString($arg['vads_site_id'])."', '"
    .SQLite3::escapeString($arg['vads_url_check_src'])."', '"
    .SQLite3::escapeString($arg['vads_payment_src'])."', '"
    .SQLite3::escapeString($arg['vads_shop_name'])."', '"
    .SQLite3::escapeString($arg['vads_ctx_mode'])."', '"
    .SQLite3::escapeString($arg['vads_trans_uuid'])."', '"
    .SQLite3::escapeString($arg['vads_trans_id'])."', '"
    .SQLite3::escapeString($arg['vads_order_id'])."', '"
    .SQLite3::escapeString($arg['vads_order_info'])."', '"
    .SQLite3::escapeString($arg['vads_payment_config'])."', '"
    .SQLite3::escapeString($arg['vads_effective_creation_date'])."', '"
    .SQLite3::escapeString($arg['vads_operation_type'])."', '"
    .SQLite3::escapeString($arg['vads_trans_status'])."', '"
    .SQLite3::escapeString($arg['vads_result'])."', '"
    .SQLite3::escapeString($arg['vads_extra_result'])."', '"
    .SQLite3::escapeString($arg['vads_effective_amount'])."', '"
    .SQLite3::escapeString($arg['vads_currency'])."', '"
    .SQLite3::escapeString($arg['vads_contract_used'])."', '"
    .SQLite3::escapeString($arg['vads_auth_mode'])."', '"
    .SQLite3::escapeString($arg['vads_card_brand'])."', '"
    .SQLite3::escapeString($arg['vads_card_number'])."', '"
    .SQLite3::escapeString($arg['vads_payment_seq'])."', '"
    .SQLite3::escapeString($arg['vads_cust_email'])."', '"

    .SQLite3::escapeString($arg['vads_capture_delay'])."', '"
    .SQLite3::escapeString($arg['vads_presentation_date'])."', '"
    .SQLite3::escapeString($arg['vads_warranty_result'])."', '"
    .SQLite3::escapeString($arg['vads_risk_control'])."', '"
    .SQLite3::escapeString($arg['vads_validation_mode'])."', '"
    .SQLite3::escapeString($arg['vads_recurrence_status'])."', '"
    .SQLite3::escapeString($arg['vads_identifier_status'])."', '"
    .SQLite3::escapeString($arg['vads_identifier'])."', '"
    .SQLite3::escapeString($arg['vads_subscription'])."', '"
    .SQLite3::escapeString($arg['vads_sub_desc'])."', '"
    .SQLite3::escapeString($arg['vads_sub_effect_date'])."', '"
    .SQLite3::escapeString($arg['vads_sub_currency'])."', '"
    .SQLite3::escapeString($arg['vads_sub_amount'])."', '"
    .SQLite3::escapeString($arg['vads_sub_init_amount_number'])."', '"
    .SQLite3::escapeString($arg['vads_sub_init_amount'])."', '"
    .SQLite3::escapeString($arg['vads_contrib'])."', '"
    .SQLite3::escapeString($arg['vads_ext_info_donation'])."', '"
    .SQLite3::escapeString($arg['vads_ext_info_donation_recipient'])."', '"
    .SQLite3::escapeString($arg['vads_ext_info_donation_recipient_name'])."', '"
    .SQLite3::escapeString($arg['vads_ext_info_donation_merchant'])."', '"


    .SQLite3::escapeString(json_encode($arg))."', '"
    .SQLite3::escapeString($arg['signature'])."', '"
    .SQLite3::escapeString($checked)
    ."')";
$db->exec( $sql );


// Send sql request by eMail for debug purposes
//
#mail ( "" , "IPN call siteId ".$arg['vads_site_id']." transaction ".$arg['vads_trans_id'] ,"sqlite result\n[". $db->lastErrorMsg()."] \n\nSql request \n$sql" );


// Error & Timeout managment
//
// if the Payzen parameter vads_ext_info_IpnError is sent we will use it 
// either to simulate an TimeOut 
// or respond with an special status code  
//
if ($arg['vads_ext_info_IpnError']=="TimeOut") {
  sleep(rand(37, 49));
} elseif (preg_match("/[1-5]{1}[0-2]{1}[0-9]{1}/",$arg['vads_ext_info_IpnError'])) {
  header_status($arg['vads_ext_info_IpnError']);
}


// Tell Payzen signature & sqlite result
//
echo "IPN signature=$checked sqlite=[".$db->lastErrorMsg()."]  ";

//
// End
