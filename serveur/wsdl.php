<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	header("Content-Type: text/xml");

	if (!file_exists($_GET['wsdl'] . '.wsdl'))
	{
		header('HTTP/1.1 404 Not Found');
		die;
	}

	require_once('application/config/tsConfig.php');
	require_once('application/common/Logger.php');
	tsConfig::loadConfig('config');

	$wsdl = file_get_contents($_GET['wsdl'] . '.wsdl');
	$wsdl = str_replace('{BASE_URL}', tsConfig::get('BASE_URL'), $wsdl);
	
	$dom = new DOMDocument();
	$dom -> loadXML($wsdl);
	$messages = $dom -> getElementsByTagName('message');
	
	if ($messages -> length > 0)
	{
		for ($i=0 ; $i<$messages -> length ; $i++)
		{
			$message = $messages -> item($i);
			
			if (preg_match('/Request$/', $message -> getAttribute('name')))
			{
				$partElement = $dom -> createElement('part');
				$partElement -> setAttribute('name', 'hookParams');
				$partElement -> setAttribute('type', 'xs:anyType');
				$partElement -> setAttribute('nillable', 'true');
				$message -> appendChild($partElement);
			}
			
			if (preg_match('/Response$/', $message -> getAttribute('name')))
			{
				$partElement = $dom -> createElement('part');
				$partElement -> setAttribute('name', 'hookResponses');
				$partElement -> setAttribute('type', 'xs:anyType');
				$partElement -> setAttribute('nillable', 'true');
				$message -> appendChild($partElement);
			}
		}
	}
	
	echo $dom -> saveXML();
