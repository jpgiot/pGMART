<?php

/** 
 * pGMART
 *
 * PHP implementation of 
 * Generalized Method for Adaptive Randomization in Trials
 *
 * Author: Jean-Philippe Giot jp@giot.net
 * Licence: 
 */

include_once ("init.php");

// checking that a user is authenticated
// then redirecting to index page
if ($auth->getAuthStatus()){
	// basically reads $_SESSION.login
	header('Location: index.php');
}
else {
	//var_dump($_COOKIE);
}

$page = array(
	'title' => 'Login',
	'includemenu' => false,
	'includebanner' => false,
	'includecss' => true);

if (isset($_POST) && !empty($_POST)){
	// trying to log in
	$untrusted_login = $_POST['username'];
	$untrusted_pass = $_POST['password'];
	if (!$untrusted_login && !$untrusted_pass){
		mydie('Impossible to log user. Please hit back button');
	}
	// user data is sent as is to the auth object
	// uses prepared statements
	if (!$auth->login($untrusted_login, $untrusted_pass)){
		mydie('Impossible to log user. <a href="login.php">Click here to retry</a>');
	}
	// successfully logged in the 3 lines before. redirecting to the page index
	header('Location: index.php');
}	

	
try {
	$smarty->display('login.tpl');
} catch (Exception $e) {
	mydie ("unable to render page <br />\n".$e->getMessage());
}