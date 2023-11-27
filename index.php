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

$page['title'] = 'Studies list';

// we affect studies list fetch in init script to be displayed
$smarty->assign('studies', $studies);

try {
	$smarty->display('index.tpl');
} catch (Exception $e) {
	mydie ("unable to render page <br />\n".$e->getMessage());
}