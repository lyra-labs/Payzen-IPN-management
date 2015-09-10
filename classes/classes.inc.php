<?PHP

/////////////////////////////////////////////


// Variables & Constants
//

define("DATABASE_FILE", "./database/payzen.sqlite");


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
    vads_shop_name STRING, 
    vads_ctx_mode STRING, 
    vads_trans_uuid STRING, 
    vads_order_id STRING, 
    vads_effective_creation_date DATETIME, 
    vads_operation_type STRING, 
    vads_trans_status STRING, 
    vads_effective_amount STRING, 
    vads_currency STRING, 
    vads_auth_mode STRING, 
    vads_card_brand STRING, 
    vads_card_number STRING, 
    vads_cust_email STRING, 
    vads_payment_seq BLOB, 
    full BLOB, 
    signature STRING, 
    checked STRING
    )"
  );
  echo DATABASE_FILE." created<br />";
}




