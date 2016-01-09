<?php
//
// Instantiate Class and variables
//
include_once('classes/classes.inc.php');
include_once('classes/tbs_class.php');
include_once('classes/tbs_plugin_html.php'); // Plug-in for selecting HTML items.
include_once('classes/tbs_plugin_bypage.php'); // Plug-in for selecting HTML items.
include_once('classes/tbs_plugin_navbar.php'); // Plug-in for selecting HTML items.

$data = array();

// database
//
if (!file_exists(DATABASE_FILE)) die("run InitScript"); 
$db   = new SQLite3(DATABASE_FILE);

// Filters
//
$siteId   = (!empty($_GET["siteId"]))  ? $_GET["siteId"] : NULL;
$orderId  = (!empty($_GET["orderId"])) ? $_GET["orderId"] : NULL;
$uuid     = (!empty($_GET["uuid"]))    ? $_GET["uuid"] : NULL;
$email    = (!empty($_GET["email"]))   ? $_GET["email"] : NULL;
$cards    = (!empty($_GET["cards"]))   ? $_GET["cards"] : NULL;

// Pagination 
$pageNum  = (!empty($_GET["pageNum"])) ? $_GET["pageNum"] : 1;
$recCnt   = (!empty($_GET["recCnt"]))  ? intval($_GET["recCnt"]) : -1;
$pageSize = 16;

// Build condition 
//
$where = '';
$other_prms = '';
if (!empty($siteId)) {
  if (strlen($siteId) != '8') $where .= "vads_contract_used = '".SQLite3::escapeString($siteId)."'"; 
   else $where .= " (vads_site_id = '".SQLite3::escapeString($siteId)."' OR vads_contract_used = '".SQLite3::escapeString($siteId)."')";
  $other_prms .= "&siteId=$siteId";
}
if (!empty($orderId)) {
  if (!empty($where)) $where .= ' AND ';
  $where .= " vads_order_id LIKE '".SQLite3::escapeString($orderId)."' ";
  $other_prms .= "&orderId=$orderId";
}
if (!empty($uuid)) {
  if (!empty($where)) $where .= ' AND ';
  if (strlen($uuid) == 6) $where .= "vads_trans_id = '".SQLite3::escapeString($uuid)."' ";
   else $where .= "vads_trans_uuid = '".SQLite3::escapeString($uuid)."' ";
  $other_prms .= "&uuid=$uuid";
}
if (!empty($email)) {
  if (!empty($where)) $where .= ' AND ';
  $where .= " vads_cust_email like '".SQLite3::escapeString($email)."'";
  $other_prms .= "&email=$email";
}
if (!empty($cards)) {
  if (!empty($where)) $where .= ' AND ';
  $where .= " vads_card_brand = '".SQLite3::escapeString($cards)."'";
  $other_prms .= "&cards=$cards";
}
if (!empty($where)) $where = " WHERE $where ";

// prepare select
//

// Build card list from existing IPN records 
$resc = $db->query("select DISTINCT vads_card_brand FROM ipn WHERE vads_card_brand <> ''ORDER BY vads_card_brand");

while ($rescards = $resc->fetchArray()) {
  $cardlst[] = $rescards;
}


// Grep data from database
//
$results = $db->query("SELECT * FROM ipn $where ORDER by ts DESC");



//
// Prepare array for TBS template
//
while ($res = $results->fetchArray()) {

  // prepare/sanitize the data of multi-payments  
  $wrkmulti = json_decode($res['vads_payment_seq']);
  $res['vads_payment_seq'] = json_encode($wrkmulti, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);

  // prepare/sanitize the data set for a better detail display
  $wrk = json_decode($res['full'],true);

  // replace the multi payment details by a reference to the MULTI button
  if ($wrk['vads_payment_seq'] != '' ) $wrk['vads_payment_seq'] = "<code>=>  MULTI</code>";
  $res['full'] = json_encode($wrk, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
 
  // populate extra data details from the json info
  $res['vads_sequence_number'] = $wrk['vads_sequence_number'];
 
  // populate data array
  $data[] = $res;
}


//
// Ask TBS to display data
//
$TBS = new clsTinyButStrong;
$TBS->LoadTemplate('tpl/tpl_ipn.html');
$TBS->MergeBlock('cardlst',$cardlst);

#$TBS->MergeBlock('list',$data);

// Merge ByPage block
$TBS->PlugIn(TBS_BYPAGE,$pageSize,$pageNum,$recCnt);
$recCnt = $TBS->MergeBlock('list',$data);

// Merge  NavBar
$TBS->PlugIn(TBS_NAVBAR,'nv','',$pageNum,$recCnt,$pageSize);


$TBS->Show();
