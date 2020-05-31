<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth {

	protected $CI;

	private $id;
	private $username;
	private $password;
	private $remember;

	private static $REMEMBER_COOKIE_VALID = 60 * 60 * 24 * 7;	// 7days

	private $table;

	public function __construct(array $config = [
		'col' => [
			'id' => 'id',
			'username' => 'email',
			'password' => 'password',
			'remember' => 'remember_token',
		],
		'db' => [
			'table' => 'users'
		]
	])
	{
		$this->CI =& get_instance();

		$this->CI->load->database("default");

		$this->CI->load->library('twilight/encryption/hash');
		$this->CI->load->library('twilight/middleware/middleware');

		$this->CI->load->library('session');
		$this->CI->load->library('encryption');

		$this->CI->load->helper('cookie');

		foreach ($config as $key => $properties) {
			foreach($properties as $col_property => $value) {
				$this->{$col_property} = $value;
			}
		}
	}

	/**
	 * attempt to login via username, password
	 * 
	 * sets session with [bool logged_in  & int logged_in_uid]
	 * @return array 
	 */
	public function attempt(string $username, string $password, bool $remember = FALSE) : array
	{
		// allowed only if user not logged in
		$this->CI->middleware->execMiddleware('guest');

		$query = $this->CI->db->get_where($this->table, [$this->username => $username], 1);
		if($query->num_rows() === 1) {
			$user = $query->row();

			if($this->CI->hash->match($password, $user->{$this->password})) {
				$this->CI->session->set_userdata([
					'logged_in' => 1,
					'logged_in_uid' => $user->{$this->id}
				]);

				if($remember) $this->setRemember($user->{$this->id});

				return [
					'success' => 1,
					'id' => $user->{$this->id}
				];
			}

			return [
				'success' => 0,
				'message' => 'incorrect password',
			];
		}

		return [
			'success' => 0,
			'message' => 'user not found',
		];
	}

	/**
	 * fetch the current remember cookie token from db
	 */
	private function getRememberToken(int $id)
	{
		$this->CI->db->select($this->remember);
		$this->CI->db->from($this->table);
		$this->CI->db->where($this->id, $id);
		$query = $this->CI->db->get();

		if(! $query || $query->num_rows() !== 1) {
			throw new Exception("can't process remember functionality, error fetching user data");
		}

		$result = $query->row();
		return $result->{$this->remember};
	}

	/**
	 * set remember cookie
	 */
	private function setRemember(int $id)
	{
		$currentToken = $this->getRememberToken($id);

		if($currentToken !== NULL) {
			$ciphertext = $this->CI->encryption->encrypt($currentToken);

			set_cookie('remember_web', base64_encode($ciphertext), self::$REMEMBER_COOKIE_VALID);
			return;
		}

		$token = bin2hex(openssl_random_pseudo_bytes(30));
		$ciphertext = $this->CI->encryption->encrypt($token);
		
		set_cookie('remember_web', base64_encode($ciphertext), self::$REMEMBER_COOKIE_VALID);

		$this->CI->db->where($this->id, $id);
		$this->CI->db->update($this->table, [$this->remember => $token]);

		if($this->CI->db->affected_rows() !== 1) {
			throw new Exception("can't process remember functionality, db write error");
		}
	}

	/**
	 * check if remember cookie is valid & authenticable
	 * @return bool
	 */
	public function viaRemember() : bool
	{
		$cookie = get_cookie('remember_web');

		if($cookie === NULL) return FALSE;

		$ciphertext = base64_decode($cookie);

		$token = $this->CI->encryption->decrypt($ciphertext);

		if(! $token) {
			return FALSE;
		}

		$this->CI->db->select($this->id);
		$this->CI->db->from($this->table);
		$this->CI->db->where($this->remember, $token);
		$query = $this->CI->db->get();

		if(! $query) {
			throw new Exception("can't process remember functionality, error fetching user data");
		}

		if($query->num_rows() !== 1) {
			delete_cookie('remember_web');
			return FALSE;
		}

		$result = $query->row();

		$this->CI->session->set_userdata([
			'logged_in' => 1,
			'logged_in_uid' => $result->{$this->id}
		]);

		return TRUE;
	}

	/**
	 * return authenticated user's data
	 */
	public function get(string $column)
	{
		// only if user is logged in currently
		$this->CI->middleware->execMiddleware('auth');

		$this->CI->db->select($column);
		$this->CI->db->from($this->table);
		$this->CI->db->where($this->id, $_SESSION['logged_in_uid']);

		$query =$this->CI->db->get();

		if(! $query) {
			$error =$this->CI->db->error();
			show_error(403, $error['message']);
			return FALSE;
		}

		$result = $query->row();

		return $result->{$column};
	}

	/**
	 * check if user logged in
	 */
	public function check() : bool
	{
		return isset($_SESSION['logged_in'], $_SESSION['logged_in_uid'])
				&& $_SESSION['logged_in'] === 1;
	}

	/**
	 * logout authenticated user & session destroy
	 */
	public function logout()
	{
		// only if user is logged in currently
		$this->CI->middleware->execMiddleware('auth');

		delete_cookie('remember_web');

		// clean the remember token
		$this->CI->db->where($this->id, $_SESSION['logged_in_uid']);
		$this->CI->db->update($this->table, [$this->remember => NULL]);

		$this->CI->session->unset_userdata(['logged_in', 'logged_in_uid']);
		$this->CI->session->sess_destroy();

		return TRUE;
	}
}
