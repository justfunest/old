<?php
$config = array(
	'mysql' => array(
		'host' => 'db1.localnet',
		'database' => 'phptest_raudsepp',
		'user' => 'crbms',
		'password' => 'verbatim2000'
	),
	'pipedrive' => array(
		'api_url' => 'https://api.pipedrive.com/v1/',
		'api_token' => 'fa6ad05eda5c2b27ecc491e318f8b16effd70865',
		'org_owner_id' => '893320',
	),
	'requestMap' => array(
		'/organization' => 'Organization'
	),
	'logging' => array(
		'enabled' => array(
			'Pipe\Controller\Organization',
			'Pipe\Mapper\Organization',
			'Pipe\Mapper\OrganizationRelationship',
			'Pipe\PipeApi'
		),
		'dir' => '/tmp/'
	)
);