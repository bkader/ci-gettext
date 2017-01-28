<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Gettext Helper
 * @package 	CodeIgniter\CI-Gettext
 * @category 	Helpers
 * @author 	Kader Bouyakoub <bkader@mail.com>
 * @link 	http://www.bkader.com/
 */

if ( ! function_exists('current_language')) {
	/**
	 * Returns current used language
	 * @param 	mixed 	string, strings or array or strings
	 * @return 	mixed
	 */
	function current_language()
	{
		return call_user_func_array(array(
			get_instance()->gettext,
			'get_current'
		), func_get_args());
	}
}

if ( ! function_exists('default_language')) {
	/**
	 * Returns website default languag used language
	 * @param 	mixed 	string, strings or array or strings
	 * @return 	mixed
	 */
	function default_language()
	{
		return call_user_func_array(array(
			get_instance()->gettext,
			'get_default'
		), func_get_args());
	}
}

if ( ! function_exists('client_language')) {
	/**
	 * Returns client's supported language
	 * @param 	mixed 	string, strings or array or strings
	 * @return 	mixed
	 */
	function client_language()
	{
		return call_user_func_array(array(
			get_instance()->gettext,
			'get_client'
		), func_get_args());
	}
}

if ( ! function_exists('languages')) {
	/**
	 * Returns an array of available languages
	 * @param 	mixed 	string, strings or array
	 * @return 	mixed
	 */
	function languages($lang = NULL, $key = NULL)
	{
		return call_user_func_array(array(
			get_instance()->gettext,
			'languages'
		), func_get_args());
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('line'))
{
	/**
	 * Uses either gettext() OR dgettext()
	 * @param 	string 	$msgid 		the message to look for in our MO file
	 * @param 	mixed 	$args 		string, integer or array or arguments
	 * @param 	string 	$domain 	domain name if to use a different one
	 * @return 	string         		the message to return
	 */
	function line($msgid, $args = NULL, $domain = NULL)
	{
		if ($domain !== NULL && $domain <> 'messages') {
			T_bindtextdomain($domain, APPPATH.'language');
			$msgstr = T_dgettext($domain, $msgid);
		} else {
			$msgstr = T_gettext($msgid);
		}

		return ($args) ? vsprintf($msgstr, (array) $args) : $msgstr;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('nline')) {
	/**
	 * Use the plural form of gettext
	 * @param 	string 	$singular 	the singular form of the message
	 * @param 	string 	$plural 	the plural form of the message
	 * @param 	integer $number 	the integer used for comparison
	 * @oaram 	string 	$domain 	in case of a different domain
	 */
	function nline($singular, $plural, $number, $domain = NULL)
	{
		if ($domain !== NULL AND $domain <> 'messages') {
			T_bindtextdomain($domain, APPPATH.'languages');
			$msgstr = T_dngettext($domain, $singular, $plural, $number);
		} else {
			$msgstr = T_ngettext($singular, $plural, $number);
		}

		return sprintf($msgstr, (int) $number);

	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('__')) {
	/**
	 * If php_getttext extension is enabled, this function will be declared
	 * and usable as alias of line() function. But if php_gettext is not
	 * available, in this case we use php-gettext library that comes with
	 * an already-declared __() function.
	 *
	 * PHP-Gettext's library __() function takes only one arguments which
	 * is the $msgid while this one here takes three.
	 *
	 * @param 	string 	$msgid 	the message to look for
	 * @param 	mixed 	$args 	string, number or array
	 * @param 	string 	$domain domain name if different
	 */

	function __($msgid, $args = NULL, $domain = NULL)
	{
		return line($msgid, $args, $domain);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('e_line')) {
	/**
	 * This function calls line() and echoes the message
	 * @param 	string 	$msgid 	the message to look for
	 * @param 	mixed 	$args 	string, integer or array
	 * @param 	string 	$domain in case of a different domain
	 */
	function e_line($msgid, $args = NULL, $domain = NULL)
	{
		echo line($msgid, $args, $domain);
	}
}

if ( ! function_exists('_e')) {
	function _e($msgid, $args = NULL, $domain = NULL)
	{
		echo line($msgid, $args, $domain);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('e_nline')) {
	/**
	 * This function calls nline() and echoes the message
	 * @param 	string 	$msgid 	the message to look for
	 * @param 	mixed 	$args 	string, integer or array
	 * @param 	string 	$domain in case of a different domain
	 */
	function e_nline($singular, $plural, $number, $domain = NULL)
	{
		echo nline($singular, $plural, $number, $domain);
	}
}

// ------------------------------------------------------------------------
// Declaring gettext functions if the extension is not loaded or available
// ------------------------------------------------------------------------

if ( ! function_exists('gettext')) {
	function gettext($msgid)
	{
		return T_gettext($msgid);
	}
}

if ( ! function_exists('ngettext')) {
	function ngettext($singular, $plural, $number)
	{
		return T_ngettext($singular, $plural, $number);
	}
}

if ( ! function_exists('dgettext')) {
	function dgettext($domain, $msgid)
	{
		return T_dgettext($domain, $msgid);
	}
}

if ( ! function_exists('dngettext')) {
	function dngettext($domain, $singular, $plural, $number)
	{
		return T_dngettext($domain, $singular, $plural, $number);
	}
}

if ( ! function_exists('dcgettext')) {
	function dcgettext($domain, $msgid, $category)
	{
		return T_dcgettext($domain, $msgid, $category);
	}
}

if ( ! function_exists('dcngettext')) {
	function dcngettext($domain, $singular, $plural, $number, $category)
	{
		return T_dcnpgettext($domain, $singular, $plural, $number, $category);
	}
}

if ( ! function_exists('pgettext')) {
	function pgettext($context, $msgid)
	{
		return T_pgettext($context, $msgid);
	}
}

if ( ! function_exists('dpgettext')) {
	function dpgettext($domain, $context, $msgid)
	{
		return T_dpgettext($domain, $context, $msgid);
	}
}

if ( ! function_exists('dcpgettext')) {
	function dcpgettext($domain, $context, $msgid, $category)
	{
		return T_dcpgettext($domain, $context, $msgid, $category);
	}
}

if ( ! function_exists('npgettext')) {
	function npgettext($context, $singular, $plural)
	{
		return T_npgettext($context, $singular, $plural);
	}
}

if ( ! function_exists('dnpgettext')) {
	function dnpgettext($domain, $context, $singular, $plural)
	{
		return T_dnpgettext($domain, $context, $singular, $plural);
	}
}

if ( ! function_exists('dcnpgettext')) {
	function dcnpgettext($domain, $context, $singular, $plural, $category)
	{
		return T_dcnpgettext($domain, $context, $singular, $plural, $category);
	}
}

/* End of file gettext_helper.php */
/* Location: ./system/helpers/gettext_helper.php */