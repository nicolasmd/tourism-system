<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pUtilisateur extends tsProxy
	{

		public function getUtilisateur($params)
		{
			$oWsUtilisateur = new wsClient('utilisateur');
			$response = $oWsUtilisateur->getUtilisateur(PSession::$SESSION['tsSessionId'], $params['idUtilisateur']);

			echo json_encode($response['utilisateur']);
		}

		public function getUtilisateurs($params)
		{
			$oWsUtilisateur = new wsClient('utilisateur');
			$response = $oWsUtilisateur->getUtilisateurs(PSession::$SESSION['tsSessionId']);

			if (tsDroits::getDroit('UTILISATEUR_GROUPE'))
			{
				$oWsGroupe = new wsClient('groupe');
				$resGroupes = $oWsGroupe->getGroupes(PSession::$SESSION['tsSessionId']);

				$groupes = array();
				foreach ($resGroupes['groupes'] as $groupe)
				{
					$groupes[$groupe->idGroupe] = $groupe->nomGroupe;
				}

				foreach ($response['utilisateurs'] as &$utilisateur)
				{
					$utilisateur->nomGroupe = $groupes[$utilisateur->idGroupe];
				}
			}

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array('email'));

			echo $oProxyStore->getProxyResponse();
		}

		public function createUtilisateur($params)
		{
			$idGroupe = (isset($params['idGroupe']) && is_numeric($params['idGroupe']) ? $params['idGroupe'] : null);

			$oWsUtilisateur = new wsClient('utilisateur');
			$response = $oWsUtilisateur->createUtilisateur(PSession::$SESSION['tsSessionId'], strtolower($params['email']), $params['typeUtilisateur'], $idGroupe);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function updateUtilisateur($params)
		{
			$oWsUtilisateur = new wsClient('utilisateur');

			if (!empty($params['oldPassword']) && !empty($params['password']))
			{
				$response = $oWsUtilisateur->updateUtilisateurPassword(PSession::$SESSION['tsSessionId'], $params['oldPassword'], $params['password'], $params['idUtilisateur']);
				$oProxyResponse = new proxyResponse();
				$oProxyResponse->setSoapResponse($response);
			}

			$response = $oWsUtilisateur->updateUtilisateurGroupe(PSession::$SESSION['tsSessionId'], $params['idUtilisateur'], $params['idGroupe']);
			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteUtilisateur($params)
		{
			$oWsUtilisateur = new wsClient('utilisateur');
			$response = $oWsUtilisateur->deleteUtilisateur(PSession::$SESSION['tsSessionId'], $params['idUtilisateur']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function sendPassword($params)
		{
			$oWsUtilisateur = new wsClient('utilisateur');
			$response = $oWsUtilisateur->getUtilisateur(PSession::$SESSION['tsSessionId'], $params['idUtilisateur']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			$message = file_get_contents('../../include/tpl/mailIdentifiants.html');
			$message = str_replace('{url}', TS_CLIENT_URL, $message);
			$message = str_replace('{email}', $response['utilisateur']->email, $message);
			$message = str_replace('{password}', $response['utilisateur']->password, $message);

			$headers = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From: noreply@tourism-system.fr' . "\r\n";

			mail($response['utilisateur']->email, 'Vos identifiants Tourism System', $message, $headers);

			echo $oProxyResponse->getProxyResponse();
		}

		public function getAdmins($params)
		{
			$oWsUtilisateur = new wsClient('utilisateur');
			$response = $oWsUtilisateur->getUtilisateurs(PSession::$SESSION['tsSessionId']);

			foreach ($response['utilisateurs'] as $k => $v)
			{
				if (!in_array($v->typeUtilisateur, array('superadmin', 'admin')))
				{
					unset($response['utilisateurs'][$k]);
				}
			}
			sort($response['utilisateurs']);

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array('email'));

			echo $oProxyStore->getProxyResponse();
		}

		public function getDroitsUtilisateur($params)
		{
			$oWsUtilisateur = new wsClient('utilisateur');
			$response = $oWsUtilisateur->getDroitsUtilisateur(PSession::$SESSION['tsSessionId'], $params['idUtilisateur']);
			echo json_encode(array('dataCount' => 2, 'dataRoot' => $response['droits']));
		}

		public function getSessions($params)
		{
			$oWsUtilisateur = new wsClient('utilisateur');
			$response = $oWsUtilisateur->getSessionsUtilisateur(PSession::$SESSION['tsSessionId'], $params['idUtilisateur']);
			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array());

			echo $oProxyStore->getProxyResponse();
		}

	}

?>