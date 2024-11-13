<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/collection/ficheFichierCollection.php');

	final class ficheFichierModele extends baseModele implements WSDLable
	{
		
		protected $idFiche;
		protected $idFichier;
		protected $md5;
		protected $nomFichier;
		protected $type;
		protected $path;
		protected $url;
		protected $extension;
		protected $proprietes = array();
		protected $principal;
		protected $content;
		
		
		public function setPrincipal($value)
		{
			$this -> principal = ($value == 'Y' || $value === true);
		}
		
		
		public function __toString()
		{
			$str = '<h2>Fichier</h2>';
			$str .= '<h4>idFiche : ' . $this -> idFiche . '</h4>';
			$str .= '<h4>idFichier : ' . $this -> idFichier . '</h4>';
			$str .= '<h4>MD5 : ' . $this -> md5 . '</h4>';
			$str .= '<h4>Chemin : ' . $this -> path . '</h4>';
			$str .= '<h4>Url : ' . $this -> url . '</h4>';
			$str .= '<h4>nomFichier : ' . $this -> nomFichier . '</h4>';
			$str .= '<h4>type : ' . $this -> type . '</h4>';
			$str .= '<h4>extension : ' . $this -> extension . '</h4>';
			$str .= '<h4>proprietes : ' . $this -> proprietes . '</h4>';
			return $str;
		}		
	} 
