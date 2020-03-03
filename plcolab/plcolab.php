<?php
namespace PLColab;

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Post\PostFile;
use GuzzleHttp\Exception\ClientException;


session_start();

class ApiClient
{
	/** @var string root API uri */
    private $rootUri;

	/** @var string user name */
    private $user;

	/** @var string user password */
    private $password;

	/** @var string subscription key */
    private $subscriptionKey;

	/** @var string tenant id (optional) */
    private $tenantId;
	
	/** @var string url for proxy (optional) */
    private $proxyUrl;

	/** @var string last call response */
    public $LastResponse;

    /** @var bool true if login has already taken place */
	private $tokenIsCached;
	
    public function __construct(
        $rootUri, 
        $user, 
		$password, 
		$subscriptionKey,
		$tenantId = null
		)
	{
		$this->rootUri = $rootUri;
		$this->user = $user;
		$this->password = $password;
		$this->subscriptionKey = $subscriptionKey;
		$this->tenantId = $tenantId;
	}


	private function getTokenKey()
	{
		$key = "Token_".$this->user;
		if ($this->tenantId)
		{
			$key .= "|".$this->tenantId;
		}
		return $key;
	}
	
	private function getToken()
	{
		if ( isset($_SESSION[$this->getTokenKey()]) )
		{
			return $_SESSION[$this->getTokenKey()];
		}
		else {
			return null;
		}
	}
	
	private function setToken($token)
	{
		$_SESSION[$this->getTokenKey()] = $token;
	}
	
	public function findToken()
	{
		$token = $this->getToken();
		$this->tokenIsCached = 1;
		if ($token == null)
		{
			$this->tokenIsCached = 0;
			$json = $this->login();
			$token = $json['accessToken'];
			$this->setToken($token);
		}
		
		return $token;
	}
	
	public function getTokenIsCached()
	{
		return $this->tokenIsCached;
	}
	
	
	public function login()
	{
		$client = $this->getClient();
		$loginBody = ['u' => $this->user, 'p' => $this->password];
		if ($this->tenantId)
		{
			$loginBody['t'] = $this->tenantId;
		}
		
		$request = $client->createRequest('POST', '/Auth/Login', ['json' => $loginBody]);
		$request->setHeader('Content-Type', 'application/json');
		$request->setHeader('X-Who', $this->subscriptionKey);
		
		$response = $client->send($request);
		$json = $response->json();
		
		$this->LastResponse = $response->getBody();
		
		$statusCode = $response->getStatusCode();
		if ($statusCode != 200)
		{
            throw new \InvalidArgumentException($response->getStatusCode() . ": " . $response->getStatusPhrase());
		}
		
		return $json;
	}
	
	public function setAuthentication($request)
	{
		$token = $this->findToken();
		$request->setHeader('X-Who', $this->subscriptionKey);
		$request->setHeader('Authorization', "Bearer ".$token);
		$request->setHeader('X-KEYCONTROL', "fc8eac422eba16e22ffd8c6f94b3f40a6e38162c");//este dato en de cada resolucion de la DIAN, general variable para que sea de actualizacion por SF
	}


	public function setCustomHeader($request, $options, $optionsKey, $headerName, $defaultValue = null)
	{
		if (array_key_exists($optionsKey, $options)) {
			$request->setHeader($headerName, $options[$optionsKey]);
		}
		else if ($defaultValue != null) {
			$request->setHeader($headerName, $defaultValue);
		}
	}
	
	public function useProxy()
	{
		$this->proxyUrl = "http://localhost:8888";
	}
	
	private function getClient()
	{
		$defaults = [];
		if ($this->proxyUrl)
		{
			$defaults['proxy'] = $this->proxyUrl;
			// DISABLE SSL certificate check
			$defaults['verify'] = false;
		}

		$client = new Client([
			'base_url' => $this->rootUri,
			'defaults' => $defaults
		]);

		return $client;
	}

	public function Issue($documentType, $xml, $options, $attachmentPaths = null)
	{
		$validDocumentTypes = array("FACTURA-UBL", "NC-UBL", "ND-UBL", "FACTURA-CONTINGENCIA-UBL", "FA_EXPORTACION", 
									"FA_DIGITAL", "RECAUDO", "RFQ", "ORDER", "QUOTE", "ESTADO_CUENTA");
		if (!in_array($documentType, $validDocumentTypes)) {
			throw new \InvalidArgumentException("Invalid document type: '".$documentType."'");
		}
		
		$client = $this->getClient();
		$request = $client->createRequest('POST', 'Issue/XML3');
		$this->setAuthentication($request);
		$request->setHeader('X-REF-DOCUMENTTYPE',  $documentType);
		

		$postBody = $request->getBody();
		$postBody->setField('application/xml', $xml);
		if ($attachmentPaths)
		{
			foreach ($attachmentPaths as $attachmentPath)
			{
				$postBody->addFile(new PostFile(basename($attachmentPath), fopen($attachmentPath, 'r')));
			}
		}
		try{
			$response = $client->send($request);
		}
		catch(ClientException $e){
			
			$response = $e->getResponse();
			$responseBodyAsString = $response->getBody()->getContents();
			
			echo $responseBodyAsString;
		}
		
		return $response->json();
	}
}
?>
