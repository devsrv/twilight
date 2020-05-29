<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth {

	protected $CI;

	private $id;
	private $username;
	private $password;
	private $remember;

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

		$this->CI->load->library('session');

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
		$query = $this->CI->db->get_where($this->table, [$this->username => $username], 1);
		if($query->num_rows() === 1) {
			$user = $query->row();

			if($this->CI->hash->match($password, $user->{$this->password})) {
				$this->CI->session->set_userdata([
					'logged_in' => 1,
					'logged_in_uid' => $user->{$this->id}
				]);

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
	 * return authenticated user's data
	 */
	public function get(string $column)
	{
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
		$this->CI->session->unset_userdata(['logged_in', 'logged_in_uid']);
		$this->CI->session->sess_destroy();

		return TRUE;
	}
}
