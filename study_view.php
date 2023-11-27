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

// creating study object
$study = new study_GMART();
$study->pdo = $pdo;
$study->table_prefix = $_DB['table_prefix'];
$study->get_study_data($study_id);

// var_dump($study);
//var_dump($study->design);	

$page = array(
	'title' => 'Study '.$study->acronym,
	'includemenu' => true,
	'includebanner' => true,
	'includecss' => true);

// looking for associated simulations
include_once ("core.simulation_GMART.class.php");
$simulations = new simulations();
$simulations->study_id = $study_id;
$simulations->pdo = $pdo;
$simulations->table_prefix = $_DB['table_prefix'];
$simulations->get_associated_simulations();
$smarty->assign('simulations', $simulations->get_all());
	
// data to be displayed
$smarty->assign('study_data', $study->study_data);
$smarty->assign('study', $study);
$smarty->assign('stratification', $study->design);

$smarty->assign('log', implode ($study->log,"<br />\n"));

try {
	$smarty->display('study_view.tpl');
} catch (Exception $e) {
	mydie ("unable to render page <br />\n".$e->getMessage());
}