<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hash {
	protected $CI;
	private array $hash_config;

	private static $algorithms = [
		'bcrypt' => PASSWORD_BCRYPT,
		'argon' => PASSWORD_ARGON2I,
		'argon2id' => PASSWORD_ARGON2ID,
	];

	public function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->config->load('encryption', TRUE);

		$this->hash_config = $this->CI->config->item('hash', 'encryption');

		if(! array_key_exists($this->hash_config['algorithm'], self::$algorithms)) {
			throw new Exception('Hash Algorithm not supported');
		}
	}

	/**
	 * @paaram string $text
	 * @return string hashed output
	 */
	public function make(string $text) : string
	{
		$options = $this->hash_config['algorithm'] === 'bcrypt'? 
													$this->hash_config['bcrypt'] : 
													$this->hash_config['argon'];

		return password_hash(
			$text, 
			self::$algorithms[$this->hash_config['algorithm']], 
			$options
		);
	}

	/**
	 * verify if hash match
	 */
	public function match(string $text, string $hash) : bool
	{
		return password_verify($text, $hash);
	}
}
