<?php
namespace Pipe;
class Organization {
	const VISIBLE_TO_COMPANY = 3;

	private $name = '';
	private $pipeId = 0;
	private $pipeRelId = 0;

	/**
	 * @return int
	 */
	public function getPipeRelId() {
		return $this->pipeRelId;
	}

	/**
	 * @param int $pipeRelId
	 * @return Organization
	 */
	public function setPipeRelId($pipeRelId) {
		$this->pipeRelId = (int)$pipeRelId;
		return $this;
	}
	private $id = 0;

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return Organization
	 */
	public function setId($id) {
		$this->id = (int)$id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPipeId() {
		return $this->pipeId;
	}

	/**
	 * @param int $pipeId
	 * @return Organization
	 */
	public function setPipeId($pipeId) {
		$this->pipeId = (int)$pipeId;
		return $this;
	}
	private $daughters = array();

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return Organization
	 */
	public function setName($name) {
		$this->name = (string)$name;
		return $this;
	}

	/**
	 * @return Organization[]
	 */
	public function getDaughters() {
		return $this->daughters;
	}

	/**
	 * @param array $daughter
	 * @return Organization
	 */
	public function addDaughter(Organization $daughter) {
		$this->daughters[] = $daughter;
		return $this;
	}
}
