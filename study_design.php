<?php
/** 
 * pGMART
 *
 * PHP Generalized Method for Adaptive Randomization in clinical Trials
 *
 * Author: Jean-Philippe Giot jp@giot.net
 * Licence: GPL
 */

include_once ("init.php");

// checking that a user is authenticated
if (!$auth->getAuthStatus()){
	//echo "<a href='login.php'>Login</a>";
	//mydie ('Not connected.');
	include_once('login.php');
	die();
}

// fetching study based on given id
if (!isset($_REQUEST['study_id'])) {
	mydie ('invalid request');
}

$study_id = (int) $_REQUEST['study_id'];

// fetching study based on given id
if ((false == $study_id) || (0 >= $study_id)){
	mydie ('invalid positive integer');
}

$study = new study_GMART();
$study->pdo = $pdo;
$study->table_prefix = $_DB['table_prefix'];
$study->get_study_data($study_id);

// for new studies, design is empty so we affect a default design
if (!isset($study->design_study_global)) {
	$study->design_study_global = array(
		'group0' => '',
		'group1' => '',
		'allocation_odds' => '',
		'stratum_weight' => '',
		'unset_flag' => true);
	
}

$smarty->assign('study_globals',$study->design_study_global);

// checking that study is in design mode
/*
if (!$study->design_mode) {
	$page = array(
		'title' => 'Message - '.$study->acronym,
		'includemenu' => true,
		'includebanner' => true,
		'includecss' => true);
	$smarty->assign('text', 'Study is not in design mode. Aborting');
	try {
		$smarty->display('message.tpl');
	} catch (Exception $e) {
		mydie ("unable to render page <br />\n".$e->getMessage());
	}
	die();
}
*/

if (!empty($_POST)) {
	// sanitize
	$group0 = (float) $_POST['group0'];
	$group1 = (float) $_POST['group1'];
	$stratum_weight = (float) $_POST['stratum_weight'];
	$study->set_study_globals($group0,$group1,$stratum_weight);
	if ($study->save_design())
		$smarty->assign('text', 'Saved new globals. <a href="study_design.php?study_id='.$study->study_id.'">Proceed</a>');
	else
		$smarty->assign('text', 'Error saving new globals. <a href="study_design.php?study_id='.$study->study_id.'">Proceed</a>');
	$page = array(
		'title' => 'Message - '.$study->acronym,
		'includemenu' => true,
		'includebanner' => true,
		'includecss' => true);

	try {
		$smarty->display('message.tpl');
	} catch (Exception $e) {
		mydie ("unable to render page <br />\n".$e->getMessage());
	}
	die();	
}


$page = array(
	'title' => 'Design '.$study->acronym,
	'includemenu' => true,
	'includebanner' => true,
	'includecss' => true);

	
// data to be displayed
$smarty->assign('study_data', $study->study_data);
$smarty->assign('stratification', $study->design);
$smarty->assign('study', $study);

try {
	$smarty->display('study_design.tpl');
} catch (Exception $e) {
	mydie ("unable to render page <br />\n".$e->getMessage());
}