<?php
	
/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
	
	require_once('application/collection/versionCollection.php');
	
	final class versionModele extends baseModele implements WSDLable
	{
		
		protected $idFicheVersion;
		protected $idFiche;
		protected $dateVersion;
		protected $idUtilisateur;
		protected $etat;
		protected $dateValidation;


		public function __toString()
		{
			$str = '<h3>Utilisateur</h3>';
			$str .= '<h5>idFicheVersion : ' . $this -> idFicheVersion . '</h5>';
			$str .= '<h5>idFiche : ' . $this -> idFiche . '</h5>';
			$str .= '<h5>Date version : ' . $this -> dateVersion . '</h5>';
			$str .= '<h5>idUtilisateur : ' . $this -> idUtilisateur . '</h5>';
			$str .= '<h5>Etat : ' . $this -> etat . '</h5>';
			$str .= '<h5>Date validation : ' . $this -> dateValidation . '</h5>';
			return $str;
		}
		
	}
