<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->library('twilight/authenticator/auth');
		$this->load->library('twilight/encryption/hash');
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function login() {
		$this->auth->attempt('developer.srv1@gmail.com', 'sourav');
	}

	public function logout() {
		$this->auth->logout();
	}

	public function dashboard() {
		if($this->auth->check()) {
			echo 'logged in <strong>'. $this->auth->get('name') .'</strong>';
		} else {
			echo 'not logged in';
		}
	}
}
