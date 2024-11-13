<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class wsClient
	{

		private $service;
		private $soapClient;
		private $soapOptions = array(
			'trace' => 1,
			'soap_version' => SOAP_1_1,
			'compression' => SOAP_COMPRESSION_ACCEPT,
			'cache_wsdl' => WSDL_CACHE_NONE
		);

		public function __construct($service)
		{
			$this -> service = $service;
			
			$wsdl = 'TS_WS' . strtoupper($this -> service) . '_URL';
			if (!defined($wsdl))
			{
				throw new Exception("Ce service n'existe pas : " . $this -> service);
			}
			
			$this -> soapClient = new SoapClient(constant($wsdl), $this -> soapOptions);
		}
		
		public function __call($method, array $arguments)
		{
			$functions = $this -> soapClient -> __getFunctions();
			
			$methodExists = false;
			foreach ($functions as $function)
			{
				if (preg_match('/' . $method . '\((.*)\)/', $function, $matches))
				{
					$methodExists = true;
					$params = isset($matches[1]) ? explode(', ', $matches[1]) : array();
				}
			}
			
			if ($methodExists)
			{
				// Permet de setter les paramètres optionnels et de bien pusher les hookParams en dernier
				for($i=0 ; $i<(count($params)-1) ; $i++)
				{
					if (!isset($arguments[$i]))
					{
						$arguments[$i] = null;
					}
				}
				
				// Hook Params
				$arguments[] = tsPlugins::getHookParams($this -> service, $method);
				
				$response = call_user_func_array(array($this -> soapClient, $method), $arguments);
				
				// Hook Responses
				tsPlugins::setHookResponses($this -> service, $method, (array) $response['hookResponses']);
				unset($response['hookResponses']);
				
				if ($response['status'] -> success === false)
				{
					if ($response['status']->errorCode == 510)
					{
						foreach ($_SESSION as $k => $v)
						{
							unset(PSession::$SESSION[$k]);
						}
						PSession::destroy();

						throw new SessionException("La session est expirée");
					}
					else
					{
						throw new Exception($response['status'] -> message);
					}
				}
				
				return $response;
			}
			else
			{
				throw new Exception("La méthode : " . $method . " n'existe pas");
			}
		}

	}

?>