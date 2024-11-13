<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pIdentification
	{

		public function identification($params)
		{
			$login = $params['login'];
			$pass = $params['pass'];

			$oWsIdentification = new wsClient('identification');
			$response = $oWsIdentification->identification($login, $pass);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			PSession::$SESSION['tsSessionId'] = $response['sessionId'];

			if (PSession::$SESSION['tsSessionId'] == SESSION_ID_ROOT)
			{
				PSession::$SESSION['email'] = 'root';
				PSession::$SESSION['typeUtilisateur'] = 'root';
			}
			else
			{
				$oWsUtilisateur = new wsClient('utilisateur');
				$utilisateur = $oWsUtilisateur->getUtilisateur(PSession::$SESSION['tsSessionId']);

				PSession::$SESSION['idUtilisateur'] = $utilisateur['utilisateur']->idUtilisateur;
				PSession::$SESSION['email'] = $utilisateur['utilisateur']->email;
				PSession::$SESSION['typeUtilisateur'] = $utilisateur['utilisateur']->typeUtilisateur;
				PSession::$SESSION['idGroupe'] = $utilisateur['utilisateur']->idGroupe;

				$oWsGroupe = new wsClient('groupe');
				$groupe = $oWsGroupe->getGroupe(SESSION_ID_ROOT, PSession::$SESSION['idGroupe']);

				PSession::$SESSION['nomGroupe'] = $groupe['groupe']->nomGroupe;
				PSession::$SESSION['descriptionGroupe'] = $groupe['groupe']->descriptionGroupe;
			}

			$oWsPlugin = new wsClient('plugin');
			$response = $oWsPlugin->getPlugins(PSession::$SESSION['tsSessionId']);
			$plugins = array();
			if (is_array($response['plugins']))
			{
				foreach ($response['plugins'] as $plugin)
				{
					if ($plugin->actif == 'Y')
					{
						$plugins[] = $plugin->nomPlugin;
					}
				}
			}
			PSession::$SESSION['plugins'] = $plugins;

			echo $oProxyResponse->getProxyResponse();
		}

		public function deconnexion($params)
		{
			foreach ($_SESSION as $k => $v)
			{
				unset(PSession::$SESSION[$k]);
			}
			PSession::destroy();
		}

		public function forgottenPass($params)
		{
			$oWsUtilisateur = new wsClient('utilisateur');
			$response = $oWsUtilisateur->getUtilisateurs(SESSION_ID_ROOT);

			foreach ($response['utilisateurs'] as $utilisateur)
			{
				if ($utilisateur->email === $params['login'])
				{
					$message = file_get_contents('../../include/tpl/mailIdentifiants.html');
					$message = str_replace('{url}', TS_CLIENT_URL, $message);
					$message = str_replace('{email}', $utilisateur->email, $message);
					$message = str_replace('{password}', $utilisateur->password, $message);

					$headers = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
					$headers .= 'From: noreply@tourism-system.fr' . "\r\n";

					mail($utilisateur->email, 'Vos identifiants Tourism System', $message, $headers);

					echo json_encode(array('success' => true, 'msg' => 'Vos identifiants ont été envoyés à votre adresse'));
					return;
				}
			}

			throw new Exception("Utilisateur inconnu");
		}

		public function getUtilisateurs($params)
		{
			if (isAuthorizedIP())
			{
				$oWsUtilisateur = new wsClient('utilisateur');
				$response = $oWsUtilisateur->getUtilisateurs(SESSION_ID_ROOT);
			}
			else
			{
				$response = array();
			}

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array('email'));

			echo $oProxyStore->getProxyResponse();
		}

	}

?>