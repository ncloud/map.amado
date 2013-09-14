<?php

	function only_english($string) {
		if (!preg_match('/[^A-Za-z0-9]/', $string)) {
			return true;
		} else {
			return false;
		}
	}