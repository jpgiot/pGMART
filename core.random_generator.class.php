<?php

class random_generator{
	public $min;
	public $max;
}

class rg_hardware extends random_generator {

	function __construct(){
		$pr_bits = '';

		// Unix/Linux platform?
		$fp = @fopen('/dev/urandom','rb');
		if ($fp !== FALSE) {
			$pr_bits .= @fread($fp,16);
			@fclose($fp);
			$this->os = 'nix';
		}

		// MS-Windows platform?
		if (@class_exists('COM')) {
			// http://msdn.microsoft.com/en-us/library/aa388176(VS.85).aspx
			try {
				$CAPI_Util = new COM('CAPICOM.Utilities.1');
				$pr_bits .= $CAPI_Util->GetRandom(16,0);

				// if we ask for binary data PHP munges it, so we
				// request base64 return value.  We squeeze out the
				// redundancy and useless ==CRLF by hashing...
				if ($pr_bits) { $pr_bits = md5($pr_bits,TRUE); }
			} catch (Exception $ex) {
				// echo 'Exception: ' . $ex->getMessage();
			}
			$this->os = 'win';
		}

		if (strlen($pr_bits) < 16) {
			// do something to warn system owner that
			// pseudorandom generator is missing
			throw new Exception ('missing pseudorandom hardware number generator');
			return false;
		}

		$this->generator = 'hardware';
		return true;
	
	}
	
	function get(){
	
		$pr_bits = '';

		// Unix/Linux platform?
		$fp = @fopen('/dev/urandom','rb');
		if ($fp !== FALSE) {
			$pr_bits .= @fread($fp,16);
			@fclose($fp);
		}

		// MS-Windows platform?
		if (@class_exists('COM')) {
			// http://msdn.microsoft.com/en-us/library/aa388176(VS.85).aspx
			try {
				$CAPI_Util = new COM('CAPICOM.Utilities.1');
				$pr_bits .= $CAPI_Util->GetRandom(16,0);

				// if we ask for binary data PHP munges it, so we
				// request base64 return value.  We squeeze out the
				// redundancy and useless ==CRLF by hashing...
				if ($pr_bits) { $pr_bits = md5($pr_bits,TRUE); }
			} catch (Exception $ex) {
				// echo 'Exception: ' . $ex->getMessage();
			}
		}

		if (strlen($pr_bits) < 16) {
			// do something to warn system owner that
			// pseudorandom generator is missing
			throw new Exception ('missing pseudorandom hardware number generator');
			return false;
		}
		
		return $pr_bits;
	}
	
	function get_ranged()
	{
		
	}
}

class rg_openssl extends random_generator {

	function __construct(){
		if (!function_exists ('openssl_random_pseudo_bytes')){
			throw new Exception ('missing openssl number generator');
			return false;
		}
		$this->generator = 'openssl_random_pseudo_bytes';
	}
	
	function get(){	
		$i = 16;
		$r = openssl_random_pseudo_bytes ($i,$cstrong);
		$this->openssl_cstrong = $cstrong;
		return $r;
	}
	
	function get_ranged()
	{
		if (!isset($this->max)) {
			throw new ErrorException('no min value',0,0,__FILE__,__LINE__);
			return false;
		}
		if (!isset($this->min)) {
			throw new ErrorException('no max value',0,0,__FILE__,__LINE__);
			return false;
		}
		if ($this->min >= $this->max) {
			throw new ErrorException('invalid range ['.$this->min.";".$this->max.']',0,0,__FILE__,__LINE__);
			return false;
		}
		// we have to translate the range to a 0 based range
		$delta = $this->min;
		$range = ($this->max - $this->min);
		
		// how much bits do we need ?
		$length = (int) (log($range,2) / 8 )+1;
		
		return $this->min + (hexdec(bin2hex(openssl_random_pseudo_bytes($length,$s))) % $range);

	}
}

class rg_mtrand extends random_generator {

	function __contruct(){
		$this->generator = 'mt_rand';
	}
	
	function get(){	

		return $r;
	}
	
	function get_ranged()
	{
		if (!isset($this->max)) return false;
		if (!isset($this->min)) return false;
		if ($this->min >= $this->max) return false;
		
		return mt_rand ( $this->min , $this->max );
	}
}
