<?php
namespace Sellastica\Connector\Model;

/**
 * @method onResponseReceived(DownloadResponse $response)
 * @method onItemModified(ConnectorResponse $response, $itemData)
 * @method onItemRemoved(ConnectorResponse $response, string $externalId)
 * @method onOffsetSynchronized(DownloadResponse $response)
 * @method onSynchronizationFinished(DownloadResponse $response)
 */
class DownloadSynchronizer extends \Sellastica\Connector\Model\AbstractSynchronizer
{
	use \Nette\SmartObject;

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
	/** @var \Nette\Localization\ITranslator */
	private $translator;
	/** @var \Sellastica\Core\Model\Environment */
	private $environment;


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
			$identifierFactory
		);
		$this->dataGetter = $dataGetter;
		$this->dataHandler = $dataHandler;
		$this->loggerFactory = $loggerFactory;
		$this->eventDispatcher = $eventDispatcher;
		$this->translator = $translator;
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
		$this->dataGetter->setProcessId($this->processId);
		$this->dataHandler->setProcessId($this->processId);
		$this->dataHandler->initialize();

		//synchronization entity
		$synchronization = $this->createSynchronization(
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

		$logger = $this->loggerFactory->create($synchronization->getId());
		$logger->clearOldLogEntries();
		$this->dataHandler->setLogger($logger);

		try {
			do {
				if ($synchronization->getStatus()
					->equals(\Sellastica\Connector\Model\SynchronizationStatus::interrupted())) {
					break;
				}

				$logger->notice($this->translator->translate(
					'core.connector.trying_to_download_items', $this->itemsPerPage
				));

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
				$item = $logger->notice($this->translator->translate(
					$dataCount ? 'core.connector.number_of_items_fetched' : 'core.connector.no_items_fetched',
					$dataCount
				));
				if ($downloadResponse->getData() !== null) {
					$item->setSourceData(serialize($downloadResponse->getData()));
				}

				//items to create or update
				foreach ($downloadResponse->getData() as $itemData) {
					set_time_limit(10);
					//set data to local items
					$response = $this->dataHandler->modify(
						$itemData, $synchronization->getChangesSince(), $this->params
					);
					if (!$response->getSourceData()) {
						$response->setSourceData($itemData);
					}

					$this->onItemModified($response, $itemData);
					if ($response->getStatusCode() !== ConnectorResponse::IGNORED) {
						$logger->fromResponse($response);
					}
				}

				if (sizeof($downloadResponse->getData())) {
					$logger->notice($this->translator->translate('core.connector.processing_data_finished'));
				}

				//items to remove
				if (sizeof($downloadResponse->getExternalIdsToRemove())) {
					$logger->notice($this->translator->translate(
						'removing_number_of_items', sizeof($downloadResponse->getExternalIdsToRemove())
					));
					foreach ($downloadResponse->getExternalIdsToRemove() as $externalId) {
						$response = $this->dataHandler->remove($externalId);
						$this->onItemRemoved($response, $externalId);
						$logger->fromResponse($response);
					}

					$logger->notice($this->translator->translate('core.connector.removing_of_items_finished'));
					$this->em->flush(); //flush removed items
				}

				$logger->save(); //save after each iteration
				$this->onOffsetSynchronized($downloadResponse);
			} while (
				$downloadResponse->getData()
				&& $this->finishSynchronizing !== true
			);

			$synchronization->end();
			$synchronization->setStatus(SynchronizationStatus::success());
			$this->em->persist($synchronization);

			$this->onSynchronizationFinished($downloadResponse ?? null);
			$this->eventDispatcher->dispatchEvent('connector.synchronization.finished');
			$this->em->flush(); //flush changes after event

		} catch (\Sellastica\Connector\Exception\IErpConnectorException $e) {
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
	 * @param mixed $id
	 * @param OptionsRequest $params
	 * @param bool $manual
	 * @return DownloadResponse
	 * @throws \Exception
	 */
	public function downloadOne(
		$id,
		OptionsRequest $params = null,
		bool $manual = false
	): DownloadResponse
	{
		//initialization
		$this->sinceWhen = ISynchronizer::SINCE_EVER;
		$this->dataGetter->setProcessId($this->processId);
		$this->dataHandler->setProcessId($this->processId);
		$this->dataHandler->initialize();

		//synchronization entity
		$synchronization = $this->createSynchronization(
			SynchronizationType::single(),
			$this->dataGetter->getSource(),
			$this->dataHandler->getTarget()
		);
		$synchronization->setParams($params);
		$synchronization->setManual($manual);
		$synchronization->setChangesSince(null);
		$synchronization->start();

		$this->em->persist($synchronization);
		$this->em->flush();

		$logger = $this->loggerFactory->create($synchronization->getId());
		$logger->clearOldLogEntries();
		$this->dataHandler->setLogger($logger);

		$logger->notice($this->translator->translate('core.connector.trying_to_download_sigle_item'));

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

			$logger->notice($this->translator->translate('core.connector.processing_data_finished'));
			$logger->save(); //save after each iteration
			$this->onOffsetSynchronized($downloadResponse);

			$synchronization->end();
			$synchronization->setStatus(SynchronizationStatus::success());
			$this->em->persist($synchronization);
			$this->em->flush();

			return $downloadResponse;

		} catch (\Sellastica\Connector\Exception\IErpConnectorException $e) {
			$logger->notice($this->translator->translate('core.connector.item_not_found'));

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
