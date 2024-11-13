<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/pluginDb.php');

	/**
	 * Classe wsPlugin - endpoint du webservice Plugin
	 * Gestion des plugins Tourism System
	 * Accessible aux utilisateurs root
	 */
	final class wsPlugin extends wsEndpoint
	{
		
		/**
		 * Récupère la liste des plugins disponibles pour l'utilisateur courant
		 * @return pluginCollection plugins : collection de pluginModele
		 * @access root
		 */
		protected function _getPlugins()
		{
			$plugins = tsDroits::getTypeUtilisateur() == 'root'
				? pluginDb::getPlugins()
				:$plugins = pluginDb::getPluginsByGroupe(tsDroits::getGroupeUtilisateur());
			return array('plugins' => $plugins);
		}
		
		/**
		 * Récupère la liste des groupes ayant accès à un plugin
		 * @return pluginCollection plugins : collection de pluginModele
		 * @access root
		 */
		protected function _getPluginGroupes($nomPlugin)
		{
			$this -> restrictAccess('root');
			$groupes = pluginDb::getPluginGroupes($nomPlugin);
			return array('groupes' => $groupes);
		}
		
		
		/**
		 * Met à jour un plugin installé
		 * @param string $nomPlugin : identifiant du plugin
		 * @access root
		 */
		protected function _updatePlugin($nomPlugin)
		{
			$this -> restrictAccess('root');
			$oPlugin = pluginDb::getPlugin();
			pluginDb::updatePlugin($nomPlugin);
			// @todo :  update des sources ?
			return array();
		}
		
		
		/**
		 * Active un plugin installé
		 * @param string $nomPlugin : identifiant du plugin
		 * @access root
		 */
		protected function _enablePlugin($nomPlugin)
		{
			$this -> restrictAccess('root');
			pluginDb::enablePlugin($nomPlugin);
			return array();
		}
		
		/**
		 * Désactive un plugin installé
		 * @param string $nomPlugin : identifiant du plugin
		 * @access root
		 */
		protected function _disablePlugin($nomPlugin)
		{
			$this -> restrictAccess('root');
			pluginDb::disablePlugin($nomPlugin);
			return array();
		}
		

		/**
		 * Installe un plugin depuis le serveur de synchronisation
		 * @param string $nomPlugin : identifiant du plugin
		 * @param string $password (optional) : mot de passe pour le tééchargement du plugin
		 * @access root
		 */
		protected function _installPlugin($nomPlugin, $password = null)
		{
			$this -> restrictAccess('root');
			$client = new Soapclient(tsConfig::get('TS_URL_SYNCHRO'));
			$plugin = $client -> getPlugin($nomPlugin, $password);
			
			// Téléchargement des sources serveur
			if (isset($plugin -> pathServer))
			{
				$content = file_get_contents($plugin -> pathServer);
				$tmpName = $_SERVER['DOCUMENT_ROOT'] . '/plugins/_tmp/'.$nomPlugin.'.zip';
				$file = file_put_contents($tmpName, $content);
				
				$oZip = new ZipArchive();
				$res = $oZip -> open($tmpName);
				if ($res === false)
				{
					throw new ApplicationException('La décompression du fichier ' . $nomPlugin . ' a échoué');
				}
				$dir = $_SERVER['DOCUMENT_ROOT'] . '/plugins/'.$nomPlugin .'/';
				if (file_exists($dir) == false)
				{
					mkdir($dir, 0777);
				}
				
				$oZip -> extractTo($dir);
			}
			
			// Exécution du script d'installation sql
			if (file_exists($dir . 'install.sql'))
			{
				// @todo : à revoir
				exec('mysql -C -h '.tsConfig::get('TS_BDD_SERVER').' -u '.tsConfig::get('TS_BDD_USER').
					' --password='.tsConfig::get('TS_BDD_PASSWORD').' '.tsConfig::get('TS_BDD_NAME').
					' < '.$dir.'install.sql');
			}
			
			pluginDb::installPlugin($nomPlugin, $plugin -> version);

			return array('plugin' => $plugin);
		}
		
		
		/**
		 * Désinstalle un plugin
		 * @param string $nomPlugin : identifiant du plugin
		 * @access root
		 */
		protected function _uninstallPlugin($nomPlugin)
		{
			$this -> restrictAccess('root');
			pluginDb::uninstallPlugin($nomPlugin);
			$dir = $_SERVER['DOCUMENT_ROOT'] . '/plugins/'.$nomPlugin .'/';

			// Exécution du script uninstall.sql
			if (file_exists($dir . 'uninstall.sql'))
			{
				// @todo : à revoir
				exec('mysql -C -h '.tsConfig::get('TS_BDD_SERVER').' -u '.tsConfig::get('TS_BDD_USER').
						' --password='.tsConfig::get('TS_BDD_PASSWORD').' '.tsConfig::get('TS_BDD_NAME').
						' < '.$dir.'uninstall.sql');
			}
			
			// Suppression des fichiers
			removeDir($dir);
			return array();
		}
		
		
		/**
		 * Récupère tous les plugons disponibles en téléchargement sur le serveur de synchronisation
		 * @access root
		 */
		protected function _getAllPlugins()
		{
			$this -> restrictAccess('root');
			$client = new Soapclient(tsConfig::get('TS_URL_SYNCHRO'));
			$serverPlugins = $client -> getPlugins();
			return array('plugins' => $serverPlugins);
		}

		
	}
