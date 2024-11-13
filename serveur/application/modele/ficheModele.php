<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/collection/ficheCollection.php');

	final class ficheModele extends baseModele implements WSDLable
	{

		protected $idFiche;
		protected $raisonSociale;
		protected $codeTIF;
		protected $codeInsee;
		protected $bordereau;
		protected $gpsLat;
		protected $gpsLng;
		protected $idGroupe;
		protected $publication;
		protected $referenceExterne;
		protected $dateCreation;

		protected $xml;
		protected $idVersion;
		protected $dateVersion;
		protected $etatVersion;

		public function __toString()
		{
			$str = '<h2>Fiche</h2>';
			$str .= '<h4>idFiche : ' . $this -> idFiche . '</h4>';
			$str .= '<h4>Raison sociale : ' . $this -> raisonSociale . '</h4>';
			$str .= '<h4>Code TIF : ' . $this -> codeTIF . '</h4>';
			$str .= '<h4>Code insee : ' . $this -> codeInsee . '</h4>';
			$str .= '<h4>Bordereau : ' . $this -> bordereau . '</h4>';
			$str .= '<h4>Latitude : ' . $this -> gpsLat . '</h4>';
			$str .= '<h4>Longitude : ' . $this -> gpsLng . '</h4>';
			$str .= '<h4>Groupe : ' . $this -> idGroupe . '</h4>';
			$str .= '<h4>Publication : ' . $this -> publication . '</h4>';

			$str .= '<h4>Version : ' . $this -> idVersion . '</h4>';
			$str .= '<h4>Date version : ' . $this -> dateVersion . '</h4>';
			$str .= '<h4>Etat : ' . $this -> etatVersion . '</h4>';
			$str .= '<h4>XML : <xmp>' . $this -> xml . '</xmp></h4>';
			return $str;
		}

	}
