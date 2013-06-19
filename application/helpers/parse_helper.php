<?php
	
	// input parsing
	function parseInput($value) {
	  $value = htmlspecialchars($value, ENT_QUOTES);
	  $value = str_replace("\r", "", $value);
	  $value = str_replace("\n", "", $value);
	  return $value;
	}
	
	function parseForLat($a, $b) {
	    $av = ($a->lat);
	    $bv = ($b->lat);
	    
	    if ($av == $bv) {
	        return 0;
	    }
	    return ($av < $bv) ? 1 : -1;
	}
	
	function parseForLng($a, $b) {
	    $av = ($a->lng);
	    $bv = ($b->lng);
	    
	    if ($av == $bv) {
	        return 0;
	    }
	    return ($av < $bv) ? 1 : -1;
	}