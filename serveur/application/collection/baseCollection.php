<?php

/**
 * @version		0.4 alpha-test - 2013-06-03
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */

	abstract class baseCollection extends ArrayIterator // SPL : implements Iterator , Traversable , ArrayAccess , SeekableIterator , Countable
	{

		protected $pos;
		protected $collection;
		protected $index;

		public function __construct($array = array())
		{
			$this -> pos = 0;
			$this -> index = 0;
			$this -> collection = $array;
		}

		public function rewind()
		{
			$this -> pos = 0;
		}

		public function current()
		{
			return $this -> collection[$this -> pos];
		}

		public function key()
		{
			return $this -> pos;
		}

		public function next()
		{
			$this -> pos += 1;
		}

		public function valid()
		{
			return isset($this -> collection[$this -> pos]);
		}

		public function offsetSet($offset, $value)
		{
			if (is_null($offset))
			{
				$offset = $this -> index;
			}
			if (is_int($offset) && $offset >= $this -> index)
			{
				$this -> index = $offset + 1;
			}
			$this -> collection[$offset] = $value;
		}

		public function offsetExists($offset)
		{
			return isset($this -> collection[$offset]);
		}

	 	public function offsetUnset($offset)
		{
			unset($this -> collection[$offset]);
		}

		public function offsetGet($offset)
		{
			return isset($this -> collection[$offset]) ? $this -> collection[$offset] : null;
		}


		public function &getCollection()
		{
			return $this -> collection;
		}

	}
