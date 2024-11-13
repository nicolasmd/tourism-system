<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pUtilisateurDroitTerritoire extends tsProxy
	{

		public function getCommunesUtilisateur($params)
		{
			$idUtilisateur = isset($params['idUtilisateur']) ? $params['idUtilisateur'] : PSession::$SESSION['idUtilisateur'];

			$oWsUtilisateurDT = new wsClient('utilisateurDroitTerritoire');
			$response = $oWsUtilisateurDT->getDroitsTerritoire(PSession::$SESSION['tsSessionId'], $idUtilisateur);

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);

			$communes = array();
			$idTerritoiresTmp = array();
			$oWsTerritoires = new wsClient('territoires');
			foreach ($response['droitsTerritoire'] as $droit)
			{
				if (in_array($droit->idTerritoire, $idTerritoiresTmp))
				{
					continue;
				}
				$idTerritoiresTmp[] = $droit->idTerritoire;

				$response = $oWsTerritoires->getCommunesByTerritoire(PSession::$SESSION['tsSessionId'], $droit->idTerritoire);

				foreach ($response['communes'] as $commune)
				{
					$communes[$commune->codeInsee] = $commune;
				}
			}

			$oProxyStore->setData(array_values($communes));
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array('libelle'));

			echo $oProxyStore->getProxyResponse();
		}

		public function getBordereauxUtilisateur($params)
		{
			$idUtilisateur = isset($params['idUtilisateur']) ? $params['idUtilisateur'] : PSession::$SESSION['idUtilisateur'];

			$oWsUtilisateurDT = new wsClient('utilisateurDroitTerritoire');
			$response = $oWsUtilisateurDT->getDroitsTerritoire(PSession::$SESSION['tsSessionId'], $idUtilisateur);

			$bordereaux = array();
			$bordereauxTmp = array();
			foreach ($response['droitsTerritoire'] as $droit)
			{
				if (in_array($droit->bordereau, $bordereauxTmp))
				{
					continue;
				}
				$bordereauxTmp[] = $droit->bordereau;

				$bordereaux[] = array(
					'bordereau' => $droit->bordereau,
					'libelleBordereau' => $GLOBALS['bordereaux'][$droit->bordereau]
				);
			}
			unset($response['droitsTerritoire']);
			$response['bordereaux'] = $bordereaux;

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);

			echo $oProxyStore->getProxyResponse();
		}

		public function getDroitsTerritoire($params)
		{
			$oWsUtilisateurDT = new wsClient('utilisateurDroitTerritoire');
			$response = $oWsUtilisateurDT->getDroitsTerritoire(PSession::$SESSION['tsSessionId'], $params['idUtilisateur']);

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);

			echo $oProxyStore->getProxyResponse();
		}

		public function getDroitTerritoireChamp($params)
		{
			if (is_numeric($params['idProfil']) && $params['idProfil'] != 0)
			{
				require_once('pProfilDroit.php');
				$oProxyProfilDroit = new pProfilDroit();
				return $oProxyProfilDroit->getProfilDroitChamp($params);
			}

			$oWsUtilisateurDT = new wsClient('utilisateurDroitTerritoire');
			$response = $oWsUtilisateurDT->getDroitTerritoire(PSession::$SESSION['tsSessionId'], $params['idUtilisateur'], $params['bordereau'], $params['idTerritoire']);

			$response['droitsChamp'] = $response['droitTerritoire']->droitsChamp;
			unset($response['droitTerritoire']);

			$oWsChamp = new wsClient('champ');
			$resChamps = $oWsChamp->getChamps(PSession::$SESSION['tsSessionId']);

			$champs = array();
			foreach ($resChamps['champs'] as $champ)
			{
				$champs[$champ->idChamp] = $champ->libelle;
			}

			foreach ($response['droitsChamp'] as &$droit)
			{
				$droit->libelle = $champs[$droit->idChamp];
			}

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);

			echo $oProxyStore->getProxyResponse();
		}

		public function setDroitTerritoire($params)
		{
			$oWsUtilisateurDT = new wsClient('utilisateurDroitTerritoire');
			$oDroit = new stdClass;
			foreach ($params as $droit => $value)
			{
				if (in_array($droit, array(
						'visualisation',
						'modification',
						'validation',
						'suppressionFiches',
						'creationFiches',
						'administration'
					)) && $value == 'true')
				{
					$oDroit->$droit = true;
				}
			}

			$bordereaux = isset($params['bordereaux']) ? explode(',', $params['bordereaux']) : array($params['bordereau']);
			foreach ($bordereaux as $bordereau)
			{
				$response = $oWsUtilisateurDT->setDroitTerritoire(PSession::$SESSION['tsSessionId'], $params['idUtilisateur'], $bordereau, $params['idTerritoire'], $oDroit);
			}

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function setDroitTerritoireChamp($params)
		{
			$oWsUtilisateurDT = new wsClient('utilisateurDroitTerritoire');
			$oDroit = new stdClass;
			foreach ($params as $droit => $value)
			{
				if (in_array($droit, array(
						'visualisation',
						'modification',
						'validation'
					)) && $value == 'true')
				{
					$oDroit->$droit = true;
				}
			}
			$response = $oWsUtilisateurDT->setDroitTerritoireChamp(PSession::$SESSION['tsSessionId'], $params['idUtilisateur'], $params['bordereau'], $params['idTerritoire'], $params['idChamp'], $oDroit);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteDroitTerritoire($params)
		{
			$oWsUtilisateurDT = new wsClient('utilisateurDroitTerritoire');
			$response = $oWsUtilisateurDT->deleteDroitTerritoire(PSession::$SESSION['tsSessionId'], $params['idUtilisateur'], $params['bordereau'], $params['idTerritoire']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteDroitTerritoireChamp($params)
		{
			$oWsUtilisateurDT = new wsClient('utilisateurDroitTerritoire');
			$response = $oWsUtilisateurDT->deleteDroitTerritoireChamp(PSession::$SESSION['tsSessionId'], $params['idUtilisateur'], $params['bordereau'], $params['idTerritoire'], $params['idChamp']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function setDroitTerritoireProfil($params)
		{
			$oWsUtilisateurDT = new wsClient('utilisateurDroitTerritoire');

			if (is_numeric($params['idProfil']) && $params['idProfil'] > 0)
			{
				$response = $oWsUtilisateurDT->setDroitTerritoireProfil(PSession::$SESSION['tsSessionId'], $params['idUtilisateur'], $params['bordereau'], $params['idTerritoire'], $params['idProfil']);
			}
			else
			{
				$response = $oWsUtilisateurDT->unsetDroitTerritoireProfil(PSession::$SESSION['tsSessionId'], $params['idUtilisateur'], $params['bordereau'], $params['idTerritoire']);
			}

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

	}

?>