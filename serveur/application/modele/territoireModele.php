<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/collection/territoireCollection.php');

	final class territoireModele extends baseModele implements WSDLable
	{
		
		protected $libelle;
		protected $idTerritoire;
		
		
		public function __toString()
		{
			$str = '<h2>Territoire</h2>';
			$str .= '<h4>Libelle : ' . $this -> libelle . '</h4>';
			$str .= '<h4>idTerritoire : ' . $this -> idTerritoire . '</h4>';
			return $str;
		}
		
	}
