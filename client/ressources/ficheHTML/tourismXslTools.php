<?php

	class tourismXslTools
	{

		public static function getXpathValue($domXpath, $oChamp, $node = null)
		{

			if (count($oChamp -> champs) > 0)
			{
				$retour = array();
				$nodelist = $domXpath -> query($oChamp -> xPath);
				for($i = 0; $i < $nodelist -> length; $i++)
				{
					foreach($oChamp -> champs as $champ)
					{
						$domNode = $nodelist -> item($i);
						$retour[$i][$champ -> identifiant] = self::getXpathValue($domXpath, $champ, $domNode);
					}
				}
				return $retour;
			}

			$nodelist = (is_null($node)) ? $domXpath -> query($oChamp -> xPath) :
			$domXpath -> query($oChamp -> xPath, $node);

			if ($nodelist -> length == 0)
			{
				$retour = '';
			}
			elseif($nodelist -> length == 1)
			{
				$retour = $nodelist -> item(0) -> nodeValue;
			}
			else
			{
				$retour = array();
				for ($i = 0; $i < $nodelist -> length; $i++)
				{
					$retour[] = $nodelist -> item($i) -> nodeValue;
				}
			}
			return $retour;
		}

		/**
		 * Convertit le code XML de getXmlFiche en HTML pour l'affichage
		 * en mode 'list' ou en mode 'detail'
		*/
		public static function convertXmlToHtml($oFiche, $mode, $playlistId=null) {
			global $language;
			$path = MODULE_BASE_PATH . "/htmls" . "/". SITE_ID . "/" . $playlistId;
			if (!file_exists($path)) {
			  mkdir($path, 0777, true);
			}
			if (file_exists($path . $oFiche->idFiche . "_$mode.html")
				&& isset($_GET['renew']) === false)
			{
				//drupal_set_message('La fiche n° '. $oFiche->idFiche . ' n\'a pas été généré en mode '.$mode, 'warning');
				return false;
			}

			$lafiche = array();
			foreach ($oFiche as $key => $val) {
				$lafiche["fiche"][$key] = $val;
			}

			$lafiche["fiche"]["description_commerciale_fr"] = str_replace('<','&lt;',str_replace('>','&gt;',str_replace('\n','<br/>',nl2br($lafiche["fiche"]["description_commerciale_fr"]))));
			if ($mode=='detail')
			{
				if (array_search("vCard",$oFiche->fonctionnalites)!==false)
				{
					//Création de la vCard
					$vcard = "BEGIN:VCARD".PHP_EOL;
					$vcard .= "FN:".$oFiche->raison_sociale.PHP_EOL;
					$vcard .= "TEL;HOME:".$oFiche->telephone1.PHP_EOL;
					$vcard .= "ADR;HOME:;;".$oFiche->adresse1.' '.$oFiche->adresse2.' '.$oFiche->adresse3.' '.$oFiche->code_postal.' '.$oFiche->commune.PHP_EOL;
					$vcard .= "EMAIL;INTERNET:".$oFiche->email.PHP_EOL;
					$vcard .= "URL:".$oFiche->site_web.PHP_EOL;
					$vcard .= "END:VCARD";
					file_put_contents($path. "/" . $oFiche->idFiche . ".vcf", $vcard);
				}
			}

			$xmlBrut = self::arrayToXml($lafiche);

			file_put_contents($path. "/" . $oFiche->idFiche . ".xml", $xmlBrut);

			$xml = new DOMDocument('1.0');
			$xml->loadXML($xmlBrut);
			$xsl = new DOMDocument('1.0');
			if (is_null($playlistId)) {
				$xsl->load(MODULE_BASE_PATH . "/ressources/defaultViews/$mode.xsl");
			} else {
				if (file_exists(MODULE_BASE_PATH . "/ressources/defaultViews/$mode" . "_$playlistId" . ".xsl"))
				{
					$xsl->load(MODULE_BASE_PATH . "/ressources/defaultViews/$mode" . "_$playlistId" . ".xsl");
				}
				else
				{
					$xsl->load(MODULE_BASE_PATH . "/ressources/defaultViews/$mode.xsl");
				}
			}


			$proc = new XSLTProcessor;
			$proc->importStyleSheet($xsl);
			$proc->setParameter('', 'codeLangue', $language->language);
			$proc->registerPHPFunctions();

			$code = $proc->transformToXML($xml);
			//$dom = $proc->transformToDoc($xml);

			/*$dom->formatOutput = true;
			$code = $dom->saveXML();*/

			$code = str_replace('&amp;lt;', '<', $code);
			$code = str_replace('&amp;gt;', '>', $code);
			$code = str_replace('&lt;', '<', $code);
			$code = str_replace('&gt;', '>', $code);

			file_put_contents($path. "/" . $oFiche->idFiche . "_$mode.html", $code);

			//drupal_set_message('La fiche n° '. $oFiche->idFiche . ' à été généré en mode '.$mode, 'success');
			return $code;
		}





		/**
		 * Convertit un type array en XML
		 * @param <type> $arr
		 * @param <type> $root
		 * @param <type> $parent
		 * @return string
		 */
		public static function arrayToXml($arr, $root = true, $parent = null) {
			$strXml = '';

			if ($root === true) {
				$strXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
			}

			if (is_array($arr) === true) {
				// Tableau à indices numériques ?
				// Simplification de la vérification : existe t-il un indice égal à 0 ?
				if (isset($arr[0]) === true) {
					if (is_null($parent) === true) {
						throw new Exception("Aucun bloc de précisé pour la génération du xml");
					}

					foreach ($arr as $bloc) {
						$strXml .= '<' . $parent . '>';
						if (is_array($bloc) === true) {
							$strXml .= self::arrayToXml($bloc, false);
						} else {
							$strXml .= strip_tags(str_replace('&', '&amp;', $bloc));
						}
						$strXml .= '</' . $parent . '>';
					}
				} else {
					foreach ($arr as $blocName => $bloc) {
						if (is_array($bloc) === true && isset($bloc[0])) {
							$strXml .= self::arrayToXml($bloc, false, $blocName);
						} else if (is_array($bloc) === true) {
							$strXml .= '<' . $blocName . '>';
							$strXml .= self::arrayToXml($bloc, false, $blocName);
							$strXml .= '</' . $blocName . '>';
						} else {
							$strXml .= '<' . $blocName . '>';
							$strXml .= strip_tags(str_replace('&', '&amp;', $bloc));
							$strXml .= '</' . $blocName . '>';
						}
					}
				}
			}
			return $strXml;
		}








		public static function convertir_date($date)
		{
			if (strlen($date)>10) $date=substr($date,0,10);
			$timestamp = strtotime($date);

			$date_array=array_reverse(explode('-',$date));

			$jours_array=explode('/',date("N/n",$timestamp));

			$jours=array("lun","mar","mer","jeu","ven","sam","dim");
			$mois=array("Janv.","Févr.","Mars","Avril","Mai","Juin","Juill.","Août","Sept.","Oct.","Nov.","Déc.");

			$retour = $jours[$jours_array[0]-1]." ".$date_array[0]." ".$mois[$jours_array[1]-1]." ".$date_array[2];
			return $retour;
		}



		public static function convertir_date2($date)
		{
			$timestamp = strtotime($date);

			$date_array=array_reverse(explode('-',$date));

			$jours_array=explode('/',date("N/n",$timestamp));

			$jours=array("lundi","mardi","mercredi","jeudi","vendredi","samedi","dimanche");
			$mois=array("Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");

			$retour = $jours[$jours_array[0]-1]." ".$date_array[0]." ".$mois[$jours_array[1]-1]." ".$date_array[2];
			return $retour;
		}



		/**
		 *
		 * @global <type> $language
		 * @param <type> $param
		 * @return <type>
		 */
		public static function traductionTIF($param,$mode="", $useCache = false) {

			//construire le memcache
			/*if($useCache)
				tsCache::load("memcache");*/

			//Construire la clef
			//$cle = "tradTIF$param$langue/$mode";

			//regarder si la clef existe
			//$value = tsCache::get($cle);

			//if ($value == null) {
				//si non le génerer (comment ?)
				$value = file_get_contents("http://services.tourism-system.fr/traductionTIF.php?image&codeLangue=fr&tif=$param&mode=$mode");
				/*if($useCache)
					tsCache::set($cle, $value);*/
			//}

			//$value = (preg_match('#not found#i', $value)) ? '' : $value;
			return $value;
		}
		/**
		 *
		 * @param string $src
		 * @return <type>
		 */
		public static function redimensionListe($src) {
			$tmpFile = file_get_contents($src);
			$parts = explode('/', $src);
			$fileName = $parts[count($parts) - 1];
			$src = file_directory_path() . '/' . $fileName;
			file_put_contents($src, $tmpFile);
			$img = imagecache_create_url('tourism_vignette_liste_2_cols', $src);
			return (getimagesize($img) === false) ? '/sites/all/modules/_raccourci/tourism_raccourci/images/visuel_defaut.jpg' : $img;
		}
		/**
		 *
		 * @param string $src
		 * @return <type>
		 */
		public static function redimensionDetailManif($src) {
			$tmpFile = file_get_contents($src);
			$parts = explode('/', $src);
			$fileName = $parts[count($parts) - 1];
			$src = file_directory_path() . '/' . $fileName;
			file_put_contents($src, $tmpFile);
			$img = imagecache_create_url('tourism_detail_manifs', $src);
			return (getimagesize($img) === false) ? '/images/visuel_defaut_224x290.jpg' : $img;
		}
		/**
		 *
		 * @param string $src
		 * @return <type>
		 */
		public static function redimensionDetailLarge($src) {
			$tmpFile = file_get_contents($src);
			$parts = explode('/', $src);
			$fileName = $parts[count($parts) - 1];
			$src = file_directory_path() . '/' . $fileName;
			file_put_contents($src, $tmpFile);
			$img = imagecache_create_url('tourism_detail_large', $src);
			return (getimagesize($img) === false) ? '/images/visuel_defaut.jpg' : $img;
		}
		/**
		 *
		 * @param string $src
		 * @return <type>
		 */
		public static function redimensionDetailFormResa($src) {
			$tmpFile = file_get_contents($src);
			$parts = explode('/', $src);
			$fileName = $parts[count($parts) - 1];
			$src = file_directory_path() . '/' . $fileName;
			file_put_contents($src, $tmpFile);
			$img = imagecache_create_url('tourism_detail_contact', $src);
			return (getimagesize($img) === false) ? '/images/visuel_defaut_contact.jpg' : $img;
		}
		/**
		 *
		 * @param string $src
		 * @return <type>
		 */
		public static function redimensionAvis($src) {
			$tmpFile = file_get_contents($src);
			$parts = explode('/', $src);
			$fileName = $parts[count($parts) - 1];
			$src = file_directory_path() . '/' . $fileName;
			file_put_contents($src, $tmpFile);
			$img = imagecache_create_url('tourism_detail_avis', $src);
			return (getimagesize($img) === false) ? '' : $img;
		}
		/**
		 *
		 * @param string $src
		 * @return <type>
		 */
		public static function redimensionGMap($src) {
			$tmpFile = file_get_contents($src);
			$parts = explode('/', $src);
			$fileName = $parts[count($parts) - 1];
			$src = file_directory_path() . '/' . $fileName;
			file_put_contents($src, $tmpFile);
			$img = imagecache_create_url('tourism_GMap', $src);
			return (getimagesize($img) === false) ? '' : $img;
		}

		public static function redimensionImage($src, $type, $defaut = 'false')
		{
			$tailles = array(
				'tourism_vignette_liste_2_cols' => array('w' => 320, 'h' => 480),
				'tourism_detail_large' => array('w' => 665, 'h' => 500)
			);

			$urlSource = 'http://www.sit-client.dev:82/application/proxy/proxy.php?service=ficheFichier&action=resizeImage&u='.$src.'&w='.$tailles[$type]['w'].'&h='.$tailles[$type]['h'];
			$arrUrl = array_reverse(explode('/', $src));
			$pathDestination = '../../ressources/tmpFiles/miniatures/' . time() . '_' . strtolower(strtr(str_replace(' ', '_', trim($arrUrl[0])), $GLOBALS['normalizeChars']));

			copy($urlSource, $pathDestination);

			return $pathDestination;

			$imageDefaut = ($defaut=='true') ? "/images/visuel_defaut_$type.jpg" : "";
			$tmpFile = file_get_contents($src);
			$src = utf8_decode($src);
			$parts = explode('/', $src);
			$fileName = $parts[count($parts) - 1];

			//hack acogit
			$tmpArray = explode('?', $fileName);
			if (is_array($tmpArray) && count($tmpArray) > 1) {
				$tmpArray = explode('=', $tmpArray[1]);
				$fileName = $tmpArray[1];
			}

			$src2 = 'http://www.sit-client.dev:82/ressources/tmpFiles/' . str_replace(array(utf8_decode("è"),utf8_decode("é")),array("e","e"),$fileName);
			file_put_contents($src2, $tmpFile);
			$img = imagecache_create_url($type, $src2);
			if (@getimagesize($img) === false)
			{
				mail('jimmy@raccourci.fr','DEBUG - redimensionImage',$src.PHP_EOL.$src2.PHP_EOL.$type.PHP_EOL.$defaut);
				return "";
			}
			return (getimagesize($img) === false) ? $imageDefaut : $img;
		}


	}




?>
