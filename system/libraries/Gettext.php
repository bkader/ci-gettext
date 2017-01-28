<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
						'name'    => 'English',
						'name_en' => 'English',
						'folder'  => 'english',
						'code'    => 'en',
						'flag'    => 'us',
					),
				),
			);
		}

		// If gettext_enabled is set to FALSE, we return;
		if ( ! $config['gettext_enabled']) return;

		// We check whether the cookie use is enabled or not
		isset($config['gettext_cookie']) OR $config['gettext_cookie'] = 'lang';
		if ($config['gettext_cookie'] !== NULL)
		{
			function_exists('get_cookie') OR $this->CI->load->helper('cookie');
			$this->cookie = $config['gettext_cookie'];
		}

		// If we are using session as well, we make sure session library
		// is loaded.
		isset($config['gettext_session']) OR $config['gettext_session'] = NULL;
		if ($config['gettext_session'] !== NULL)
		{
			class_exists('CI_Session') OR $this->CI->load->library('session');
			$this->session = $config['gettext_session'];
		}

		// Make sure available language is never empty
		if ( ! isset($config['gettext_languages']) OR empty($config['gettext_languages']))
		{
			$config['gettext_languages'] = array('english');
		}

		foreach ($config['gettext_languages'] as $lang)
		{
			if (array_key_exists($lang, $config['available_languages']))
			{
				$this->languages[$lang] = $config['available_languages'][$lang];
			}
		}

		// Now we set site default language
		isset($config['gettext_default']) OR $config['gettext_default'] = config_item('language');
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
	public function languages($lang = NULL, $key = NULL)
	{
		$return = $this->languages;
		if ($lang !== NULL && array_key_exists($lang, $return))
		{
			$return = $return[$lang];

			if ($key !== NULL && array_key_exists($key, $return))
				$return = $return[$key];
		}

		return $return;
	}

	// ------------------------------------------------------------------------

	/**
	 * Returns site default language default
	 * @access 	public
	 * @param 	string 	$key 	key to return
	 * @return 	string
	 */
	public function get_default($key = NULL)
	{
		$return = $this->default;

		if ($key !== NULL && array_key_exists($key, $return))
			$return = $return[$key];

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
	 * @param 	string 	$key 	key to return
	 * @return 	string
	 */
	public function get_client($key = NULL)
	{
		$return = $this->client;
		
		if ($key !== NULL && array_key_exists($key, $return))
			$return = $return[$key];

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
		$_lang = NULL;


		// If the use of COOKIES is enabled, we check the cookie
		if ($this->cookie !== NULL)
		{
			$_lang = get_cookie($this->cookie, TRUE);
			$_lang OR $_lang = $this->get_client('folder');
		}

		// In case we use SESSION instead of COOKIE
		elseif($this->session !== NULL)
		{
			$_lang = $this->CI->session->{$this->session};
			$_lang OR $_lang = $this->get_client('folder');
		}

		// If neither COOKIE nor SESSION are used, we use
		// default language
		else
		{
			$_lang = $this->default['folder'];
		}

		// We prepare our default language
		$current = $this->default;

		// We make sure the language is available
		if ($lang = $this->find_by('folder', $_lang))
		{
			$current = $lang;
			unset($lang);
		}

		// We set COOKIE if enabled
		if ($this->cookie !== NULL)
			set_cookie($this->cookie, $current['folder'], 2678400);

		// If no cookie but session is ON
		elseif($this->session !== NULL)
			$_SESSION[$this->session] = $current['folder'];

		return $current;
	}

	/**
	 * Returns current language's details
	 * @access 	public
	 * @param 	string 	$key 	key to return
	 * @return 	string
	 */
	public function get_current($key = NULL)
	{
		$return = $this->current;

		if ($key !== NULL && array_key_exists($key, $return))
			$return = $return[$key];

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
				return $lang;
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
		// We make sure the language exists
		if ($lang = $this->find_by('code', $code))
		{
			// If the use of cookies is ON
			if ($this->cookie !== NULL)
				set_cookie($this->cookie, $lang['folder'], 2678400);

			// In case COOKIE are off but SESSION is on
			elseif ($this->session !== NULL)
				$_SESSION[$this->session] = $lang['folder'];

			// Change now current language
			$this->current = $lang;

			return TRUE;
		}

		return FALSE;
	}
}

/* End of file Gettext.php */
/* Location: ./system/libraries/Gettext.php */