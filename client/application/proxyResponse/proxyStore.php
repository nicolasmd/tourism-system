<?php

	/**
	 * @version		0.4 alpha-test - 2013-06-03
	 * @package		Tourism System Client
	 * @copyright	Copyright (C) 2010 Raccourci Interactive
	 * @license		GNU GPLv3 ; see LICENSE.txt
	 * @author		Jeremie Perrin <jeremie.raccourci@gmail.com>
	 */
	class proxyStore
	{

		private $soapResponse;
		private $start = 0;
		private $limit = null;
		private $query = null;
		private $queryField = null;
		private $gridfilters = null;
		private $sort = null;
		private $dir = 'ASC';
		private $searchableFields = array();
		private $dataCount = 0;
		private $data = array();

		public function __construct()
		{

		}

		public function getData()
		{
			return $this->data;
		}

		public function setData($data)
		{
			$this->data = $data;
		}

		public function setSoapResponse(array $value)
		{
			$this->soapResponse = $value;
			unset($value['status']);
			reset($value);
			$this->data = current($value);
		}

		public function setParams(array $params)
		{
			$this->start = (isset($params['start'])) ? intval($params['start']) : $this->start;
			$this->limit = (isset($params['limit'])) ? intval($params['limit']) : $this->limit;
			$this->query = (isset($params['query'])) ? $params['query'] : $this->query;
			$this->queryField = (isset($params['queryField'])) ? $params['queryField'] : $this->queryField;
			$this->gridfilters = (isset($params['gridfilters'])) ? json_decode($params['gridfilters']) : $this->gridfilters;
			$this->sort = (isset($params['sort'])) ? $params['sort'] : $this->sort;
			$this->dir = (isset($params['dir'])) ? $params['dir'] : $this->dir;
		}

		public function setSearchableFields(array $fields)
		{
			if (isset($this->queryField) && $this->queryField != '')
			{
				$fields = array($this->queryField);
			}
			$this->searchableFields = $fields;
		}

		public function search()
		{
			$dataRoot = array();

			if (isset($this->gridfilters) && is_array($this->gridfilters))
			{
				foreach ($this->data as $data)
				{
					$keep = true;

					foreach ($this->gridfilters as $gridfilters)
					{
						$sortFunc = 'filterBy' . ucfirst($gridfilters->type);
						$keep = $this->$sortFunc($data, $gridfilters);

						if ($keep === false)
						{
							break;
						}
					}

					if ($keep)
					{
						$dataRoot[] = $data;
					}
				}
				$this->data = $dataRoot;
			}

			$dataRoot = array();

			if (isset($this->query) && $this->query !== ''
				&& count($this->searchableFields) > 0)
			{
				foreach ($this->searchableFields as $field)
				{
					foreach ($this->data as $k => $v)
					{
						$fieldValue = strtolower(strtr(trim($v->$field), $GLOBALS['normalizeChars']));
						$query = strtolower(strtr(trim($this->query), $GLOBALS['normalizeChars']));

						if (strpos($fieldValue, $query) !== false)
						{
							$dataRoot[] = $v;
							unset($this->data[$k]);
						}
					}
				}
				$this->data = $dataRoot;
			}
		}

		public function sort()
		{
			if (isset($this->sort) && $this->sort !== '')
			{
				usort($this->data, array($this, 'usortFunction'));
			}
		}

		public function slice()
		{
			$this->dataCount = count($this->data);
			if (isset($this->start) && isset($this->limit) && is_array($this->data))
			{
				$this->data = array_slice($this->data, $this->start, $this->limit);
			}
		}

		public function getJsonData()
		{
			return json_encode(array(
					'dataCount' => $this->dataCount,
					'dataRoot' => $this->data));
		}

		public function getProxyResponse()
		{
			$this->search();
			$this->sort();
			$this->slice();
			return $this->getJsonData();
		}

		private function filterByString($data, $filterOpt)
		{
			$field = $filterOpt->field;
			$dataValue = strtolower(strtr(trim($data->$field), $GLOBALS['normalizeChars']));
			$filterValue = strtolower(strtr(trim($filterOpt->value), $GLOBALS['normalizeChars']));

			$comparison = (isset($filterOpt->comparison) && $filterOpt->comparison != '') ? $filterOpt->comparison : 'any';

			switch ($comparison)
			{
				case 'start':
					$res = strpos($dataValue, $filterValue) === 0;
					break;
				case 'regex':
					$res = preg_match($filterValue, $dataValue) === 1;
					break;
				default:
					$res = strpos($dataValue, $filterValue) !== false;
					break;
			}

			return $res;
		}

		private function filterByList($data, $filterOpt)
		{
			$field = $filterOpt->field;
			return in_array($data->$field, $filterOpt->value);
		}

		private function filterByDate($data, $filterOpt)
		{
			$field = $filterOpt->field;
			$dataValue = strtotime(date('Y-m-d', strtotime($data->$field)));
			$filterValue = strtotime($filterOpt->value);

			switch ($filterOpt->comparison)
			{
				case 'eq':
					$res = $dataValue == $filterValue;
					break;
				case 'lt':
					$res = $dataValue < $filterValue;
					break;
				case 'gt':
					$res = $dataValue > $filterValue;
					break;
			}

			return $res;
		}

		private function filterByBoolean($data, $filterOpt)
		{
			$field = $filterOpt->field;
			return $data->$field == $filterOpt->value;
		}

		private function usortFunction($a, $b)
		{
			$sort = $this->sort;
			$valA = strtolower(strtr(trim($a->$sort), $GLOBALS['normalizeChars']));
			$valB = strtolower(strtr(trim($b->$sort), $GLOBALS['normalizeChars']));
			if ($valA < $valB)
			{
				return $this->dir == 'ASC' ? -1 : 1;
			}
			if ($valA > $valB)
			{
				return $this->dir == 'ASC' ? 1 : -1;
			}
			return 0;
		}

	}

?>