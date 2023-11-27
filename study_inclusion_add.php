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

// creating study object and fetching data from database
$study = new study_GMART();
$study->pdo = $pdo;
$study->table_prefix = $_DB['table_prefix'];
$study->get_study_data($study_id);
$study->set_random_generator($random);

$smarty->assign('study', $study);

// fetching required inputs
$inputs = $study->describe_inputs();

$smarty->assign('inputs', $inputs);

//var_dump($study);

// checking that study is in design mode
if (empty($_POST)) {
	$page = array(
		'title' => 'New inclusion - '.$study->acronym,
		'includemenu' => true,
		'includebanner' => true,
		'includecss' => true);
	//$smarty->assign('text', 'Study is not in design mode. Aborting');
	try {
		$smarty->display('study_inclusion_add.tpl');
	} catch (Exception $e) {
		mydie ("unable to render page <br />\n".$e->getMessage());
	}
	die();
}

// checking that all stratum have been filled
foreach ($inputs as $name => $possible_values) {

	echo "input for $name is ".$_POST['stratification_'.$name]."<br />";
	
	$inclusion_parameters[$name] = (int) $_POST['stratification_'.$name];
}
echo "inclusion parameters";
var_dump($inclusion_parameters);

// performing the inclusion
$treatment_group = $study->new_inclusion($inclusion_parameters);

if (false === $treatment_group) {
	$message = "error ". print_r($treatment_group,true);
} elseif ((0 === $treatment_group) || (1 === $treatment_group)) {
	$message = "<p>Patient is affected to treatement group $treatment_group.</p>" .
		"<p>The odds for affectation to group 0 were ".$study->last_inclusion['odds'].
		" and the random number is ".$study->last_inclusion['drawn'];
} else {
	$message = "error ". print_r($treatment_group,true);
}

//echo "all inclusions before this one";
//var_dump($study->all_inclusions);

$smarty->assign('text', $message);

try {
	$smarty->display('message.tpl');
} catch (Exception $e) {
	mydie ("unable to render page <br />\n".$e->getMessage());
}
die();