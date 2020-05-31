<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	private array $csrf;

	public function __construct() {
		parent::__construct();

		$this->load->library('twilight/authenticator/auth');

		$this->csrf = [
			'name' => $this->security->get_csrf_token_name(),
			'hash' => $this->security->get_csrf_hash()
		];
	}

	public function index() {
		$user = sprintf("%s < %s >", 
									$this->auth->get('name'), 
									$this->auth->get('email'));

		$this->load->view('dashboard', ['user' => $user, 'csrf' => $this->csrf]);
	}
}
