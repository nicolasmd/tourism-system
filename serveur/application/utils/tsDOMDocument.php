<?php
    
/**
 * @version		1.0 alpha-test - 2011-01-27
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive, Inc. All rights reserved
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */
	
	require_once('application/db/thesaurusDb.php');
	
	/**
	 * Dom Extensions
	 */
	
	
	class tsDOMDocument extends DOMDocument
	{
		
		/**
		 * Crée un nouvel objet DOMDocument 
		 * @return Object : DOMDocument
		 * @param $version string[optional] : Le numéro de version du document en tant que partie de la déclaration XML. 
		 * @param $encoding string[optional] : L'encodage du document en tant que partie de la déclaration XML. 
		 */
		public function __construct($version = null, $encoding = null)
		{
	        parent::__construct($version, $encoding);
	    }
		
		
		
		
		/**
		 * 
		 * @return 
		 * @param $xpathQuery Object
		 * @param $oFiche Object
		 * @param $node Object
		 * @param $key Object
		 * @param $value Object
		 */
		public function setValueFromXPath($xpathQuery, $value)
		{
			$xml = $this -> saveXML();
			
			// Jermey
			//if ($value == '') return;
			
			// On récupère le dernier noeud de la requête pour savoir si c'est un attribut ou une nodeValue
			$value = str_replace('&', '&amp;', $value);
			
			// Est-ce que le noeud existe ?
			$xpath = new DOMXPath($this);
			
			/*Logger::file($xpathQuery);
			Logger::file($value);*/
			
			// C'est ici !!
			
			//$this -> getNew();
			
			$nodelist = $xpath -> query($xpathQuery);
			if ($nodelist -> length == 0)
			{
				$nodes = explode('/', $xpathQuery);
				$finalNode = $nodes[count($nodes) - 1];
				//$xml = $this -> saveXML();
				$cnode = $this -> createXmlFromXPath($xpathQuery, $xml);
				
				if (strpos($finalNode, '@') !== false)
				{
					$finalNode = trim(str_replace('@', '', $finalNode));
					// Attention ! new DOMAttr peut provoquer l'erreur Invalid Character Error
					// => La cause est la présence de caractères indésirabes dans finalNode (retour chariot...)
					$cnode -> setAttributeNode(new DOMAttr($finalNode, $value));
					
					// Vérification que le type de noeud n'est pas un libellé
					if (strpos($finalNode, 'libelle') === false)
					{
						// Jermey
						//$listName = thesaurusDb::getListeFromCle($value);
						$listName = thesaurusDb::getListeFromCle($value, false);
						// Jermey
						if (self::listHasLibelle($listName))
						{
							$libelle = thesaurusDb::getValueByKey($value, 'fr');
							if (!is_null($libelle)
								/*&& $GLOBALS[$oFiche -> bordereau][$key]['libelle'] !== false &&
								!isset($GLOBALS[$oFiche -> bordereau][$key]['libelle'])*/)
							{
								$cnode -> setAttributeNode(new DOMAttr('libelle', $libelle));
							}
							$cnode -> setAttributeNode(new DOMAttr('xml:lang', 'fr'));
						}
						elseif(self::listHasLibelle($listName) === false)
						{
							$libelle = thesaurusDb::getValueByKey($value, 'fr');
							$cnode -> setAttributeNode(new DOMAttr('xml:lang', 'fr'));
							$cnode -> nodeValue = $libelle;
						}
					}
				}
				else
				{
					$cnode -> nodeValue = $value;
				}
			}
			else
			{
				$node = $nodelist -> item(0);
				$node -> nodeValue = $value;
			}
			$this -> getNew();
		}
		
		
		
		
		/**
		 * 
		 * @return 
		 * @param $xpathQuery Object
		 * @param $oFiche Object
		 */
		public function createXmlFromXPath($xpathQuery, $xmlFiche)
		{
			//Logger::file('createXmlFromXPath');
			//Logger::file($xpathQuery);
			
			$l = 0;
			
			if (strpos($xpathQuery, '/') === 0)
			{
				$xpathQuery = substr($xpathQuery, 1);
			}
			
			$nodes = explode('/', $xpathQuery);
			
			// Root par défaut pour le current_node
			$xpath = new DOMXpath($this);
			$current_node = $xpath -> query('/*') -> item(0);
			
			// Découpage du XPath en noeuds pour le parcours
			foreach($nodes as $node)
			{
				$xpath = new DOMXpath($this);
				$t_attributes = array();
				
				// reconstitution de la requête jusqu'au noeud courant
				$req_xpath = implode('/', array_slice($nodes, 0, ++$l));
				$current_nodelist = $xpath -> query('//' . $req_xpath);
				preg_match_all("/([^\[]*)\[([^\]]*)\]/", $node, $matches, PREG_PATTERN_ORDER);
				
				$itemNumber = (isset($matches[2][0]) && is_numeric($matches[2][0])) ? $matches[2][0] - 1 : 0;
				
				// Le noeud courant devient le noeud à parser s'il existe, ou le noeud courant
				$current_node = ($current_nodelist -> length == 0) ?
						$current_node : $current_nodelist -> item(0);
            
				//$strXml = $this -> saveXML();

				// Le noeud à parser n'existe pas, il faut le créer
				if ($current_nodelist -> length == 0)
				{
					//Logger::file("$req_xpath n'existe pas");
					if (strpos($node, '@') !== false)
					{
						continue;
					}
					
					if (strpos($node, '[') !== false && strpos($node, ']') !== false)
					{
						preg_match_all("/([^\[]*)\[([^\]]*)\]/", $node, $matches, PREG_PATTERN_ORDER);
						$node = $matches[1][0];

						if (strpos($matches[2][0], 'attribute::') !== false
							 && strpos($matches[2][0], 'contains') === false
							 && !is_numeric($matches[2][0]))
						{
							$matches[2][0] = str_replace('attribute::', '', $matches[2][0]);
							$matches[2][0] = str_replace(' and ', ' ', $matches[2][0]);
							preg_match_all('/[^\=]*\=\"[^\"]*\"/', $matches[2][0], $t_attributes);
						}
					}
					
					$newnode = $this -> createElement($node);
          
					if (is_array($t_attributes[0]))
					{
						foreach($t_attributes[0] as $attribute)
						{
							$att = explode('=', trim($attribute));
							if (count($att) > 1)
							{
								$newnode -> setAttributeNode(new DOMAttr(trim($att[0]), trim($att[1], '"')));
							}
							// Ajout du libelle et de la langue pour les attributs "type"
							// Jermey
							//$listName = thesaurusDb::getListeFromCle(trim($att[1], '"'));
							$listName = thesaurusDb::getListeFromCle(trim($att[1], '"'), false);
							// Jermey
							
							if (self::listHasLibelle($listName) && count($t_attributes[0]) == 1)
							{
								//Logger::file('Libelle');
								$libelle = thesaurusDb::getValueByKey(trim($att[1], '"'));
								if (!is_null($libelle))
								{
									$newnode -> setAttributeNode(new DOMAttr('libelle', $libelle));
									$newnode -> setAttributeNode(new DOMAttr('xml:lang', 'fr'));
								}
							}
							elseif(self::listHasLibelle($listName) === false)
							{
								//Logger::file('No Libelle');
								$libelle = thesaurusDb::getValueByKey(trim($att[1], '"'));
								$newnode -> setAttributeNode(new DOMAttr('xml:lang', 'fr'));
								$newnode -> nodeValue = $libelle;
							}
							//$this -> getNew();
						}
						//$this -> getNew();
					}
					$current_node = $current_node -> appendChild($newnode);
					//$this -> getNew();
					
					$this -> saveXML();
					
					
				}
				//$this -> getNew();
			}
			
			//$this -> getNew();
			return($current_node);
		}
		
		
		
		
		public function getNew()
		{
			$xml = $this -> saveXML();
			
			//$filename = rand(0,1000);
			//Logger::file("filename :  $filename");
			//file_put_contents('/StockSite/www.sit-serveur.dev/logs/fiche-' . $filename . '.xml', $xml);
			
			if ($xml === false)
			{
				Logger::file('Erreur de saveXml');	
			}
			else
			{
				$this -> loadXML($xml);
			}
			
		}
		
		
		
		
		public static function listHasLibelle($listName)
		{
			$listWithAttribute = array(
				'LS_Acces',
				'LS_CapaciteGlobale',
				'LS_CapacitePrestation',
				'LS_CapaciteUnites',
				'LS_Carte',
				'LS_Client',
				'LS_Clienteles',
				'LS_Contact',
				'LS_Coordonnees',
				'LS_Denivele',
				'LS_Description',
				'LS_Devises',
				'LS_Disposition',
				'LS_DistanceUnite',
				'LS_DureeUnite',
				'LS_Environnement',
				'LS_Geolocalisation',
				'LS_Itineraires',
				'LS_Jour_lib_jour',
				'LS_Jours',
				'LS_ModeReservation',
				'LS_MoyensCom',
				'LS_Multimedia',
				'LS_OffrePrestation',
				'LS_Periode',
				'LS_Personne',
				'LS_Planning',
				'LS_Point',
				'LS_PrestationLiee',
				'LS_SuperficiesUnites',
				'LS_Tarifs',
				'LS_TypeClassement',
				'LS_Unite',
				'LS_Usage',
				'LS_Zoom',
				'LS_ChampSpecifique'
			);

			$listWithoutAttribute = array(
				'LS_Civilite',
				'LS_Classement',
				'LS_Classification',
				'LS_Communes',
				'LS_ControlledVocabulary',
				'LS_EtatPlanning',
				'LS_Langue',
				'LS_ModeGestion',
				'LS_ModePaiement',
				'LS_MontantPourcent',
				'LS_Pays',
				'LS_Prestation',
				'LS_TarifMode',
				'LS_ThesaurusOrigine',
				'LS_TypeBatiment',
				'LS_ZoneNoms',
				'LS_ChampSpecifiqueValeur'
			);


			if (in_array($listName, $listWithAttribute))
			{
				return(true);
			}
			elseif (in_array($listName, $listWithoutAttribute))
			{
				return(false);
			}
			else
			{
				return(null);
			}
		}
	}
