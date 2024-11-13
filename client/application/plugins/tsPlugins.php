<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	class tsPlugins
	{

		private static $plugins = array();
		private static $vars = array();
		private static $params = array();
		private static $responses = array();

		public static function loadPlugins()
		{
			if (isset(PSession::$SESSION['plugins']) && is_array(PSession::$SESSION['plugins']))
			{
				foreach (PSession::$SESSION['plugins'] as $plugin)
				{
					if (is_dir(PLUGINS_PATH . $plugin . '/'))
					{
						self::$plugins[] = $plugin;
					}
				}
			}
		}

		private static function loadFile($filePath)
		{
			$required = false;
			if (file_exists(PLUGINS_PATH . $filePath))
			{
				require_once(PLUGINS_PATH . $filePath);
				$required = true;
			}

			return $required;
		}

		public static function loadConfigs()
		{
			foreach (self::$plugins as $plugin)
			{
				self::loadFile($plugin . '/config/config.php');
			}
		}

		public static function loadDroits()
		{
			foreach (self::$plugins as $plugin)
			{
				self::loadFile($plugin . '/droits/' . PSession::$SESSION['typeUtilisateur'] . '.php');
			}
		}

		public static function hookInterfaces($interface)
		{
			foreach (self::$plugins as $plugin)
			{
				self::loadFile($plugin . '/' . $plugin . '.php');
				echo PHP_EOL;
				self::loadFile($plugin . '/hooks/interfaces/' . $interface . '.php');
				echo PHP_EOL;
			}
		}

		public static function hookProxy($service, $action)
		{
			foreach (self::$plugins as $plugin)
			{
				if (self::loadFile($plugin . '/hooks/proxy/p' . ucfirst($service) . '.php'))
				{
					$className = $plugin . '_p' . ucfirst($service);
					if (method_exists($className, $action))
					{
						$oHook = new $className();
						$oHook->$action();
					}
				}
			}
			self::clearVars();
		}

		public static function setHookParam($service, $method, $pluginName, $param)
		{
			self::$params[$service . '_' . $method][$pluginName] = $param;
		}

		public static function getHookParams($service, $method)
		{
			return isset(self::$params[$service . '_' . $method])
				? self::$params[$service . '_' . $method] : null;
		}

		public static function setHookResponses($service, $method, $responses)
		{
			self::$responses[$service . '_' . $method] = $responses;
		}

		public static function getHookResponse($service, $method, $pluginName)
		{
			if (!isset(self::$responses[$service . '_' . $method]))
			{
				return null;
			}

			return isset(self::$responses[$service . '_' . $method][$pluginName])
				? self::$responses[$service . '_' . $method][$pluginName] : null;
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
			return self::$vars[$varName];
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

?>
