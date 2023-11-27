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


// creating study object and fetching data from database
$study = new study_GMART();
$study->pdo = $pdo;
$study->table_prefix = $_DB['table_prefix'];


//$study->get_study_data($study_id);
//$study->set_random_generator($random);

$smarty->assign('study', $study);


// checking that study is in design mode
if (empty($_POST)) {
	$page = array(
		'title' => 'New study',
		'includemenu' => true,
		'includebanner' => true,
		'includecss' => true);
	//$smarty->assign('text', 'Study is not in design mode. Aborting');
	try {
		$smarty->display('study_new.tpl');
	} catch (Exception $e) {
		mydie ("unable to render page <br />\n".$e->getMessage());
	}
	die();
}

var_dump($_POST);

try {
	$result = $study->new_study($_POST['study_acronym'], $_POST['study_name']);
}
catch (Exception $e) {
	$result = false;
}


if (false === $result) {
	$message = "error ". $e->getMessage();
} else {
	$message = "OK new study ". $_POST['study_acronym']. " has been created";
}

//echo "all inclusions before this one";
//var_dump($study->all_inclusions);

$message .= "<p><a href='index.php'>Back to studies list</a></p>";

$smarty->assign('text', $message);

try {
	$smarty->display('message.tpl');
} catch (Exception $e) {
	mydie ("unable to render page <br />\n".$e->getMessage());
}
die();