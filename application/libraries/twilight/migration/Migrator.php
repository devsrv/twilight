<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrator
{
	protected $CI;

	public function __construct() {
		$this->CI =& get_instance();

		$this->CI->load->library('migration');

		$this->CI->load->database('default');

		$this->CI->load->config('migration', TRUE);
	}

	/**
	 * run all latest created migration files ignoring $config['migration_version']
	 */
	public function migrateAll()
	{
		$result = $this->CI->migration->latest();

		if(is_bool($result)) {
			if ($result === FALSE) {
				show_error($this->CI->migration->error_string());
			}
			else {
				return 'nothing to migrate';
			}
		}
		else {
			return 'migration created, currently pointer at migration version <code>'.$result.'</code>';
		}
	}
	
	/**
	 * can be used to roll back changes or step forwards programmatically to specific versions
	 */
	public function jumpTo($version) : string
	{
		$result = $this->CI->migration->version($version);

		if(is_bool($result)) {
			if ($result === FALSE) {
				show_error($this->CI->migration->error_string());
			}
			else {
				return 'No where to jump, already migration at <code>'.$version.'</code>';
			}
		}
		else {
			return 'Jumped to <code>'.$result.'</code>';
		}
	}

	/**
	 * roll back changes or step forwards by integer step number
	 */
	public function step(string $towards = 'back', int $n = 1) : string
	{
		if(! in_array($towards, ['back', 'forward'])) throw new Exception("direction nonprocessable, accept only - back / forward");
		if($n <= 0) throw new Exception("step number must be positive integer");

		$versions = $this->getAllVersions();
		$targetIndex = $this->getTargetIndex($towards, $n);

		if($targetIndex === -1) {
			return $this->jumpTo(0);
			die();
		}
		else if(array_key_exists($targetIndex, $versions)) {
			return $this->jumpTo($versions[$targetIndex]);
			die();
		}
		else {
			return "Migration step range not available, can't step ". $towards . ' to '. $n;
			die();
		}
	}

	/**
	 * calculate which version to jump
	 */
	private function getTargetIndex(string $towards = 'back', int $n = 1)
	{
		$versions = $this->getAllVersions();

		$query = $this->CI->db->get($this->CI->config->item('migration_table', 'migration'));
		$result = $query->row();
		$currentVersion = $query->num_rows() !== 0? $result->version : 0;

		$currentVersionIndex = array_search($currentVersion, $versions);

		if($currentVersionIndex === FALSE && $currentVersion != 0) {
			throw new Exception("Error! possible migration file missing");
		}

		if($currentVersion == 0) {
			if($towards === 'back') {
				echo "Migration can't rollback any further";
				die();
			}

			$targetIndex = $n - 1;
		}
		else {
			if($currentVersion == end($versions) && $towards === 'forward') {
				echo "Migration can't step forward any further";
				die();
			}

			$targetIndex = $towards === 'back'? $currentVersionIndex - $n : $currentVersionIndex + $n;
		}

		return $targetIndex;
	}

	/**
	 * return all migration file versions in ascending order
	 */
	private function getAllVersions() : array
	{
		$migrationFiles = $this->CI->migration->find_migrations();
		$fileNames = array_map('basename', $migrationFiles);

		$versions = [];

		foreach($fileNames as $fileName) {
			$partials = explode('_', $fileName);
			$versions[] = $partials[0];
		}

		sort($versions);
		
		return $versions;
	}
}
