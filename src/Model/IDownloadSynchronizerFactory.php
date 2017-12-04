<?php
namespace Sellastica\Connector\Model;

use Sellastica\App\Entity\App;

interface IDownloadSynchronizerFactory
{
	/**
	 * @param string $identifier
	 * @param int $processId
	 * @param App $app
	 * @param IDownloadCollectionDataGetter $dataGetter
	 * @param IDownloadDataHandler $dataHandler
	 * @return DownloadSynchronizer
	 */
	function create(
		string $identifier,
		int $processId,
		App $app,
		IDownloadCollectionDataGetter $dataGetter,
		IDownloadDataHandler $dataHandler
	): DownloadSynchronizer;
}