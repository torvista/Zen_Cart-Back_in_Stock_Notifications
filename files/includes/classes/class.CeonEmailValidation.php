<?php

/**
 * Class for validating E-mail Addresses and Headers.
 *
 * Ported from Ceon Site Engine to Zen Cart (inc backporting to PHP4 from PHP5).
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: class.CeonEmailValidation.php 904 2012-01-01 17:24:17Z conor $
 */
class CeonEmailValidation
{
	function CeonEmailValidation()
	{
	}
	
	// {{{ isValid()
	
	/**
	 * Checks that an E-mail Address has a valid syntax.
	 *
	 * @access  public
	 * @author  W. Jason Gilmore
	 * @author  Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
	 * @param   string    $email   E-mail Address to validate
	 * @return  boolean   Status of validation (true for valid, false for invalid).
	 */
	function isValid($email)
	{
		// Create the syntactical validation regular expression (2-6 includes .uk -> .museum)
		$regexp = "/^([_a-z0-9-]+)(\.[_a-z0-9\+=-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,6})$/i";
		
		// Presume that the email is invalid
		$valid = false;
		
		// Validate the syntax
		if (preg_match($regexp, $email)) {
			$valid = true;
		}
		
		return $valid;
	}
	
	// }}}
	
	
	// {{{ isHeaderInjection()
	
	/**
	 * Checks an E-mail header field to see if an attempt was made to inject code into the header.
	 *
	 * @access  public
	 * @author  Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
	 * @param   string    $header   E-mail header to check for injection attempt
	 * @return  boolean   Status of detection (true if Injection Detected, false if None Detected)
	 */
	function isHeaderInjection($header)
	{
		// Define strings to test against
		$test_strings = array(
			"\r",
			"\n",
			"bcc:",
			"Content-Type:",
			"Mime-Type:",
			"cc:",
			"to:");
		
		for ($i = 0, $num_test_strings = sizeof($test_strings); $i < $num_test_strings; $i++) {
			if (!(strpos(strtolower($header), strtolower($test_strings[$i])) === false)) {
				// Attempt found
				return true;
			}
		}
		
		// No attempts detected, header is fine
		return false;
	}
	
	// }}}
}

?>