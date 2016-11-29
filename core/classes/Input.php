<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-dev
 *
 *  License: MIT
 *
 *  Input class
 */
class Input {
	
	// Check that input actually exists
	// Params: $type (string) - check for either POST or GET submission (optional, defaults to POST)
	public static function exists($type = 'post'){
		switch($type) {
			case 'post';
				// Check the $_POST variable
				return (!empty($_POST)) ? true : false;
			break;
			
			case 'get';
				// Check the $_GET variable
				return (!empty($_GET)) ? true : false;
			break;
			
			default:
				// Otherwise, return false
				return false;
			break;
		}
	}
	
	// Get input with the specified name
	// Params: $item (string) - name of element containing input
	public static function get($item){
		// Check to see if the element is within the $_POST variable or the $_GET variable
		if(isset($_POST[$item])){
			// It is within the $_POST variable, return the item
			return $_POST[$item];
			
		} else if(isset($_GET[$item])){
			// It is in the $_GET variable, return the item
			return $_GET[$item];
		}
		
		// It is not in either $_GET or $_POST, return an empty string
		return '';
	}
}