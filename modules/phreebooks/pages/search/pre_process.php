<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright (c) 2008, 2009, 2010, 2011, 2012 PhreeSoft, LLC       |
// | http://www.PhreeSoft.com                                        |
// +-----------------------------------------------------------------+
// | This program is free software: you can redistribute it and/or   |
// | modify it under the terms of the GNU General Public License as  |
// | published by the Free Software Foundation, either version 3 of  |
// | the License, or any later version.                              |
// |                                                                 |
// | This program is distributed in the hope that it will be useful, |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of  |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the   |
// | GNU General Public License for more details.                    |
// +-----------------------------------------------------------------+
//  Path: /modules/phreebooks/pages/search/pre_process.php
//
$security_level = validate_user(SECURITY_ID_SEARCH);
/**************  include page specific files    *********************/
require_once(DIR_FS_WORKING . 'functions/phreebooks.php');
/**************   page specific initialization  *************************/
$sort_by = array(
  'date' => TEXT_DATE,
  'id'   => TEXT_JOURNAL_TYPE,
  'amt'  => TEXT_AMOUNT,
);
$choices = array(
  'all'  => TEXT_ALL,
  'rng'  => TEXT_RANGE,
  'eq'   => TEXT_EQUAL,
  'neq'  => TEXT_NOT_EQUAL,
  'like' => TEXT_CONTAINS,
);
$journal_choices = array('0' => TEXT_ALL);
for ($i = 1; $i < 30; $i++) {
  $j_constant = str_pad($i, 2, '0', STR_PAD_LEFT);
  if (defined('GEN_ADM_TOOLS_J' . $j_constant)) {
	$journal_choices[$i] = sprintf(TEXT_JID_ENTRY, constant('GEN_ADM_TOOLS_J' . $j_constant));
  }
}
$_GET['sort_id']         = $_POST['sort_id']           ? db_prepare_input($_POST['sort_id'])         : $_GET['sort_id'];
$_GET['date_id']         = $_POST['date_id']           ? db_prepare_input($_POST['date_id'])         : $_GET['date_id'];
$_GET['date_from']       = $_POST['date_from']         ? db_prepare_input($_POST['date_from'])       : $_GET['date_from'];
$_GET['date_to']         = $_POST['date_to']           ? db_prepare_input($_POST['date_to'])         : $_GET['date_to'];
$_GET['journal_id']      = isset($_POST['journal_id']) ? db_prepare_input($_POST['journal_id'])      : $_GET['journal_id'];
$_GET['ref_id']          = $_POST['ref_id']            ? db_prepare_input($_POST['ref_id'])          : $_GET['ref_id'];
$_GET['ref_id_from']     = $_POST['ref_id_from']       ? db_prepare_input($_POST['ref_id_from'])     : $_GET['ref_id_from'];
$_GET['ref_id_to']       = $_POST['ref_id_to']         ? db_prepare_input($_POST['ref_id_to'])       : $_GET['ref_id_to'];
$_GET['account_id']      = $_POST['account_id']        ? db_prepare_input($_POST['account_id'])      : $_GET['account_id'];
$_GET['account_id_from'] = $_POST['account_id_from']   ? db_prepare_input($_POST['account_id_from']) : $_GET['account_id_from'];
$_GET['account_id_to']   = $_POST['account_id_to']     ? db_prepare_input($_POST['account_id_to'])   : $_GET['account_id_to'];
$_GET['sku_id']          = $_POST['sku_id']            ? db_prepare_input($_POST['sku_id'])          : $_GET['sku_id'];
$_GET['sku_id_from']     = $_POST['sku_id_from']       ? db_prepare_input($_POST['sku_id_from'])     : $_GET['sku_id_from'];
$_GET['sku_id_to']       = $_POST['sku_id_to']         ? db_prepare_input($_POST['sku_id_to'])       : $_GET['sku_id_to'];
$_GET['amount_id']       = $_POST['amount_id']         ? db_prepare_input($_POST['amount_id'])       : $_GET['amount_id'];
$_GET['amount_id_from']  = $_POST['amount_id_from']    ? $currencies->clean_value(db_prepare_input($_POST['amount_id_from'])) : $currencies->clean_value($_GET['amount_id_from']);
$_GET['amount_id_to']    = $_POST['amount_id_to']      ? $currencies->clean_value(db_prepare_input($_POST['amount_id_to']))   : $currencies->clean_value($_GET['amount_id_to']);
$_GET['gl_acct_id']      = $_POST['gl_acct_id']        ? db_prepare_input($_POST['gl_acct_id'])      : $_GET['gl_acct_id'];
$_GET['gl_acct_id_from'] = $_POST['gl_acct_id_from']   ? db_prepare_input($_POST['gl_acct_id_from']) : $_GET['gl_acct_id_from'];
$_GET['gl_acct_id_to']   = $_POST['gl_acct_id_to']     ? db_prepare_input($_POST['gl_acct_id_to'])   : $_GET['gl_acct_id_to'];
$_GET['main_id']         = $_POST['main_id']           ? db_prepare_input($_POST['main_id'])         : $_GET['main_id'];
$_GET['main_id_from']    = $_POST['main_id_from']      ? db_prepare_input($_POST['main_id_from'])    : $_GET['main_id_from'];
$_GET['main_id_to']      = $_POST['main_id_to']        ? db_prepare_input($_POST['main_id_to'])      : $_GET['main_id_to'];
// set some defaults
$_GET['date_id']   = $_GET['date_id']   ? $_GET['date_id']   : 'l'; // default to current period
$_GET['date_from'] = $_GET['date_from'] ? $_GET['date_from'] : gen_locale_date(CURRENT_ACCOUNTING_PERIOD_START);
$_GET['date_to']   = $_GET['date_to']   ? $_GET['date_to']   : gen_locale_date(CURRENT_ACCOUNTING_PERIOD_END);
$action = (isset($_GET['action']) ? $_GET['action'] : $_POST['todo']);
// load the sort fields
$_GET['sf'] = $_POST['sort_field'] ? $_POST['sort_field'] : $_GET['sf'];
$_GET['so'] = $_POST['sort_order'] ? $_POST['sort_order'] : $_GET['so'];
/***************   hook for custom actions  ***************************/
$custom_path = DIR_FS_WORKING . 'custom/pages/search/extra_actions.php';
if (file_exists($custom_path)) { include($custom_path); }
/***************   Act on the action request   *************************/
switch ($action) {
  case 'go_first':    $_GET['list'] = 1;     break;
  case 'go_previous': $_GET['list']--;       break;
  case 'go_next':     $_GET['list']++;       break;
  case 'go_last':     $_GET['list'] = 99999; break;
  case 'reset':
	$_GET['date_id']         = 'l';
	$_GET['date_from']       = gen_locale_date(CURRENT_ACCOUNTING_PERIOD_START);
	$_GET['date_to']         = gen_locale_date(CURRENT_ACCOUNTING_PERIOD_END);
	$_GET['journal_id']      = '-1';
	$_GET['ref_id']          = 'all';
	$_GET['ref_id_from']     = '';
	$_GET['ref_id_to']       = '';
	$_GET['account_id']      = 'all';
	$_GET['account_id_from'] = '';
	$_GET['account_id_to']   = '';
	$_GET['sku_id']          = 'all';
	$_GET['sku_id_from']     = '';
	$_GET['sku_id_to']       = '';
	$_GET['amount_id']       = 'all';
	$_GET['amount_id_from']  = '';
	$_GET['amount_id_to']    = '';
	$_GET['gl_acct_id']      = 'all';
	$_GET['gl_acct_id_from'] = '';
	$_GET['gl_acct_id_to']   = '';
	$_GET['main_id']         = 'all';
	$_GET['main_id_from']    = '';
	$_GET['main_id_to']      = '';
    $_GET['search_text']     = ''; 
  	$_GET['list']            = 1; 
	unset($_GET['list_order']);
	break;
  case 'search':
  case 'go_page':
  default:
}

/*****************   prepare to display templates  *************************/
$cal_from = array(
  'name'      => 'dateFrom',
  'form'      => 'site_search',
  'fieldname' => 'date_from',
  'imagename' => 'btn_date_1',
  'default'   => $_GET['date_from'],
  'params'    => array('align' => 'left'),
);
$cal_to = array(
  'name'      => 'dateTo',
  'form'      => 'site_search',
  'fieldname' => 'date_to',
  'imagename' => 'btn_date_2',
  'default'   => $_GET['date_to'],
  'params'    => array('align' => 'left'),
);

// load gl accounts
$gl_array_list = gen_coa_pull_down(SHOW_FULL_GL_NAMES, true, false, true);

// build the list header
$heading_array = array(
  'm.id'                  => TEXT_JOURNAL_RECORD_ID,
  'm.description'         => TEXT_DESCRIPTION,
  'm.bill_primary_name'   => GEN_PRIMARY_NAME,
  'm.post_date'           => TEXT_POST_DATE,
  'm.purchase_invoice_id' => TEXT_REFERENCE,
  'm.total_amount'        => TEXT_TOTAL,
);
$result      = html_heading_bar($heading_array, $_GET['sf'], $_GET['so']);
$list_header = $result['html_code'];
$disp_order  = $result['disp_order'];

// build the list for the page selected
$criteria = array();
if ($_GET['journal_id']) $criteria[] = 'm.journal_id = ' . $_GET['journal_id'];

$ref_fields = array('m.purchase_invoice_id', 'm.purch_order_id');
$result = build_search_sql($ref_fields, $_GET['ref_id'], $_GET['ref_id_from'], $_GET['ref_id_to']);
if ($result) $criteria[] = $result;

$account_fields = array('m.bill_primary_name', 'm.ship_primary_name');
$result = build_search_sql($account_fields, $_GET['account_id'], $_GET['account_id_from'], $_GET['account_id_to']);
if ($result) $criteria[] = $result;

$sku_fields = array('i.sku', 'i.description');
$result = build_search_sql($sku_fields, $_GET['sku_id'], $_GET['sku_id_from'], $_GET['sku_id_to']);
if ($result) $criteria[] = $result;

$amt_fields = array('m.total_amount', 'i.debit_amount', 'i.credit_amount');
$result = build_search_sql($amt_fields, $_GET['amount_id'], $currencies->clean_value($_GET['amount_id_from']), $currencies->clean_value($_GET['amount_id_to']));
if ($result) $criteria[] = $result;

$gl_acct_fields = array('m.gl_acct_id', 'i.gl_account');
$result = build_search_sql($gl_acct_fields, $_GET['gl_acct_id'], $_GET['gl_acct_id_from'], $_GET['gl_acct_id_to']);
if ($result) $criteria[] = $result;

$main_fields = array('m.id');
$result = build_search_sql($main_fields, $_GET['main_id'], $_GET['main_id_from'], $_GET['main_id_to']);
if ($result) $criteria[] = $result;

$date_prefs = array(
	'fieldname' => 'm.post_date',
	'params' => $_GET['date_id'] . ':' . ($_GET['date_from']) . ':' . ($_GET['date_to']));
$temp = gen_build_sql_date($date_prefs['params'], $date_prefs['fieldname']);
if ($temp['sql']) $criteria[] = '(' . $temp['sql'] . ')';

$crit = ($criteria) ? (" where " . implode(' and ', $criteria)) : '';
$query_raw = "select distinct m.id, m.journal_id, m.post_date, m.description, m.total_amount, 
	m.purchase_invoice_id, m.bill_primary_name, m.bill_acct_id   
	from " . TABLE_JOURNAL_MAIN . " m inner join " . TABLE_JOURNAL_ITEM . " i 
	on m.id = i.ref_id " . $crit . " order by $disp_order";

$query_result = $db->Execute($query_raw);
$query_split  = new splitPageResults($_GET['list'], MAX_DISPLAY_SEARCH_RESULTS, $query_raw, $query_numrows);
$query_result = $db->Execute($query_raw);

$include_header   = true;
$include_footer   = true;
$include_tabs     = false;
$include_calendar = true;
$include_template = 'template_main.php'; // include display template (required)
define('PAGE_TITLE', HEADING_TITLE_SEARCH_INFORMATION);
?>