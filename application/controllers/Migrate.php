<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller
{
	public function __construct() {
		parent::__construct();

		$this->load->library('migration');

		$this->load->database('default');

		$this->load->config('migration', TRUE);
	}

	/**
	 * latest created migration file run ignoring $config['migration_version']
	 */
	public function index()
	{
		$result = $this->migration->latest();

		if(is_bool($result)) {
			if ($result === FALSE) {
				show_error($this->migration->error_string());
			}
			else {
				echo 'nothing to migrate';
			}
		}
		else {
			echo 'migration created, currently pointer at migration version <code>'.$result.'</code>';
		}
	}
	
	/**
	 * can be used to roll back changes or step forwards programmatically to specific versions
	 */
	public function jumpTo($version)
	{
		$result = $this->migration->version($version);

		if(is_bool($result)) {
			if ($result === FALSE) {
				show_error($this->migration->error_string());
			}
			else {
				echo 'No where to jump, already migration at <code>'.$version.'</code>';
			}
		}
		else {
			echo 'Jumped to <code>'.$result.'</code>';
		}
	}

	/**
	 * roll back changes or step forwards by integer step number
	 */
	public function step(string $towards = 'back', int $n = 1)
	{
		if(! in_array($towards, ['back', 'forward'])) throw new Exception("direction nonprocessable, accept only - back / forward");
		if($n <= 0) throw new Exception("step number must be positive integer");

		$versions = $this->getAllVersions();
		$targetIndex = $this->getTargetIndex($towards, $n);

		if($targetIndex === -1) {
			echo $this->jumpTo(0);
			die();
		}
		else if(array_key_exists($targetIndex, $versions)) {
			echo $this->jumpTo($versions[$targetIndex]);
			die();
		}
		else {
			echo "Migration step range not available, can't step ". $towards . ' to '. $n;
			die();
		}
	}

	/**
	 * calculate which version to jump
	 */
	private function getTargetIndex(string $towards = 'back', int $n = 1)
	{
		$versions = $this->getAllVersions();

		$query =$this->db->get($this->config->item('migration_table', 'migration'));
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
		$migrationFiles = $this->migration->find_migrations();
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
