<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/collection/entreeThesaurusCollection.php');

	final class entreeThesaurusModele extends baseModele implements WSDLable
	{

		protected $cle;
		protected $liste;
		protected $lang;
		protected $libelle;
		protected $libellesExternes;

		public function __toString()
		{
			$str = '<h2>Entrée Thésaurus</h2>';
			$str .= '<h4>Clé : ' . $this -> cle . '</h4>';
			$str .= '<h4>Liste : ' . $this -> liste . '</h4>';
			$str .= '<h4>Langue : ' . $this -> lang . '</h4>';
			$str .= '<h4>Libelle : ' . $this -> libelle . '</h4>';
			return $str;
		}

	}
