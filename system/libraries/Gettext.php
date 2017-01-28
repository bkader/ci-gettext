<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Gettext Library
 *
 * This library allows the use of php_gettext extension to make your CodeIgniter
 * applications multilingual.
 * The good thing about it is that you can use php_gettext functions even if the
 * extension is not enabled and that thanks Launcphad's php-gettext library added
 * add-in (BASEPATH/vendor/php-gettext).
 *
 * @package 	CodeIgniter
 * @category 	Libraries
 * @author 	Kader Bouyakoub <bkader@mail.com>
 * @link 	http://github.com/bkader
 */

class CI_Gettext
{
	/**
	 * @var object - instance of CI object
	 */
	protected $CI;

	/**
	 * @var array - site default language
	 */
	protected $default;

	/**
	 * @var array - site available languages
	 */
	protected $languages = array();

	/**
	 * @var string - language cookie name
	 * @var string - language session name
	 */
	protected $cookie  = NULL;
	protected $session = NULL;

	/**
	 * @var array - client supported language
	 */
	protected $client;

	/**
	 * @var array - site current language
	 */
	protected $current;

	/**
	 * @var string - gettext domain
	 */
	protected $domain = 'messages';

	/**
	 * Constructor
	 */
	public function __construct($config = array())
	{
		// Prepare CI object
		$this->CI =& get_instance();

		// initialize class
		$this->initialize($config);

        // Load gettext helper
        $this->CI->load->helper('gettext');

        log_message('debug', 'Gettext Class Initialized');
	}

	/**
	 * Initialize Gettext library class
	 * @access 	protected
	 * @param 	array
	 * @return 	void
	 */
	protected function initialize(array $config = array())
	{
		// Make sure the configuration is never empty
		if (empty($config))
		{
			$config = array(
				'gettext_enabled'     => TRUE,
				'gettext_default'     => 'english',
				'gettext_domain'      => 'messages',
				'gettext_languages'   => array('english'),
				'available_languages' => array(
					'english' => array(
						'name'      => 'English',
						'name_en'   => 'English',
						'folder'	=> 'english',
						'locale'    => 'en-US',
						'direction' => 'ltr',
						'code'      => 'en',
						'flag'      => 'us',
					),
				),
			);
		}

		// We stop the library if gettext is disable and no cookie
		// or session names are provided.
		if ( ! $config['gettext_enabled'] 
			OR empty($config['gettext_cookie']) 
			&& empty($config['gettext_session']))
		{
			return;
		}

		// We check whether the cookie use is enabled or not
		isset($config['gettext_cookie']) OR $config['gettext_cookie'] = 'lang';
		$this->cookie = $config['gettext_cookie'];

		// If we are using session as well, we make sure session library
		// is loaded.
		isset($config['gettext_session']) OR $config['gettext_session'] = NULL;
		$this->session = $config['gettext_session'];
		if ($this->session !== NULL)
		{
			class_exists('CI_Session') OR $this->CI->load->library('session');
		}

		// Make sure available language is never empty
		if ( ! isset($config['gettext_languages']) OR empty($config['gettext_languages']))
		{
			$config['gettext_languages'] = array('english');
		}

		foreach ($config['gettext_languages'] as $lang)
		{
			if (isset($config['available_languages'][$lang]))
			{
				$this->languages[$lang] = $config['available_languages'][$lang];
			}
		}

		// Now we set site default language
		if ( ! isset($config['get_default']) OR empty($config['gettext_default'])) {
			$config['gettext_default'] = $this->CI->config->item('language');
		}
		$this->default = $this->languages[$config['gettext_default']];

		// Set our client language now
		$this->client = $this->_set_client_language();

		// Set current language
		$this->current = $this->_set_current_language();

		// Set gettext domain name
		$config['gettext_domain'] && $this->domain = $config['gettext_domain'];

		// If php_gettext extension is enabled, we use the built-in
		// functions, otherwise, we use php-gettext library.
        if (function_exists('gettext')) {
        	putenv('LANG='.$this->current['folder']);
        }
        T_setlocale(LC_MESSAGES, $this->current['folder']);
        T_bindtextdomain($this->domain, APPPATH.'language');
        T_bind_textdomain_codeset($this->domain, 'UTF-8');
        T_textdomain($this->domain);

        // Change language in config file
        $this->CI->config->set_item('language', $this->current['folder']);
	}

	// ------------------------------------------------------------------------

	/**
	 * Returns a list of available languages
	 * @access 	public
	 * @param 	string 	$lang 	language folder name
	 * @param 	string 	$key 	key to return
	 * @return 	mixed
	 */
	public function languages()
	{

        // We prepare our array of languages codes
        $languages = $this->languages;

        // If any arguments are passed to this method
        if ( ! empty($args = func_get_args())) {


            // Prepare an empty array and fill it after
            $_languages = array();

            // Make sure $args is not a multidimensional array
            isset($args[0]) && is_array($args[0]) && $args = $args[0];

            // We walk through languages codes and fill our array
            foreach ($languages as $key => $value) {
                
                // We start by assigning the key with an empty value
                $_languages[$key] = array();
                // We walk through passed arguments
                foreach ($args as $arg) {

                	if (isset($value[$arg])) {
                		$_languages[$key][$arg] = $value[$arg];
                	}
                }
            }

            // replace our $languages array with $_languages
            $languages  = $_languages;
            unset($_languages);
        }

        return $languages;
	}

	// ------------------------------------------------------------------------

	/**
	 * Returns site default language default
	 * @access 	public
	 * @param 	mixed 	string, strings or array
	 * @return 	string
	 */
	public function get_default()
	{
		$return = $this->default;

		if (count($args = func_get_args()) >= 1) 
		{
			isset($args[0]) && is_array($args[0]) && $args = $args[0];
			$_return = array();
			foreach ($args as $arg)
			{
				if (isset($return[$arg]))
				{
					$_return[$arg] = $return[$arg];
				}
			}

			empty($_return) OR $return = $_return;
			unset($_return);
		}
		return $return;
	}

	// ------------------------------------------------------------------------

	/**
	 * Automatically sets client's language
	 * @access 	protected
	 * @param 	none
	 * @return 	array
	 */
	protected function _set_client_language()
	{
		$code = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		$lang = $this->find_by('code', $code);
		$lang OR $lang = $this->default;
		return $lang;
	}

	/**
	 * Returns client's language details
	 * @access 	public
	 * @param 	mixed 	string, strings or array
	 * @return 	string
	 */
	public function get_client()
	{
		$return = $this->client;

		if (count($args = func_get_args()) >= 1) 
		{
			isset($args[0]) && is_array($args[0]) && $args = $args[0];
			$_return = array();
			foreach ($args as $arg)
			{
				if (isset($return[$arg]))
				{
					$_return[$arg] = $return[$arg];
				}
			}

			empty($_return) OR $return = $_return;
			unset($_return);
		}
		return $return;
	}

	// ------------------------------------------------------------------------

	/**
	 * Sets site current language
	 * @access 	protected
	 * @param 	none
	 * @return 	array
	 */
	protected function _set_current_language()
	{
		$folder = NULL;


		// If the use of COOKIES is enabled, we check the cookie
		if ($cookie = $this->CI->input->cookie($this->cookie, TRUE))
		{
			$folder = $cookie;
			unset($cookie);
		}

		// In case we use SESSION instead of COOKIE
		elseif ($this->session !== NULL)
		{
			$folder = $this->CI->session->{$this->session};
			$folder OR $folder = $this->client['folder'];
		}

		// If neither COOKIE nor SESSION are used, we use
		// default language
		else
		{
			$folder = $this->client['folder'];
		}

		// We prepare our default language
		$current = $this->default;

		// We make sure the language is available
		if ($lang = $this->find_by('folder', $folder))
		{
			$current = $lang;
			unset($lang);
		}

		// We set COOKIE if enabled
		if ($this->cookie !== NULL)
		{
			$this->CI->input->set_cookie($this->cookie, $current['folder'], 2678400);
		}

		// If no cookie but session is ON
		elseif ($this->session !== NULL)
		{
			$_SESSION[$this->session] = $current['folder'];
		}

		return $current;
	}

	/**
	 * Returns current language's details
	 * @access 	public
	 * @param 	none 	string, array of multiple strings
	 * @return 	string
	 */
	public function get_current()
	{
		$return = $this->current;

		if (count($args = func_get_args()) >= 1) 
		{
			isset($args[0]) && is_array($args[0]) && $args = $args[0];
			$_return = array();
			foreach ($args as $arg)
			{
				if (isset($return[$arg]))
				{
					$_return[$arg] = $return[$arg];
				}
			}

			empty($_return) OR $return = $_return;
			unset($_return);
		}
		return $return;
	}

	// ------------------------------------------------------------------------

	/**
	 * Search through languages array
	 * @access 	public
	 * @param 	string 	$field 	field to compare to
	 * @param 	string 	$match 	value to compare field to
	 * @return 	array
	 */
	public function find_by($field = 'folder', $match = 'english')
	{
		foreach($this->languages as $lang)
		{
			if (isset($lang[$field]) && $lang[$field] === $match)
			{
				return $lang;
			}
		}

		return NULL;
	}

	// ------------------------------------------------------------------------

	/**
	 * Change current language
	 * @access 	public
	 * @param 	string 	$code 	language code to use
	 * @return 	boolean
	 */
	public function change($code = 'en')
	{
		// We make sure the language is not the same as the current
		if ($code === $this->current['code'])
		{
			return TRUE;
		}

		// We make sure the language exists
		if ($lang = $this->find_by('code', $code))
		{
			// If the use of cookies is ON
			if ($this->cookie !== NULL)
			{
				$this->CI->input->set_cookie($this->cookie, $lang['folder'], 2678400);
			}

			// In case COOKIE are off but SESSION is on
			elseif ($this->session !== NULL)
			{
				$_SESSION[$this->session] = $lang['folder'];
			}

			// Change now current language
			$this->current = $lang;

			return TRUE;
		}

		return FALSE;
	}
}

/* End of file Gettext.php */
/* Location: ./system/libraries/Gettext.php */