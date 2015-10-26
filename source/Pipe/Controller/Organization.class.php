<?php
namespace Pipe\Controller;

class Organization extends Base {
	public function run() {
		switch ($this->getRequestType()) {
			case 'POST':
				$this->save();
				break;
			case 'GET':
				$this->load();
				break;
			case 'DELETE':
				$this->delete();
				break;
		}
	}

	public function save() {
		$postData = file_get_contents('php://input');
		$organizationData = json_decode($postData, true);

		if ($organizationData) {
			$orgMap = new \Pipe\Mapper\Organization($this->getDb());
			$relationMap = new \Pipe\Mapper\OrganizationRelationship($this->getDb());
			$organization = $orgMap->parseOrganization($organizationData);
			try {
				//$orgMap->deleteAllFromPipe();
				//$this->delete();

				$orgMap->saveToPipe($organization);
				$relationMap->saveToPipe($organization);
				$orgMap->saveToLocal($organization);
				$relationMap->saveToLocal($organization);
			} catch (\Exception $e) {
				$this->printJson(array(
					'success' => false,
					'error' => $e->getMessage()
				));
				return false;
			}
		}
		$this->printJson(array(
			'success' => true
		));
	}


	public function load() {
		if (isset($_GET['org_id'])) {
			$relationMap = new \Pipe\Mapper\OrganizationRelationship($this->getDb());
			$org = $relationMap->loadFromLocal($_GET['org_id']);
			$orgMap = new \Pipe\Mapper\Organization($this->getDb());
			$this->printJson($orgMap->getAsArray($org));

		}
	}

	public function delete() {
		(new \Pipe\Mapper\Organization($this->getDb()))->deleteFromLocal();
		(new \Pipe\Mapper\OrganizationRelationship($this->getDb()))->deleteFromLocal();
	}
}