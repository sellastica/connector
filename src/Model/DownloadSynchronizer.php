<?php
namespace Sellastica\Connector\Model;

/**
 * @method onStart
 * @method onResponseReceived(DownloadResponse $response)
 * @method onBeforeItemModified($itemData)
 * @method onItemModified(ConnectorResponse $response, $itemData)
 * @method onItemRemoved(ConnectorResponse $response, string $externalId)
 * @method onOffsetSynchronized(DownloadResponse $response)
 * @method onSynchronizationFinished(DownloadResponse $response)
 */
class DownloadSynchronizer extends \Sellastica\Connector\Model\AbstractSynchronizer
{
	use \Nette\SmartObject;

	/** @var array */
	public $onStart = [];
	/** @var array */
	public $onResponseReceived = [];
	/** @var array */
	public $onBeforeItemModified = [];
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
	/** @var \Sellastica\Connector\Logger\LoggerFactory */
	private $loggerFactory;
	/** @var int */
	private $itemsPerPage = 1000;
	/**
	 * @var mixed
	 * Offset in first iteration = 0, offset in the next iteration must be returned from the DownloadResponse
	 */
	private $offset = 0;
	/** @var \Sellastica\Events\EventDispatcher */
	private $eventDispatcher;
	/** @var \Sellastica\Core\Model\Environment */
	private $environment;
	/** @var string */
	private $lookupHistory = '-1 week';
	/** @var bool */
	private $logNotices = false;


	/**
	 * @param string $identifier
	 * @param int $processId
	 * @param \Sellastica\App\Entity\App $app
	 * @param IIdentifierFactory $identifierFactory
	 * @param IDownloadCollectionDataGetter $dataGetter
	 * @param IDownloadDataHandler $dataHandler
	 * @param \Sellastica\Entity\EntityManager $em
	 * @param \Sellastica\Connector\Logger\LoggerFactory $loggerFactory
	 * @param \Sellastica\Events\EventDispatcher $eventDispatcher
	 * @param \Nette\Localization\ITranslator $translator
	 * @param \Sellastica\Core\Model\Environment $environment
	 */
	public function __construct(
		string $identifier,
		int $processId,
		\Sellastica\App\Entity\App $app,
		IIdentifierFactory $identifierFactory,
		IDownloadCollectionDataGetter $dataGetter,
		IDownloadDataHandler $dataHandler,
		\Sellastica\Entity\EntityManager $em,
		\Sellastica\Connector\Logger\LoggerFactory $loggerFactory,
		\Sellastica\Events\EventDispatcher $eventDispatcher,
		\Nette\Localization\ITranslator $translator,
		\Sellastica\Core\Model\Environment $environment
	)
	{
		parent::__construct(
			$identifier,
			$processId,
			$app,
			$em,
			$identifierFactory,
			$translator
		);
		$this->dataGetter = $dataGetter;
		$this->dataHandler = $dataHandler;
		$this->loggerFactory = $loggerFactory;
		$this->eventDispatcher = $eventDispatcher;
		$this->environment = $environment;
	}

	/**
	 * @param OptionsRequest $params
	 * @param bool $manual
	 * @return void
	 * @throws \Exception
	 * @throws \InvalidArgumentException
	 * @throws \Throwable
	 * @throws \UnexpectedValueException
	 */
	public function synchronize(
		OptionsRequest $params = null,
		bool $manual = false
	)
	{
		//optimize imports
		$this->em->optimizeImports(min($this->itemsPerPage, 100));

		//initialization
		$this->dataHandler->initialize();

		//synchronization entity
		$synchronization = $this->environment->isSellastica()
			? $this->createSynchronization(
				SynchronizationType::full(),
				$this->dataGetter->getSource(),
				$this->dataHandler->getTarget()
			)
			: $this->createMongoSynchronization(
				SynchronizationType::full(),
				$this->dataGetter->getSource(),
				$this->dataHandler->getTarget()
			);

		$synchronization->setParams($params);
		$synchronization->setManual($manual);
		$synchronization->setChangesSince(
			$this->getSinceWhenDate($this->dataGetter->getSource(), $this->dataHandler->getTarget())
		);
		$synchronization->start();
		$this->em->persist($synchronization);
		$this->em->flush();

		$logger = $this->environment->isSellastica()
			? $this->loggerFactory->create($synchronization->getId())
			: $this->loggerFactory->createMongoLogger($synchronization->getObjectId());

		$this->dataHandler->setLogger($logger);
		//clear records with current identifier older than lookup history
		$logger->clearOldLogEntries($this->lookupHistory, $this->identifier);
		//clear all records older than 1 week
		$logger->clearOldLogEntries('-1 week');

		//synchronization start
		$this->onStart();

		try {
			do {
				$this->forceContinueSynchronizing = false;

				if ($this->logNotices) {
					$logger->notice($this->translator->translate(
						'core.connector.trying_to_download_items', $this->itemsPerPage
					));
				}

				//download remote data
				//retrive whole response, so we could handle some extra data not present in the data array above
				set_time_limit(20);
				$downloadResponse = $this->sinceWhen === ISynchronizer::SINCE_EVER
					? $this->dataGetter->getAll($this->itemsPerPage, $this->offset, $this->params)
					: $this->dataGetter->getModified(
						$this->itemsPerPage, $this->offset, $synchronization->getChangesSince(), $this->params
					);

				$this->onResponseReceived($downloadResponse);

				$dataCount = sizeof($downloadResponse->getData());
				if ($this->logNotices) {
					$item = $logger->notice($this->translator->translate(
						$dataCount ? 'core.connector.number_of_items_fetched' : 'core.connector.no_items_fetched',
						$dataCount
					));
					if ($downloadResponse->getData() !== null
						&& is_array($downloadResponse->getData())) {
						$item->setSourceData(serialize($downloadResponse->getData()));
					}
				}

				//items to create or update
				$iteration = 0;
				if (is_iterable($downloadResponse->getData())) {
					foreach ($downloadResponse->getData() as $itemData) {
						set_time_limit(10);

						$this->onBeforeItemModified($itemData);

						//set data to local items
						$response = $this->dataHandler->modify(
							$itemData, $synchronization->getChangesSince(), $this->params
						);

						$this->onItemModified($response, $itemData);
						if ($response->getStatusCode() !== ConnectorResponse::IGNORED) {
							if (!$response->getSourceData()) {
								$response->setSourceData($itemData);
							}

							$logger->fromResponse($response);
						}

						if ($iteration % 100 === 0) {
							$logger->save();
							//check manual interruption
							$this->checkManualInterruption($synchronization->getId());
						}

						//memory limit check
						$this->checkMemoryLimit();
						$iteration++;
					}
				}

				if ((is_array($downloadResponse->getData()) || $downloadResponse->getData() instanceof \Countable)
					&& sizeof($downloadResponse->getData())
					&& $this->logNotices) {
					$logger->notice($this->translator->translate('core.connector.processing_data_finished'));
				}

				//items to remove
				if (sizeof($downloadResponse->getExternalIdsToRemove())) {
					if ($this->logNotices) {
						$logger->notice($this->translator->translate(
							'removing_number_of_items', sizeof($downloadResponse->getExternalIdsToRemove())
						));
					}

					foreach ($downloadResponse->getExternalIdsToRemove() as $externalId) {
						//check manual interruption
						$this->checkManualInterruption($synchronization->getId());

						$response = $this->dataHandler->remove($externalId);
						$this->onItemRemoved($response, $externalId);
						$logger->fromResponse($response);
					}

					if ($this->logNotices) {
						$logger->notice($this->translator->translate('core.connector.removing_of_items_finished'));
					}

					$this->em->flush(); //flush removed items
				}

				$logger->save(); //save after each iteration
				$this->onOffsetSynchronized($downloadResponse);
			} while (
				($downloadResponse->getData() || $this->isSynchronizingForced())
				&& $this->finishSynchronizing !== true
			);

			$synchronization->end();
			$synchronization->setStatus(SynchronizationStatus::success());
			$this->em->persist($synchronization);

			$this->onSynchronizationFinished($downloadResponse ?? null);
			$this->eventDispatcher->dispatchEvent('connector.synchronization.finished');
			$this->em->flush(); //flush changes after event
			$this->restoreMemoryLimit();

		} catch (\Sellastica\Connector\Exception\IErpConnectorException $e) {
			//log synchronization fail
			$synchronization->end();
			if ($e instanceof \Sellastica\Connector\Exception\AbortException) {
				$synchronization->setStatus(SynchronizationStatus::interrupted());
			} elseif (!$synchronization->getStatus()->isInterrupted()) {
				$synchronization->setStatus(SynchronizationStatus::fail());
			}

			$this->em->persist($synchronization);

			//log exception - IErpConnectorException message can be logged
			$synchronization->setNote($e->getMessage());

			$logger->save();
			$this->restoreMemoryLimit();
			throw $e;
		}
	}

	/**
	 * @param mixed $id
	 * @param OptionsRequest $params
	 * @param bool $manual
	 * @return ConnectorResponse
	 * @throws \Sellastica\Connector\Exception\IErpConnectorException
	 */
	public function downloadOne(
		$id,
		OptionsRequest $params = null,
		bool $manual = false
	): ConnectorResponse
	{
		set_time_limit(20);

		//initialization
		$this->sinceWhen = ISynchronizer::SINCE_EVER;
		$this->dataHandler->initialize();

		//synchronization entity
		$synchronization = $this->environment->isSellastica()
			? $this->createSynchronization(
				SynchronizationType::full(),
				$this->dataGetter->getSource(),
				$this->dataHandler->getTarget()
			)
			: $this->createMongoSynchronization(
				SynchronizationType::full(),
				$this->dataGetter->getSource(),
				$this->dataHandler->getTarget()
			);
		$synchronization->setParams($params);
		$synchronization->setManual($manual);
		$synchronization->setChangesSince(null);
		$synchronization->start();

		$this->em->persist($synchronization);
		$this->em->flush();

		$logger = $this->environment->isSellastica()
			? $this->loggerFactory->create($synchronization->getId())
			: $this->loggerFactory->createMongoLogger($synchronization->getObjectId());

		$this->dataHandler->setLogger($logger);
		//clear records with current identifier older than lookup history
		$logger->clearOldLogEntries($this->lookupHistory, $this->identifier);
		//clear all records older than 1 week
		$logger->clearOldLogEntries('-1 week');

		if ($this->logNotices) {
			$logger->notice($this->translator->translate('core.connector.trying_to_download_sigle_item'));
		}

		//download remote data
		//retrive whole response, so we could handle some extra data not present in the data array above
		try {
			set_time_limit(20);
			$downloadResponse = $this->dataGetter->getOne($id, $this->params);
			$this->onResponseReceived($downloadResponse);

			set_time_limit(10);
			//set data to local items
			$response = $this->dataHandler->modify(
				$downloadResponse->getData(), null, $this->params
			);
			if (!$response->getSourceData()) {
				$response->setSourceData($downloadResponse->getData());
			}

			$this->onItemModified($response, $downloadResponse->getData());
			if ($response->getStatusCode() !== ConnectorResponse::IGNORED) {
				$logger->fromResponse($response);
			}

			if ($this->logNotices) {
				$logger->notice($this->translator->translate('core.connector.processing_data_finished'));
			}

			$logger->save(); //save after each iteration
			$this->onOffsetSynchronized($downloadResponse);

			$synchronization->end();
			$synchronization->setStatus(SynchronizationStatus::success());
			$this->em->persist($synchronization);
			$this->em->flush();

			return $response;

		} catch (\Sellastica\Connector\Exception\IErpConnectorException $e) {
			if ($this->logNotices) {
				$logger->notice($this->translator->translate('core.connector.item_not_found'));
			}

			//log synchronization fail
			$synchronization->end();
			$synchronization->setStatus(SynchronizationStatus::fail());
			$this->em->persist($synchronization);

			$logger->fromException($e);
			$logger->save();

			throw $e;
		}
	}

	/**
	 * @param string $lookupHistory
	 */
	public function setLookupHistory(string $lookupHistory): void
	{
		$this->lookupHistory = $lookupHistory;
	}

	/**
	 * @param bool $logNotices
	 */
	public function setLogNotices(bool $logNotices): void
	{
		$this->logNotices = $logNotices;
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

	/**
	 * @param int|string $synchronizationId
	 * @throws \Sellastica\Connector\Exception\AbortException
	 */
	private function checkManualInterruption($synchronizationId): void
	{
		if (!$this->environment->isSellastica()) {
			return;
		}

		$status = $this->em->getRepository(\Sellastica\Connector\Entity\Synchronization::class)
			->findField($synchronizationId, 'status');
		if ($status === SynchronizationStatus::INTERRUPTED) {
			throw new \Sellastica\Connector\Exception\AbortException(
				$this->translator->translate('core.connector.notices.manually_interrupted')
			);
		}
	}
}
