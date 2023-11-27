<?php

// GMAR install script

// if this var is set to true the script is allowed to run
//$install_mode = false;
$install_mode = true;


if (!$install_mode) die ();

// loads configuration variables
include_once ("config/config.php");
include_once ("core.functions.php");
include_once ("core.authpdo.class.php");

echo "Connecting to server...";

try {
    // preparing dsn for database connection
    $dsn = sprintf("mysql:host=%s;charset=UTF8",$_DB['server']);
    
    $pdo = new PDO($dsn, $_DB['user'],$_DB['pass'], array( 
        PDO::ATTR_PERSISTENT => false,
        ));
} catch (PDOException $e) {
    $txt = "Failed connection error!: " . $e->getMessage() . "<br/>";
    mydie($txt);
}
echo "Done.<br />\n";

echo "Selecting database...";
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
echo "Done.<br />\n";


echo "Preparing authentication tables...";
// creating tables for authenticating if they do not exists
$auth = new AuthPDO();
$auth->pre_salt = $_DB['pre_salt']; 
$auth->post_salt = $_DB['post_salt'];
$auth->table_prefix = $_DB['table_prefix'];
$auth->pdo = $pdo;
try {
	$result = $auth->createTables();
}
catch (Exception $e){
	$txt = "Failed preparing authentification tables ". $e->getMessage();
	mydie($txt);
}

if (!$result) {
	$txt = "Something went wrong while preparing auth table";
	mydie($txt);
}

echo "Done.<br />\n";


echo "Checking admin user...";

try {
	$result = $auth->userExists('admin');
}
catch (Exception $e){
	$txt = "Failed looking for admin user ". $e->getMessage();;
	mydie($txt);
}

if (!$result){
	echo "<br />\n&nbsp;&nbsp;&nbsp;&nbsp;Creating admin user...";
	
	try {
		$result = $auth->createUser('admin','admin','Administrator');
	}
	catch (Exception $e){
		$txt = "Failed creating admin user ". $e->getMessage();;
		mydie($txt);
	}
}

echo "Done.<br />\n";


echo "Preparing study tables...";
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
echo "Done.<br />\n";

echo "Gathering studies...<ul>";
$stmt->setFetchMode(PDO::FETCH_INTO, new study);
foreach($stmt as $study){
    echo '<li>'.$study->study_acronym.'</li>';
} 
echo "</ul>Done.<br />\n";

echo "<b>Server is up and running</b>\n";