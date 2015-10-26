<?php
namespace Pipe;
use Utils\DB;
use Utils\Log;

/**
 * Class Base
 * @package Pipe
 */
abstract class Base {
	/**
	 * @var Log
	 */
	protected $log;
	protected $db;

	/**
	 *
	 */
	public function __construct(DB $db = null) {
		$this->log = new Log(get_called_class());
		$this->setDb($db);
	}

	/**
	 * @param array $data
	 */
	public function printJson(array $data) {
		header('Content-Type: application/json');
		echo json_encode($data);
	}

	/**
	 * @param \Exception $e
	 * @throws \Exception
	 */
	public function logAndForwardException(\Exception $e) {
		$this->log->write('FAIL:' . $e->getMessage());
		throw $e;
	}

	/**
	 * @return Log
	 */
	public function getLog() {
		return $this->log;
	}

	/**
	 * @param Log $log
	 * @return Base
	 */
	public function setLog($log) {
		$this->log = $log;
		return $this;
	}

	/**
	 * @return null | \Utils\DB
	 */
	public function getDb() {
		return $this->db;
	}

	/**
	 * @param \Utils\DB $db
	 * @return $this
	 */
	public function setDb(\Utils\DB $db = null) {
		$this->db = $db;
		return $this;
	}
}