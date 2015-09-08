<?php
//
// Instantiate Class
//
include_once('classes/tbs_class.php');

// Filters
//
$siteId   = (!empty($_GET["siteId"])) ? $_GET["siteId"] : NULL;
$search   = (!empty($_GET["search"])) ? $_GET["search"] : NULL;

// Build condition 
//
$where='';
if (!empty($siteId)) {
  $where .= " vads_site_id = '$siteId'";
}
if (!empty($search)) {
  if (!empty($where)) $where .= ' AND ';
  $where .= "( vads_cust_email = '$search' OR vads_trans_uuid = '$search' OR vads_order_id = '$search' )";
}
if (!empty($where)) $where = " WHERE $where ";


// Grep data from database
//
$db   = new SQLite3("./database/payzen.sqlite");
$results = $db->query("SELECT * FROM ipn $where");



//
// Prepare array for TBS template
//
while ($res = $results->fetchArray()) {
  $res['vads_payment_seq'] = json_encode(json_decode($res['vads_payment_seq']), JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
  #$res['full'] = json_encode(json_decode($res['full']), JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
  $wrk = json_decode($res['full'],true);
  if ($wrk['vads_payment_seq'] != '' ) $wrk['vads_payment_seq'] = "<code>=>  MULTI</code>";
  $res['full'] = json_encode($wrk, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
  $data[] = $res;
}


//
// Ask TBS to display data
//
$TBS = new clsTinyButStrong;
$TBS->LoadTemplate('tpl/tpl_ipn.html');
$TBS->MergeBlock('list',$data);
$TBS->Show();
