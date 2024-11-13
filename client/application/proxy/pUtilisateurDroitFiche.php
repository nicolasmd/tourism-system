<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pUtilisateurDroitFiche extends tsProxy
	{

		public function getDroitsFiche($params)
		{
			$oWsUtilisateurDF = new wsClient('utilisateurDroitFiche');
			$response = $oWsUtilisateurDF->getDroitsFiche(PSession::$SESSION['tsSessionId'], $params['idUtilisateur']);

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);

			echo $oProxyStore->getProxyResponse();
		}

		public function getDroitFicheChamp($params)
		{
			if (is_numeric($params['idProfil']) && $params['idProfil'] != 0)
			{
				require_once('pProfilDroit.php');
				$oProxyProfilDroit = new pProfilDroit();
				return $oProxyProfilDroit->getProfilDroitChamp($params);
			}

			$oWsUtilisateurDF = new wsClient('utilisateurDroitFiche');
			$response = $oWsUtilisateurDF->getDroitFiche(PSession::$SESSION['tsSessionId'], $params['idUtilisateur'], $params['idFiche']);

			$response['droitsChamp'] = $response['droitFiche']->droitsChamp;
			unset($response['droitFiche']);

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

		public function setDroitFiche($params)
		{
			$oWsUtilisateurDF = new wsClient('utilisateurDroitFiche');
			$oDroit = new stdClass;
			foreach ($params as $droit => $value)
			{
				if (in_array($droit, array(
						'visualisation',
						'modification',
						'validation',
						'suppressionFiches'
					)) && $value == 'true')
				{
					$oDroit->$droit = true;
				}
			}
			$response = $oWsUtilisateurDF->setDroitFiche(PSession::$SESSION['tsSessionId'], $params['idUtilisateur'], $params['idFiche'], $oDroit);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function setDroitFicheChamp($params)
		{
			$oWsUtilisateurDF = new wsClient('utilisateurDroitFiche');
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
			$response = $oWsUtilisateurDF->setDroitFicheChamp(PSession::$SESSION['tsSessionId'], $params['idUtilisateur'], $params['idFiche'], $params['idChamp'], $oDroit);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteDroitFiche($params)
		{
			$oWsUtilisateurDF = new wsClient('utilisateurDroitFiche');
			$response = $oWsUtilisateurDF->deleteDroitFiche(PSession::$SESSION['tsSessionId'], $params['idUtilisateur'], $params['idFiche']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteDroitFicheChamp($params)
		{
			$oWsUtilisateurDF = new wsClient('utilisateurDroitFiche');
			$response = $oWsUtilisateurDF->deleteDroitFicheChamp(PSession::$SESSION['tsSessionId'], $params['idUtilisateur'], $params['idFiche'], $params['idChamp']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function setDroitFicheProfil($params)
		{
			$oWsUtilisateurDF = new wsClient('utilisateurDroitFiche');

			if (is_numeric($params['idProfil']) && $params['idProfil'] > 0)
			{
				$response = $oWsUtilisateurDF->setDroitFicheProfil(PSession::$SESSION['tsSessionId'], $params['idUtilisateur'], $params['idFiche'], $params['idProfil']);
			}
			else
			{
				$response = $oWsUtilisateurDF->unsetDroitFicheProfil(PSession::$SESSION['tsSessionId'], $params['idUtilisateur'], $params['idFiche']);
			}

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

	}

?>