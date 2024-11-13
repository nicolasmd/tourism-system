<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pChamp extends tsProxy
	{

		public function getChamp($params)
		{

			$oWsChamp = new wsClient('champ');
			$response = $oWsChamp->getChamp(PSession::$SESSION['tsSessionId'], $params['idChamp']);
			$response['success'] = $response['status']->success;
			unset($response['status']->success);

			echo json_encode($response);
		}

		public function getChamps($params)
		{
			$oWsChamp = new wsClient('champ');
			$response = $oWsChamp->getChamps(PSession::$SESSION['tsSessionId']);

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array('identifiant', 'libelle', 'bordereau', 'xPath'));

			echo $oProxyStore->getProxyResponse();
		}

		public function getChampsByBordereau($params)
		{
			$oWsChamp = new wsClient('champ');
			$response = $oWsChamp->getChamps(PSession::$SESSION['tsSessionId']);

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);

			$champs = array();
			foreach ($response['champs'] as $champ)
			{
				if (strpos($champ->bordereau, $params['bordereau']) !== false)
				{
					$champs[] = $champ;
				}
			}

			$oProxyStore->setData($champs);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array('identifiant', 'libelle', 'xPath'));

			echo $oProxyStore->getProxyResponse();
		}

		public function getChampsPrimaryKey($params)
		{
			header("Content-type: text/html; charset=UTF-8;");

			$oWsChamp = new wsClient('champ');
			$response = $oWsChamp->getChamps(SESSION_ID_ROOT);

			$fields = array();
			foreach ($response['champs'] as $champ)
			{
				if (isset($champ->champs) && is_array($champ->champs))
				{
					foreach ($champ->champs as $sousChamp)
					{
						if ($sousChamp->cle == 'Y')
						{
							$fields[$champ->identifiant] = $sousChamp->identifiant;
						}
					}
				}
			}

			echo "Ext.ts.complexeFields = " . json_encode($fields) . ";";
		}

		public function getChampsSpecifiques($params)
		{
			$oWsChamp = new wsClient('champ');
			$response = $oWsChamp->getChamps(PSession::$SESSION['tsSessionId']);

			$champs = array();
			foreach ($response['champs'] as &$champ)
			{
				if (strpos($champ->identifiant, 'cs_') === 0)
				{
					if (strpos($champ->xPath, 'tif:ChampSpecifiqueTexte') !== false)
					{
						$champ->type = 'texte';
					}
					elseif (strpos($champ->xPath, 'tif:ChampSpecifiqueSelect') !== false)
					{
						$champ->type = 'select';

						preg_match('/type="([^"]*)"/', $champ->xPath, $result);
						$champ->key = $result[1];
					}
					elseif (strpos($champ->xPath, 'tif:ChampSpecifiqueMultiple') !== false)
					{
						$champ->type = 'multiple';

						preg_match('/type="([^"]*)"/', $champ->xPath, $result);
						$champ->key = $result[1];
					}

					$champs[] = $champ;
				}
			}

			$response['champs'] = $champs;

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array('identifiant', 'libelle', 'bordereau'));

			echo $oProxyStore->getProxyResponse();
		}

		public function createChamp($params)
		{
			$oWsChamp = new wsClient('champ');
			$bordereaux = (isset($params['bordereaux'])) ? implode(',', $params['bordereaux']) : null;
			$idChampParent = (isset($params['idChamp']) && $params['idChamp'] != '') ? $params['idChamp'] : null;
			$response = $oWsChamp->createChamp(PSession::$SESSION['tsSessionId'], $params['identifiant'], $params['libelle'], $params['xPath'], $params['liste'], $bordereaux, $idChampParent);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function updateChamp($params)
		{
			$oWsChamp = new wsClient('champ');
			$bordereaux = (isset($params['bordereaux'])) ? implode(',', $params['bordereaux']) : null;
			$response = $oWsChamp->updateChamp(PSession::$SESSION['tsSessionId'], $params['idChamp'], $params['identifiant'], $params['libelle'], $params['xPath'], $params['liste'], $bordereaux);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteChamp($params)
		{
			$oWsChamp = new wsClient('champ');
			$response = $oWsChamp->deleteChamp(PSession::$SESSION['tsSessionId'], $params['idChamp']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

	}

?>