<?php
namespace Utils;

/**
 * Class Config
 * @package Utils
 */
class Config {
	/**
	 * @var null
	 */
	protected static $data = null;

	/**
	 * @param array $config
	 */
	public static function init(array $config) {
		self::$data = $config;
	}

	/**
	 * @param $variable
	 * @return bool
	 */
	public static function get($variable) {
		if (!is_null(self::$data)) {
			if (isset(self::$data[$variable])) {
				return self::$data[$variable];
			} else {
				return false;
			}
		}
		return false;
	}
}
