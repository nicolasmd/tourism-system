<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pCron
	{

		public function cleanTmpFolder($params)
		{
			header('Content-Type: text/html; charset=utf-8');

			$path = TMP_PATH;
			$dir = opendir($path);
			if ($dir !== false)
			{
				while ($entry = readdir($dir))
				{
					if (is_file($path . $entry))
					{
						preg_match('/^([0-9]+)_/', $entry, $matches);
						if (time() - $matches[1] > (86400 * 7))
						{
							unlink($path . $entry);
						}
					}
				}
				closedir($dir);
			}
		}

	}

?>