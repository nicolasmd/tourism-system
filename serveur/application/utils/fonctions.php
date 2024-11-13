<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	if (function_exists('lcfirst') === false)
	{
		function lcfirst($str)
		{
			return strtolower(substr($str, 0, 1)) . substr($str, 1);
		}
	}


	function storePrepare($arr, $identifiant)
	{
		if (is_array($arr))
		{
			foreach ($arr as $k => $v)
			{
				if (isset($v[$identifiant]))
				{
					unset($arr[$k]);
					$arr[$v[$identifiant]] = $v;
					unset($arr[$v[$identifiant]][$identifiant]);
				}
			}
		}
		return($arr);
	}


	function storeCompare(&$arr1, &$arr2)
	{
		if (is_array($arr1) && is_array($arr2))
		{
			foreach ($arr1 as $key => $item)
			{
				if (isset($arr2[$key]))
				{
					foreach ($item as $k => $v)
					{
						if ($arr2[$key][$k] == $v)
						{
							unset($arr1[$key][$k]);
							unset($arr2[$key][$k]);
						}
					}
					if (empty($arr1[$key]) && empty($arr2[$key]))
					{
						unset($arr1[$key]);
						unset($arr2[$key]);
					}
				}
			}
		}
	}

	function removeDir($dir)
	{
		$d = dir($dir);
		while($entry = $d -> read())
		{
			if ($entry != "." && $entry != '..')
			{
				if(is_dir($dir . '/' . $entry))
				{
					removeDir($dir . '/' . $entry);
				}
				else
				{
					unlink($dir . '/' . $entry);
				}
			}
		}
		$d -> close();
		rmdir($dir);
	}

	function strStartsWith( $haystack, $needles )
	{
		foreach($needles as $needle)
		{
			$length = strlen( $needle );
			if ( substr( $haystack, 0, $length ) === $needle )
			{
				return true;
			}
		}
		return false;
	}

	function strEndsWith( $haystack, $needle )
	{
		$length = strlen( $needle );
		if( $length == 0 )
		{
			return true;
		}
		return ( substr( $haystack, -$length ) === $needle );
	}
	
	function getParamsName($className, $methodName)
	{
		$oClass = new ReflectionClass($className);
		$oMethod = $oClass->getMethod($methodName);
		$parameters = $oMethod->getParameters();
		
		$paramsName = array();
		if (count($parameters) > 1)
		{
			foreach ($parameters as $parameter)
			{
				$paramsName[] = $parameter->name;
			}
		}
		
		return $paramsName;
	}
