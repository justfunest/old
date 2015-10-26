<?php
namespace Pipe;
use Utils\Config;
use Utils\DB;

class Api {
	protected $requestType;
	protected $db = null;

	public function __construct(DB $db = null) {
		$this->db = $db;
	}

	public function run() {
		$request = $_REQUEST['_url'];
		if (in_array($request, array_keys(Config::get('requestMap')))) {
			$controllerName = '\\Pipe\\Controller\\' . Config::get('requestMap')[$request];
			(new $controllerName)
				->setRequestType($_SERVER['REQUEST_METHOD'])
				->setDb($this->db)
				->run();
		} else {
			echo json_encode(array(
				'success' => false,
				'error' => "Unknown request {$request}"
			));
		}
	}
}