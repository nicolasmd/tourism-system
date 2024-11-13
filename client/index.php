<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	ini_set('max_execution_time', 500);
	ini_set('session.gc_maxlifetime', 7200);

	header("Content-type: text/html; charset=UTF-8;");

	if (file_exists('application/common/config.php'))
	{
		require_once('application/common/config.php');
	}
	else
	{
		require_once('interfaces/install.php');
		die;
	}
	require_once('application/common/fonctions.php');
	require_once('application/common/Logger.php');
	require_once('application/common/PSession.php');
	PSession::start();

	if (MAINTENANCE === true && (!isAuthorizedIP() || MAINTENANCE_AUTHORIZED_IP))
	{
		require_once('interfaces/maintenance.php');
		die;
	}

	if (isset($_GET['pg']) === false)
		$_GET['pg'] = PAGE_DEFAULT;

	if ($_GET['pg'] == 'deconnexion')
	{
		PSession::destroy();
		header('Location: ' . TS_CLIENT_URL);
	}
	
	if (isset(PSession::$SESSION['tsSessionId']) === false)
	{
		$page = 'interfaces/auth.php';
	}
	else
	{
		PSession::$SESSION['nocache'] = (
			isset($_GET['cache']) ? false : (
				isset($_GET['nocache']) ? true :
					PSession::$SESSION['nocache']
				)
			);
		PSession::$SESSION['purgecache'] = isset($_GET['purgecache']) ? true : null;

		if (strpos($_GET['pg'], 'plugin_') === 0)
		{
			$arrPath = array_reverse(explode('_', $_GET['pg']));
			$page = 'plugins/' . $arrPath[1] . '/interfaces/' . $arrPath[0] . '.php';
		}
		else
		{
			$page = 'interfaces/' . $_GET['pg'] . '.php';
		}
	}

	if (file_exists($page))
	{
		try
		{
			require_once('application/plugins/tsPlugins.php');
			require_once('application/droits/tsDroits.php');

			tsPlugins::loadPlugins();
			tsPlugins::loadConfigs();
			tsDroits::loadDroits();

			require_once($page);
		}
		catch (Exception $e)
		{
			die($e->getMessage());
		}
	}
	else
	{
		header('Refresh:1; url=' . TS_CLIENT_URL, true);
		echo 'Page not found.';
	}

	PSession::commit();
?>
