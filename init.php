<?php

// pGMART install script

// loads configuration variables
include_once ("config/config.php");
include_once ("core.functions.php");
include_once ("core.authpdo.class.php");
include_once ("core.study_GMART.class.php");

$gmart_root = dirname(__FILE__).DIRECTORY_SEPARATOR;

// starting sessions
session_start();

try {
    // preparing dsn for database connection
    $dsn = sprintf("mysql:host=%s;charset=UTF8",$_DB['server']);
    
    $pdo = new PDO(
		$dsn, 
		$_DB['user'],
		$_DB['pass'], 
		array( 
			PDO::ATTR_PERSISTENT => false,
        ));
} catch (PDOException $e) {
    $txt = "Failed connection error!: " . $e->getMessage() . "<br/>";
    mydie($txt);
}

// trying to select database
try {
    $stmt = $pdo->prepare("USE ".$_DB['database']);
	$result = $stmt->execute();
} catch (PDOException $e) {
    $txt = "Failed executing database selection: " . $e->getMessage() . "<br/>";
	mydie($txt);
}

if (!$result) {
	$txt = "Failed selecting database. Please create databse matching config parameter";
	mydie($txt);
}

// authenticating
$auth = new AuthPDO();
$auth->pre_salt = $_DB['pre_salt']; 
$auth->post_salt = $_DB['post_salt'];
$auth->table_prefix = $_DB['table_prefix'];
$auth->pdo = $pdo;


// trying to read the studies table
try {
	$sql = sprintf("SELECT * FROM %sstudies ORDER BY study_acronym ASC",
		$_DB['table_prefix']);
    $stmt = $pdo->prepare($sql);
	$result = $stmt->execute();
} catch (PDOException $e) {
    $txt = "Failed executing studies request at connection level: " . $e->getMessage();
	mydie($txt);
}

if (!$result) {
	$txt = "Failed executing studies request at request level ".$sql;
	mydie($txt);
}

$stmt->setFetchMode(PDO::FETCH_OBJ);
foreach($stmt as $study){
    $studies[$study->study_id] = $study;
}


define('SMARTY_DIR', $gmart_root.'lib/smarty/');
require_once (SMARTY_DIR . 'Smarty.class.php');


$smarty = new Smarty();
$smarty->template_dir 	= $gmart_root.'templates/';
$smarty->compile_dir 	= $gmart_root.'cache/smarty/templates_c/';
$smarty->config_dir 	= $gmart_root.'configs/';
$smarty->cache_dir 		= $gmart_root.'cache/smarty/cache/';
$smarty->caching 		= 0;
$smarty->cache_lifetime = 300; // 5 minutes

// default page parameters
$page = array(
	'title' => 'Default title',
	'includemenu' => true,
	'includebanner' => true,
	'includecss' => true);
	
// may be altered before rendering of a particular page
$smarty->assignByRef("page",$page);

// pseudorandom number generator
include_once ('core.random_generator.class.php');
$random = NULL;
try {
	$random = new rg_hardware();
} catch (Exception $e) {
	// failed loading hardware. trying with openssl
}

if (!is_object($random))
{
	try {
		$random = new rg_openssl();
	} catch (Exception $e) {
		// failed loading openssl. trying with openssl
	}
}

if (!is_object($random))
{
	try {
		$random = new rg_mtrand();
	} catch (Exception $e) {
		// failed loading openssl. trying with openssl
	}
}

$random->min = 0;
$random->max = 100;


// usage $random->get_ranged() returns an integer within range);
