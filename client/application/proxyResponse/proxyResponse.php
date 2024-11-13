<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	class proxyResponse
	{

		private $soapResponse;
		private $header = false;
		private $failureMsg = "Une erreur s'est produite durant l'opération.";
		private $successMsg = "L'opération s'est déroulée avec succès.";
		private $data = array();

		public function __construct()
		{

		}

		public function getData()
		{
			return $this->data;
		}

		public function setData($data)
		{
			$this->data = $data;
		}

		public function setParams(array $params)
		{
			$this->header = (isset($params['method']) && $params['method'] == 'request') ? true : false;
		}

		public function setSoapResponse($value)
		{
			$this->soapResponse = $value;
			unset($value['status']);
			reset($value);
			$this->data = current($value);
		}

		public function getJsonData()
		{
			return json_encode(array(
					'success' => true,
					'msg' => isset($this->soapResponse['status']->message) ? $this->soapResponse['status']->message : $this->successMsg,
					'reponse' => $this->data
				));
		}

		public function getProxyResponse()
		{
			return $this->getJsonData();
		}

	}

?>