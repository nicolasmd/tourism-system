<?php
	
/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
	
	require_once('application/collection/droitCollection.php');
	
	class droitModele extends baseModele implements WSDLable
	{

		protected $creationFiches;
		protected $suppressionFiches;
		protected $administration;
		
		protected $visualisation;
		protected $modification;
		protected $validation;
		
		
		public function __construct()
		{
			$this -> loadDroit(0);
		}
		
		
		public function loadDroit($intDroit)
		{
			$this -> setVisualisation(($intDroit & DROIT_VISUALISATION) > 0);
			$this -> setModification(($intDroit & DROIT_MODIFICATION) > 0);
			$this -> setValidation(($intDroit & DROIT_VALIDATION) > 0);
			$this -> setSuppressionFiches(($intDroit & DROIT_SUPPRESSION_FICHES) > 0);
			$this -> setCreationFiches(($intDroit & DROIT_CREATION_FICHES) > 0);
			$this -> setAdministration(($intDroit & DROIT_ADMINISTRATION) > 0);
		}
		
		
		public function getDroit()
		{
			$droit = $this -> visualisation * DROIT_VISUALISATION +
					$this -> modification * DROIT_MODIFICATION +
					$this -> validation * DROIT_VALIDATION +
					$this -> suppressionFiches * DROIT_SUPPRESSION_FICHES +
					$this -> creationFiches * DROIT_CREATION_FICHES +
					$this -> administration * DROIT_ADMINISTRATION;
			return $droit;
		}
		
		
		
		public function setModification($modification)
		{
			$bool = !!$modification;
			$this -> visualisation = ($bool === true) ? true : $this -> visualisation;;
			$this -> modification = ($bool === true) ? true : $this -> modification;
		}
		
		
		public function setAdministration($administration)
		{
			$bool = !!$administration;
			$this -> visualisation = ($bool === true) ? true : $this -> visualisation;
			$this -> modification = ($bool === true) ? true : $this -> modification;
			$this -> validation = ($bool === true) ? true : $this -> validation;
			$this -> suppressionFiches = ($bool === true) ? true : $this -> suppressionFiches;
			$this -> creationFiches = ($bool === true) ? true : $this -> creationFiches;
			$this -> administration = ($bool === true) ? true : $this -> administration;
		}
			


		
	}
