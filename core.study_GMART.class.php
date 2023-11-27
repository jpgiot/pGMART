 <?php
/**
 * Study design class
 *
 * Author: Jean-Philippe Giot jp@giot.net
 * License: GPL
 */
 
class StudyException extends ErrorException {
}
	
class study_GMART extends study {



    function __construct() {
		$this->design_warning_treatment = false;
    }
	
	/**
	 */
	public function get_study_data($sid){
		
		$this->study_id = (int) $sid;
		$sql = sprintf(
			'SELECT * FROM `%1$sstudies` WHERE study_id=:sid;',
			$this->table_prefix);
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':sid', $sid, PDO::PARAM_INT);	
		try {
			$stmt->execute();
		} catch (Exception $e) {
			throw new ErrorException ('failed fetching study data '.$e->getMessage());
		}
		$this->study_data = $stmt->fetch(PDO::FETCH_OBJ);
		
		//var_dump($this->study_data);
		
		$this->name = $this->study_data->study_name;
		$this->acronym = $this->study_data->study_acronym;
		$this->design_mode = $this->study_data->study_design_mode;
		
		// importing design as an organized array
		$this->import_Design($this->study_data->study_design);
		
		return true;
	}

    /**
     */
    public function import_Design($json_design) {
	
		// reseting current design
		$this->design = false;
		$this->design_warning_treatment = false;
		
		$this->log[] = 'importing design of '.strlen($json_design)." bytes";
		
		if ('' == $json_design){
			// no design yet so set up the minimal design : 
			// treatment 0 (A) or 1 (B) by fall of
			$this->log[] = 'nothing designed yet';
		} else {
			// importing json structure : 2 levels array
			$checkvalidity = json_decode ($json_design, true);
			$this->log[] = 'json'.print_r($checkvalidity,true);
			if (!empty ($checkvalidity)){
				// checking validity
				
				foreach ($checkvalidity as $stratum_name => $options){
					$this->log[] = '- stratum '.$stratum_name;
					
					if ('__study_globals__' == $stratum_name)
					{
						// to not divide by 0
						if ($options['group1']) 
							$allocation_odds = $options['group0'] / $options['group1'];
						else
							$allocation_odds = 'incalculable';
							
						$this->design_study_global = array (
							'group0' => $options['group0'],
							'group1' => $options['group1'],
							'allocation_odds' => $allocation_odds,
							'stratum_weight' => $options['stratum_weight']);
							continue;
					}					
					
					$this->log[] = '- weight '.$options['weight'];
					
					$stratum_values = $options['values'];
				
					if (!is_array($stratum_values)) {
						throw new StudyException ('Study design error', $errno, 0, __FILE__, __LINE__);
					}
					
					//if (!$this->values_are_valid($stratum_values))
					//	return false;
					
					$this->log[] = '-- '.print_r($stratum_values,true);
					

					
					// adding to the structure
					$this->add_stratification($stratum_name,$options['weight'],$stratum_values);
				}
			}
		}
		
		if (!isset($this->design['treatment'])) {
			// emit a warning, by concept a trial needs a treatment stratum
			$this->design_warning_treatment = true;
		}
		
		//$this->log[] = print_r($this->design);
		
		$this->log[] = 'finished importing design';
		return true;
    }
	
	function export_design(){
		return json_encode ($this->design);
	}
	
	function check_study_id(){
		if (!isset($this->study_id))	return false;
		if (!is_int($this->study_id))	return false;
		if ($this->study_id <= 0)	return false;
		return true;
	}
	
	function save_design(){
	
		if (!$this->check_study_id()){
			throw new StudyException ('Cannot save design of unkown study', $errno, 0, __FILE__, __LINE__);
			return false;
		}
		
		// preparing the study globals to be added to the structure
		$this->design['__study_globals__'] = $this->design_study_global;
		
		
		$sql = sprintf(
			'UPDATE `%1$sstudies` '.
			'SET study_design=:design '.
			'WHERE study_id=:sid',
			$this->table_prefix);
			
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':design', $this->export_design(), PDO::PARAM_STR);
        $stmt->bindValue(':sid', $this->study_id, PDO::PARAM_STR);
        if ($stmt->execute()) {
			// removing study globals
			unset ($this->design['__study_globals__']);
			return true;
		}
		// removing study globals
		unset ($this->design['__study_globals__']);		
		return false;
	}
	
	function new_study($study_acronym,$study_name) {
	
		// 2 steps :  create a new table and insert studies data in table
		
		$sql = sprintf(
			'CREATE TABLE IF NOT EXISTS `%1$sstudy_%2$s%3$s` ('.
			'`inclusion_id` int(11) NOT NULL AUTO_INCREMENT, '.
			'`patient_identifier` varchar(15) COLLATE utf8_unicode_ci NOT NULL, '.
			'`stratification` text COLLATE utf8_unicode_ci NOT NULL, '.
			'`treatment_group` int(11) NOT NULL, '.
			'`inclusion_log` text COLLATE utf8_unicode_ci NOT NULL, '.
			'PRIMARY KEY (`inclusion_id`) '.
			') ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;',
			$this->table_prefix,
			$study_acronym,$this->simulation_suffix);
			
		$stmt = $this->pdo->prepare($sql);	
		$stmt->execute();
        if (!$stmt->execute())
			throw new ErrorException('Failed creating new study table '.$sql, $errno, 0, __FILE__, __LINE__);		
		
		if ($this->simulation_suffix) return true;
		
		// we insert the official result table if not a simulation
		$sql = sprintf(
			'INSERT INTO `%1$sstudies` '.
			'(`study_acronym`, `study_name`, '.
			'`study_design_mode`, `study_design`) '.
			' VALUES '.
			'( :acro , :name , \'1\', \'\'); ',
			$this->table_prefix);
			
			var_dump($sql);
	
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':acro', $study_acronym, PDO::PARAM_STR);		
		$stmt->bindValue(':name', $study_name, PDO::PARAM_STR);		
		
        if (!$stmt->execute())
			throw new ErrorException('Failed inserting new study '.$sql, 0, 0, __FILE__, __LINE__);		
				
		
		
		
		
		return true;
	
	
	}
	
	function values_are_valid($values) {
	
		// ok is an array accepted keys are 0 and 1
		/*
		if (!isset($values[0])) {
			throw new StudyException ('Study design error', $errno, 0, __FILE__, __LINE__);
			return false;
		}
		if (!isset($values[1])) {
			throw new StudyException ('Study design error', $errno, 0, __FILE__, __LINE__);
			return false;
		}
		*/
		return true;
	}

    /**
     * @param str $login
     * @param str $pass
     */
     
    function add_stratification($name,$weight,$values) {
	
		// checking that stratum does not already exists
		if (isset($this->design[$name])){
			throw new StudyException ('Stratification already exists', $errno, 0, __FILE__, __LINE__);
		}
		
		//if (!$this->values_are_valid($values))
		//	return false;
		
		$this->design[$name] = array('weight'=> $weight,'values' => $values);
		return true;
    }
	
	/** 
	 *
	 */
	function set_study_globals($group0,$group1,$stratum_weight){
	
		$this->design_study_global = array (
			'group0' => $group0,
			'group1' => $group1,
			'stratum_weight' => $stratum_weight);
	
	}
	
	function describe_inputs()
	{
		// we have to return an array of items
		// key : stratum name
		// values in an array
		$return = array();
		//var_dump($this->design);
		foreach ($this->design as $name => $options) {
			if ('treatment' === $name) continue;
			$return[$name] = $options['values'];
		}
		
		return $return;		
	}
	
	function set_random_generator($object) {
		$this->random = $object;
	}
	
	function set_random_limits($min,$max) {
		$this->random->max = $max;
		$this->random->min = $min;
	}
	
	function get_inclusions() {
	
		$sql = sprintf(
			'SELECT * FROM `%1$sstudy_%2$s%3$s`',
			$this->table_prefix, $this->acronym,$this->simulation_suffix);
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':sid', $this->study_id, PDO::PARAM_INT);		
		$stmt->execute();
		
		$this->inclusion_table = array();
		$this->strata = array();
		$this->all_inclusions = array();
		while ($inclusion = $stmt->fetch(PDO::FETCH_OBJ))
		{
			//var_dump($inclusion);
			$this->all_inclusions[$inclusion->inclusion_id] = $inclusion;
			foreach ($this->design as $stratification_name => $stratification_options)
			{
				@$this->inclusion_table[$stratification_name][$inclusion->treatment_group] ++;
			}
			@$this->strata[$inclusion->stratification][$inclusion->treatment_group] ++;
			
		}
		//echo "inclusion table of stratifications";
		//var_dump($this->inclusion_table);
		
		//echo "strata level";
		//var_dump($this->strata);
		
		$this->total_inclusions = count($this->all_inclusions);

	}
	
	/**
	 * $parameters is an array of stratification variables of the patient
	 */ 
	function new_inclusion($parameters)
	{
		//performing the inclusion.
		$last_inclusion_log = array();
		
		// sorting by alpha name the parameters
		ksort($parameters);
		
		// fetch all inclusions and prepare result tables for computes
		$this->get_inclusions();
		
		$last_inclusion_log[] = "the number of patients already included is ".count($this->all_inclusions);
		
		$last_inclusion_log[] = "computing differences and weigted sum";
		
		// allocation odds : size of group 0 respectively to group 1. 
		// if you don't know, start with 1 and 1
		$AO = $this->design_study_global['group0'] / $this->design_study_global['group1'];
		$sAO = sqrt($AO);
		
		$last_inclusion_log[] = "allocation odds are ".$this->design_study_global['group0'].':'.$this->design_study_global['group1'];
		
		// the overall participants are in 
		// var_dump($this->inclusion_table);
		if (isset($this->inclusion_table['treatment']))
			$overall = $this->inclusion_table['treatment'];
		else
			$overall = array(0 => 0,1=>0);
		
		// calculating the overall difference
		$d_total = $sAO*$overall[1] - $overall[0]/$sAO;
		$last_inclusion_log[] = "difference for overall is $sAO * ".$overall[1]." - ".$overall[0]." / $sAO = $d_total";
		
		// calculating for each stratification
		foreach ($this->inclusion_table as $stratification_name => $results) {
			if ('treatment' == $stratification_name) continue;
			$d_strat[$stratification_name] = $sAO*$results[1] - $results[0]/$sAO;
			$last_inclusion_log[] = "difference for stratification $stratification_name is ".$d_strat[$stratification_name];
		}
		// if no data it will not change the odds
		
		// calculating difference at stratum level
		$stratum_json = json_encode($parameters);
		if (isset ($this->strata[$stratum_json]))
		{
			$stratum = $this->strata[$stratum_json];
			//$last_inclusion_log[] = print_r($parameters,true);
			//$last_inclusion_log[] = print_r($stratum,true);
			if (!isset($stratum[0])) $stratum[0] = 0;
			if (!isset($stratum[1])) $stratum[1] = 0;
			$d_stratum = $sAO*$stratum[1] - $stratum[0]/$sAO;
			$last_inclusion_log[] = "allocated patients for this stratum 1: ".$stratum[1]." 0: ".$stratum[0];
		}
		else {
			// default values
			$d_stratum = 0;
		}
		$last_inclusion_log[] = "difference for stratum is $d_stratum";
		
		// weighted sum of all levels
		$weight_total = $this->design['treatment']['weight'];
		$weight_stratum = $this->design_study_global['stratum_weight'];
		
		$ws = $weight_total*$d_total*$d_total*sign($d_total);
		$last_inclusion_log[] = "overall partial weighted sum is $weight_total * $d_total * $d_total * sign($d_total) = $ws";
		
		foreach ($this->inclusion_table as $stratification_name => $results) {
			if ('treatment' == $stratification_name) continue;
			$d_s = $d_strat[$stratification_name];
			$d_w = $this->design[$stratification_name]['weight'];
			$partial = $d_w * $d_s * $d_s * sign($d_s);
			$ws +=  $partial;
			$last_inclusion_log[] = "stratification $stratification_name partial weighted sum is $d_w * $d_s * $d_s * sign($d_s) = $partial";
		}

		// stratum
		$partial = $weight_stratum*$d_stratum*$d_stratum*sign($d_stratum);
		$last_inclusion_log[] = "stratum partial weighted sum is $weight_stratum*$d_stratum*$d_stratum*sign($d_stratum) = $partial";
		
		$ws += $partial;
		
		$last_inclusion_log[] = "weighted sum final is $ws";
		
		$logit = ($AO*exp($ws))/(1+($AO*exp($ws)));
		
		$last_inclusion_log[] = "logit is $logit";
		
		$odds_treatement_0 = $logit;
		
		$last_inclusion_log[] = "finished computing";		
		
		$precision = 1e3;
		
		// setting required limits for random number generation
		$this->set_random_limits(0,$precision);
		
		// getting random number between 0 and $precision, converting it to a percent
		$randomize_raw = $this->random->get_ranged() / $precision;
		
		$last_inclusion_log[] = 'randomly draw number '.$randomize_raw;
		
		// what group?
		if ($randomize_raw <= $odds_treatement_0) $treatment_group = 0;
		else $treatment_group = 1;
		
		$inclusion_log = array(
			'allocation_odds' => $AO,
			'odds' => $odds_treatement_0,
			'random_generator' => $this->random->generator,
			'drawn' => $randomize_raw,
			'result' => $treatment_group);
		

		// saving inclusion. preparting sql request
		
		
		$stratification = json_encode ($parameters);
		
		$sql = sprintf('INSERT INTO `%1$sstudy_%2$s%3$s` ( '.
			'`patient_identifier`, `stratification`, `treatment_group`,`inclusion_log`) VALUES '.
			'(:pid,:stratification, :tg, :il);', $this->table_prefix, $this->acronym,$this->simulation_suffix);
			
		$stmt = $this->pdo->prepare($sql);	
		$stmt->bindValue(':pid', 'patientid', PDO::PARAM_STR);		
		$stmt->bindValue(':stratification', $stratification, PDO::PARAM_STR);		
		$stmt->bindValue(':tg', $treatment_group, PDO::PARAM_INT);	
		$stmt->bindValue(':il', json_encode($inclusion_log), PDO::PARAM_STR);	

		//var_dump($last_inclusion_log);
		
		$this->last_inclusion_log = $last_inclusion_log;
		$this->last_inclusion = $inclusion_log;		
		
		if ($stmt->execute()) return $treatment_group;
		
		//$inclusion_log['sql'] = $sql;
		
		
		
		echo "something went wrong ".print_r($stmt->errorInfo(),true);
		
		return false;
	
	}

}
