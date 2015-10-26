<?php
namespace Pipe\Mapper;

use Pipe\Organization;
use Pipe\PipeApi;

/**
 * Class OrganizationRelationship
 * @package Pipe\Mapper
 */
class OrganizationRelationship extends \Pipe\Base {

	/**
	 * @param \Pipe\Organization $org
	 * @throws \Exception
	 */
	public function saveToPipe(\Pipe\Organization $org) {
		if (count($org->getDaughters())) {
			foreach ($org->getDaughters() as $daughter) {
				$postData = array(
					'type' => \Pipe\OrganizationRelationship::TYPE_PARENT,
					'rel_owner_org_id' => $org->getPipeId(),
					'rel_linked_org_id' => $daughter->getPipeId()
				);
				$this->log->write('Saving ' . print_r($postData, true) . 'to pipe drive');
				try {
					$responseData = (new PipeApi())->makeRequest($postData, 'organizationRelationships');
					$response = json_decode($responseData, true);
				} catch (\Exception $e) {
					$this->logAndForwardException($e);
				}
				$this->log->write('Response' . print_r($response, true));

				if ($response) {
					$this->saveToPipe($daughter);
					if (!$response['success']) {
						throw new \Exception($response['error']);
					} else {
						$daughter->setPipeRelId($response['data']['id']);
					}
				} else {
					throw new \Exception('No RESPONSE!');
				}
			}
		}
	}

	public function saveToLocal(\Pipe\Organization $org) {
		if (count($org->getDaughters())) {
			foreach ($org->getDaughters() as $daughter) {
				$this->getDb()->query('
					INSERT INTO organizationRelation
					SET
						rel_owner_org_id = :rel_owner_org_id,
						rel_linked_org_id = :rel_linked_org_id,
						rel_type = :rel_type
				', array(
					'rel_owner_org_id' => $org->getId(),
					'rel_linked_org_id' => $daughter->getId(),
					'rel_type' => \Pipe\OrganizationRelationship::TYPE_PARENT
				));
				$this->saveToLocal($daughter);
			}
		}
	}

	public function loadFromLocal($pipeOrganizationId) {
		$org = null;
		//check for parent
		$result = $this->getDb()->query('
			SELECT oo.*
			FROM organization o
			JOIN organizationRelation o_rel ON
				o.id = o_rel.rel_linked_org_id AND o.pipe_id = :pipe_id
			JOIN organization oo ON
				o_rel.rel_owner_org_id = oo.id
			LIMIT 1
		', array(
			'pipe_id' => $pipeOrganizationId
		));
		if ($result && $row = $this->getDb()->fetchRow($result)) {
			$org = $this->setDataFromArray($row, new Organization());
			$this->loadDaughters($org);
		} else {
			$result = $this->getDb()->query('
				SELECT o.* FROM organization o
				JOIN organizationRelation o_rel ON
					o.id = o_rel.rel_owner_org_id AND o.pipe_id = :pipe_id
			', array(
				'pipe_id' => $pipeOrganizationId
			));

			while ($result && $row = $this->getDb()->fetchRow($result)) {
				$org = $this->setDataFromArray($row, new Organization());
				$this->loadDaughters($org);
			}
		}

		return $org;
	}

	public function loadDaughters(\Pipe\Organization $org) {
		$result = $this->getDb()->query('
			SELECT o.* FROM organization o
			JOIN organizationRelation o_rel ON
				o.id = o_rel.rel_linked_org_id AND o_rel.rel_owner_org_id = :id
		', array(
			'id' => $org->getId()
		));
		while ($result && $row = $this->getDb()->fetchRow($result)) {
			$daughter = $this->setDataFromArray($row, new Organization());
			$org->addDaughter($daughter);
			$this->loadDaughters($daughter);
		}
	}

	public function setDataFromArray(array $data, \Pipe\Organization $org) {
		$org
			->setId($data['id'])
			->setName($data['name'])
			->setPipeId($data['pipe_id'])
			->setPipeRelId($data['pipe_rel_id']);
		return $org;
	}

	public function deleteFromLocal() {
		$this->getDb()->query('TRUNCATE organizationRelation');
	}
}