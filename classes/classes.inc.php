<?PHP

/////////////////////////////////////////////
// Classes used for 
//
//  - Constant names 
//  - Tables creations
//  - TinyButStrong functions 
//
/////////////////////////////////////////////


// Variables & Constants
//

define("OBFUSCATION","YES"); 
define("DATABASE_FILE", "./database/payzen.sqlite");

define("PROJECT_NAME", "IPN");
define("PROJECT_LICENSE", "The MIT License (MIT)");

// Create the IPN table 
//
function create_ipn_table() {
  $db  = new SQLite3(DATABASE_FILE);
  $db->exec(
    "CREATE TABLE IF NOT EXISTS ipn (
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
    ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    status STRING, 
    vads_site_id STRING, 
    vads_url_check_src STRING,
    vads_payment_src STRING,
    vads_shop_name STRING, 
    vads_ctx_mode STRING, 
    vads_trans_uuid STRING, 
    vads_trans_id STRING, 
    vads_order_id STRING,
    vads_order_info STRING,
    vads_payment_config STRING, 
    vads_effective_creation_date DATETIME, 
    vads_operation_type STRING, 
    vads_trans_status STRING,
    vads_result STRING,
    vads_extra_result STRING,
 
    vads_effective_amount STRING, 
    vads_currency STRING,
    vads_contract_used STRING, 
    vads_auth_mode STRING, 
    vads_card_brand STRING, 
    vads_card_number STRING,
    vads_payment_seq BLOB, 
 
    vads_cust_email STRING, 

    vads_capture_delay STRING,
    vads_presentation_date DATETIME,
    vads_warranty_result STRING,
    vads_risk_control STRING,

    vads_validation_mode STATUS,
    vads_recurrence_status STRING,

    vads_identifier_status STRING,
    vads_identifier STRING,

    vads_subscription STRING,
    vads_sub_desc STRING,
    vads_sub_effect_date DATE,
    vads_sub_currency STRING,
    vads_sub_amount STRING,
    vads_sub_init_amount_number STRING,
    vads_sub_init_amount STRING,

    vads_contrib STRING,

    vads_ext_info_donation STRING,
    vads_ext_info_donation_recipient STRING,
    vads_ext_info_donation_recipient_name STRING,
    vads_ext_info_donation_merchant STRING,

    full BLOB,
 
    signature STRING, 
    checked STRING
    )"
  );
  echo DATABASE_FILE." created<br />";
}

// TBS
//  TinyButStrong function for eMail obfuscation
//  use :
//   vads_cust_email;onformat=zemail;domaine=exemple.com
//   will replace email domain name with value defined in 'domain' 
// 
function obfmail($FieldName, &$CurrVal, &$CurrPrm) {
  if (!empty($CurrVal) && OBFUSCATION == 'YES') $CurrVal= substr($CurrVal, 0, strpos($CurrVal, '@')) . '@' . $CurrPrm['domaine'];
}


function obfdata($FieldName, &$CurrVal, &$CurrPrm) {
  if (!empty($CurrVal)) {
    $CurrVal= str_replace('    ','&nbsp;&nbsp;',$CurrVal);
    if (OBFUSCATION == 'YES') $CurrVal= preg_replace('/(vads_cust_address|vads_cust_email|vads_cust_first_name|vads_cust_last_name|vads_cust_name|vads_ship_to_first_name|vads_ship_to_last_name|vads_ship_to_name|vads_ship_to_phone_num|vads_ship_to_street2?|vads_cust_phone|vads_cust_cell_phone)(":)(.+)/ ','$1$2 <i>redacted</i>, ',$CurrVal);
  }
}

function indent($FieldName, &$CurrVal, &$CurrPrm) {
  if (!empty($CurrVal)) {
    $CurrVal= str_replace('    ','&nbsp;&nbsp;',$CurrVal);
  }
}



