<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pFicheExport extends tsProxy
	{

		public function getFicheXml($params)
		{
			$idFicheVersion = (isset($params['idFicheVersion']) && is_numeric($params['idFicheVersion'])) ? $params['idFicheVersion'] : null;

			header('Location: ' . TS_URL_XML . $params['idFiche'] . (!is_null($idFicheVersion) ? '-' . $idFicheVersion : '') . '.xml');
		}

		public function getFichePdf($params)
		{
			$oWsFicheExport = new wsClient('ficheExport');
			$response = $oWsFicheExport->exportFichePdf(PSession::$SESSION['tsSessionId'], $params['idFiche']);

			$oProxyResponse = new proxyResponse();
			$oProxyResponse->setParams($params);
			$oProxyResponse->setSoapResponse($response);

			echo $oProxyResponse->getProxyResponse();
		}

		public function exportXml($params)
		{
			$timestamp = time();
			$pathfile = TMP_PATH . $timestamp . '_exportXml.zip';
			$urlfile = TMP_URL . $timestamp . '_exportXml.zip';

			$zip = new ZipArchive;
			$result = $zip->open($pathfile, ZipArchive::CREATE);

			if ($result === false)
			{
				throw new Exception("Impossible de créer le fichier zip.");
			}

			$idFiches = explode(',', $params['idFiches']);

			if (is_array($idFiches) === false || count($idFiches) == 0)
			{
				throw new Exception("La sélection des fiches est vide.");
			}

			foreach ($idFiches as $idFiche)
			{
				$zip->addFromString($idFiche . '.xml', file_get_contents(TS_URL_XML . $idFiche . '.xml'));
			}

			$zip->close();

			echo json_encode(array('success' => true, 'url' => $urlfile));
		}

		public function exportPdf($params)
		{
			$timestamp = time();
			$pathfile = TMP_PATH . $timestamp . '_exportPdf.zip';
			$urlfile = TMP_URL . $timestamp . '_exportPdf.zip';

			$zip = new ZipArchive;
			$result = $zip->open($pathfile, ZipArchive::CREATE);

			if ($result === false)
			{
				throw new Exception("Impossible de créer le fichier zip.");
			}

			$idFiches = explode(',', $params['idFiches']);

			if (is_array($idFiches) === false || count($idFiches) == 0)
			{
				throw new Exception("La sélection des fiches est vide.");
			}

			$oProxyResponse = new proxyResponse();
			foreach ($idFiches as $idFiche)
			{
				$oWsFicheExport = new wsClient('ficheExport');
				$response = $oWsFicheExport->exportFichePdf(PSession::$SESSION['tsSessionId'], $idFiche);

				$oProxyResponse->setParams($params);
				$oProxyResponse->setSoapResponse($response);

				$zip->addFromString($idFiche . '.pdf', file_get_contents($response['url']));
			}

			$zip->close();

			echo json_encode(array('success' => true, 'url' => $urlfile));
		}

	}

?>