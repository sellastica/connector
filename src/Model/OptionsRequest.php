<?php
namespace Sellastica\Connector\Model;

class OptionsRequest
{
	/** @var bool */
	protected $dump = false;


	/**
	 * @return bool
	 */
	public function isDump(): bool
	{
		return $this->dump;
	}

	/**
	 * @param bool $dump
	 * @return OptionsRequest
	 */
	public function setDump(bool $dump): OptionsRequest
	{
		$this->dump = $dump;
		return $this;
	}
}
