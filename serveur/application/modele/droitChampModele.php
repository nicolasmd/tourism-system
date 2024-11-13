<?php
	
/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
	
	require_once('application/modele/droitModele.php');
	require_once('application/collection/droitChampCollection.php');
	
	final class droitChampModele extends droitModele implements WSDLable
	{

		protected $idChamp;
		
		public function setAdministration($value)
		{
			return false;
		}
		
		public function setCreationFiches($value)
		{
			return false;
		}
		
		public function setSuppressionFiches($value)
		{
			return false;
		}
		
	}
