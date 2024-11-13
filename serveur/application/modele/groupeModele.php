<?php
	
/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
	
	require_once('application/collection/groupeCollection.php');
	
	final class groupeModele extends baseModele implements WSDLable
	{
		
		protected $idGroupe;
		protected $idGroupeParent;
		protected $nomGroupe;
		protected $descriptionGroupe;
		protected $idSuperAdmin;


		public function __toString()
		{
			$str = '<h3>Groupe</h3>';
			$str .= '<h5>idGroupe : ' . $this -> idGroupe . '</h5>';
			$str .= '<h5>idGroupeParent : ' . $this -> idGroupeParent . '</h5>';
			$str .= '<h5>Libellé : ' . $this -> nomGroupe . '</h5>';
			$str .= '<h5>Description : ' . $this -> descriptionGroupe . '</h5>';
			$str .= '<h5>Super admin : ' . $this -> idSuperAdmin . '</h5>';
			return $str;
		}
		
	}
