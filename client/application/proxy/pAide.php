<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pAide extends tsProxy
	{

		public function getAide()
		{
			header('Content-type: text/xml;');

			$xmlAide = $this->builXml('../../include/aide/');

			if (is_array(PSession::$SESSION['plugins']))
			{
				foreach (PSession::$SESSION['plugins'] as $plugin)
				{
					$pluginHelpPath = '../../plugins/' . $plugin . '/aide/';
					if (file_exists($pluginHelpPath . 'aide.xml'))
					{
						$xmlTmp = $this->builXml($pluginHelpPath);

						// Ajout des noeuds au xml principal
						$nodes = $xmlTmp->documentElement->childNodes;
						for ($i = 0; $i < $nodes->length; $i++)
						{
							if ($nodes->item($i)->nodeType == XML_ELEMENT_NODE)
							{
								$newnode = $xmlAide->importNode($nodes->item($i), true);
								$xmlAide->documentElement->appendChild($newnode);
							}
						}
					}
				}
			}

			echo $xmlAide->saveXML();
		}

		private function builXml($path)
		{
			$xml = new DOMDocument();
			$xml->load($path . 'aide.xml');

			$oXPath = new DOMXPath($xml);

			// Gestion des droits
			$resChapitre = $oXPath->query('//Chapitre[@tsDroits]');
			for ($i = 0; $i < $resChapitre->length; $i++)
			{
				$node = $resChapitre->item($i);
				$droit = $node->getAttribute('tsDroits');

				if (tsDroits::getDroit($droit) === false)
				{
					$parentNode = $node->parentNode;
					$parentNode->removeChild($node);
				}

				$node->removeAttribute('tsDroits');
			}

			// Gestion des contenus
			$resPage = $oXPath->query('//Page[@content]');
			for ($i = 0; $i < $resPage->length; $i++)
			{
				$node = $resPage->item($i);
				$content = $node->getAttribute('content');

				$filename = $path . $content;
				if (file_exists($filename))
				{
					$node->nodeValue = htmlspecialchars(file_get_contents($filename));
				}

				$node->removeAttribute('content');
			}

			return $xml;
		}

	}

?>