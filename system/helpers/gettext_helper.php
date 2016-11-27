<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Gettext Helper
 * @package 	CodeIgniter\CI-Gettext
 * @category 	Helpers
 * @author 	Kader Bouyakoub <bkader@mail.com>
 * @link 	http://www.bkader.com/
 */

if ( ! function_exists('current_lang'))
{
	/**
	 * Returns current used language
	 * @param 	string 	$key 	key to return
	 * @return 	mixed
	 */
	function current_lang($key = NULL)
	{
		$CI =& get_instance();
		class_exists('CI_Gettext') OR $CI->load->library('gettext');
		return $CI->gettext->get_current($key);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('languages'))
{
	/**
	 * Returns an array of available languages
	 * @param 	string 	$lang 	a single language to return
	 * @param 	string 	$key 	a single key to return
	 * @return 	mixed
	 */
	function languages($lang = NULL, $key = NULL)
	{
		$CI =& get_instance();
		class_exists('CI_Gettext') OR $CI->load->library('gettext');
		return $CI->gettext->languages($lang, $key);
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
		if ($domain !== NULL && $domain <> 'messages')
		{
			// If we are using php-gettext library, we use
			// their own T_bindtextdomain() function
			if (function_exists('_gettext'))
				T_bindtextdomain($domain, APPPATH.'language');
			// Otherwise, we use php built-in function
			else
				bindtextdomain($domain, APPPATH.'language');
		}

		// If a domain is provided and is different from the
		// default domain used, we use dgettext or _dgettext
		if ($domain !== NULL && $domain <> 'messages')
		{
			$msgstr = (function_exists('_dgettext')) ? _dgettext($domain, $msgid) : dgettext($domain, $msgid);
		}

		// If no domain is provided or is the default one, we
		// use gettext() or _gettext() functions.
		else
		{
			$msgstr = (function_exists('_gettext')) ? _gettext($msgid) : gettext($msgid);
		}

		return ($args) ? vsprintf($msgstr, (array) $args) : $msgstr;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('nline'))
{
	/**
	 * Use the plural form of gettext
	 * @param 	string 	$singular 	the singular form of the message
	 * @param 	string 	$plural 	the plural form of the message
	 * @param 	integer $number 	the integer used for comparison
	 * @oaram 	string 	$domain 	in case of a different domain
	 */
	function nline($singular, $plural, $number, $domain = NULL)
	{
		// If the domain is set and different from the default one
		if ($domain !== NULL AND $domain <> 'messages')
		{
			if (function_exists('_dngettext'))
			{
				T_bindtextdomain($domain, APPPATH.'languages');
				return _dngettext($domain, $singular, $plural, $number);
			}
			else
			{
				bindtextdomain($domain, APPPATH.'languages');
				return dngettext($domain, $singular, $plural, $number);
			}
		}

		$line = (function_exists('_ngettext'))
				? _ngettext($singular, $plural, $number) 
				: ngettext($singular, $plural, $number);

		return sprintf($line, (int) $number);

	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('__'))
{
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

if ( ! function_exists('e_line'))
{
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

// ------------------------------------------------------------------------

if ( ! function_exists('e_nline'))
{
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

/* End of file gettext_helper.php */
/* Location: ./system/helpers/gettext_helper.php */