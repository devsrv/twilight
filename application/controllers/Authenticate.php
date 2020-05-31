<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authenticate extends CI_Controller {
	private array $csrf;

	public function __construct() {
		parent::__construct();

		$this->load->library('twilight/authenticator/auth');

		$this->load->helper('url');

		$this->csrf = [
			'name' => $this->security->get_csrf_token_name(),
			'hash' => $this->security->get_csrf_hash()
		];
	}

	public function login() {
		if($this->auth->viaRemember()) redirect('/dashboard', 'refresh');

		if($this->input->method(TRUE) === 'POST') {
			$response = $this->auth->attempt(
				$this->input->post('email'), 
				$this->input->post('password'),
				null !== $this->input->post('remember')
			);

			if($response['success'] === 1) redirect('/dashboard', 'refresh');
			else $this->load->view('loginform', ['error' => 1, 'csrf' => $this->csrf]);

			return;
		}

		$this->load->view('loginform', ['error' => 0, 'csrf' => $this->csrf]);
	}

	public function logout() {
		if($this->input->method(TRUE) !== 'POST') show_error('HTTP method not allowed', 405);

		$this->auth->logout();
		redirect('/login', 'refresh');
	}

	public function dashboard() {
		if($this->auth->check()) {
			echo 'logged in <strong>'. $this->auth->get('name') .'</strong>';
		}
	}
}
