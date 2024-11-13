<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */

	class pFicheFichier extends tsProxy
	{

		protected $uploadPath = TMP_PATH;
		protected $uploadUrl = TMP_URL;
		protected $permittedTypes = array();
		protected $permittedImageTypes = array(
			'image/gif',
			'image/jpeg',
			'image/pjpeg',
			'image/png',
			'image/x-png'
		);
		protected $permittedTextTypes = array(
			'text/plain',
			'application/pdf'
			//'application/msword', // .doc
			//'application/vnd.ms-excel' // .xls
		);
		protected $permittedAudioTypes = array(
			'audio/mpeg', // .mp3
			'audio/mp3' // .mp3
		);
		protected $permittedVideoTypes = array(
			'video/quicktime', // .mov
			'video/mp4', // .mp4
			'video/3gpp' // .3gp
		);

		public function __construct()
		{
			$this->permittedTypes = array_merge(
				$this->permittedTypes, $this->permittedImageTypes, $this->permittedTextTypes, $this->permittedAudioTypes, $this->permittedVideoTypes
			);
		}

		public function uploadFile($params)
		{
			header('Content-Type: text/html; charset=utf-8');
			if (!$this->isPermittedType($params['file']['type']))
			{
				throw new Exception("Type de fichier non accepté.");
			}

			if ($params['file']['size'] > MAX_SIZE_FILE)
			{
				throw new Exception("Le poids du fichier doit être inférieur à 5Mo.");
			}

			$res = $this->upload($params['file']);

			if ($res['success'] === false)
			{
				throw new Exception("Une erreur s'est produite durant l'upload du fichier.");
			}
			
			$fileData = array(
				'success' => true,
				'type' => $this->getType($params['file']['type']),
				'name' => $params['file']['name'],
				'url' => $this->uploadUrl . $res['filename']
			);
			
			tsPlugins::registerVar('fileData', $fileData);
			tsPlugins::registerVar('params', $params);
			tsPlugins::registerVar('res', $res);
			tsPlugins::hookProxy('ficheFichier', 'uploadFile');

			echo json_encode($fileData);
		}

		public function uploadImage($params)
		{
			header('Content-Type: text/html; charset=utf-8');

			if (!$this->isPermittedType($params['file']['type'], $this->permittedImageTypes))
			{
				throw new Exception("Ce fichier n'est pas un fichier image.");
			}

			if ($params['file']['size'] > MAX_SIZE_IMAGE)
			{
				throw new Exception("Le poids du fichier doit être inférieur à 2Mo.");
			}

			$res = $this->upload($params['file']);

			if ($res['success'] === false)
			{
				throw new Exception("Une erreur s'est produite durant l'upload du fichier.");
			}

			echo json_encode(array('success' => true, 'name' => $params['file']['name'], 'url' => $this->uploadUrl . $res['filename']));
		}

		public function uploadAudio($params)
		{
			header('Content-Type: text/html; charset=utf-8');

			if (!$this->isPermittedType($params['file']['type'], $this->permittedAudioTypes))
			{
				throw new Exception("Ce fichier n'est pas un fichier audio.");
			}

			if ($params['file']['size'] > MAX_SIZE_AUDIO)
			{
				throw new Exception("Le poids du fichier doit être inférieur à 5Mo.");
			}

			$res = $this->upload($params['file']);

			if ($res['success'] === false)
			{
				throw new Exception("Une erreur s'est produite durant l'upload du fichier.");
			}

			echo json_encode(array('success' => true, 'name' => $params['file']['name'], 'url' => $this->uploadUrl . $res['filename']));
		}

		public function uploadVideo($params)
		{
			header('Content-Type: text/html; charset=utf-8');

			if (!$this->isPermittedType($params['file']['type'], $this->permittedVideoTypes))
			{
				throw new Exception("Ce fichier n'est pas un fichier vidéo.");
			}

			$res = $this->upload($params['file']);

			if ($res === false)
			{
				throw new Exception("Une erreur s'est produite durant l'upload du fichier.");
			}

			echo json_encode(array('success' => true, 'name' => $params['file']['name'], 'url' => $this->uploadUrl . $res['filename']));
		}

		public function resizeImage($params)
		{
			preg_match("/^.+\.(gif|jpe?g|png)$/i", $params['u'], $ext);

			$params['u'] = str_replace(' ', '%20', $params['u']);
			$params['u'] = str_replace('&', '%26', $params['u']);

			switch (strtolower($ext[1]))
			{
				case 'jpg' :
				case 'jpeg' :
					$im = imagecreatefromjpeg($params['u']);
					break;
				case 'gif' :
					$im = imagecreatefromgif($params['u']);
					break;
				case 'png' :
					$im = imagecreatefrompng($params['u']);
					break;
				default:
					$im = false;
					break;
			}

			if (!$im)
			{
				$im = imagecreatefromgif(TS_CLIENT_PATH . 'images/nonsupporte.gif');
			}

			$x = imagesx($im);
			$y = imagesy($im);

			if (($params['w'] / $params['h']) < ($x / $y))
			{
				// Ne pas agrandir
				//$params['w'] = $params['w'] > $x ? $x : $params['w'];
				$save = imagecreatetruecolor($x / ($x / $params['w']), $y / ($x / $params['w']));
			}
			else
			{
				// Ne pas agrandir
				//$params['h'] = $params['h'] > $y ? $y : $params['h'];
				$save = imagecreatetruecolor($x / ($y / $params['h']), $y / ($y / $params['h']));
			}
			imagecopyresized($save, $im, 0, 0, 0, 0, imagesx($save), imagesy($save), $x, $y);

			header("Content-type: image/jpeg");
			imagejpeg($save, null, 100);
		}

		protected function isPermittedType($type, $permittedTypes = null)
		{       
			$permittedTypes = is_null($permittedTypes) ? $this->permittedTypes : $permittedTypes;
			return in_array($type, $permittedTypes);
		}

		protected function getType($mimeType)
		{
			if (in_array($mimeType, $this->permittedImageTypes))
			{
				return '03.01.01';
			}
			if (in_array($mimeType, $this->permittedTextTypes))
			{
				return '03.01.02';
			}
			if (in_array($mimeType, $this->permittedAudioTypes))
			{
				return '03.01.03';
			}
			if (in_array($mimeType, $this->permittedVideoTypes))
			{
				return '03.01.04';
			}

			return 'Texte';
		}

		protected function cleanFilename($filename)
		{
			$parts = explode('.', $filename);
			$extension = array_pop($parts);
			$name = implode('.', $parts);
			return preg_replace('/[^a-zA-Z0-9\_\-]/', '', strtolower(strtr(str_replace(' ', '_', trim($name)), $GLOBALS['normalizeChars']))) . '.' . $extension;
		}

		protected function upload($file)
		{
			$filename = time() . '_' . $this->cleanFilename($file['name']);

			$res = move_uploaded_file($file['tmp_name'], $this->uploadPath . $filename);

			return array('success' => $res, 'filename' => $filename);
		}

	}

?>