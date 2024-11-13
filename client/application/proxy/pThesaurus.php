<?php

	/**
	 * @version        0.4 alpha-test - 2013-06-03
	 * @package        Tourism System Client
	 * @copyright      Copyright (C) 2010 Raccourci Interactive
	 * @license        GNU GPLv3 ; see LICENSE.txt
	 * @author         Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pThesaurus extends tsProxy
	{

		public function getListeThesaurus($params)
		{
			$pop = isset($params['pop']) ? $params['pop'] : null;
			$cle = isset($params['key']) ? $params['key'] : null;
			$lst = isset($params['ls']) ? $params['ls'] : null;

			$response = $this->getListeFromCache($lst, $cle, $pop);

			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);

			$retour = $oProxyStore->getProxyResponse();

			echo $retour;
		}

		protected function getListeFromCache($liste, $cle = null, $pop = null)
		{
			$key = array('LTH', $liste, (isset($cle) && $cle != '' ? '_' . $cle : ''));

			$response = tsCache::get($key);
			if ($response === false)
			{
				$oWsThesaurus = new wsClient('thesaurus');
				$response = $oWsThesaurus->getListeThesaurus(PSession::$SESSION['tsSessionId'], $liste, $cle, $pop);
				$oProxyResponse = new proxyResponse();
				$oProxyResponse->setSoapResponse($response);
				tsCache::set($key, $response);
			}

			return $response;
		}

		public function getArbreThesaurus($params)
		{
			$pkey = isset($params['key']) ? $params['key'] : null;
			$pop = isset($params['pop']) ? $params['pop'] : null;

			$cacheKey = array('THESO_TREE', $pkey, $pop);
			$retour = tsCache::get($cacheKey);
			if ($retour === false)
			{
				$oWsThesaurus = new wsClient('thesaurus');

				tsPlugins::registerVar('params', $params);
				tsPlugins::hookProxy('thesaurus', 'beforeGetArbreThesaurus');

				$response = $oWsThesaurus->getArbreThesaurus(PSession::$SESSION['tsSessionId'], $pkey, $pop );
				$retour = json_encode($response['arbre']);
				tsCache::set($cacheKey, $retour);
			}
			echo $retour;
		}

		public function getCriteresSearchEngine($params)
		{
			if (isset($params['cle']) && $params['cle'] != 'root')
			{
				$response = $this->getListeFromCache($params['liste'], $params['cle']);

				$xml = '<Items>';
				foreach ($response['liste'] as $entree)
				{
					$xml .= '<Item key="' . $entree['cle'] . '" value="' . htmlspecialchars($entree['libelle']) . '"></Item>';
				}
				$xml .= '</Items>';

				header('Content-type: text/xml');
				echo $xml;
			}
			else
			{
				$xmlpath = '../../include/searchEngine/';
				$bordereau = isset($params['bordereau']) ? $params['bordereau'] : '';
				$xmlfile = file_exists($xmlpath . $bordereau . '.xml') ? $xmlpath . $bordereau . '.xml' : $xmlpath . 'empty.xml';

				header('Content-type: text/xml');
				echo file_get_contents($xmlfile);
			}
		}

		public function getThesaurii($params)
		{
			$key = array(__METHOD__, PSession::$SESSION['tsSessionId'], $params);
			$retour = tsCache::get($key);
			if ($retour === false)
			{
				$oWsThesaurus = new wsClient('thesaurus');
				$response = $oWsThesaurus->getThesaurii(PSession::$SESSION['tsSessionId']);
				$oProxyStore = new proxyStore();
				$oProxyStore->setSoapResponse($response);
				$oProxyStore->setParams($params);
				$oProxyStore->setSearchableFields(array('libelle', 'codeThesaurus'));
				$retour = $oProxyStore->getProxyResponse();
				tsCache::set($key, $retour, 3600);
			}
			echo $retour;
		}

		public function getUserThesaurii($params)
		{
			header("Content-type: text/html; charset=UTF-8;");

			$oWsThesaurus = new wsClient('thesaurus');
			$response = $oWsThesaurus->getUserThesaurii(PSession::$SESSION['tsSessionId']);

			$thesaurii = array();
			foreach ($response['thesaurii'] as $thesaurus)
			{
				$thesaurus->prefixe = empty($thesaurus->prefixe) ? 0 : $thesaurus->prefixe;
				$thesaurii[$thesaurus->prefixe] = $thesaurus->libelle;
			}

			echo "Ext.ts.thesaurii = " . json_encode($thesaurii) . ";";
		}

		public function getEntreesThesaurus($params)
		{
			$key = array(__METHOD__, PSession::$SESSION['tsSessionId'], $params);
			$retour = tsCache::get($key);
			if ($retour === false)
			{
				$oWsThesaurus = new wsClient('thesaurus');
				$codeLangue = !empty($params['codeLangue']) ? $params['codeLangue'] : TS_LANG;
				$response = $oWsThesaurus->getEntreesThesaurus(PSession::$SESSION['tsSessionId'], $params['codeThesaurus'], $codeLangue);
				$oProxyStore = new proxyStore();
				$oProxyStore->setSoapResponse($response);
				$oProxyStore->setParams($params);
				$oProxyStore->setSearchableFields(array('cle', 'libelle', 'liste'));
				$retour = $oProxyStore->getProxyResponse();
				tsCache::set($key, $retour, 3600);
			}
			echo $retour;
		}

		public function getEntreesThesaurii($params)
		{
			$oWsThesaurus = new wsClient('thesaurus');
			$codesThesaurii = explode(',', $params['codesThesaurii']);
			$codeLangue = !empty($params['codeLangue']) ? $params['codeLangue'] : TS_LANG;
			$response = $oWsThesaurus->getEntreesThesaurii(PSession::$SESSION['tsSessionId'], $codesThesaurii, $codeLangue);
			$oProxyStore = new proxyStore();
			$oProxyStore->setSoapResponse($response);
			$oProxyStore->setParams($params);
			$oProxyStore->setSearchableFields(array('cle', 'libelle', 'liste'));
			echo $oProxyStore->getProxyResponse();
		}

		public function editThesaurus($params)
		{
			$oWsThesaurus = new wsClient('thesaurus');
			$response = $oWsThesaurus->updateThesaurus(PSession::$SESSION['tsSessionId'], $params['codeThesaurus'], $params['libelle']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function createThesaurus($params)
		{
			$oWsThesaurus = new wsClient('thesaurus');
			$response = $oWsThesaurus->createThesaurus(PSession::$SESSION['tsSessionId'], $params['codeThesaurus'], $params['libelle']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function addEntreeThesaurus($params)
		{
			$oWsThesaurus = new wsClient('thesaurus');
			$response = $oWsThesaurus->addEntreeThesaurus(PSession::$SESSION['tsSessionId'], $params['codeThesaurus'], $params['cle'], $params['libelle'], $params['codeLangue']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function editEntreeThesaurus($params)
		{
			$oWsThesaurus = new wsClient('thesaurus');
			$response = $oWsThesaurus->setEntreeThesaurus(PSession::$SESSION['tsSessionId'], $params['cle'], $params['codeLangue'], $params['libelle']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function translateEntreeThesaurus($params)
		{
			$oWsThesaurus = new wsClient('thesaurus');
			$response = $oWsThesaurus->translateEntreeThesaurus(PSession::$SESSION['tsSessionId'], $params['cle'], $params['codeLangue'], $params['libelle']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteEntreeThesaurus($params)
		{
			$oWsThesaurus = new wsClient('thesaurus');
			$response = $oWsThesaurus->deleteEntreeThesaurus(PSession::$SESSION['tsSessionId'], $params['cle']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function deleteThesaurus($params)
		{
			$oWsThesaurus = new wsClient('thesaurus');
			$response = $oWsThesaurus->deleteThesaurus(PSession::$SESSION['tsSessionId'], $params['codeThesaurus']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function exportThesaurus($params)
		{
			header('Content-type: text/xml');
			header('Content-Disposition: attachment; filename="' . $params['codeThesaurus'] . '.xml"');

			$oWsThesaurus = new wsClient('thesaurus');
			$codeLangue = !empty($params['codeLangue']) ? $params['codeLangue'] : TS_LANG;
			$response = $oWsThesaurus->getEntreesThesaurus(PSession::$SESSION['tsSessionId'], $params['codeThesaurus'], $codeLangue);

			$entrees = $response['entreesThesaurus'];

			$xml = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
			$xml .= '<Thesaurus name="' . $params['codeThesaurus'] . '">' . PHP_EOL;

			if (is_array($entrees))
			{
				foreach ($entrees as $entree)
				{
					$xml .= '    <Term' . (!empty($entree->liste) ? ' liste="' . $entree->liste . '"' : '' ) . '>' . PHP_EOL;
					$xml .= '        <Code>' . $entree->cle . '</Code>' . PHP_EOL;
					$xml .= '        <Libelle xml:lang="' . $entree->lang . '">' . $entree->libelle . '</Libelle>' . PHP_EOL;
					$xml .= '    </Term>' . PHP_EOL;
				}
			}

			$xml .= '</Thesaurus>' . PHP_EOL;

			echo $xml;
		}

	}

?>
