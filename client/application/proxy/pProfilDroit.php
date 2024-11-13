<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pProfilDroit extends tsProxy
	{

		public function getProfils($params)
		{
			$oWsProfil = new wsClient('profilDroit');
			$response = $oWsProfil->getProfils(PSession::$SESSION['tsSessionId']);

			if (PSession::$SESSION['typeUtilisateur'] == 'root')
			{
				$oWsGroupe = new wsClient('groupe');
				$resGroupes = $oWsGroupe->getGroupes(PSession::$SESSION['tsSessionId']);

				$groupes = array();
				foreach ($resGroupes['groupes'] as $groupe)
				{
					$groupes[$groupe->idGroupe] = $groupe->nomGroupe;
				}

				foreach ($response['profils'] as &$profil)
				{
					$profil->nomGroupe = $groupes[$profil->idGroupe];
				}
			}

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array('libelle'));

			echo $oProxyStore->getProxyResponse();
		}

		public function createProfil($params)
		{
			$idGroupe = ($params['idGroupe'] == 0) ? null : $params['idGroupe'];

			$oWsProfil = new wsClient('profilDroit');
			$response = $oWsProfil->createProfil(PSession::$SESSION['tsSessionId'], $params['libelle'], $idGroupe);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			$params['idProfil'] = $response['idProfil'];
			$this->updateProfil($params);
		}

		public function updateProfil($params)
		{
			$oWsProfil = new wsClient('profilDroit');
			$oDroit = new stdClass;
			foreach ($params as $droit => $value)
			{
				if (in_array($droit, array(
						'visualisation',
						'modification',
						'validation',
						'creationFiches',
						'suppressionFiches',
						'administration'
					)) && $value == 'true')
				{
					$oDroit->$droit = true;
				}
			}
			$response = $oWsProfil->updateProfil(PSession::$SESSION['tsSessionId'], $params['idProfil'], $oDroit);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteProfil($params)
		{
			$oWsProfil = new wsClient('profilDroit');
			$response = $oWsProfil->deleteProfil(PSession::$SESSION['tsSessionId'], $params['idProfil']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function getProfilDroitChamp($params)
		{
			$oWsProfil = new wsClient('profilDroit');
			$response = $oWsProfil->getProfilDroits(PSession::$SESSION['tsSessionId'], $params['idProfil']);

			$response['droitsChamp'] = $response['profilDroits']->droitsChamp;
			unset($response['profilDroits']);

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
			$oProxyStore->setSearchableFields(array('libelle'));

			echo $oProxyStore->getProxyResponse();
		}

		public function setProfilDroitChamp($params)
		{
			$oWsProfil = new wsClient('profilDroit');
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
			$response = $oWsProfil->setProfilDroitChamp(PSession::$SESSION['tsSessionId'], $params['idProfil'], $params['idChamp'], $oDroit);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function setProfilDroitChamps($params)
		{
			$oWsProfil = new wsClient('profilDroit');
			$response = $oWsProfil->getProfilDroits(PSession::$SESSION['tsSessionId'], $params['idProfil']);

			$droitsChamp = $response['profilDroits']->droitsChamp;

			$oldChamps = array();
			foreach ($droitsChamp as $champ)
			{
				$oldChamps[] = $champ->idChamp;
			}
			$newChamps = explode(',', $params['champs']);

			$champsToAdd = array_diff($newChamps, $oldChamps);
			$champsToDelete = array_diff($oldChamps, $newChamps);

			$oDroit = new stdClass;
			$oDroit->visualisation = ($params['visualisation'] == 'true');
			$oDroit->modification = ($params['modification'] == 'true');
			$oDroit->validation = ($params['validation'] == 'true');

			foreach ($champsToAdd as $idChamp)
			{
				$response = $oWsProfil->setProfilDroitChamp(PSession::$SESSION['tsSessionId'], $params['idProfil'], $idChamp, $oDroit);
			}

			foreach ($champsToDelete as $idChamp)
			{
				$response = $oWsProfil->deleteProfilDroitChamp(PSession::$SESSION['tsSessionId'], $params['idProfil'], $idChamp);
			}

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteProfilDroitChamp($params)
		{
			$oWsProfil = new wsClient('profilDroit');
			$response = $oWsProfil->deleteProfilDroitChamp(PSession::$SESSION['tsSessionId'], $params['idProfil'], $params['idChamp']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

	}

?>