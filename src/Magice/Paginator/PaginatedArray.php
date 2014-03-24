<?php
namespace Magice\Paginator;

class PaginatedArray implements PaginatedInterface
{
	protected $data;

	function __construct(array $data)
	{
		$this->data = $data;
	}

	public function count()
	{
		return count($this->data);
	}

	public function getSlice($length, $offset)
	{
		return array_slice($this->data, $offset, $length);
	}
}
