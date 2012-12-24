<?php

class RestRequest {
	
	public static function send($url, $data=null, $method='GET', $curl_options=array()) {
		$data	= self::prepareData($data);
		$handle	= curl_init();
		
		self::setCurlOption($handle, CURLOPT_RETURNTRANSFER, true, $curl_options);
		
		if($method == 'POST') :
			curl_setopt($handle, CURLOPT_POST, true);
			if ($data) :
				curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
			endif;
		elseif($method == 'GET') :
			if (strpos($url, "?") !== false) :
				$url .= "&".$data;
			else :
				$url .= "?".$data;
			endif;
		endif;
		
		curl_setopt($handle, CURLOPT_URL, $url);
		
		if (!empty($curl_options)) :
			foreach($curl_options as $option => $value) :
				curl_setopt($handle, $option, $value);
			endforeach;
		endif;
		
		$receive_headers	= isset($curl_options[CURLOPT_HEADER]);
		$response			= new RestResponse($handle, $receive_headers);
		
		return $response;
	}
	
	private static function setCurlOption(&$handle, $name, $value, $curl_options=array()) {
		$options	= array_keys($curl_options);
		if (in_array($name, $options)) :
			return false;
		else :
			curl_setopt($handle, $name, $value);
			return true;
		endif;
	}
	
	private static function prepareData($data) {
		if (is_array($data)) :
			$data = http_build_query($data);
		endif;
		return $data;
	}
	
}

class RestResponse {
	protected $headers	= array();
	protected $body 	= null;
	protected $info		= null;
	protected $error	= null;
	
	public function __construct(&$handle, $receive_headers) {
		$this->processRequest($handle, $receive_headers);
	}
	
	protected function processRequest(&$handle, $receive_headers) {
		$response = curl_exec($handle);
		
		if ($receive_headers) :
			list($headers, $body) = explode("\r\n\r\n", $response, 2);
			$this->setHeaders($headers);
		else :
			$body = $response;
		endif;
				
		$this->setBody($body);
		$this->setInfo(curl_getinfo($handle));
		$this->setErrors($handle);
		curl_close($handle);
	}
	
	public function setErrors(&$handle) {
		$this->error			= new stdClass();
		$this->error->code		= null;
		$this->error->message	= null;
		
		if(curl_errno($handle)) :
			$this->error->code		= curl_errno($handle);
			$this->error->message	= curl_error($handle);
			return;
		endif;

		$code	= $this->getInfo('http_code');
		if ($code >= 400 && $code < 600) :
			$this->error->code = $code;
			$this->error->message = $this->getStatusMessage($code);
		endif;
		
	}
	
	public function hasError() {
		if ($this->error->code) :
			return true;
		else :
			return false;
		endif;
	}
	
	public function getErrorCode() {
		return $this->error->code;
	}
	
	public function setErrorCode($code) {
		$this->error->code = $code;
		return $this->error->code;
	}
	
	public function getErrorMsg() {
		return $this->error->message;
	}
	
	public function setErrorMsg($msg) {
		$this->error->message = $msg;
		return $this->error->message;
	}
	
	public function getBody() {
		return $this->body;
	}
	
	public function setBody($body) {
		$this->body = $body;
		return $this->body;
	}
	
	protected function setInfo($info) {
		$this->info	= $info;
		return $this->info;
	}
	
	protected function setHeaders($header_string) {
		$headers		= explode("\r\n", $header_string);
		$this->headers	= $headers;
		return $this->headers;
	}
	
	public function getInfo($key=null) {
		if ($key) :
			if (isset($this->info[$key])) :
				return $this->info[$key];
			else :
				return null;
			endif;
		else :
			return $this->info;
		endif;
	}
	
	public static function getStatusMessage($status) {
		$codes = Array(
		    100  => 'Continue',
		    101  => 'Switching Protocols',
		    200  => 'OK',
		    201  => 'Created',
		    202  => 'Accepted',
		    203  => 'Non-Authoritative Information',
		    204  => 'No Content',
		    205  => 'Reset Content',
		    206  => 'Partial Content',
		    300  => 'Multiple Choices',
		    301  => 'Moved Permanently',
		    302  => 'Found',
		    303  => 'See Other',
		    304  => 'Not Modified',
		    305  => 'Use Proxy',
		    306  => '(Unused)',
		    307  => 'Temporary Redirect',
		    400  => 'Bad Request',
		    401  => 'Unauthorized',
		    402  => 'Payment Required',
		    403  => 'Forbidden',
		    404  => 'Not Found',
		    405  => 'Method Not Allowed',
		    406  => 'Not Acceptable',
		    407  => 'Proxy Authentication Required',
		    408  => 'Request Timeout',
		    409  => 'Conflict',
		    410  => 'Gone',
		    411  => 'Length Required',
		    412  => 'Precondition Failed',
		    413  => 'Request Entity Too Large',
		    414  => 'Request-URI Too Long',
		    415  => 'Unsupported Media Type',
		    416  => 'Requested Range Not Satisfiable',
		    417  => 'Expectation Failed',
		    500  => 'Internal Server Error',
		    501  => 'Not Implemented',
		    502  => 'Bad Gateway',
		    503  => 'Service Unavailable',
		    504  => 'Gateway Timeout',
		    505  => 'HTTP Version Not Supported'
		  );
		if (isset($codes[$status])) :
			return $codes[$status];
		else :
			return null;
		endif;
	}
	
}