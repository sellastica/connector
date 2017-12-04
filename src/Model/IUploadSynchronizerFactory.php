<?php
namespace Sellastica\Connector\Model;

use Sellastica\App\Entity\App;

interface IUploadSynchronizerFactory
{
	/**
	 * @param string $identifier
	 * @param int $processId
	 * @param \Sellastica\App\Entity\App $app
	 * @param IUploadDataGetter $dataGetter
	 * @param IUploadDataHandler $dataHandler
	 * @return UploadSynchronizer
	 */
	function create(
		string $identifier,
		int $processId,
		App $app,
		IUploadDataGetter $dataGetter,
		IUploadDataHandler $dataHandler
	): UploadSynchronizer;
}