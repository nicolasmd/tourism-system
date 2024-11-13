<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pTerritoires extends tsProxy
	{

		public function getTerritoires($params)
		{
			$oWsTerritoires = new wsClient('territoires');
			$response = $oWsTerritoires->getTerritoires(PSession::$SESSION['tsSessionId']);

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array('libelle'));

			echo $oProxyStore->getProxyResponse();
		}

		public function createTerritoire($params)
		{
			$oWsTerritoires = new wsClient('territoires');
			$response = $oWsTerritoires->createTerritoire(PSession::$SESSION['tsSessionId'], $params['libelle']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function updateTerritoire($params)
		{
			$oWsTerritoires = new wsClient('territoires');
			$response = $oWsTerritoires->updateTerritoire(PSession::$SESSION['tsSessionId'], $params['idTerritoire'], $params['libelle']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteTerritoire($params)
		{
			$oWsTerritoires = new wsClient('territoires');
			$response = $oWsTerritoires->deleteTerritoire(PSession::$SESSION['tsSessionId'], $params['idTerritoire']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function getCommunesByTerritoire($params)
		{
			$oWsTerritoires = new wsClient('territoires');
			$response = $oWsTerritoires->getCommunesByTerritoire(PSession::$SESSION['tsSessionId'], $params['idTerritoire']);

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array('codeInsee', 'codePostal', 'libelle'));

			echo $oProxyStore->getProxyResponse();
		}

		public function addCommuneTerritoire($params)
		{
			$oWsTerritoires = new wsClient('territoires');

			$communes = explode(',', $params['codeInsee']);
			foreach ($communes as $codeInsee)
			{
				$response = $oWsTerritoires->addCommuneTerritoire(PSession::$SESSION['tsSessionId'], $params['idTerritoire'], $codeInsee);
			}

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function setCommuneTerritoirePrive($params)
		{
			$oWsTerritoires = new wsClient('territoires');
			$response = $oWsTerritoires->setCommuneTerritoirePrive(PSession::$SESSION['tsSessionId'], $params['idTerritoire'], $params['codeInsee'], $params['prive'] == 'true');

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteCommuneTerritoire($params)
		{
			$oWsTerritoires = new wsClient('territoires');
			$response = $oWsTerritoires->deleteCommuneTerritoire(PSession::$SESSION['tsSessionId'], $params['idTerritoire'], $params['codeInsee']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function getCommunes($params)
		{
			require_once('../../ressources/communes/communes.php');

			foreach ($communes as &$commune)
			{
				$commune = (object) $commune;
			}

			$communes = array_values($communes);

			$oProxyStore = new proxyStore();
			$oProxyStore->setData($communes);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array('codeInsee', 'codePostal', 'libelle'));

			echo $oProxyStore->getProxyResponse();
		}

		public function getThesaurusByTerritoire($params)
		{
			$oWsTerritoires = new wsClient('territoires');
			$response = $oWsTerritoires->getThesaurusByTerritoire(PSession::$SESSION['tsSessionId'], $params['idTerritoire']);

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array());

			echo $oProxyStore->getProxyResponse();
		}

		public function addThesaurusTerritoire($params)
		{
			$oWsTerritoires = new wsClient('territoires');
			$response = $oWsTerritoires->addThesaurusTerritoire(PSession::$SESSION['tsSessionId'], $params['idTerritoire'], $params['codeThesaurus']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteThesaurusTerritoire($params)
		{
			$oWsTerritoires = new wsClient('territoires');
			$response = $oWsTerritoires->deleteThesaurusTerritoire(PSession::$SESSION['tsSessionId'], $params['idTerritoire'], $params['codeThesaurus']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

	}

?>