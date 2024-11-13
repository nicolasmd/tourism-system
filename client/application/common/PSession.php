<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	class PSession implements ArrayAccess
	{

		static $sessionArray = null;
		static $SESSION = null; // object transitoire pour acces sans trop de modifs au code

		public function __construct()
		{

		}

		public static function start()
		{
			@session_start();
			if (self::$SESSION === null)
			{
				self::$SESSION = new PSession();
				self::$sessionArray = array();
				self::$sessionArray = $_SESSION;
			}
			session_write_close();
		}

		private static function check()
		{
			if (self::$SESSION === null)
			{
				self::start();
			}
		}

		public static function commit()
		{
			if (self::$SESSION !== null)
			{
				@session_start();
				$_SESSION = self::$sessionArray;
				session_write_close();
			}
		}

		public static function set($key, $val)
		{
			self::check();
			self::$sessionArray[$key] = $val;
		}

		public static function get($key)
		{
			self::check();
			return isset(self::$sessionArray[$key]) ? self::$sessionArray[$key] : null;
		}

		public static function destroy()
		{
			self::check();
			foreach (self::$sessionArray as $k => $v)
			{
				unset(self::$sessionArray[$k]);
			}
			@session_destroy();
			self::commit();
		}

		public static function delete($index)
		{
			self::check();
			unset(self::$sessionArray[$index]);
		}

		public static function exists($index)
		{
			self::check();
			return isset(self::$sessionArray[$index]);
		}

		public static function unsets()
		{
			self::check();
			session_unset();
		}

		public function offsetGet($index)
		{
			return self::get($index);
		}

		public function offsetSet($index, $value)
		{
			self::set($index, $value);
		}

		public function offsetUnset($index)
		{
			self::delete($index);
		}

		public function offsetExists($index)
		{
			return isset(self::$sessionArray[$index]);
		}

	}

?>