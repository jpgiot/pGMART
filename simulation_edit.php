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
include_once ('core.simulation_GMART.class.php');

// checking that a user is authenticated
if (!$auth->getAuthStatus()){
	//echo "<a href='login.php'>Login</a>";
	//mydie ('Not connected.');
	include_once('login.php');
	die();
}

var_dump($_REQUEST);

// fetching study based on given id
$study_id = false;
if (isset($_REQUEST['study_id']))
	$study_id = (int) $_REQUEST['study_id'];

// fetching study based on given id
$sim_id = false;
if (isset($_REQUEST['sim_id']))
	$sim_id = (int) $_REQUEST['sim_id'];

// or we create a new sim for $study_id or we edit the sim $sim_id

if ((false !== $study_id) && (0 >= $study_id)) mydie ('invalid positive integer '.__LINE__);

if ((false !== $sim_id) && (0 >= $sim_id)) mydie ('invalid positive integer '.__LINE__);

if ($sim_id && $study_id) {
	Throw new ErrorException('impossible to create and edit a simulation at the same time',
		0,0,__FILE__,__LINE__);
}

// creating the simulations collection object
$simulations = new simulations();
$simulations->pdo = $pdo;
$simulations->table_prefix = $_DB['table_prefix'];	


// creating the simulation object
$new_simulation = false;
$simulation = false;

if ($study_id)  {
	$new_simulation = true;
	// new one
	$simulation = New simulation_GMART();
	$simulation->sim_id = false;
	$simulation->pdo = $pdo;
	$simulation->table_prefix = $_DB['table_prefix'];	
	$simulation->sim_name = '';	
	$simulation->name = '';	
}	

if ($sim_id) {
	$simulation = New simulation_GMART();
	$simulation->pdo = $pdo;
	$simulation->table_prefix = $_DB['table_prefix'];
	$simulation->current_sim_id = $sim_id;
	// fetch database record and import design
	if (!$simulation->get_simulation_data())
		throw new SimulationException('Error fetching simulation data');
	// if editing, the study id
	$study_id = $simulation->sim_study_id;
	
	var_dump($simulation);
}

// creating study object and fetching data from database
$study = new study_GMART();
$study->pdo = $pdo;
$study->table_prefix = $_DB['table_prefix'];
$study->get_study_data($study_id);
$study->set_random_generator($random);

// let the simulation object access the study parameters
$simulation->study = $study;

$smarty->assign('study', $study);

// fetching required inputs
$inputs = $study->describe_inputs();
$smarty->assign('inputs', $inputs);

$smarty->assign('simulation', $simulation);
$smarty->assign('new_simulation', $new_simulation);

//var_dump($study);

// if empty form is displayed
if (empty($_POST)) {

	// setting default or current values for form display
	
	if ($sim_id) {
		// fetching already saved values
		$p['simulation_name'] = 	$simulation->name;
		$p['simulation_patients'] = $simulation->parameters['patients'];
		$p['simulation_runs'] = $simulation->parameters['runs'];
		$p['simulation_weights_inherit'] = $simulation->parameters['weights_inherit'];
		$p['simulation_overall_treatment_weight'] = $simulation->parameters['weights']['global']['overall'];
		$p['simulation_stratum_weight'] = $simulation->parameters['weights']['global']['stratum'];
		foreach ($inputs as $input_name => $possible_values) {
			var_dump( $possible_values);
			$p['simulation_stratification_weight_'.$input_name] = $simulation->parameters['weights']['stratifications'][$input_name];
			foreach ($possible_values as $i => $i_name)
				$p['simulation_stratification_'.$input_name.'_'.$i] = $simulation->parameters['input_frequencies'][$input_name][$i];
		}
	}
	else {
		// default values;
		$p['simulation_name'] = '';
		$p['simulation_patients'] = '';
		$p['simulation_runs'] = '';
		$p['simulation_weights_inherit'] = '';
		$p['simulation_overall_treatment_weight'] = '';
		$p['simulation_stratum_weight'] = '';
		foreach ($inputs as $input_name => $possible_values) {
			$p['simulation_stratification_weight_'.$name] = '';
			$p['simulation_stratification_'.$name.'_'.$possible_values] = '';
		}
	}
	
	var_dump($p);
	
	$smarty->assign('p',$p);
	
	$page = array(
		'title' => 'Simulation for '.$study->acronym,
		'includemenu' => true,
		'includebanner' => true,
		'includecss' => true);
	//$smarty->assign('text', 'Study is not in design mode. Aborting');
	try {
		$smarty->display('simulation_edit.tpl');
	} catch (Exception $e) {
		mydie ("unable to render page <br />\n".$e->getMessage());
	}
	die();
}

// for new simulations
$simulation->current_study_id = $study_id;

// creating json design
$simulation->set_name($_REQUEST['simulation_name']);
$simulation->set_patients((int) $_REQUEST['simulation_patients']);
$simulation->set_runs((int) $_REQUEST['simulation_runs']);

	
	// function set_input_frequencies($array){
	// function set_weights($array){
	// function set_weights_inherit(int)
	
$weights_global = array (
	'overall' => (float) $_REQUEST['simulation_stratum_weight'],
	'stratum' => (float) $_REQUEST['simulation_overall_treatment_weight']);

// checking stratum for weights and frequency
foreach ($inputs as $name => $possible_values) {
	//var_dump ($possible_values);
	
	// stratification weight
	$weights_stratification[$name] = (float) $_REQUEST['simulation_stratification_weight_'.$name];
	
	// stratification frequencies
	foreach ($possible_values as $p => $p_name) {
		$key = 'simulation_stratification_'.$name.'_'.$p;
		echo "input for $name $p_name is ".$_POST[$key]."<br />";
		//$inclusion_parameters[$name] = (int) $_POST[$key];
		$population[$name][$p] = $_REQUEST[$key];
		@$frequency_check[$name] += $_REQUEST[$key];
	}
}



// $parameters [weights] = array (
//		'global' => array (
//			'overall' => ,
//			'stratum' => )
// 		'stratifications' => array(
//			'strat-a' => ,
//			'strat-b' => );



foreach ($frequency_check as $name => $total) {
	echo "$name:$total ";
	if (1 == $total) echo "ok";
	else {
		echo "error, not 100% frequency";
		$frequency_warning = true;
	}
	echo "<br />";
}

$simulation->set_input_frequencies($population);
$simulation->set_weights(array(
	'global' => $weights_global,
	'stratifications' => $weights_stratification));

$simulation->set_weights_inherit((int) $_REQUEST['simulation_weights_inherit']);

if ($simulation->save_parameters())
	$message = 'OK, parameters saved for the simulation';
else
	$message = 'Error in the process';


$smarty->assign('text', $message);

try {
	$smarty->display('message.tpl');
} catch (Exception $e) {
	mydie ("unable to render page <br />\n".$e->getMessage());
}