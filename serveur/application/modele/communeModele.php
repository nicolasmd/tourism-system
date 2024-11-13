<?php
	
/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
	
	require_once('application/collection/communeCollection.php');
	
	final class communeModele extends baseModele implements WSDLable
	{
		
		protected $libelle;
		protected $codeInsee;
		protected $codePostal;
		protected $codePays;
		protected $gpsLat;
		protected $gpsLng;

		public function __toString()
		{
			$str = '<h2>Commune</h2>';
			$str .= '<h4>Libelle : ' . $this -> libelle . '</h4>';
			$str .= '<h4>Code Insee : ' . $this -> codeInsee . '</h4>';
			$str .= '<h4>Code postal : ' . $this -> codePostal . '</h4>';
			$str .= '<h4>Code pays : ' . $this -> codePays . '</h4>';
			$str .= '<h4>Latitude : ' . $this -> gpsLat . '</h4>';
			$str .= '<h4>Longitude : ' . $this -> gpsLng . '</h4>';
			return $str;
		}
	}
