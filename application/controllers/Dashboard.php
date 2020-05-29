<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->library('twilight/authenticator/auth');
	}

	public function index() {
		$user = sprintf("%s < %s >", 
									$this->auth->get('name'), 
									$this->auth->get('email'));

		$this->load->view('dashboard', ['user' => $user]);
	}
}
