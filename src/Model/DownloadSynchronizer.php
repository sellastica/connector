<?php
namespace Sellastica\Connector\Model;

use Bazo\Events\EventDispatcher;
use Nette\SmartObject;
use Sellastica\App\Entity\App;
use Sellastica\Connector\Exception\IErpConnectorException;
use Sellastica\Connector\Logger\LoggerFactory;
use Sellastica\Entity\EntityManager;

/**
 * @method onResponseReceived(DownloadResponse $response)
 * @method onItemModified(ConnectorResponse $response, $itemData)
 * @method onItemRemoved(ConnectorResponse $response, string $externalId)
 * @method onOffsetSynchronized(DownloadResponse $response)
 * @method onSynchronizationFinished(DownloadResponse $response)
 */
class DownloadSynchronizer extends \Sellastica\Connector\Model\AbstractSynchronizer
{
	use SmartObject;

	/** @var array */
	public $onResponseReceived = [];
	/** @var array */
	public $onItemModified = [];
	/** @var array */
	public $onOffsetSynchronized = [];
	/** @var array */
	public $onSynchronizationFinished = [];
	/** @var array */
	public $onItemRemoved = [];

	/** @var IDownloadCollectionDataGetter */
	private $dataGetter;
	/** @var IDownloadDataHandler */
	private $dataHandler;
	/** @var LoggerFactory */
	private $loggerFactory;
	/** @var int */
	private $itemsPerPage = 1000;
	/**
	 * @var mixed
	 * Offset in first iteration = 0, offset in the next iteration must be returned from the DownloadResponse
	 */
	private $offset = 0;
	/** @var EventDispatcher */
	private $eventDispatcher;


	/**
	 * @param string $identifier
	 * @param int $processId
	 * @param App $app
	 * @param IIdentifierFactory $identifierFactory
	 * @param IDownloadCollectionDataGetter $dataGetter
	 * @param IDownloadDataHandler $dataHandler
	 * @param EntityManager $em
	 * @param \Sellastica\Connector\Logger\LoggerFactory $loggerFactory
	 * @param EventDispatcher $eventDispatcher
	 */
	public function __construct(
		string $identifier,
		int $processId,
		App $app,
		IIdentifierFactory $identifierFactory,
		IDownloadCollectionDataGetter $dataGetter,
		IDownloadDataHandler $dataHandler,
		EntityManager $em,
		LoggerFactory $loggerFactory,
		EventDispatcher $eventDispatcher
	)
	{
		\Sellastica\Connector\Model\AbstractSynchronizer::__construct(
			$identifier,
			$processId,
			$app,
			$em,
			$identifierFactory
		);
		$this->dataGetter = $dataGetter;
		$this->dataHandler = $dataHandler;
		$this->loggerFactory = $loggerFactory;
		$this->eventDispatcher = $eventDispatcher;
	}

	/**
	 * @return void
	 * @throws \Exception
	 */
	public function synchronize()
	{
		//optimize imports
		$this->em->optimizeImports(min($this->itemsPerPage, 100));

		//initialization
		$this->dataGetter->setProcessId($this->processId);
		$this->dataHandler->setProcessId($this->processId);
		$this->dataHandler->initialize();
		//synchronization entity
		$synchronization = $this->createSynchronization(SynchronizationType::full(), Direction::down());
		$synchronization->start();
		$this->em->persist($synchronization);
		$this->em->flush();

		$logger = $this->loggerFactory->create($synchronization->getId());
		$logger->clearOldLogEntries();
		$this->dataHandler->setLogger($logger);

		try {
			do {
				if ($synchronization->isBreak()) {
					break;
				}

				$logger->notice(sprintf('Trying to download %s items from remote server', $this->itemsPerPage));

				//download remote data from ERP
				//retrive whole response, so we could handle some extra data not present in the data array above
				set_time_limit(20);
				$downloadResponse = $this->sinceWhen === ISynchronizer::SINCE_EVER
					? $this->dataGetter->getAll($this->itemsPerPage, $this->offset, $this->params)
					: $this->dataGetter->getModified(
						$this->itemsPerPage, $this->offset, $synchronization->getChangesSince(), $this->params
					);

				$this->onResponseReceived($downloadResponse);

				$logger->notice(sprintf(
					'%s items fetched%s',
					sizeof($downloadResponse->getData()),
					sizeof($downloadResponse->getData()) ? '. Processing data' : ''
				));

				//items to create or update
				foreach ($downloadResponse->getData() as $itemData) {
					set_time_limit(10);
					//set data to local items
					$response = $this->dataHandler->modify(
						$itemData, $synchronization->getChangesSince(), $this->params
					);
					$this->onItemModified($response, $itemData);
					if ($response->getStatusCode() !== ConnectorResponse::IGNORED) {
						$logger->fromResponse($response);
					}
				}

				if (sizeof($downloadResponse->getData())) {
					$logger->notice('Processing data finished');
				}

				//items to remove
				if (sizeof($downloadResponse->getExternalIdsToRemove())) {
					$logger->notice(sprintf(
						'Removing %s items',
						sizeof($downloadResponse->getExternalIdsToRemove())
					));
					foreach ($downloadResponse->getExternalIdsToRemove() as $externalId) {
						$response = $this->dataHandler->remove($externalId);
						$this->onItemRemoved($response, $externalId);
						$logger->fromResponse($response);
					}

					$logger->notice('Removing finished');
					$this->em->flush(); //flush removed items
				}

				$logger->save(); //save after each iteration
				$this->onOffsetSynchronized($downloadResponse);
			} while (
				$downloadResponse->getData()
				&& $this->finishSynchronizing !== true
			);

			$synchronization->end();
			$synchronization->setSuccess(true);
			$this->em->persist($synchronization);

			$this->onSynchronizationFinished($downloadResponse ?? null);
			$this->eventDispatcher->dispatchEvent('connector.synchronization.finished');
			$this->em->flush(); //flush changes after event

		} catch (IErpConnectorException $e) {
			//log synchronization fail
			$synchronization->end();
			$synchronization->setSuccess(false);
			$this->em->persist($synchronization);

			$logger->fromException($e);
			$logger->save();
			throw $e;
		}
	}

	/**
	 * @return int
	 */
	public function getItemsPerPage(): int
	{
		return $this->itemsPerPage;
	}

	/**
	 * @param int $itemsPerPage
	 */
	public function setItemsPerPage(int $itemsPerPage)
	{
		$this->itemsPerPage = $itemsPerPage;
	}

	/**
	 * @return mixed
	 */
	public function getOffset()
	{
		return $this->offset;
	}

	/**
	 * Sometimes is it a page number, sometimes real offset, sometimes is it whole page URL etc...
	 * @param mixed $offset
	 */
	public function setOffset($offset)
	{
		$this->offset = $offset;
	}

	/**
	 * @return IDownloadCollectionDataGetter
	 */
	public function getDataGetter(): IDownloadCollectionDataGetter
	{
		return $this->dataGetter;
	}

	/**
	 * @return IDownloadDataHandler
	 */
	public function getDataHandler(): IDownloadDataHandler
	{
		return $this->dataHandler;
	}
}
