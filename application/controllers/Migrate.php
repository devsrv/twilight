<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller
{
	public function __construct() {
		parent::__construct();

		$this->load->library('twilight/migration/migrator');
	}

	/**
	 * latest created migration file run ignoring $config['migration_version']
	 */
	public function index()
	{
		echo $this->migrator->migrateAll();
	}

	/**
	 * roll back changes or step forwards to specific version
	 * @param string|integer version number
	 */
	public function jumpTo()
	{
		echo $this->migrator->jumpTo("20200528124400");
	}

	/**
	 * @param string "back" / "forward"
	 */
	public function step()
	{
		try {
			echo $this->migrator->step("forward", 1);
		} catch (\Exception $e) {
			show_error($e->getMessage());
		}
	}
}
