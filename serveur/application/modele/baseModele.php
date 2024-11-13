<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	abstract class baseModele
	{

		//private $setted = array();

		final public function __get($name)
		{
			/*if (array_key_exists($name, $this -> setted) === false)
			{
				throw new ApplicationException("La propriété $name n'est pas définie");
			}*/
			return $this -> $name;
		}



		public function __call($method, $args)
		{
			$paramName = lcfirst(substr($method, 3));

			if (substr($method, 0, 3) == "set")
			{
				$this -> $paramName = $args[0];
				//$this -> setted[$name] = true;
				return true;
			}
			elseif (substr($method, 0, 3) == "get")
			{
				return $this -> __get($paramName);
			}
			else
			{
				throw new ApplicationException("La méthode demandée n'existe pas");
			}
		}



		public function __toString()
		{
			$str = '<h3>' . get_class($this) . '</h3>';
			foreach($this as $varName => $ok)
			{
				$str .= '<h5>' . $varName . ' : ' . $this -> $varName . '</h5>';
			}
			return $str;
		}



		final public static function &getInstance(stdClass $object, $className)
		{
			$instance = new $className();
			$oReflection = new ReflectionClass($className);
			$properties = $oReflection -> getProperties();
			foreach($properties as $property)
			{
				$name = $property -> getName();
				if ($property -> isProtected())
				{
					if (isset($object -> $name))
					{
						$value = $object -> $name;
						$method = 'set' . lcfirst($name);
						call_user_func_array(array($instance, $method), array($value));
					}
				}
			}
			return $instance;
		}



		final public function &getObject()
		{
			$obj = new stdClass;
			foreach($this as $varName => $ok)
			{
				$obj -> $varName = $this -> $varName;
			}
			return $obj;
		}



		/*final public static function load($obj, $classname)
		{
			//$classname = __CLASS__;
			$instance = new $classname();
			foreach($obj as $varName => $value)
			{
				$instance -> $varName = $value;
			}
			return $instance;
		}*/



		final public function toWsdl()
		{
			// @todo
		}



	}
