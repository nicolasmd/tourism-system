<?php

	require_once( './application/utils/stemming/PaiceHuskStemmer/PaiceHuskStemmer.php' );

	class Stem
	{
		private $resultArray = null;
		private $initCount = -1;
		private $initUniqueCount = -1;
		private $finalCount = -1;


		public function __construct()
		{
			$this->resultArray = array();
		}


		public function stem($text , $outputSeparator = null , $lang = 'fr')
		{
			// r�initialiser le r�sultat
			$this->resultArray = null;

			// suppression de tout ce qui n'est pas :alnum:
			$text = preg_replace( '/[^\pL_0-9]/' , ' ' , $text );

			// suppression des accents
			// algo moche mais fonctionnel
			// http://www.developpez.net/forums/d207285/php/langage/regex/remplacement-accents-regex
			$text = htmlentities( $text );
			// remplacer toutes htmlentities par leur second char qui est la lettre concern�e
			$text = preg_replace( '/&(.)(.*?);/' , '$1' , $text );
			// suppression de tous les non alphanum, remplacer par des espaces
			$text = preg_replace( '/\W+/' , ' ' , $text );

			// en minuscules
			$text = strtolower($text);

			// �clater en mots
			$words = explode( " " , $text );

			// lib�ration du texte devenu inutile
			unset($texte);

			// nombre initial de mots uniques
			$this->initCount = count($words);

			// rendre les mots trouv�s uniques
			$words = array_unique($words);

			// nombre initial de mots uniques
			$this->initUniqueCount = count($words);

			// stems
			$res = array();

			foreach( $words as $word )
			{
				// sauter tous les mots de moins de 3 lettres qui ne sont pas des nombres num�riques
				if( strlen( $word ) < 3 && preg_match( '/[1-9]/' , $word ) !== 1 )
				{
					continue;
				}

				// stem
				$r      = PaiceHuskStemmer( $word , $lang);
				$res[] = $r;
				// echo $word . "\t--> " . $r . PHP_EOL;
				// echo $word . ";" . $r . PHP_EOL;
				// echo $r . "\n";
			}

			// faire des r�sultats uniques
			$res = array_unique($res);
			// les trier par ordre croissant
			//sort($res);

			// r�sultat stock� dans l'objet
			$this->resultArray = $res;

			// nombre de stems finaux uniques trouv�s
			$this->finalCount = count($res);

			// sortie soit en tableau soit en texte format�
			if($outputSeparator === null)
			{
				return $this->resultArray;
			}
			else
			{
				return $this->getResultSeparatedBy($outputSeparator);
			}
		}


		// obtenir le r�sultat du pr�c�dent stem()
		public function getResultArray()
		{
			return $this->resultArray;
		}


		// obtenir le r�sultat avec un s�parateur X
		public function getResultSeparatedBy($separator)
		{
			return implode($separator,$this->resultArray);
		}


		// obtenir le nombre initial de mots
		public function getInitCount()
		{
			return $this->initCount;
		}


		// obtenir le nombre initial de mots uniques
		// les mots "insignifiants" sont exclus de ce comptage
		public function getInitUniqueCount()
		{
			return $this->initUniqueCount;
		}


		// obtenir le nombre de stems uniques trouv�s
		public function getFinalCount()
		{
			return $this->finalCount;
		}


		// obtenir le ratio de stemming
		public function getStemRatio()
		{
			return $this->finalCount / $this->initCount;
		}


	}
