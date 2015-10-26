<?php
namespace Utils;
class Log {
	private $enabled = false;
	private $logDir = '';
	private $logName = '';
	static $filePointer=array();

	public function __construct($logName = '') {
		$this->logName = $logName;
		$this->logDir = Config::get('logging')['dir'];
		if (in_array($logName, Config::get('logging')['enabled'])) {
			$this->enabled = true;
		}
	}

	public function write($message) {
		if (!$this->enabled) {
			return;
		}

		if (is_array($message) || is_object($message)) {
			$message = print_r($message, true);
		}

		return $this->writeFile($message);
	}

	private function writeFile($message) {
		if (!isset(Log::$filePointer[$this->logName])) {
			Log::$filePointer[$this->logName] = fopen($this->logDir. str_replace("\\", "_", $this->logName) . '.log', "a");
		}

		if (Log::$filePointer[$this->logName]) {
			fwrite(Log::$filePointer[$this->logName],date('[d.m.Y H:i:s] ') . $message . "\n");
		}
		return true;
	}

}
