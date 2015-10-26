<?php
namespace Pipe\Mapper;

use Pipe\OrganizationRelationship;
use Pipe\PipeApi;
use Utils\Config;

/**
 * Class Organization
 * @package Pipe\Mapper
 */
class Organization extends \Pipe\Base {


	/**
	 * @param \Pipe\Organization $org
	 */
	public function saveToLocal(\Pipe\Organization $org) {
		$this->getDb()->query('
			INSERT INTO organization
			SET
				name = :name,
				pipe_id = :pipe_id,
				pipe_rel_id = :pipe_rel_id
		', array(
			'name' => $org->getName(),
			'pipe_id' => $org->getPipeId(),
			'pipe_rel_id' => $org->getPipeRelId()
		));

		$org->setId($this->getDb()->getLastInsertId());
		if (count($org->getDaughters())) {
			foreach ($org->getDaughters() as $daughter) {
				$this->saveToLocal($daughter);
			}
		}
	}

	/**
	 * @param \Pipe\Organization $org
	 * @throws \Exception
	 */
	public function saveToPipe(\Pipe\Organization $org) {
		$postData = array(
			'name' => $org->getName(),
			'owner_id' => Config::get('pipedrive')['org_owner_id'],
			'visible_to' => $org::VISIBLE_TO_COMPANY
		);

		$this->log->write('Saving ' .print_r($postData, true) . 'to pipe drive');
		try {
			$responseData = (new PipeApi())->makeRequest($postData, 'organizations');
			$response = json_decode($responseData, true);
		} catch (\Exception $e) {
			$this->logAndForwardException($e);
		}
		$this->log->write('Response' . print_r($response, true));

		if ($response) {
			if (!$response['success']) {
				throw new \Exception($response['error']);
			} else {
				$org->setPipeId($response['data']['id']);
			}
		} else {
			throw new \Exception('No RESPONSE!');
		}

		//save daughters
		if (count($org->getDaughters())) {
			foreach ($org->getDaughters() as $daughter) {
				$this->saveToPipe($daughter);
			}
		}
	}

	/**
	 * @param array $organizationData
	 * @return \Pipe\Organization
	 */
	public function parseOrganization(array $organizationData) {
		$organization = (new \Pipe\Organization())
			->setName($organizationData['org_name']);

		if (isset($organizationData['daughters'])) {
			foreach ($organizationData['daughters'] as $daughter) {
				$organization->addDaughter($this->parseOrganization($daughter));
			}
		}
		return $organization;
	}


	/**
	 * @return mixed|null
	 * @throws \Exception
	 */
	public function getAllIdsFromPipe() {
		$response = null;
		try {
			$responseData = (new PipeApi())->makeRequest(null, 'organizations', PipeApi::REQUEST_GET);
			$response = json_decode($responseData, true);
		} catch (\Exception $e) {
			$this->logAndForwardException($e);
		}
		$ids = array();
		if ($response && $response['success']) {
			foreach ($response['data'] as $org) {
				$ids[] = $org['id'];
			}
		}

		return $ids;
	}

	public function deleteFromLocal() {
		$this->getDb()->query('TRUNCATE organization');
	}

	/**
	 * @throws \Exception
	 */
	public function deleteAllFromPipe() {
		$ids = $this->getAllIdsFromPipe();
		$this->deleteFromPipe($ids);
	}

	/**
	 * @param array $ids
	 * @throws \Exception
	 */
	public function deleteFromPipe(array $ids) {
		if (!count($ids)) {
			return;
		}

		$requestData = array(
			'ids' => implode(',', $ids)
		);
		try {
			(new PipeApi())->makeRequest($requestData, 'organizations', PipeApi::REQUEST_DELETE);
		} catch (\Exception $e) {
			$this->logAndForwardException($e);
		}
	}

	public function getAsArray(\Pipe\Organization $org = null, &$response = null) {
		if (is_null($response)) {
			$response = array(
				'success' => true
			);
		}
		if (!is_null($org)) {
			if (count($org->getDaughters())) {
				foreach ($org->getDaughters() as $daughter) {
					$response['data'][] = array(
						'id' => $daughter->getPipeRelId(),
						'type' => OrganizationRelationship::TYPE_PARENT,
						'rel_owner_org_id' => array(
							'name' => $org->getName(),
							'value' => $org->getPipeId()
						),
						'rel_linked_org_id' => array(
							'name' => $daughter->getName(),
							'value' => $daughter->getPipeId()
						),
						'add_time' => '',
						'update_time' => '',
						'active_flag' => 1,
						'calculated_type' => OrganizationRelationship::TYPE_PARENT,
						'related_organization_name' => $daughter->getName(),
						'calculated_related_org_id' => $daughter->getPipeId()
					);
					$this->getAsArray($daughter, $response);
				}
			}
		}
		return $response;
	}
}