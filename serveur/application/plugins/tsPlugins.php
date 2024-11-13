<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/pluginDb.php');

	final class tsPlugins
	{

		private static $plugins = array();
		private static $vars = array();
		private static $params = array();
		private static $responses = array();

		public static function loadPlugins()
		{
			$cacheKey = 'pluginDb_getPlugins';
			$plugins = tsCache::get($cacheKey);

			if ($plugins === false)
			{
				// TODO getPluginsByGroupe
				$plugins = pluginDb::getPlugins();
				tsCache::set($cacheKey, $plugins, 86400);
			}

			foreach ($plugins as $plugin)
			{
				$pluginFile = tsConfig::get('TS_PATH_PLUGINS') . $plugin -> nomPlugin . '/' . $plugin -> nomPlugin . 'Hook.php';

				if (file_exists($pluginFile))
				{
					self::$plugins[] = $plugin -> nomPlugin;
					require_once($pluginFile);
				}
			}
		}

		public static function callHook($className, $methodName, $hookName)
		{
			foreach (self::$plugins as $plugin)
			{
				$hookClass = $plugin . 'Hook';
				$hookMethod = $className . '_' . $methodName . '_' . $hookName;
				if (method_exists($hookClass, $hookMethod))
				{
					$hookClass::$hookMethod();
				}
				// Pour les hooks présents dans wsEndpoint, peuvent s'appliquer au service entier
				$hookMethod = $className . '_' . $hookName;
				if (method_exists($hookClass, $hookMethod))
				{
					$hookClass::$hookMethod();
				}
			}
			self::clearVars();
		}

		public static function setHookParams($hookParams)
		{
			self::$params = $hookParams;
		}

		public static function getHookParam($pluginName)
		{
			return isset( self::$params[ $pluginName ] ) ? self::$params[ $pluginName ] : null;
		}

		public static function setHookResponse($pluginName, $response)
		{
			self::$responses[$pluginName] = $response;
		}

		public static function getHookResponses()
		{
			return self::$responses;
		}

		public static function registerVar($varName, &$varValue)
		{
			self::$vars[$varName] = &$varValue;
		}

		public static function getVars()
		{
			return array_keys(self::$vars);
		}

		public static function getVar($varName)
		{
			return isset(self::$vars[$varName]) ? self::$vars[$varName] : null;
		}

		public static function setVar($varName, $varValue)
		{
			if (array_key_exists($varName, self::$vars))
			{
				self::$vars[$varName] = $varValue;
			}
		}

		public static function clearVars()
		{
			self::$vars = array();
		}

	}
