<?php

namespace Bissolli\FullContact;

class FullContact
{
	const USER_AGENT = 'fullcontact-php';

	protected $_baseUri = 'https://api.fullcontact.com/';
	protected $_version = 'v2';

	protected $_apiKey = null;
	protected $_bearerApiKey = null;

	public $response_obj  = null;
	public $response_code = null;
	public $response_json = null;

	/**
     * The base constructor needs the API key available from here:
     * http://fullcontact.com/getkey
     *
     * @param type $api_key
     */
	public function __construct($api_key)
	{
		$this->_apiKey = $api_key;
		$this->_bearerApiKey = strpos($api_key, 'Bearer ') === false ? 'Bearer '.$api_key : $api_key;
	}

    /**
     * @param   array $params
     * @param null $resourceUri
     * @return  object
     * @throws FullContactExceptionNotImplemented
     * @throws ServicesFullContactExceptionNoCredit
     */
	protected function _execute($params = [], $resourceUri = null)
	{
		if(!in_array($params['method'], $this->_supportedMethods)){
			throw new FullContactExceptionNotImplemented(__CLASS__ .
			" does not support the [" . $params['method'] . "] method");
		}

		if($resourceUri === NULL)
		{
			$fullUrl = $this->_baseUri . $this->_version . $this->_resourceUri .'?' . http_build_query($params);

		}
		else
		{
			$fullUrl = $this->_baseUri . $this->_version . $resourceUri .'?' . http_build_query($params);

		}

		if ($resourceUri === "/stats.json")
		{
			$cached_json = false;
		}
		else
		{
			$cached_json = $this->_getFromCache($fullUrl);
		}

		if ( $cached_json !== false )
		{
			$this->response_json = $cached_json;
			$this->response_code = 200;
			$this->response_obj  = json_decode($this->response_json);
		}
		else
		{
			// create header
			$header = [];
			$header[] = 'Content-type: application/json';
			$header[] = 'Authorization: '.$this->_bearerApiKey;

			//open connection
			$connection = curl_init($fullUrl);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_USERAGENT, self::USER_AGENT);

			//execute request
			$this->response_json = curl_exec($connection);
			$this->response_code = curl_getinfo($connection, CURLINFO_HTTP_CODE);
			if ( '200' == $this->response_code )
			{
				$this->_saveToCache($fullUrl, $this->response_json);
			}
			$this->response_obj  = json_decode($this->response_json);

			curl_close($connection);

			if ('403' == $this->response_code) {
				throw new ServicesFullContactExceptionNoCredit($this->response_obj->message);
			}
		}

		return $this->response_obj;
	}

	protected function _saveToCache($url, $response_json)
	{
		$cache_path = 'FullContactCache/';
		$cache_file_name = $cache_path.'/'.md5(urldecode($url)).'.json';

		return \Storage::put($cache_file_name, $response_json);
	}

	protected function _getFromCache($url)
	{
		$cache_path = 'FullContactCache/';
		$cache_file_name = $cache_path.'/'.md5(urldecode($url)).'.json';

		if ( \Storage::exists($cache_file_name) )
		{
			$json_content = \Storage::get($cache_file_name);
			return $json_content;
		}

		return false;
	}
}
