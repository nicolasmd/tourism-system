<?php

	/**
	 * @version        0.4 alpha-test - 2013-06-03
	 * @package        Tourism System Client
	 * @copyright      Copyright (C) 2010 Raccourci Interactive
	 * @license        GNU GPLv3 ; see LICENSE.txt
	 * @author         Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pFiche extends tsProxy
	{

		// @todo
		/* private $ListMTH = array(
		  'agrement' => 'cle',
		  'chaine' => 'cle',
		  'label' => 'cle',
		  'handicap' => 'cle',
		  'mode_paiement' => 'cle',
		  'type_etablissement' => 'cle',
		  'asc_activites_culturelles' => 'cle',
		  'asc_activites_sportives' => 'cle',
		  'asc_categorie_activites_sportives' => 'cle',
		  'asc_formules_itinerantes' => 'cle',
		  'deg_produits' => 'cle',
		  'deg_statut_exploitant' => 'cle',
		  'fma_categories' => 'cle',
		  'fma_evenements' => 'cle',
		  'fma_themes' => 'cle',
		  'fma_types' => 'cle',
		  'org_organismes_tourisme_institutionnels' => 'cle',
		  'org_organismes_receptifs' => 'cle',
		  'org_services_informations_touristiques_prives' => 'cle',
		  'org_types_entreprise' => 'cle',
		  'org_types_structures' => 'cle',
		  'pcu_categories_musees' => 'cle',
		  'pcu_categories_parcs_jardins' => 'cle',
		  'pcu_categories_sites_monuments' => 'cle',
		  'pcu_equipements' => 'cle',
		  'pcu_styles_parcs_jardins' => 'cle',
		  'pcu_styles_sites_monuments' => 'cle',
		  'pcu_themes_musees' => 'cle',
		  'pcu_themes_parcs_jardins' => 'cle',
		  'pcu_themes_sites_monuments' => 'cle',
		  'pcu_types_centres_interpretation' => 'cle',
		  'res_categories' => 'cle',
		  'res_types' => 'cle',
		  'res_types_cuisine' => 'cle',
		  'patrimoine' => 'cle',
		  'accessibilite' => 'cle',
		  'langues_parlees_accueil' => 'cle'
		  ); */

		private static $fieldsToDuplicate = array(
			'gps_lat',
			'gps_lng',
			'points_acces',
			'environnement',
			'description_commerciale_fr',
			'description_commerciale_en',
			'description_commerciale_de',
			'description_commerciale_es',
			'description_commerciale_nl',
			'description_commerciale_it',
			'slogan_fr',
			'slogan_en',
			'slogan_de',
			'slogan_es',
			'slogan_nl',
			'slogan_it',
			'langues_parlees_accueil',
			'ape_naf',
			'siret',
			'rcs',
			'ouverture',
			'handicap',
			'activite',
			'confort',
			'equipement',
			'service',
			'cs_contrat'
		);

		public function getFiches($params)
		{
                        $parameters = array();
                    
                        $parameters['bordereau'] = (isset($params['bordereau'])) ? $params['bordereau'] : null;
                        $parameters['filters'] = (isset($params['filters']) && is_array($params['filters'])) ? $params['filters'] : array();
			$parameters['filtreCommune'] = isset($params['communes']) ? $params['communes'] : null;
			                        
			$parameters['start'] = (isset($params['start'])) ? intval($params['start']) : 0;
			$parameters['limit'] = (isset($params['limit'])) ? intval($params['limit']) : 50;
			$parameters['query'] = (isset($params['query'])) ? $params['query'] : null;
			$parameters['queryField'] = (isset($params['queryField'])) ? $params['queryField'] : null;
			$parameters['gridfilters'] = (isset($params['gridfilters'])) ? json_decode($params['gridfilters']) : null;
			$parameters['sort'] = (isset($params['sort'])) ? $params['sort'] : 'raisonSociale';
			$parameters['dir'] = (isset($params['dir'])) ? $params['dir'] : 'ASC';
                        $parameters['searchableField'] = array('idFiche', 'codeTIF', 'referenceExterne', 'raisonSociale');
                        
                        $parameters['filtersType'] = false;
                        if(sizeof($parameters['filters']) > 0 && strpos($parameters['filters'][0],'or'))
                        {
                            $parameters['filtersType'] = 'or';
                            $parameters['filters'] = explode('or',$parameters['filters'][0]);
                        }
                        if(sizeof($parameters['filters']) > 0 && strpos($parameters['filters'][0],'and'))
                        {
                            $parameters['filtersType'] = 'and';
                            $parameters['filters'] = explode('and',$parameters['filters'][0]);
                        }
                        
                        $oWsFiche = new wsClient('fiche');
                        $response = $oWsFiche->listFiches(PSession::$SESSION['tsSessionId'], $parameters);
                        echo json_encode($response['fiches']);
                        
                        
                        
    
		}

		public function getFichesForMap($params)
		{
			echo json_encode(PSession::$SESSION['fichesForMap']);
			//			return json_encode($_SESSION['fichesForMap']);
		}

		public function createFiche($params)
		{
			$oWsFiche = new wsClient('fiche');
			$response = $oWsFiche->createFiche(PSession::$SESSION['tsSessionId'], $params['bordereau'], $params['codeInsee']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteFiches($params)
		{
			$idFiches = explode(',', $params['idFiches']);

			foreach ($idFiches as $idFiche)
			{
				$oWsFiche = new wsClient('fiche');
				$response = $oWsFiche->deleteFiche(PSession::$SESSION['tsSessionId'], $idFiche);
			}

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function duplicateFiche($params)
		{
			$oWsFiche = new wsClient('fiche');

			$fiche = $oWsFiche->createFiche(PSession::$SESSION['tsSessionId'], $params['bordereau'], $params['codeInsee']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($fiche);

			$oldFiche = $oWsFiche->getFiche(PSession::$SESSION['tsSessionId'], $params['idFiche']);
			$newFiche = $oWsFiche->getFiche(PSession::$SESSION['tsSessionId'], $fiche['idFiche']);

			$oFiche = array();

			$newCommune = false;
			$oldContacts = $oldFiche['fiche']->editable['contact'];
			$newContacts = array();
			foreach ($oldContacts as $contact)
			{
				if ($contact['type_contact'] == "04.03.13")
				{
					if ($contact['code_insee'] != $params['codeInsee'])
					{
						$newCommune = true;
						$contact['adresse1'] = '';
						$contact['adresse2'] = '';
						$contact['adresse3'] = '';
					}
					$contact['commune'] = $newFiche['fiche']->editable['commune'];
					$contact['code_insee'] = $newFiche['fiche']->editable['code_insee'];
					$contact['code_postal'] = $newFiche['fiche']->editable['code_postal'];
					$contact['raison_sociale'] = $params['raisonSociale'];
				}

				$newContacts[] = $contact;
			}

			$oFiche['contact'] = $newContacts;

			foreach (self::$fieldsToDuplicate as $field)
			{
				if ($newCommune && in_array($field, array('environnement', 'points_acces', 'gps_lat', 'gps_lng')))
				{
					continue;
				}

				if (isset($oldFiche['fiche']->editable[$field]))
				{
					$oFiche[$field] = $oldFiche['fiche']->editable[$field];
				}
			}

			$oWsFiche->sauvegardeFiche(PSession::$SESSION['tsSessionId'], $fiche['idFiche'], $oFiche);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteFiche($params)
		{
			$oWsFiche = new wsClient('fiche');
			$response = $oWsFiche->deleteFiche(PSession::$SESSION['tsSessionId'], $params['idFiche']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function setPublicationFiches($params)
		{
			$idFiches = explode(',', $params['idFiches']);

			foreach ($idFiches as $idFiche)
			{
				$oWsFiche = new wsClient('fiche');
				$response = $oWsFiche->setPublicationFiche(PSession::$SESSION['tsSessionId'], $idFiche, $params['publication'] == 'true');
			}

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function setPublicationFiche($params)
		{
			// Cache désactivé
			//$key = $this -> getFicheKeyMemcache($params['idFiche']);
			//$this -> deleteFicheMemcache($key);

			$oWsFiche = new wsClient('fiche');
			$response = $oWsFiche->setPublicationFiche(PSession::$SESSION['tsSessionId'], $params['idFiche'], $params['publication'] == 'true');

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function getListeFiches($params)
		{
			$idFiches = explode(',', $params['idFiches']);

			$oWsFiche = new wsClient('fiche');

			$fiches = array();
			foreach ($idFiches as $idFiche)
			{
				$response = $oWsFiche->getFiche(PSession::$SESSION['tsSessionId'], $idFiche);
				$fiches[] = array(
					'idFiche' => $idFiche,
					'raisonSociale' => $response['fiche']->raisonSociale,
					'adresse1' => $response['fiche']->editable['adresse1'],
					'codePostal' => $response['fiche']->editable['code_postal'],
					'commune' => $response['fiche']->editable['commune'],
					'telephone1' => $response['fiche']->editable['telephone1'],
					'email' => $response['fiche']->editable['email'],
					'photo' => $response['fiche']->editable['photos_fichiers'][0]['url_fichier']
				);
			}

			$oProxyStore = new proxyStore();
			$oProxyStore->setData($fiches);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array());

			echo $oProxyStore->getProxyResponse();
		}

		public function getFicheVersions($params)
		{
			$oWsFiche = new wsClient('fiche');
			$response = $oWsFiche->getFicheVersions(PSession::$SESSION['tsSessionId'], $params['idFiche']);

			$oWsUtilisateur = new wsClient('utilisateur');

			$utilisateursTmp = array();
			foreach ($response['versions'] as &$version)
			{
				if (is_null($version->idUtilisateur))
				{
					continue;
				}

				if (!isset($utilisateursTmp[$version->idUtilisateur]))
				{
					$responseUser = $oWsUtilisateur->getUtilisateur(SESSION_ID_ROOT, $version->idUtilisateur);
					$utilisateursTmp[$version->idUtilisateur] = $responseUser['utilisateur'];
				}

				$version->email = $utilisateursTmp[$version->idUtilisateur]->email;
			}

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);

			echo $oProxyStore->getProxyResponse();
		}

		public function restoreFicheVersion($params)
		{
			// Cache désactivé
			//$key = $this -> getFicheKeyMemcache($params['idFiche']);
			//$this -> deleteFicheMemcache($key);

			$oWsFiche = new wsClient('fiche');
			$response = $oWsFiche->restoreFicheVersion(PSession::$SESSION['tsSessionId'], $params['idFiche'], $params['idFicheVersion']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteFicheVersion($params)
		{
			// Cache désactivé
			//$key = $this -> getFicheKeyMemcache($params['idFiche']);
			//$this -> deleteFicheMemcache($key);

			$oWsFiche = new wsClient('fiche');
			$response = $oWsFiche->deleteFicheVersion(PSession::$SESSION['tsSessionId'], $params['idFiche'], $params['idFicheVersion']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		//		public function getFiche($params)
		//		{
		//			$response = tsCacheAPC::get($params);
		//			if( $response === false )
		//			{
		//				$response = self::getFiche2($params);
		//				// $response = $oWsDiffusion->getPlaylistFiches($token, $params[ 'identifiant' ], true);
		//				tsCacheAPC::set($params, $response);
		//			}
		//			echo $response;
		//		}
		// @todo
		// Revoir la gestion des champs
		public function getFiche($params)
		{
			if (isset($params['idFiche']) === false || is_numeric($params['idFiche']) === false)
			{
				throw new Exception('Impossible de charger la fiche.');
			}

			// Hook beforeGetFiche
			tsPlugins::registerVar('params', $params);
			tsPlugins::hookProxy('fiche', 'beforeGetFiche');

			$idFicheVersion = ( isset($params['idFicheVersion']) && is_numeric($params['idFicheVersion']) ) ? $params['idFicheVersion'] : null;

			// Cache désactivé
			//$key = $this -> getFicheKeyMemcache($params['idFiche'], $idFicheVersion);
			//$oFiche = $this -> getFicheMemcache($key);

			$key = array($params['idFiche'], $idFicheVersion);
			$retour = tsCache::get($key);
			//			$retour = false;
			if ($retour === false || true)
			{
				$oWsFiche = new wsClient('fiche');
				$response = $oWsFiche->getFiche(PSession::$SESSION['tsSessionId'], $params['idFiche'], $idFicheVersion);

				if (isset($response['status']) === false || $response['status']->success === false)
				{
					throw new Exception($response['status']->message);
				}

				$oFiche = $response['fiche'];

				// Commune
				require_once( '../../ressources/communes/communes.php' );
				$oFiche->libelleCommune = $communes[$oFiche->codeInsee]['libelle'];
				$oFiche->codePostal = $communes[$oFiche->codeInsee]['codePostal'];

				// Description commerciale
				/*if (isset($oFiche->editable['description_commerciale_fr']))
				{
					$description_commerciale = array();
					foreach ($oFiche->editable as $k => $v)
					{
						if (strpos($k, 'description_commerciale') !== false)
						{
							$arr = array_reverse(explode('_', $k));
							$description_commerciale[$arr[0]] = $v;
							unset($oFiche -> editable[$k]);
						}
					}
					$oFiche->editable['description_commerciale'] = $description_commerciale;
				}
				elseif (isset($oFiche->readable['description_commerciale_fr']))
				{
					$description_commerciale = array();
					foreach ($oFiche->readable as $k => $v)
					{
						if (strpos($k, 'description_commerciale') !== false)
						{
							$arr = array_reverse(explode('_', $k));
							$description_commerciale[$arr[0]] = $v;
							unset($oFiche -> readable[$k]);
						}
					}
					$oFiche->readable['description_commerciale'] = $description_commerciale;
				}*/

				// Slogan
				/*if (isset($oFiche->editable['slogan_fr']))
				{
					$slogan = array();
					foreach ($oFiche->editable as $k => $v)
					{
						if (strpos($k, 'slogan') !== false)
						{
							$arr = array_reverse(explode('_', $k));
							$slogan[$arr[0]] = $v;
							unset($oFiche->editable[$k]);
						}
					}
					$oFiche->editable['slogan'] = $slogan;
				}
				elseif (isset($oFiche->readable['slogan_fr']))
				{
					$slogan = array();
					foreach ($oFiche->readable as $k => $v)
					{
						if (strpos($k, 'slogan') !== false)
						{
							$arr = array_reverse(explode('_', $k));
							$slogan[$arr[0]] = $v;
							unset($oFiche->readable[$k]);
						}
					}
					$oFiche->readable['slogan'] = $slogan;
				}*/

				// Langue
				/* $champs = array(
				  '11.01.01' => 'langues_parlees_accueil',
				  '11.01.06' => 'langues_parlees_documentation',
				  '11.01.07' => 'langues_parlees_panneau',
				  '11.01.09' => 'langues_parlees_visite',
				  '11.01.10' => 'langues_parlees_reservation'
				  );

				  foreach ($champs as $cle => $nomChamp) {
				  if (isset($oFiche -> editable[$nomChamp])) {
				  if (!isset($langues_parlees)) {
				  $langues_parlees = array();
				  }
				  foreach ($oFiche -> editable[$nomChamp] as $value) {
				  if (!isset($langues_parlees[$value['langue']])) {
				  $langues_parlees[$value['langue']] = array(
				  'langue' => $value['langue'],
				  'usage' => array()
				  );
				  }
				  $langues_parlees[$value['langue']]['usage'][] = $cle;
				  }
				  //unset($oFiche -> editable[$nomChamp]);
				  }
				  }
				  if (isset($langues_parlees)) {
				  $oFiche -> editable['langues_parlees'] = array();
				  foreach ($langues_parlees as $langue_parlees) {
				  $oFiche -> editable['langues_parlees'][] = $langue_parlees;
				  }
				  } */

				// Contact
				/*if (isset($oFiche->editable['contact']))
				{
					if (is_array($oFiche->editable['contact']))
					{
						foreach ($oFiche->editable['contact'] as $k => $v)
						{
							if ($v['type_contact'] == '04.03.13')
							{
								unset($oFiche->editable['contact'][$k]);
							}
						}
						sort($oFiche->editable['contact']);
					}
				}*/

				// Coordonnées GPS
				if (isset($oFiche->editable['gps_lat'])
					&& isset($oFiche->editable['gps_lng'])
				)
				{
					$oFiche->editable['coordonnees_gps'] = array(
						'gpsLat' => $oFiche->editable['gps_lat'],
						'gpsLng' => $oFiche->editable['gps_lng']
					);
					unset($oFiche->editable['gps_lat']);
					unset($oFiche->editable['gps_lng']);
				}
				elseif (isset($oFiche->readable['gps_lat'])
					&& isset($oFiche->readable['gps_lng'])
				)
				{
					$oFiche->readable['coordonnees_gps'] = array(
						'gpsLat' => $oFiche->readable['gps_lat'],
						'gpsLng' => $oFiche->readable['gps_lng']
					);
					unset($oFiche->readable['gps_lat']);
					unset($oFiche->readable['gps_lng']);
				}

				// Disponibilités
				if (isset($oFiche->editable['disponibilites']))
				{
					/*foreach ($oFiche->editable['disponibilites'] as &$disponibilite)
					{
						$disponibilite['year'] = date('Y', strtotime($disponibilite['datedebut']));
						unset($disponibilite['datedebut']);
						unset($disponibilite['datefin']);
					}
					usort($oFiche->editable['disponibilites'], array($this, 'sortDispos'));*/

					$oFiche->editable['disponibilites'] = array(
						'dispos' => $oFiche->editable['disponibilites'],
						'datemaj' => $oFiche->editable['disponibilites_datemaj']
					);
				}

				// Tarifs
				if (isset($oFiche->editable['tarif']) && is_array($oFiche->editable['tarif']))
				{
					foreach ($oFiche->editable['tarif'] as $k => $tarif)
					{
						$description = array();
						foreach ($tarif as $att => $value)
						{
							if (strpos($att, 'description_') === 0)
							{
								$description[str_replace('description_', '', $att)] = $value;
								unset($oFiche->editable['tarif'][$k][$att]);
							}
						}
						$oFiche->editable['tarif'][$k]['description'] = $description;
					}
				}

				// Photos
				/*if (isset($oFiche->editable['photos_fichiers']))
				{
					$photos_fichiers = array();
					foreach ($oFiche->editable['photos_fichiers'] as $fichier)
					{
						$fichier['url_fichier'] = str_replace(' ', '%20', $fichier['url_fichier']);
						$fichier['url_fichier'] = str_replace('&', '%26', $fichier['url_fichier']);
						//if (@file_get_contents($fichier['url_fichier']) !== false)
						//{
						$photos_fichiers[] = $fichier;
						//}
					}
					$oFiche->editable['photos_fichiers'] = $photos_fichiers;
				}*/

				$champsSpecifiques = array();

				foreach ($oFiche->editable as $k => $v)
				{
					// ListMTH
					/* if (array_key_exists($k, $this -> ListMTH))
					  {
					  $newValue = array();
					  if (is_array($oFiche -> editable[$k]))
					  {
					  foreach ($oFiche -> editable[$k] as $item)
					  {
					  $newValue[] = $item[$this -> ListMTH[$k]];
					  }
					  }
					  $oFiche -> editable[$k] = $newValue;
					  } */

					// Champs Spécifiques
					if (strpos($k, 'cs_') === 0)
					{
						$oWsChamp = new wsClient('champ');
						$champ = $oWsChamp->getChampByIdentifiant(PSession::$SESSION['tsSessionId'], $k);

						if (strpos($champ['champ']->xPath, 'tif:ChampSpecifiqueTexteArea') !== false)
						{
							$type = 'textarea';
						}
						elseif (strpos($champ['champ']->xPath, 'tif:ChampSpecifiqueTexte') !== false)
						{
							$type = 'text';
						}
						elseif (strpos($champ['champ']->xPath, 'tif:ChampSpecifiqueSelect') !== false)
						{
							$type = 'select';
							$liste = $champ['champ']->liste;
							preg_match('/type="([^"]+)"/', $champ['champ']->xPath, $cle);
						}
						elseif (strpos($champ['champ']->xPath, 'tif:ChampSpecifiqueMultiple') !== false)
						{
							$type = 'multiple';
							$liste = $champ['champ']->liste;
							preg_match('/type="([^"]+)"/', $champ['champ']->xPath, $cle);
							$value = array();
							if (is_array($v))
							{
								foreach ($v as $item)
								{
									$value[] = $item['cle'];
								}
							}
							$v = $value;
						}

						$champsSpecifiques[] = array(
							'type' => $type,
							'name' => $k,
							'libelle' => $champ['champ']->libelle,
							'list' => isset($liste) ? $liste : null,
							'key' => isset($cle) ? $cle[1] : null,
							'value' => $v,
							'disabled' => false
						);
						//unset($oFiche -> editable[$k]);
					}
				}

				foreach ($oFiche->readable as $k => $v)
				{
					// ListMTH
					/* if (array_key_exists($k, $this -> ListMTH))
					  {
					  $newValue = array();
					  if (is_array($oFiche -> editable[$k]))
					  {
					  foreach ($oFiche -> editable[$k] as $item)
					  {
					  $newValue[] = $item[$this -> ListMTH[$k]];
					  }
					  }
					  $oFiche -> editable[$k] = $newValue;
					  } */

					// Champs Spécifiques
					if (strpos($k, 'cs_') === 0)
					{
						$oWsChamp = new wsClient('champ');
						$champ = $oWsChamp->getChampByIdentifiant(PSession::$SESSION['tsSessionId'], $k);

						if (strpos($champ['champ']->xPath, 'tif:ChampSpecifiqueTexteArea') !== false)
						{
							$type = 'textarea';
						}
						elseif (strpos($champ['champ']->xPath, 'tif:ChampSpecifiqueTexte') !== false)
						{
							$type = 'text';
						}
						elseif (strpos($champ['champ']->xPath, 'tif:ChampSpecifiqueSelect') !== false)
						{
							$type = 'select';
							$liste = $champ['champ']->liste;
							preg_match('/type="([^"]+)"/', $champ['champ']->xPath, $cle);
						}
						elseif (strpos($champ['champ']->xPath, 'tif:ChampSpecifiqueMultiple') !== false)
						{
							$type = 'multiple';
							$liste = $champ['champ']->liste;
							preg_match('/type="([^"]+)"/', $champ['champ']->xPath, $cle);
							$value = array();
							if (is_array($v))
							{
								foreach ($v as $item)
								{
									$value[] = $item['cle'];
								}
							}
							$v = $value;
						}

						$champsSpecifiques[] = array(
							'type' => $type,
							'name' => $k,
							'libelle' => $champ['champ']->libelle,
							'list' => isset($liste) ? $liste : null,
							'key' => isset($cle) ? $cle[1] : null,
							'value' => $v,
							'disabled' => true
						);
						//unset($oFiche -> editable[$k]);
					}
				}
				
				if (count($champsSpecifiques) > 0)
				{
					$oFiche->editable['champs_specifiques'] = $champsSpecifiques;
				}

				// Hook getFiche
				tsPlugins::registerVar('params', $params);
				tsPlugins::registerVar('oFiche', $oFiche);
				tsPlugins::hookProxy('fiche', 'getFiche');

				$retour = json_encode(array('objetFiche' => $oFiche));

				//$this -> setFicheMemcache($key, $oFiche);
				//				tsCache::set( $key , $retour );
			}

			echo $retour;
			//			return json_encode(array('objetFiche' => $oFiche));
		}

		private function sortDispos($a, $b)
		{
			return $a['year'] < $b['year'] ? -1 : 1;
		}

		public function sauvegardeFiche($params)
		{
			// Cache désactivé
			//$key = $this -> getFicheKeyMemcache($params['idFiche']);
			//$this -> deleteFicheMemcache($key);
			
			// Fiche avant la sauvegarde
			$oWsFiche = new wsClient('fiche');
			$responseF = $oWsFiche->getFiche(PSession::$SESSION['tsSessionId'], $params['idFiche']);
			$oFiche = $responseF['fiche'];

			// Validation
			// Les champs "à valider" ont été postés avec la valeur à valider
			// => on le prend pas en compte
			if (isset($params['champsAValider']))
			{
				$champsAValider = json_decode($params['champsAValider'], true);
				if (is_array($champsAValider))
				{
					foreach ($champsAValider as $champ)
					{
						unset($params[$champ]);
					}
				}
			}
			$champsValide = isset($params['champsValide']) ? json_decode($params['champsValide'], true) : null;
			$champsRefuse = isset($params['champsRefuse']) ? json_decode($params['champsRefuse'], true) : null;

			// Contact
			/*if (isset($params['contact']))
			{
				$params['contact'] = json_decode($params['contact'], true);

				if (isset($params['raison_sociale']))
				{
					$params['contact'][] = array(
						'type_contact' => '04.03.13',
						'raison_sociale' => $params['raison_sociale'],
						'code_postal' => $params['code_postal'],
						'code_insee' => $params['code_insee'],
						'commune' => $params['commune'],
						'adresse1' => $params['adresse1'],
						'adresse2' => $params['adresse2'],
						'adresse3' => $params['adresse3'],
						'cedex' => $params['cedex'],
						'telephone1' => $params['telephone1'],
						'telephone2' => $params['telephone2'],
						'fax' => $params['fax'],
						'site_web' => $params['site_web'],
						'email' => $params['email']
					);
				}
				else
				{
					foreach ($oFiche->editable['contact'] as $contact)
					{
						if ($contact['type_contact'] == '04.03.13')
						{
							$params['contact'][] = $contact;
							break;
						}
					}
				}
				$params['contact'] = json_encode($params['contact']);
			}*/

			// Disponibilités
			/*if (isset($params['disponibilites']))
			{
				$produitsBordereau = array(
					'HLO' => '15.07.*',
					'HPA' => '15.08.*',
					'HOT' => '15.09.*',
					'VIL' => '15.10.*',
					'ORG' => '15.11.*'
				);

				$modeProduit = false;

				if (isset($produitsBordereau[$oFiche->bordereau]))
				{
					$oWsThesaurus = new wsClient('thesaurus');
					$response = $oWsThesaurus->getListeThesaurus(PSession::$SESSION['tsSessionId'], 'LS_Prestation', $produitsBordereau[$oFiche->bordereau]);
					$produits = array();
					foreach ($response['liste'] as $entree)
					{
						$produits[] = $entree['cle'];
					}

					$modeProduit = count($produits) > 0;
				}

				$params['disponibilites'] = json_decode($params['disponibilites'], true);

				$years = array();
				$ouvertures = array();
				foreach (json_decode($params['ouverture'], true) as $ouverture)
				{
					$years[] = date('Y', strtotime($ouverture['datedebut']));
					$years[] = date('Y', strtotime($ouverture['datefin']));
					$ouvertures[] = array(
						'debut' => strtotime($ouverture['datedebut']),
						'fin' => strtotime($ouverture['datefin'])
					);
				}
				$years = array_unique($years);
				sort($years);

				$oldDispos = array();
				$oldInclus = array();
				foreach ($params['disponibilites'] as $disponibilite)
				{
					if ($modeProduit)
					{
						$oldDispos[$disponibilite['year']][$disponibilite['type_produit']] = str_split(str_replace('I', 'D', $disponibilite['disponibilite']));
						// Fix : l'activation ne s'appliquait pas sur toutes les années, si une année au moins activée on active les autres.
						if ($oldInclus[$disponibilite['type_produit']] != 'Y')
						{
							$oldInclus[$disponibilite['type_produit']] = $disponibilite['inclus'];
						}
					}
					else
					{
						$oldDispos[$disponibilite['year']] = str_split(str_replace('I', 'D', $disponibilite['disponibilite']));
					}
				}

				$newDispos = array();
				if (count($years) > 0)
				{
					for ($year = $years[0]; $year <= $years[count($years) - 1]; $year++)
					{
						if ($modeProduit)
						{
							foreach ($produits as $produit)
							{
								$arrDispo = array_fill(0, 365 + date('L', strtotime("$year-01-01")), 'D');

								foreach ($arrDispo as $i => $dispo)
								{
									$timestamp = strtotime("+$i day", strtotime("$year-01-01"));

									$indispo = true;
									foreach ($ouvertures as $ouverture)
									{
										if ($timestamp >= $ouverture['debut'] && $timestamp <= $ouverture['fin'])
										{
											$indispo = false;
											break;
										}
									}

									if ($indispo)
									{
										$arrDispo[$i] = 'I';
									}
									elseif (isset($oldDispos[$year][$produit][$i]))
									{
										$arrDispo[$i] = $oldDispos[$year][$produit][$i];
									}
								}

								$inclus = isset($oldInclus[$produit]) ? $oldInclus[$produit] : 'N';

								$newDispos[] = array(
									'type_produit' => $produit,
									'year' => $year,
									'datedebut' => $year . '-01-01',
									'datefin' => $year . '-12-31',
									'disponibilite' => implode('', $arrDispo),
									'inclus' => $inclus
								);
							}
						}
						else
						{
							$arrDispo = array_fill(0, 365 + date('L', strtotime("$year-01-01")), 'D');

							foreach ($arrDispo as $i => $dispo)
							{
								$timestamp = strtotime("+$i day", strtotime("$year-01-01"));

								$indispo = true;
								foreach ($ouvertures as $ouverture)
								{
									if ($timestamp >= $ouverture['debut'] && $timestamp <= $ouverture['fin'])
									{
										$indispo = false;
										break;
									}
								}

								if ($indispo)
								{
									$arrDispo[$i] = 'I';
								}
								elseif (isset($oldDispos[$year][$i]))
								{
									$arrDispo[$i] = $oldDispos[$year][$i];
								}
							}

							$newDispos[] = array(
								'year' => $year,
								'datedebut' => $year . '-01-01',
								'datefin' => $year . '-12-31',
								'disponibilite' => implode('', $arrDispo)
							);
						}
					}
				}

				$params['disponibilites'] = json_encode($newDispos);

				if (empty($params['disponibilites_datemaj']))
				{
					$params['disponibilites_datemaj'] = date('Y-m-d H:i:s');
				}
			}*/

			// Tarifs
			if (isset($params['tarif']))
			{
				$params['tarif'] = json_decode($params['tarif'], true);
				foreach ($params['tarif'] as $k => $tarif)
				{
					if (isset($tarif['description']) && is_array($tarif['description']))
					{
						foreach ($tarif['description'] as $lang => $description)
						{
							$params['tarif'][$k]['description_' . $lang] = $description;
						}
						unset($params['tarif'][$k]['description']);
					}
				}
				$params['tarif'] = json_encode($params['tarif']);
			}

			// Photos
			if (isset($params['photos_fichiers']))
			{
				$params['photos_fichiers'] = json_decode($params['photos_fichiers'], true);

				// Fichiers à sauvegarder
				$urlClt = array();
				foreach ($params['photos_fichiers'] as $fichier)
				{
					$urlClt[] = $fichier['url_fichier'];
				}

				// Fichiers existants
				$oWsFicheFichier = new wsClient('ficheFichier');
				$response = $oWsFicheFichier->getFicheFichiers(PSession::$SESSION['tsSessionId'], $params['idFiche']);
				foreach ($response['fichiers'] as $fichier)
				{
					if (in_array($fichier->url, $urlClt) === false)
					{
						$oWsFicheFichier->deleteFicheFichier(PSession::$SESSION['tsSessionId'], $fichier->idFichier);
					}
				}

				foreach ($params['photos_fichiers'] as $k => $fichier)
				{
					$arrUrl = array_reverse(explode('/', $fichier['url_fichier']));

					if ($fichier['url_fichier'] == TMP_URL . $arrUrl[0])
					{
						$response = $oWsFicheFichier->addFicheFichier(PSession::$SESSION['tsSessionId'], $params['idFiche'], $fichier['nom_fichier'], false, $fichier['url_fichier']);

						if ($response['status']->success === true)
						{
							$idFichier = $response['idFichier'];
							$oFichier = $oWsFicheFichier->getFicheFichier(PSession::$SESSION['tsSessionId'], $idFichier);

							// Remplacement de l'url temporaire par celle du serveur
							$params['photos_fichiers'][$k]['url_fichier'] = $oFichier['fichier']->url;

							// Suppression du fichier dans le répertoire temporaire
							unlink(TMP_PATH . $arrUrl[0]);
						}
						else
						{
							unset($params['photos_fichiers'][$k]);
						}
					}
				}
				$params['photos_fichiers'] = array_values($params['photos_fichiers']);

				$params['photos_fichiers'] = json_encode($params['photos_fichiers']);
			}

			// Hook sauvegardeFiche
			tsPlugins::registerVar('oFiche', $oFiche);
			tsPlugins::registerVar('params', $params);
			tsPlugins::hookProxy('fiche', 'sauvegardeFiche');

			$oWsFiche = new wsClient('fiche');
			$response = $oWsFiche->sauvegardeFiche(PSession::$SESSION['tsSessionId'], $params['idFiche'], $params, $champsValide, $champsRefuse);
			
			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);
			echo $oProxyResponse->getProxyResponse();
		}

		//		private function getFicheKeyMemcache($idFiche, $idFicheVersion = null)
		//		{
		//			return 'fiche_' . $idFiche . (!is_null($idFicheVersion) ? '_' . $idFicheVersion : '');
		//		}
		//		private function getFicheMemcache($key)
		//		{
		//			$response = tsCache::get($key);
		//
		//			$value = false;
		//			if (is_array($response))
		//			{
		//				$value = isset($response[$_SESSION['idUtilisateur']]) ? $response[$_SESSION['idUtilisateur']] : false;
		//			}
		//
		//			return $value;
		//		}
		//		private function setFicheMemcache($key, $value)
		//		{
		//			$response = tsCache::get($key);
		//
		//			$values = is_array($response) ? $response : array();
		//			$values[$_SESSION['idUtilisateur']] = $value;
		//
		//			return tsCache::set($key, $values, 86400);
		//		}
		//		private function deleteFicheMemcache($key)
		//		{
		//			// Delete fonctionne pas ?
		//			return tsCache::set($key, false);
		//		}
	}

?>
