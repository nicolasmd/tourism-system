<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/modele/pluginModele.php');
	
	final class pluginDb
	{
	
		const SQL_PLUGIN = "SELECT idPlugin, nomPlugin, version, actif, dateMaj FROM sitPlugin WHERE nomPlugin='%s'";
		const SQL_PLUGINS = "SELECT idPlugin, nomPlugin, version, actif, dateMaj FROM sitPlugin";
		const SQL_PLUGINS_GROUPE = "SELECT p.idPlugin, nomPlugin, version, actif, dateMaj FROM sitPlugin p, sitGroupePlugin gp WHERE p.idPlugin=gp.idPlugin AND idGroupe='%d'";
		const SQL_PLUGIN_GROUPES = "SELECT idGroupe FROM sitPlugin p, sitGroupePlugin gp WHERE p.idPlugin=gp.idPlugin AND nomPlugin='%s'";
		const SQL_DELETE_PLUGIN = "DELETE FROM sitPlugin WHERE nomPlugin='%s' AND actif='N'";
		const SQL_CREATE_PLUGIN = "INSERT INTO sitPlugin (nomPlugin, version, actif, dateMaj) VALUES ('%s', '%s', 'N', NOW())";
		const SQL_UPDATE_PLUGIN = "UPDATE sitPlugin SET version='%s' WHERE nomPlugin='%s'";
		const SQL_ENABLE_PLUGIN = "UPDATE sitPlugin SET actif='Y' WHERE nomPlugin='%s'";
		const SQL_DISABLE_PLUGIN = "UPDATE sitPlugin SET actif='N' WHERE nomPlugin='%s'";

		public static function getPlugin($nomPlugin)
		{
			$plugin = tsDatabase::getObject(self::SQL_PLUGIN, array($nomPlugin), DB_FAIL_ON_ERROR);
			return pluginModele::getInstance($plugin, 'pluginModele');
		}
		
		public static function getPlugins()
		{
			$oPluginCollection = new PluginCollection();
			$plugins = tsDatabase::getObjects(self::SQL_PLUGINS, array());
			foreach($plugins as $plugin)
			{
				$oPluginCollection[] = pluginModele::getInstance($plugin, 'pluginModele');
			}
			return $oPluginCollection -> getCollection();
		}
		
		public static function getPluginsByGroupe($idGroupe)
		{
			$oPluginCollection = new PluginCollection();
			$plugins = tsDatabase::getObjects(self::SQL_PLUGINS_GROUPE, array($idGroupe));
			foreach($plugins as $plugin)
			{
				$oPluginCollection[] = pluginModele::getInstance($plugin, 'pluginModele');
			}
			return $oPluginCollection -> getCollection();
		}
		
		public static function getPluginGroupes($nomPlugin)
		{
			$oGroupeCollection = new GroupeCollection();
			$idGroupes = tsDatabase::getRecords(self::SQL_PLUGIN_GROUPES, array($nomPlugin));
			foreach($idGroupes as $idGroupe)
			{
				$oGroupeCollection[] = groupeDb::getGroupe($idGroupe);
			}
			return $oGroupeCollection -> getCollection();
		}

		public static function disablePlugin($nomPlugin)
		{
			return tsDatabase::query(self::SQL_DISABLE_PLUGIN, array($nomPlugin));
		}
		
		public static function enablePlugin($nomPlugin)
		{
			return tsDatabase::query(self::SQL_ENABLE_PLUGIN, array($nomPlugin));
		}
		
		public static function installPlugin($nomPlugin, $version)
		{
			return tsDatabase::insert(self::SQL_CREATE_PLUGIN, array($nomPlugin, $version));
		}
		
		public static function unInstallPlugin($nomPlugin)
		{
			return tsDatabase::query(self::SQL_DELETE_PLUGIN, array($nomPlugin));
		}
		
	}
