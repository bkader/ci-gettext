<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * This is an example controller on how to switch language
 * @package 	CodeIgniter\CI-Gettext
 * @category 	Controllers
 * @author 	Kader Bouyakoub <bkader@mail.com>
 * @link 	http://www.bkader.com/
 */

class Process extends CI_Controller
{
	/**
	 * Change site language
	 * @access 	public
	 * @param 	string 	$code 	code of the language to use
	 * @return 	void
	 */
	public function lang($code = 'en')
	{
		function_exists('redirect') OR $this->load->helper('url');
		$this->gettext->change($code);
		redirect('');
	}
}

/* End of file Process.php */
/* Location: ./application/controllers/Process.php */