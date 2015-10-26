<?php
namespace Pipe;

use Utils\Config;

class PipeApi extends Base {
	/**
	 * @param array $requestData
	 * @return resource
	 */
	const REQUEST_POST = 1;
	const REQUEST_GET = 2;
	const REQUEST_DELETE = 3;

	private $requestUrl;

	public function getCurl(array $requestData = null, $endPoint, $requestType = PipeApi::REQUEST_POST) {
		$this->requestUrl = Config::get('pipedrive')['api_url'] . $endPoint . '?api_token=' . Config::get('pipedrive')['api_token'];

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->requestUrl);

		switch ($requestType) {
			case PipeApi::REQUEST_POST:
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $requestData);
				break;
			case PipeApi::REQUEST_DELETE:
				curl_setopt($curl, CURLOPT_POSTFIELDS, is_array($requestData) ? http_build_query($requestData) : $requestData);
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
		}

		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 100);
		curl_setopt($curl, CURLOPT_TIMEOUT, 100);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		return $curl;
	}

	/**
	 * @param array $requestData
	 * @return mixed
	 * @throws \Exception
	 */
	public function makeRequest(array $requestData = null, $endPoint, $requestType = PipeApi::REQUEST_POST) {
		$curl = $this->getCurl($requestData, $endPoint, $requestType);
		$this->log->write('Request ' . print_r($requestData, true) .' Request url ' . $this->requestUrl);
		$response = curl_exec($curl);
		$this->log->write('Response ' . $response);
		$error = curl_error($curl);
		if ($error) {
			throw new \Exception('Curl error:' . $error);
		}
		curl_close($curl);

		return $response;
	}

}