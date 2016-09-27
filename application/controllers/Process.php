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
		$this->gettext->change($code);
		$this->load->helper('url');
		redirect('');
	}
}

/* End of file Process.php */
/* Location: ./application/controllers/Process.php */