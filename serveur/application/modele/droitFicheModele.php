<?php
	
/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
	
	require_once('application/modele/droitModele.php');
	require_once('application/collection/droitFicheCollection.php');
	
	final class droitFicheModele extends droitModele implements WSDLable
	{
		
		protected $idUtilisateur;
		
		protected $idFiche;
		protected $raisonSociale;
		protected $bordereau;
		protected $droitsChamp = array();

		public function setDroitsChamp(object $droitsChamp)
		{
			$this -> droitsChamp = $droitsChamp;
		}
		
		public function setAdministration($value)
		{
			return false;
		}
		
		public function setCreationFiches($value)
		{
			return false;
		}

	}
