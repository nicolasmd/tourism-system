<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pPlugin extends tsProxy
	{

		public function getPlugins($params)
		{
			$oWsPlugin = new wsClient('plugin');

			$response = $oWsPlugin->getPlugins(PSession::$SESSION['tsSessionId']);
			$installedPlugins = $response['plugins'];

			$allPlugins = $oWsPlugin->getAllPlugins(PSession::$SESSION['tsSessionId']);

			foreach ($allPlugins['plugins'] as &$item)
			{
				$item->actif = '';
				$item->dateMaj = '';
				$item->newVersion = '';
				foreach ($installedPlugins as $plugin)
				{
					if ($item->nomPlugin == $plugin->nomPlugin)
					{
						$item->actif = $plugin->actif;
						$item->dateMaj = $plugin->dateMaj;
						$item->newVersion = $item->version != $plugin->version;
						continue;
					}
				}
			}

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($allPlugins);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array('nomPlugin'));

			echo $oProxyStore->getProxyResponse();
		}
		
		public function getPluginGroupes($params)
		{
			$oWsPlugin = new wsClient('plugin');
			$response = $oWsPlugin->getPluginGroupes(PSession::$SESSION['tsSessionId'], $params['nomPlugin']);
			
			$groupes = array();
			foreach($response['groupes'] as $groupe)
			{
				$groupes[] = $groupe->idGroupe;
			}
			
			$oWsGroupe = new wsClient('groupe');
			$response = $oWsGroupe->getGroupes(PSession::$SESSION['tsSessionId']);
			
			foreach ($response['groupes'] as &$groupe)
			{
				$groupe->actif = in_array($groupe->idGroupe, $groupes);
			}
			
			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array('nomGroupe'));

			echo $oProxyStore->getProxyResponse();
		}

		public function installPlugin($params)
		{
			$oWsPlugin = new wsClient('plugin');
			$response = $oWsPlugin->installPlugin(PSession::$SESSION['tsSessionId'], $params['nomPlugin'], $params['cle']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function uninstallPlugin($params)
		{
			$oWsPlugin = new wsClient('plugin');
			$response = $oWsPlugin->uninstallPlugin(PSession::$SESSION['tsSessionId'], $params['nomPlugin']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function enablePlugin($params)
		{
			$oWsPlugin = new wsClient('plugin');
			$response = $oWsPlugin->enablePlugin(PSession::$SESSION['tsSessionId'], $params['nomPlugin']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function disablePlugin($params)
		{
			$oWsPlugin = new wsClient('plugin');
			$response = $oWsPlugin->disablePlugin(PSession::$SESSION['tsSessionId'], $params['nomPlugin']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function updatePlugin($params)
		{
			$oWsPlugin = new wsClient('plugin');
			$response = $oWsPlugin->updatePlugin(PSession::$SESSION['tsSessionId'], $params['nomPlugin']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

	}

?>