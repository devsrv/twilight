<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authsupport {
	protected $CI;

	private static $redirectTo = '/dashboard';

	private static $redirectIFNotAuthenticated = '/login';

	public function __construct() {
		require_once APPPATH.'/libraries/twilight/authenticator/Auth.php';

		$this->CI =& get_instance();

		$this->CI->load->helper('url');
	}

	public function redirectTo()
	{
		if(! (new Auth)->check()) {
			redirect(self::$redirectIFNotAuthenticated, 'refresh');
			return;
		}

		redirect(self::$redirectTo, 'refresh');
		return;
	}
}
