<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	final class tifTools
	{

		private static $bordereaux = array(
				'ASC' => array(
					'cle' => '02.01.01',
					'libelle' => 'Activités sportives / culturelles / séjour itinérants'
				),
				'DEG' => array(
					'cle' => '02.01.02',
					'libelle' => 'Dégustation'
				),
				'FMA' => array(
					'cle' => '02.01.03',
					'libelle' => 'Fêtes et Manifestations'
				),
				'HLO' => array(
					'cle' => '02.01.04',
					'libelle' => 'Hébergement locatif'
				),
				'HOT' => array(
					'cle' => '02.01.05',
					'libelle' => 'Hôtellerie'
				),
				'HPA' => array(
					'cle' => '02.01.06',
					'libelle' => 'Hôtellerie de plein air'
				),
				'ITI' => array(
					'cle' => '02.01.07',
					'libelle' => 'Itinéraires touristiques'
				),
				'LOI' => array(
					'cle' => '02.01.08',
					'libelle' => 'Loisirs'
				),
				'ORG' => array(
					'cle' => '02.01.10',
					'libelle' => 'Organismes'
				),
				'PCU' => array(
					'cle' => '02.01.11',
					'libelle' => 'Patrimoine culturel'
				),
				'PNA' => array(
					'cle' => '02.01.12',
					'libelle' => 'Patrimoine naturel'
				),
				'RES' => array(
					'cle' => '02.01.13',
					'libelle' => 'Restauration'
				),
				'VIL' => array(
					'cle' => '02.01.14',
					'libelle' => "Hébergement d'accueil collectif"
				),
				'PRD' => array(
					'cle' => '02.01.15',
					'libelle' => 'Produits touristique'
				)
			);



		public static function getBordereau($classification)
		{
			foreach (self::$bordereaux as $name => $infos)
			{
				if($classification == $infos['cle'])
				{
					$bordereau = $name;
					break;
				}
			}

			if(!isset($bordereau))
			{
				throw new Exception("Aucun bordereau pour cette classification");
			}

			return $bordereau;

		}



		public static function getInfosBordereau($bordereau)
		{
			if(!isset(self::$bordereaux[$bordereau]))
			{
				throw new Exception("Ce bordereau n'est pas défini");
			}

			return self::$bordereaux[$bordereau];
		}


		public static function getCodeRegionByCodeInsee($codeInsee)
		{
			$departement = floor(intval($codeInsee) / 1000);
			$codeRegion = '';
			switch($departement)
			{
				case 67: case 68:
					$codeRegion = 'ALS'; break;
				case 33: case 24: case 40: case 47: case 64:
					$codeRegion = 'AQU'; break;
				case 63: case 3: case 15: case 43:
					$codeRegion = 'AUV'; break;
				case 21: case 58: case 71: case 89:
					$codeRegion = 'BOU'; break;
				case 35: case 22: case 29: case 56:
					$codeRegion = 'BRE'; break;
				case 45: case 18: case 28: case 36: case 37: case 41:
					$codeRegion = 'CEN'; break;
				case 51: case 8: case 10: case 52:
					$codeRegion = 'CHA'; break;
				case 0:
					$codeRegion = 'COR'; break;
				case 25: case 39: case 70: case 90:
					$codeRegion = 'FRC'; break;
				case 971:
					$codeRegion = 'GUA'; break;
				case 973:
					$codeRegion = 'GUY'; break;
				case 75: case 77: case 78: case 91: case 92: case 93: case 94: case 95:
					$codeRegion = 'IDF'; break;
				case 34: case 11: case 30: case 48: case 66:
					$codeRegion = 'LAR'; break;
				case 87: case 19: case 23:
					$codeRegion = 'LIM'; break;
				case 54: case 55: case 57: case 88:
					$codeRegion = 'LOR'; break;
				case 972:
					$codeRegion = 'MAR'; break;
				case 31: case 9: case 12: case 32: case 46: case 65: case 81: case 82:
					$codeRegion = 'MIP'; break;
				case 14: case 50: case 61: case 27: case 76:
					$codeRegion = 'NOR'; break;
				case 59: case 62:
					$codeRegion = 'NPC'; break;
				case 13: case 4: case 5: case 6: case 83: case 84:
					$codeRegion = 'PAC'; break;
				case 44: case 49: case 53: case 72: case 85:
					$codeRegion = 'PDL'; break;
				case 17: case 16: case 79: case 86:
					$codeRegion = 'PCH'; break;
				case 80: case 2: case 60:
					$codeRegion = 'PIC'; break;
				case 6:
					$codeRegion = 'RCA'; break;
				case 1: case 7: case 26: case 38: case 42: case 69: case 73: case 74:
					$codeRegion = 'RHA'; break;
				case 974:
					$codeRegion = 'REU'; break;
				/*	case ??
					$codeRegion = 'TAH'; break;*/
				default:
					$codeRegion = 'UNK'; break;
			}
			return($codeRegion);
		}


	}
