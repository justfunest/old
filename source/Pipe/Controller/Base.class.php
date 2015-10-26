<?php
namespace Pipe\Controller;

/**
 * Class Base
 * @package Pipe\Controller
 */
abstract class Base extends \Pipe\Base {

	/**
	 * @var string
	 */
	protected $requestType = '';


	/**
	 * @return string
	 */
	public function getRequestType() {
		return $this->requestType;
	}

	/**
	 * @param string $requestType
	 * @return Base
	 */
	public function setRequestType($requestType) {
		$this->requestType = $requestType;
		return $this;
	}
}