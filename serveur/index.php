<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	header("Content-type: text/xml");

	require_once('application/config/tsConfig.php');
	require_once('application/common/Logger.php');
	require_once('application/common/tsSession.php');

	require_once('application/cache/tsCache.php');
	require_once('application/cache/tsCacheInterface.php');

	require_once('application/collection/baseCollection.php');

	require_once('application/database/tsDatabase.php');
	require_once('application/database/tsDatabaseInterface.php');

	require_once('application/droits/tsDroits.php');
	require_once('application/droits/tsDroitsInterface.php');

	require_once('application/exception/ApplicationException.php');
	require_once('application/exception/CacheException.php');
	require_once('application/exception/ConfigException.php');
	require_once('application/exception/DatabaseException.php');
	require_once('application/exception/ImportException.php');
	require_once('application/exception/SecuriteException.php');
	require_once('application/exception/SessionException.php');

	require_once('application/gestionnaire/tsGestionnaireMessage.php');

	require_once('application/modele/baseModele.php');
	require_once('application/modele/WSDLable.php');

	require_once('application/plugins/tsPlugins.php');

	require_once('application/utils/fonctions.php');
	require_once('application/utils/tsXml.php');

	require_once('services/endpoint/wsEndpoint.php');
	require_once('services/endpoint/wsStatus.php');

	try
	{
		tsConfig::loadConfig('config');

		$isPlugin = (!empty($_GET['plugin']) && $_GET['plugin'] != 'ts');

		$endpoint = ($isPlugin ? 'plugins/' . $_REQUEST['plugin'] . '/' : 'services/endpoint/') . 'ws' . ucfirst($_REQUEST['service']) . '.php';
		if (!file_exists($endpoint))
		{
			throw new Exception('Service introuvable');
		}
		require_once($endpoint);

		$wsdl = ($isPlugin ? 'plugins/' . $_REQUEST['plugin'] . '/' : 'services/wsdl/') . $_REQUEST['service'] . '.wsdl';
		if (!file_exists($wsdl))
		{
			throw new Exception('WSDL introuvable');
		}
		$server = new SoapServer(tsConfig::get('BASE_URL') . $wsdl, array(
			'trace' => 1,
			'soap_version' => SOAP_1_1
			//'compression' => ( SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 9 ),
			//'content-encoding' => "gzip",
			//'accept-encoding' => 'gzip,deflate'
		));

		$class = 'ws' . ucfirst($_REQUEST['service']);
		if (!class_exists($class))
		{
			throw new Exception('Classe introuvable');
		}
 		$server -> setclass($class);

		$server -> handle();
	}
	catch (Exception $e)
	{
		echo $e -> getMessage();
	}
