<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	function fail()
	{
		header('HTTP/1.1 404 Not Found');
		die;
	}
	
	if (empty($_GET['idFiche']))
	{
		fail();
	}
	
	require_once('application/config/tsConfig.php');
	require_once('application/common/Logger.php');
	tsConfig::loadConfig('config');
	
	$idFiche = $idFichePath = $_GET['idFiche'];
	
	while (strlen($idFichePath) < 6)
	{
		$idFichePath = '0' . $idFichePath;
	}
	
	$pathRoot = tsConfig::get('TS_PATH_XML') . '/' . implode('/', str_split($idFichePath, 2)) . '/';
	
	if (empty($_GET['idFicheVersion']))
	{
		if (is_dir($pathRoot))
		{
			$dir = opendir($pathRoot);
			if ($dir !== false)
			{
				$idFicheVersion = 0;
				while($entry = readdir($dir))
				{
					if (is_file($pathRoot.$entry) && strpos($entry ,'.xml') !== false)
					{
						preg_match("/([0-9]+)-([0-9]+)\.xml/", $entry, $matches);
						$idFicheVersion = ($matches[2] > $idFicheVersion ? $matches[2] : $idFicheVersion);
					}
				}
				closedir($dir);
			}
			else
			{
				fail();
			}
		}
		else
		{
			fail();
		}
		
		if ($idFicheVersion > 0)
		{
			header('Location: ' . $idFiche . '-' . $idFicheVersion . '.xml');
		}
		else
		{
			fail();
		}
	}
	else
	{
		$idFicheVersion = $_GET['idFicheVersion'];
	}
	
	$pathXml = $pathRoot . $idFiche . '-' . $idFicheVersion . '.xml';
	
	if (file_exists($pathXml))
	{
		header("Content-type: text/xml; charset=UTF-8;");
		echo file_get_contents($pathXml);
	}
	else 
	{
		fail();
	}
