<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	require_once('application/db/champDb.php');
	require_once('application/db/ficheDb.php');
	require_once('ressources/fichePdf/xsl/xslFunctions.php');

	/**
	 * Classe wsFicheExport - endpoint du webservice FicheExport
	 *
	 */
	final class wsFicheExport extends wsEndpoint
	{

		/**
		 * Retourne la source xml de la fiche
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @return string xml : source xml de la fiche au format TIF v3.0
		 * @access root superadmin admin
		 */
		protected function _exportFicheXml($idFiche, $idFicheVersion = null)
		{
			$oFiche = ficheDb::getFicheByIdFiche($idFiche, $idFicheVersion);
			$this -> checkDroitFiche($oFiche, DROIT_VISUALISATION);
			$droitsFiche = tsDroits::getDroitFiche($oFiche);
			$fiche = ficheDb::getFiche($oFiche, $droitsFiche);
			return array('xml' => $fiche -> xml);
		}

		/**
		 * Retourne l'url vers le fichier pdf de la fiche
		 * @param int $idFiche : identifiant de la fiche sitFiche.idFiche
		 * @return string url : url du fichier pdf de la fiche
		 * @access root superadmin admin
		 */
		protected function _exportFichePdf($idFiche)
		{
			$oFiche = ficheDb::getFicheByIdFiche($idFiche);
			$this -> checkDroitFiche($oFiche, DROIT_VISUALISATION);
			$droitsFiche = tsDroits::getDroitFiche($oFiche);
			$fiche = ficheDb::getFiche($oFiche, $droitsFiche);

			$path = tsConfig::get('TS_PATH_PDF');

			$xslDoc = new DOMDocument();
			$xslDoc -> load($path . 'xsl/xml-to-xhtml.xsl');
			$proc = new XSLTProcessor();
			$proc -> importStylesheet($xslDoc);
			$proc -> registerPHPFunctions();

			$xmlDoc = new DOMDocument();
			$xmlDoc -> loadXML($fiche -> xml);

			$html = $proc -> transformToXML($xmlDoc);

			$filename = time() . '_' . $idFiche;
			file_put_contents($path . "tmp/$filename.html", $html);

			shell_exec("cd " . $path . "libs/ ; java -classpath xalan.jar org.apache.xalan.xslt.Process -in ../tmp/$filename.html -xsl ../xsl/xhtml-to-xslfo.xsl -out ../tmp/tmp.fo");
			shell_exec("cd " . $path . "libs/ ; java -classpath fop.jar:commons-logging-1.0.4.jar org.apache.fop.cli.Main ../tmp/tmp.fo ../tmp/$filename.pdf");

			unlink($path . "tmp/$filename.html");
			unlink($path . "tmp/tmp.fo");

			if (file_exists($path . "tmp/$filename.pdf") === false)
			{
				throw new Exception("Impossible de générer le fichier PDF.");
			}

			return array('url' => tsConfig::get('TS_URL_PDF') . "tmp/$filename.pdf");
		}

		/**
		 * Retourne la valeur d'un champ dans toutes les fiches administrables pour l'utilisateur
		 * @param string $identifiant : identifiant du champ
		 * @return array fiches : tableau composé de l'idFiche et de la valeur du champ
		 * @access root superadmin admin
		 */
		protected function _exportFichesValeurChamp($identifiant)
		{
			$oChamp = champDb::getChampByIdentifiant($identifiant);

			$fiches = array();
			foreach(tsDroits::getFichesAdministrables() as $idFiche)
			{
				$oFiche = ficheDb::getFicheByIdFiche($idFiche);
				$domFiche = new DOMDocument('1.0');
				$domFiche -> loadXML($oFiche -> xml);
				$domXpath = new DOMXpath($domFiche);
				$fiches[] = array(
					'idFiche' => $idFiche,
					$nomChamp => champDb::getFicheValueChamp($oFiche, $oChamp)
				);
			}

			return array('fiches' => $fiches);
		}
	}
