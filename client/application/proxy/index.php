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

	header('Content-Type: application/json; charset=utf-8');

	require_once('../common/config.php');
	require_once('../common/fonctions.php');
	require_once('../common/Logger.php');
	require_once( '../common/PSession.php' );
	require_once('../plugins/tsPlugins.php');
	require_once('../droits/tsDroits.php');
	require_once('../cache/tsCache.php');
	require_once('../cache/tsCacheInterface.php');
	require_once('../exception/SessionException.php');
	require_once('../soapClient/wsClient.php');
	require_once('../proxyResponse/proxyStore.php');
	require_once('../proxyResponse/proxyResponse.php');
	require_once('tsProxy.php');

	PSession::start();

	// Suppression des ext-comp
	foreach ($_REQUEST as $k => $v)
	{
		if (strpos($k, 'ext-comp') !== false)
		{
			unset($_REQUEST[$k]);
		}
	}

	tsPlugins::loadPlugins();
	tsPlugins::loadConfigs();

	$plugin = $_GET['plugin'];
	$service = $_GET['service'];
	$action = $_GET['action'];
	unset($_GET['plugin']);
	unset($_GET['service']);
	unset($_GET['action']);

	$params = $_POST;
	if (count($_GET) > 0)
	{
		$params = array_merge($params, $_GET);
	}
	if (count($_FILES) > 0)
	{
		$params = array_merge($params, $_FILES);
	}

	$path = $plugin != 'ts' ? 'plugins/' . $plugin . '/proxy/' : 'application/proxy/';
	$className = 'p' . ucfirst($service);

	require_once(TS_CLIENT_PATH . $path . $className . '.php');

	try
	{
		$oProxy = new $className();
		call_user_func_array(array($oProxy, $action), array($params));
	}
	catch (SessionException $e)
	{
		if (isset($_POST['method']) && $_POST['method'] == 'request')
		{
			header("Status: 400 Bad Request", true, 400);
		}
		echo json_encode(array(
			'success' => false,
			'msg' => $e->getMessage(),
			'expired' => true
		));
	}
	catch (Exception $e)
	{
		if (isset($_POST['method']) && $_POST['method'] == 'request')
		{
			header("Status: 400 Bad Request", true, 400);
		}
		echo json_encode(array(
			'success' => false,
			'msg' => $e->getMessage()
		));
	}

	PSession::commit();
?>