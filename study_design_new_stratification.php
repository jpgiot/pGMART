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

// user request : checking if default values are required
if (isset($_GET['default']))
	$default = 1;
else
	$default = 0;
$smarty->assign('default', $default);

// creating study object and fetching data from database
$study = new study_GMART();
$study->pdo = $pdo;
$study->table_prefix = $_DB['table_prefix'];
$study->get_study_data($study_id);

//var_dump($study);

// checking that study is in design mode
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

if (!isset($_REQUEST['new_stratification_name'])) {

	// display form if no data sent
	$page = array(
	'title' => 'New stratification for '.$study->acronym,
	'includemenu' => true,
	'includebanner' => true,
	'includecss' => true);

	// data to be displayed
	$smarty->assign('study_data', $study->study_data);
	//$smarty->assign('study_design', $study->designToHTML());
	$smarty->assign('study', $study);

	try {
		$smarty->display('study_design_new_stratification.tpl');
	} catch (Exception $e) {
		mydie ("unable to render page <br />\n".$e->getMessage());
	}
	die();
}

//var_dump($_REQUEST);

// sanitizing vars is needed here
$new_stratification_name		= $_REQUEST['new_stratification_name'];
$new_stratification_weight		= (float) $_REQUEST['new_stratification_weight'];
for ($i=0;$i<10;$i++)
{
	if ( '' != $_REQUEST['new_stratification_option_'.$i])
		$values[$i] = $_REQUEST['new_stratification_option_'.$i];
}

try {
$r = $study->add_stratification(
	$new_stratification_name,
	$new_stratification_weight,
	$values);
	
} catch (Exception $e) {
	mydie ("unable to add a new stratification <br />\n".$e->getMessage());
}

if (!$r) mydie ("failed to add a new stratification <br />\n");

var_dump($study->design);

var_dump($study->export_design());

// saving new design
if ($study->save_design()) {

	$smarty->assign('text', 
		'Study new design is saved, adding one stratification. '.
		'<a href="study_design.php?study_id='.$study->study_id.'">Return to study design page</a>');
}

try {
	$smarty->display('message.tpl');
} catch (Exception $e) {
	mydie ("unable to render page <br />\n".$e->getMessage());
}
die();