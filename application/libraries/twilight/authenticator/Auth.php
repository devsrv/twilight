<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth {

	protected $CI;

	private $id = 1;
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

		foreach ($config as $key => $properties) {
			foreach($properties as $col_property => $value) {
				$this->{$col_property} = $value;
			}
		}
	}

	/**
	 * attempt to login via username, password
	 */
	public function attempt(string $username, string $password, bool $remember = FALSE)
	{
		$query = $this->CI->db->get_where($this->table, [$this->username => $username], 1);
		if($query->num_rows() === 1) {
			$user = $query->row();

			if($this->CI->hash->match($password, $user->{$this->password})) {
				return [
					'success' => 1,
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
}
