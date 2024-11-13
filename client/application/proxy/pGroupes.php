<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pGroupes extends tsProxy
	{

		public function getGroupes($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->getGroupes(PSession::$SESSION['tsSessionId']);

			$oWsUtilisateur = new wsClient('utilisateur');
			$resUtilisateurs = $oWsUtilisateur->getUtilisateurs(PSession::$SESSION['tsSessionId']);

			foreach ($resUtilisateurs['utilisateurs'] as $k => $v)
			{
				if ($v->typeUtilisateur != 'superadmin')
				{
					unset($resUtilisateurs['utilisateurs'][$k]);
				}
			}
			sort($resUtilisateurs['utilisateurs']);

			$admins = array();
			foreach ($resUtilisateurs['utilisateurs'] as $utilisateur)
			{
				$admins[$utilisateur->idUtilisateur] = $utilisateur->email;
			}

			if (is_array($response['groupes']))
			{
				foreach ($response['groupes'] as &$groupe)
				{
					$groupe->email = $admins[$groupe->idSuperAdmin];
				}
			}

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array('nomGroupe', 'descriptionGroupe'));

			echo $oProxyStore->getProxyResponse();
		}

		public function getGroupesTree($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->getGroupes(PSession::$SESSION['tsSessionId'], $params['idGroupe']);

			$oWsUtilisateur = new wsClient('utilisateur');
			$resUtilisateurs = $oWsUtilisateur->getUtilisateurs(PSession::$SESSION['tsSessionId']);

			foreach ($resUtilisateurs['utilisateurs'] as $k => $v)
			{
				if ($v->typeUtilisateur != 'superadmin')
				{
					unset($resUtilisateurs['utilisateurs'][$k]);
				}
			}
			sort($resUtilisateurs['utilisateurs']);

			$admins = array();
			foreach ($resUtilisateurs['utilisateurs'] as $utilisateur)
			{
				$admins[$utilisateur->idUtilisateur] = $utilisateur->email;
			}

			if (is_array($response['groupes']))
			{
				foreach ($response['groupes'] as &$groupe)
				{
					$groupe->email = isset($admins[$groupe->idSuperAdmin]) ? $admins[$groupe->idSuperAdmin] : null;
				}
			}

			echo json_encode($response['groupes']);
		}

		public function createGroupe($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->createGroupe(PSession::$SESSION['tsSessionId'], $params['nomGroupe'], $params['descriptionGroupe'], $params['idGroupe']);

			if (isset($params['idSuperAdmin']) && $params['idSuperAdmin'] != '')
			{
				$oWsGroupes->setSuperAdminGroupe(PSession::$SESSION['tsSessionId'], $response['idGroupe'], $params['idSuperAdmin']);
			}

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteGroupe($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->deleteGroupe(PSession::$SESSION['tsSessionId'], $params['idGroupe']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function editGroupe($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->updateGroupe(PSession::$SESSION['tsSessionId'], $params['idGroupe'], $params['nomGroupe'], $params['descriptionGroupe']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			$idSuperAdmin = (isset($params['idSuperAdmin']) && is_numeric($params['idSuperAdmin'])) ? $params['idSuperAdmin'] : null;
			$response = $oWsGroupes->setSuperAdminGroupe(PSession::$SESSION['tsSessionId'], $params['idGroupe'], $idSuperAdmin);

			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function getGroupeTerritoires($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->getGroupeTerritoires(PSession::$SESSION['tsSessionId'], $params['idGroupe']);

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array());

			echo $oProxyStore->getProxyResponse();
		}

		public function addGroupeTerritoire($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->addGroupeTerritoire(PSession::$SESSION['tsSessionId'], $params['idGroupe'], $params['idTerritoire']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteGroupeTerritoire($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->deleteGroupeTerritoire(PSession::$SESSION['tsSessionId'], $params['idGroupe'], $params['idTerritoire']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function getGroupeCommunes($params)
		{
			$idGroupe = isset($params['idGroupe']) ? $params['idGroupe'] : PSession::$SESSION['idGroupe'];

			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->getGroupeTerritoires(SESSION_ID_ROOT, $idGroupe);

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);

			$communes = array();
			$oWsTerritoires = new wsClient('territoires');
			foreach ($response['territoires'] as $territoire)
			{
				$response = $oWsTerritoires->getCommunesByTerritoire(SESSION_ID_ROOT, $territoire->idTerritoire);

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

		public function getGroupePartenaires($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->getGroupePartenaires(PSession::$SESSION['tsSessionId'], $params['idGroupe']);

			$oWsGroupe = new wsClient('groupe');
			$resGroupes = $oWsGroupe->getGroupes(SESSION_ID_ROOT);

			$groupes = array();
			foreach ($resGroupes['groupes'] as $groupe)
			{
				$groupes[$groupe->idGroupe] = $groupe->nomGroupe;
			}

			foreach ($response['partenaires'] as $k => &$partenaire)
			{
				$partenaire->nomGroupe = $groupes[$partenaire->idGroupe];
			}

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array());

			echo $oProxyStore->getProxyResponse();
		}

		public function addGroupePartenaire($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->addGroupePartenaire(PSession::$SESSION['tsSessionId'], $params['idGroupe'], $params['idGroupePartenaire'], $params['typePartenaire']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteGroupePartenaire($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->deleteGroupePartenaire(PSession::$SESSION['tsSessionId'], $params['idGroupe'], $params['idGroupePartenaire']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteGroupePartenaireFiche($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->deleteGroupePartenaireFiche(PSession::$SESSION['tsSessionId'], $params['idFiche']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function addGroupePlugin($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->addGroupePlugin(PSession::$SESSION['tsSessionId'], $params['idGroupe'], $params['nomPlugin']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteGroupePlugin($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->deleteGroupePlugin(PSession::$SESSION['tsSessionId'], $params['idGroupe'], $params['nomPlugin']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}
		
		public function addGroupesPlugin($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->getGroupes(PSession::$SESSION['tsSessionId']);
			
			foreach ($response['groupes'] as $groupe)
			{
				$oWsGroupes->addGroupePlugin(PSession::$SESSION['tsSessionId'], $groupe->idGroupe, $params['nomPlugin']);
			}

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}
		
		public function deleteGroupesPlugin($params)
		{
			$oWsGroupes = new wsClient('groupe');
			$response = $oWsGroupes->getGroupes(PSession::$SESSION['tsSessionId']);
			
			foreach ($response['groupes'] as $groupe)
			{
				$oWsGroupes->deleteGroupePlugin(PSession::$SESSION['tsSessionId'], $groupe->idGroupe, $params['nomPlugin']);
			}

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

	}

?>