<?php
namespace Sellastica\Connector\Model;

abstract class AbstractUploadDataHandler implements IUploadDataHandler
{
	/** @var int */
	protected $processId;

	/**
	 * @param int $processId
	 */
	public function setProcessId(int $processId)
	{
		$this->processId = $processId;
	}
}
