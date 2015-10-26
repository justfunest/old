<?php

namespace Utils;

/**
 * Class AutoLoad
 * @package Utils
 */
class AutoLoad {
	/**
	 * @var array
	 */
	private $nameSpaceMap = array();

	public function __construct() {
		return spl_autoload_register(array($this, 'autoload'));
	}

	/**
	 * @param $path
	 */
	public function addNameSpaceMap($path) {
		$this->nameSpaceMap[] = $path;
	}

	/**
	 * @param $className
	 * @return bool
	 */
	public function autoload($className) {
		foreach ($this->nameSpaceMap as $path) {
			$fileName = $path . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.class.php';
			if (file_exists($fileName)) {
				require_once $fileName;
				return true;
			}
		}
		return false;
	}
}
