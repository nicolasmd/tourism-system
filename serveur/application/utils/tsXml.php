<?php
	
/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class tsXml
	{
		private static $domFiches = array();
		
		
		private static function getDomFiche($oFiche)
		{
			if (!isset(self::$domFiches[$oFiche -> idFiche]))
			{
				if (empty($oFiche->xml))
				{
					throw new ApplicationException('Fichier XML vide : ' . $oFiche -> idFiche);
				}
				$domFiche = new tsDOMDocument('1.0');
				$domFiche->loadXML($oFiche->xml);
				self::setDomFiche($oFiche, $domFiche);
			}
			
			return self::$domFiches[$oFiche -> idFiche];
		}
		
		private static function setDomFiche($oFiche, $domFiche)
		{
			self::$domFiches[$oFiche -> idFiche] = $domFiche;
		}
		
		public static function getValueChamp($oFiche, $oChamp, $node = null)
		{
			$domFiche = self::getDomFiche($oFiche);
			
			$xpath = new DOMXPath($domFiche);
			
			if (count($oChamp -> champs) > 0)
			{
				$retour = array();
				$nodelist = $xpath -> query($oChamp -> xPath);
				for($i = 0 ; $i < $nodelist -> length ; $i++)
				{
					foreach($oChamp -> champs as $champ)
					{
						$domNode = $nodelist -> item($i);
						$retour[$i][$champ -> identifiant] = self::getValueChamp($oFiche, $champ, $domNode);
					}
				}
			}
			else
			{
				$nodelist = (is_null($node))
					? $xpath -> query($oChamp -> xPath)
					: $xpath -> query($oChamp -> xPath, $node);
				
				if ($nodelist -> length == 0)
				{
					$retour = '';
				}
				elseif($nodelist -> length == 1)
				{
					$retour = trim($nodelist -> item(0) -> nodeValue);
				}
				else
				{
					$retour = array();
					for ($i = 0 ; $i < $nodelist -> length ; $i++)
					{
						$retour[] = $nodelist -> item($i) -> nodeValue;
					}
				}
			}
			
			return $retour;
		}
		
		public static function setValueChamp($oFiche, $oChamp, $value)
		{
			$domFiche = self::getDomFiche($oFiche);
			
			if (is_array($oChamp->champs) && count($oChamp->champs) > 0)
			{
				$domFiche = self::JSONtoXML($domFiche, $oChamp, $value);
			}
			else
			{
				$domFiche->setValueFromXPath($oChamp->xPath, $value);
			}
			
			self::setDomFiche($oFiche, $domFiche);
		}
		
		private static function JSONtoXML($domFiche, $oChamp, $json)
		{
			// On efface tout le noeud
			$xpath = new DOMXPath($domFiche);
			$nodelist = $xpath -> query($oChamp -> xPath);
			
			for ($i = 0 ; $i < $nodelist -> length ; $i++)
			{
				$node = $nodelist -> item($i);
				$node -> parentNode -> removeChild($node);
			}
			
			$domFiche -> saveXML();
			
			foreach($json as $itemnumber => $arritem)
			{
				foreach($oChamp -> champs as $key => $value)
				{
					$xpathQuery = $oChamp -> xPath . '['. intval($itemnumber + 1) .']/' . $value -> xPath;
					
					$v = str_replace('<br />', "\n", $arritem[$value -> identifiant]);
					$v = str_replace('u00e9', 'é', $v);
					$v = str_replace('u00e8', 'è', $v);
					$v = str_replace('u00e0', 'à', $v);
					$v = str_replace('u00e2', 'â', $v);
					$v = str_replace('u00e7', 'ç', $v);
					$v = str_replace('u00ea', 'ê', $v);
					$v = str_replace('u00f4', 'ô', $v);
					$v = str_replace('u00fb', 'û', $v);
					$v = str_replace('u00f9', 'ù', $v);
					
					if ($v != '')
					{
						$domFiche -> setValueFromXPath($xpathQuery, $v);
					}
				}
			}
			
			$domFiche -> saveXML();
			
			return $domFiche;
		}
		
		public static function getXmlFiche($oFiche)
		{
			$domFiche = self::getDomFiche($oFiche);
			return $domFiche->saveXML();
		}
		
		// Utils
		public function getValueXpath($xml, $xpathQuery)
		{
			$domFiche = new tsDOMDocument('1.0');
			$domFiche->loadXML($xml);
			
			$xpath = new DOMXPath($domFiche);
			$nodes = $xpath -> query($xpathQuery);

			if ($nodes -> length == 1)
			{
				return $nodes -> item(0) -> nodeValue;
			}

			if ($nodes -> length > 1)
			{
				$retour = array();
				for($i=0 ; $i < $nodes -> length ; $i++)
				{
					$retour[] = $nodes -> item($i) -> nodeValue;
				}
				return $retour;
			}

			return null;
		}
		
		public static function hasResult($xml, $xpathQuery)
		{
			return !is_null(self::getValueXpath($oFiche, $xpathQuery));
		}
		
	}
