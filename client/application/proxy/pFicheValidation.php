<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pFicheValidation extends tsProxy
	{

		public function getChampsFicheAValider($params)
		{
			$oWsFicheValidation = new wsClient('ficheValidation');
			$response = $oWsFicheValidation->getChampsFicheAValider(PSession::$SESSION['tsSessionId'], $params['idFiche']);

			$oWsUtilisateur = new wsClient('utilisateur');

			$utilisateursTmp = array();
			foreach ($response['champs'] as &$champ)
			{
				if (is_null($champ->idUtilisateur))
				{
					continue;
				}

				if (!isset($utilisateursTmp[$champ->idUtilisateur]))
				{
					$responseUser = $oWsUtilisateur->getUtilisateur(SESSION_ID_ROOT, $champ->idUtilisateur);
					$utilisateursTmp[$champ->idUtilisateur] = $responseUser['utilisateur'];
				}

				$champ->email = $utilisateursTmp[$champ->idUtilisateur]->email;
			}

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);

			echo $oProxyStore->getProxyResponse();
		}

		public function refuseChampFiche($params)
		{
			$oWsFicheValidation = new wsClient('ficheValidation');
			$response = $oWsFicheValidation->refuseChampFiche(PSession::$SESSION['tsSessionId'], $params['idValidationChamp']);

			$oProxyStore = new proxyResponse();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);

			echo $oProxyStore->getProxyResponse();
		}

	}

?>