 <?php
/**
 * Study design class
 *
 * Author: Jean-Philippe Giot jp@giot.net
 * License: GPL
 */
 
class SimulationException extends ErrorException {
}
	
// to run, the simulation requires parameters, encoded in a single array stored in database
// see following example
	
// $parameters [input_frequencies] = array ('stratification' => $weight, ...);
// $parameters [weights] = array (
//		'global' => array (
//			'overall' => ,
//			'stratum' => )
// 		'stratifications' => array(
//			'strat-a' => ,
//			'strat-b' => );
// $parameters [weights_inherit] = boolean;
// $parameters [patients] = integer;
// $parameters [runs] = integer;
			
class simulation_GMART extends study {



    function __construct() {

    }
	

	
	function get_simulation_data() {
		
		if (!$this->current_sim_id){
			throw new SimulationException ('no sim id',
				0,0,__FILE__,__LINE__);
		}
			
		
		// returning cache
		if (isset($this->cached) && $this->cached)
			return $this;
		
		
		$this->cached = true;
		$sql = sprintf(
			'SELECT * FROM `%1$ssimulations` WHERE sim_id=:simid ',
			$this->table_prefix);
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':simid', $this->current_sim_id, PDO::PARAM_INT);		
		
		if (!$stmt->execute()){
			throw new SimulationException ('failed fetching simulation data',
				0,0,__FILE__,__LINE__);
		}
	
		$simulation = $stmt->fetch(PDO::FETCH_OBJ);
		
		$this->name = $simulation->sim_name;
		$this->sim_parameters = $simulation->sim_parameters;
		$this->sim_study_id = $simulation->sim_study_id;
		$this->sim_id = $simulation->sim_id;
		
		// result of get simulation
		$ret = false;
		// importing json data
		if ('' != $this->sim_parameters){
			if ($this->set_parameters($this->sim_parameters)) $ret = true;
		}
		else {
			// no design set yet, new simulation
			 $ret = true;
		}
		
		// caching
		//$this->simulations[$simulation->sim_id] = $simulation;
		
		echo "finished importing simulation";
		//var_dump ($this->simulations[$simulation->sim_id]);
		
		if ($ret)
			return $this;
		return false;
	}
	
	function set_parameters($json) {

		// reseting current design
		$this->parameters = false;
		
		$this->log[] = 'importing design of '.strlen($json)." bytes";
		
		// if no design to import, 
		if ('' == $json) return true;
			

		// importing json structure : 2 levels array
		$checkvalidity = json_decode ($json, true);
		//$this->log[] = 'json'.print_r($checkvalidity,true);
		
		if (empty ($checkvalidity)){
			throw new SimulationException ('impossible to import json simulation design',
				0,0,__FILE__,__LINE__);
		}
			
		foreach ($checkvalidity as $base => $parameters){
		
			// $parameters [name] = 'simulation name'
			// $parameters [input_frequencies] = array ('stratification' => $weight);
			// $parameters [weights] = array (
			//		'global' => array (
			//			'overall' => ,
			//			'stratum' => )
			// 		'stratifications' => array(
			//			'strat-a' => ,
			//			'strat-b' => );
			// $parameters [weights_inherit] = boolean;
			// $parameters [patients] = integer;
			// $parameters [runs] = integer;
			
			//var_dump($base);
			//var_dump($parameters);
			
			if ('patients' == $base) 			$this->set_patients($parameters);
			if ('runs' == $base) 				$this->set_runs($parameters);
			if ('input_frequencies' == $base) 	$this->set_input_frequencies($parameters);
			if ('weights_inherit' == $base) 	$this->set_weights_inherit($parameters);
			if ('weights' == $base) 			$this->set_weights($parameters);
		}
		
		//$this->log[] = print_r($this->design);
		
		$this->log[] = 'finished importing design';
		return true;
	}
	
	function set_name($name){
		$this->name = $name;
	}
	
	function set_patients( $int){
		$this->parameters['patients'] = $int;
	}
	function set_runs( $int){
		$this->parameters['runs'] = $int;
	}
	
	function set_input_frequencies($array){
		$this->parameters['input_frequencies'] = $array;
	}	
	
	function set_weights_inherit( $int){
		if ($int)
			$this->parameters['weights_inherit'] = 1;
		else
			$this->parameters['weights_inherit'] = 0;
	}
	
	function set_weights($array){
		$this->parameters['weights'] = $array;

	}	
	
	function export_parameters() {
		return json_encode($this->parameters);
	}

	function save_parameters() {
	
		if (!isset($this->current_sim_id))
			$this->current_sim_id = false;
			
		if (false == $this->current_sim_id) {
			// creating a new simulation
			echo "creating a new simulation";
			if (false == $this->current_study_id) {
				throw new SimulationException ('no study to save simulation',0,0,__FILE__,__LINE__);
			}
			
			$sql = sprintf(
				'INSERT INTO `%1$ssimulations` '.
				'(`sim_study_id`, `sim_parameters`, `sim_name`) '.
				' VALUES '.
				'( :simsid , :param , :name); ',
				$this->table_prefix);
			$stmt = $this->pdo->prepare($sql);
			print_r($pdo->errorInfo());
			$stmt->bindValue(':simsid', $this->current_study_id, PDO::PARAM_INT);
		}
		else {
			echo "updating a simulation for #".$this->current_sim_id;
			$sql = sprintf(
				'UPDATE `%1$ssimulations` '.
				'SET sim_parameters=:param, '.
				'sim_name=:name '.
				'WHERE sim_id=:simid',
				$this->table_prefix);
			print_r($sql);
			$stmt = $this->pdo->prepare($sql);
			// print_r($pdo->errorInfo());
			$stmt->bindValue(':simid', $this->current_sim_id, PDO::PARAM_INT);
		}
		
        $stmt->bindValue(':param', $this->export_parameters(), PDO::PARAM_STR);
        $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
        
		$stmt->execute();
		return true;
		
        if ($stmt->execute()) {
			return true;
		}	
		throw new SimulationException ('error '.implode ('<br />',$this->pdo->errorInfo()),0,0,__FILE__,__LINE__);
		return false;
	}
	
	function init_random() {
		
	}	
	
	/** 
	 * name of the stratification
	 * random number drawn
	 */
	function get_stratification_group($name,$random_number) {
		
		// will always be the first group
		if (0 == $random_number) return 0;
		
		if (!isset($this->stratification_limits[$name])) {
			// creating the stratification limits. adding the percentage from group 0
			$current_stack = 0;
			foreach ($this->parameters['input_frequencies'][$name] as $i => $p_name) {
				$this->stratification_limits[$name][$i][0] = $current_stack;
				$this->stratification_limits[$name][$i][1] = $current_stack + $this->parameters['input_frequencies'][$name][$i];
				$current_stack += $this->parameters['input_frequencies'][$name][$i];
			}
		}

		foreach ($this->stratification_limits[$name] as $i => $limits){
			if (($random_number > $limits[0]) && ($random_number <= $limits[1]))
				return $i;
		}
		die ('allocation error random number is '.$random_number);
	}
	
	function get_Random_Stratification($name){
	
		$precision = 1e3;
		
		// setting required limits for random number generation
		$this->random->min = 0;
		$this->random->max = $precision;
		//var_dump($this->random->get_ranged());
		// getting random number between 0 and $precision, converting it to a percent
		$randomize_raw = $this->random->get_ranged() / $precision;
		
		$this->last_random_number = $randomize_raw;
		
		return $this->get_stratification_group($name,$randomize_raw);
	}
}


class simulations {

	function get_associated_simulations() {
	
		if (!$this->study_id) {
			throw new SimulationException(
				'Study design error', 
				$errno = 0, 
				$severity = 0, 
				__FILE__, 
				__LINE__);
		}
		
		$sql = sprintf(
			'SELECT * FROM `%1$ssimulations` WHERE sim_study_id=:sid ',
			$this->table_prefix);
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':sid', $this->study_id, PDO::PARAM_INT);		
		$stmt->execute();
		
		// creating or erasing previous data
		$this->simulations = array();
		while ($simulation = $stmt->fetch(PDO::FETCH_OBJ))
		{
			$this->simulations[$simulation->sim_id] = $simulation;
		}
		
		return true;
	}
	
	function get_all() {
		return $this->simulations;
	}
}
