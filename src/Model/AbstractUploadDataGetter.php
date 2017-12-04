<?php
namespace Sellastica\Connector\Model;

abstract class AbstractUploadDataGetter implements IUploadDataGetter
{
	/** @var int */
	protected $synchronizationId;


	/**
	 * @param int $processId
	 */
	public function setProcessId(int $processId)
	{
		$this->synchronizationId = $processId;
	}
}