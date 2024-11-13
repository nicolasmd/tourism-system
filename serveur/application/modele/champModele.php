<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/collection/champCollection.php');

	final class champModele extends baseModele implements WSDLable
	{
	
		protected $idChamp;
		protected $idChampParent;
		protected $identifiant;
		protected $libelle;
		protected $stockage;
		protected $xPath;
		protected $scope;
		protected $versioning;
		protected $plugin;
		protected $bordereau;
		protected $liste;
		protected $cle;
		
		protected $champs = array();

	}
