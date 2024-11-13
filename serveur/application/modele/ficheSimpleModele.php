<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/collection/ficheSimpleCollection.php');
	require_once('application/utils/tifTools.php');

	final class ficheSimpleModele extends baseModele implements WSDLable
	{

		const XPATH_RAISON_SOCIALE = 'tif:Contacts/tif:DetailContact[attribute::type="04.03.13"][1]/tif:RaisonSociale';
		const XPATH_CODE_INSEE = 'tif:Contacts/tif:DetailContact[attribute::type="04.03.13"][1]/tif:Adresses/tif:DetailAdresse/tif:Commune/@code';
		const XPATH_BORDEREAU = 'tif:DublinCore/tif:Classification/@code';
		const XPATH_GPS_LAT = 'tif:Geolocalisations/tif:DetailGeolocalisation[attribute::type="08.01.02"]/tif:Zone[attribute::type="08.02.07.02"]/tif:Points/tif:DetailPoint[attribute::type="08.02.05.11"][1]/tif:Coordonnees/tif:DetailCoordonnees[attribute::type="08.02.02.03"][1]/tif:Latitude';
		const XPATH_GPS_LNG = 'tif:Geolocalisations/tif:DetailGeolocalisation[attribute::type="08.01.02"]/tif:Zone[attribute::type="08.02.07.02"]/tif:Points/tif:DetailPoint[attribute::type="08.02.05.11"][1]/tif:Coordonnees/tif:DetailCoordonnees[attribute::type="08.02.02.03"][1]/tif:Longitude';
		const XPATH_REFERENCE_EXTERNE = 'tif:DublinCore/dc:identifier';

		protected $idFiche;
		protected $raisonSociale;
		protected $codeTIF;
		protected $codeInsee;
                protected $commune;
		protected $bordereau;
		protected $gpsLat;
		protected $gpsLng;
		protected $idGroupe;
                protected $nomGroupe;
		protected $publication;
		protected $referenceExterne;
		protected $dateCreation;

		public static function loadByXml(&$xml)
		{
			$classname = __CLASS__;
			$oFiche = new $classname();

			$oFiche -> setRaisonSociale(tsXml::getValueXpath($xml, self::XPATH_RAISON_SOCIALE));
			$oFiche -> setCodeInsee(tsXml::getValueXpath($xml, self::XPATH_CODE_INSEE));
			$oFiche -> setBordereau(tifTools::getBordereau(tsXml::getValueXpath($xml, self::XPATH_BORDEREAU)));
			$oFiche -> setGpsLat(tsXml::getValueXpath($xml, self::XPATH_GPS_LAT));
			$oFiche -> setGpsLng(tsXml::getValueXpath($xml, self::XPATH_GPS_LNG));
			$oFiche -> setReferenceExterne(tsXml::getValueXpath($xml, self::XPATH_REFERENCE_EXTERNE));

			return $oFiche;
		}


		public function __toString()
		{
			$str = '<h2>Fiche simple</h2>';
			$str .= '<h4>idFiche : ' . $this -> idFiche . '</h4>';
			$str .= '<h4>Raison sociale : ' . $this -> raisonSociale . '</h4>';
			$str .= '<h4>Code TIF : ' . $this -> codeTIF . '</h4>';
			$str .= '<h4>Code insee : ' . $this -> codeInsee . '</h4>';
			$str .= '<h4>Bordereau : ' . $this -> bordereau . '</h4>';
			$str .= '<h4>Latitude : ' . $this -> gpsLat . '</h4>';
			$str .= '<h4>Longitude : ' . $this -> gpsLng . '</h4>';
			$str .= '<h4>Groupe : ' . $this -> idGroupe . '</h4>';
			$str .= '<h4>Publication : ' . $this -> publication . '</h4>';
			$str .= '<h4>Référence externe : ' . $this -> referenceExterne . '</h4>';
			$str .= '<h4>Date création : ' . $this -> dateCreation . '</h4>';
			return $str;
		}

	}
