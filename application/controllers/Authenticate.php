<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authenticate extends CI_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->library('twilight/authenticator/auth');

		$this->load->helper('url');
	}

	public function login() {
		if($this->input->method(TRUE) === 'POST') {
			$response = $this->auth->attempt(
				$this->input->post('email'), 
				$this->input->post('password')
			);

			if($response['success'] === 1) redirect('/dashboard', 'refresh');
			else $this->load->view('loginform', ['error' => 1]);

			return;
		}

		$this->load->view('loginform', ['error' => 0]);
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
