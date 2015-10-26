<?php
require_once('unittest/bootstrap.php');
class ApiTests extends \Assets\SuiteBase {
	public function addTests(\PHPUnit_Framework_TestSuite $suite) {
		$suite->addTestSuite('\ApiTests\Apitest');
	}
}