<?php
namespace Sellastica\Connector\Model;

class OptionsRequest
{
	/** @var bool */
	protected $dumpResponse = false;
	/** @var array */
	protected $filters = [];
	/** @var array */
	protected $query = [];


	/**
	 * @return bool
	 */
	public function isDumpResponse(): bool
	{
		return $this->dumpResponse;
	}

	/**
	 * @param bool $dumpResponse
	 * @return OptionsRequest
	 */
	public function setDumpResponse(bool $dumpResponse): OptionsRequest
	{
		$this->dumpResponse = $dumpResponse;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getFilters(): array
	{
		return $this->filters;
	}

	/**
	 * @param $key
	 * @param null $default
	 * @return mixed|null
	 */
	public function getFilter($key, $default = null)
	{
		return $this->filters[$key] ?? $default;
	}

	/**
	 * @param $key
	 * @param $value
	 * @return OptionsRequest
	 */
	public function addFilter($key, $value): OptionsRequest
	{
		$this->filters[$key] = $value;
		return $this;
	}

	/**
	 * @param $key
	 * @param $value
	 * @return OptionsRequest
	 */
	public function addQuery($key, $value): OptionsRequest
	{
		if (!is_scalar($value)) {
			throw new \InvalidArgumentException('Query value must be scalar');
		}

		$this->query[$key] = $value;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getQuery(): array
	{
		return $this->query;
	}
}
