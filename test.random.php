<?php

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

$random->min = 31;
$random->max = 69;


$total = 0;
$tests = 10000;
for ($i=0;$i<$tests;$i++)
{
	$n = $random->get_ranged();
	@$result[$n] ++; 
	$total += $n;
}
echo 'mean '.($total/$tests)."<br />\n";
ksort($result);
var_dump($result);