<?php
namespace Sellastica\Connector\Model;

use Nette\SmartObject;
use Sellastica\App\Entity\App;
use Sellastica\Connector\Exception\IErpConnectorException;
use Sellastica\Connector\Logger\Logger;
use Sellastica\Connector\Logger\LoggerFactory;
use Sellastica\Core\Model\Environment;
use Sellastica\Entity\Entity\EntityCollection;
use Sellastica\Entity\Entity\IEntity;
use Sellastica\Entity\EntityManager;

/**
 * @method onDataFetched(EntityCollection $entities)
 * @method onItemModified(ConnectorResponse $response, IEntity $entity)
 * @method onSynchronizationFinished()
 */
class UploadSynchronizer extends \Sellastica\Connector\Model\AbstractSynchronizer
{
	use SmartObject;

	/** maximum number of errors, if only errors occur (no successfull items). If, stop is forced */
	private const MAX_ERRORS_COUNT = 20;

	/** @var array */
	public $onDataFetched = [];
	/** @var array */
	public $onItemModified = [];
	/** @var array */
	public $onOffsetSynchronized = [];
	/** @var array */
	public $onSynchronizationFinished = [];

	/** @var IUploadDataGetter */
	private $dataGetter;
	/** @var IUploadDataHandler */
	private $dataHandler;
	/** @var int */
	private $itemsPerPage = 1000;
	/** @var int */
	private $offset = 0;
	/** @var \Sellastica\Connector\Logger\LoggerFactory */
	private $loggerFactory;
	/** @var \Sellastica\Monolog\Logger */
	private $monolog;
	/** @var \Sellastica\Core\Model\Environment */
	private $environment;
	/** @var IIdentifierFactory */
	private $identifierFactory;


	/**
	 * @param string $identifier
	 * @param int $processId
	 * @param App $app
	 * @param IIdentifierFactory $identifierFactory
	 * @param IUploadDataGetter $dataGetter
	 * @param IUploadDataHandler $dataHandler
	 * @param EntityManager $em
	 * @param \Sellastica\Connector\Logger\LoggerFactory $loggerFactory
	 * @param \Sellastica\Monolog\Logger $monolog
	 * @param \Sellastica\Core\Model\Environment $environment
	 */
	public function __construct(
		string $identifier,
		int $processId,
		App $app,
		IIdentifierFactory $identifierFactory,
		IUploadDataGetter $dataGetter,
		IUploadDataHandler $dataHandler,
		EntityManager $em,
		LoggerFactory $loggerFactory,
		\Sellastica\Monolog\Logger $monolog,
		Environment $environment
	)
	{
		parent::__construct(
			$identifier,
			$processId,
			$app,
			$em,
			$identifierFactory
		);
		$this->dataHandler = $dataHandler;
		$this->em = $em;
		$this->dataGetter = $dataGetter;
		$this->loggerFactory = $loggerFactory;
		$this->monolog = $monolog;
		$this->environment = $environment;
		$this->identifierFactory = $identifierFactory;
	}

	/**
	 * @param OptionsRequest|null $params
	 * @param bool $manual
	 * @return void
	 * @throws \Exception In debug mode
	 * @throws \InvalidArgumentException
	 * @throws \Throwable
	 * @throws \UnexpectedValueException
	 */
	public function synchronize(
		OptionsRequest $params = null, 
		bool $manual = false
	): void
	{
		$synchronization = $this->createSynchronization(
			SynchronizationType::full(), $this->dataGetter->getSource(), $this->dataHandler->getTarget()
		);
		$synchronization->setChangesSince(
			$this->getSinceWhenDate($this->dataGetter->getSource(), $this->dataHandler->getTarget())
		);
		$synchronization->setParams($params);
		$synchronization->setManual($manual);
		$synchronization->start();
		$this->em->persist($synchronization);

		$this->dataGetter->setProcessId($this->processId);
		$this->dataHandler->setProcessId($this->processId);

		$logger = $this->loggerFactory->create($synchronization->getId());
		$logger->clearOldLogEntries();
		$this->logDataFetching($logger);

		//counts errors and finishes synchronizing if there is too much errors
		$totalItems = 0;
		$errorItems = 0;

		do {
			//get local data for upload
			set_time_limit(20);
			$entities = $this->changesOnly()
				? $this->dataGetter->getChanges($this->itemsPerPage, $this->offset, $this->params, $synchronization->getChangesSince())
				: $this->dataGetter->getAll($this->itemsPerPage, $this->offset, $this->params);

			//change offset
			$this->offset = (int)$this->offset + $this->itemsPerPage;
			$this->onDataFetched($entities);
			$this->logDataFetched($logger, $entities->count());

			/** @var IEntity $entity */
			foreach ($entities as $entity) {
				$totalItems++;
				set_time_limit(5);
				try {
					//upload remote data to ERP
					$response = $this->dataHandler->modify(
						$entity, $synchronization->getChangesSince(), $this->params
					);
					$this->onItemModified($response, $entity);

					$logger->fromResponse($response);
					$this->em->flush();

					//force finish if there is too much errors
					if (!$response->isSuccessfull()) {
						$errorItems++;
						if ($errorItems === $totalItems
							&& $errorItems >= self::MAX_ERRORS_COUNT) {
							$logger->notice('Uploading stopped, there is too much errors');
							$this->finishSynchronizing();
							break; //foreach
						}
					}
				} catch (IErpConnectorException $e) {
					//all responses with status code >= 400 must throw IErpConnectorException!
					$logger->add(
						$e->getCode(),
						$entity->getId(),
						null,
						null,
						null,
						$e->getMessage()
					);
					$logger->save();
					//syslog
					if ($this->environment->isProductionMode()) {
						$this->monolog->exception(
							$e, sprintf('%s [%s]', $e->getMessage(), $entity->getId())
						);
						if ($e instanceof \Sellastica\Connector\Exception\AbortException) {
							break(2); //foreach and while
						}
					} else {
						throw $e;
					}
				}
			}

			$logger->save(); //save after each iteration
			$this->onOffsetSynchronized();
		} while (
			$entities->count()
			&& $this->finishSynchronizing !== true
		);

		$synchronization->end();
		$synchronization->setSuccess(true);
		$this->em->persist($synchronization);

		$this->onSynchronizationFinished();
		$this->em->flush(); //flush changes after event

		if ($entities->count()) {
			$this->logFinish($logger);
		}
	}

	/**
	 * @param $data
	 * @return ConnectorResponse
	 * @throws \Exception In debug mode
	 */
	public function upload($data): ConnectorResponse
	{
		$synchronization = $this->createSynchronization(
			SynchronizationType::single(), $this->dataGetter->getSource(), $this->dataHandler->getTarget()
		);
		$synchronization->start();
		$this->em->persist($synchronization);

		$this->dataGetter->setProcessId($this->processId);

		$logger = $this->loggerFactory->create($synchronization->getId());
		$logger->clearOldLogEntries();
		$logger->notice('Uploading single object data to remote system');

		try {
			$response = $this->dataHandler->modify(
				$data, $synchronization->getChangesSince(), $this->params
			);
			$logger->fromResponse($response);
			$this->onItemModified($response, $data);

			$this->logFinish($logger);
			$logger->save();

			$synchronization->end();
			$synchronization->setSuccess(true);
			$this->em->persist($synchronization);
			$this->em->flush(); //perform external ID save in CLI run

			return $response;

		} catch (IErpConnectorException $e) {
			//all responses with status code >= 400 must throw IErpConnectorException!
			$logger->add(
				$e->getCode(),
				$data instanceof IEntity ? $data->getId() : null,
				null,
				null,
				null,
				$e->getMessage()
			);

			$logger->save();
			//synchronization failed
			$synchronization->end();
			$synchronization->setSuccess(false);
			$this->em->persist($synchronization);
			//syslog
			if ($this->environment->isProductionMode()) {
				$this->monolog->exception($e);
			} else {
				throw $e;
			}

			return ConnectorResponse::error($e->getMessage());
		}
	}

	public function batch(): void
	{
		$synchronization = $this->createSynchronization(
			SynchronizationType::batch(), $this->dataGetter->getSource(), $this->dataHandler->getTarget()
		);
		$synchronization->start();
		$this->em->persist($synchronization);
		$this->dataGetter->setProcessId($this->processId);

		$logger = $this->loggerFactory->create($synchronization->getId());
		$logger->clearOldLogEntries();
		$this->logDataFetching($logger);

		do {
			//get local data for upload
			$entities = $this->changesOnly()
				? $this->dataGetter->getChanges($this->itemsPerPage, $this->offset)
				: $this->dataGetter->getAll($this->itemsPerPage, $this->offset);

			//change offset
			$this->offset = (int)$this->offset + $this->itemsPerPage;
			$this->onDataFetched($entities);

			if ($entities->count()) {
				$this->logDataFetched($logger, $entities->count());

				try {
					//upload remote data to ERP
					$response = $this->dataHandler->batch($entities);
					$logger->fromResponse($response, $entities->count());
					$this->em->flush();
				} catch (IErpConnectorException $e) {
					//all responses with status code >= 400 must throw IErpConnectorException!
					$logger->add(
						$e->getCode(),
						null,
						null,
						null,
						null,
						$e->getMessage()
					);
					$logger->save();
					//syslog
					if ($this->environment->isProductionMode()) {
						$this->monolog->exception($e);
					} else {
						throw $e;
					}
				}
			}
		} while (
			$entities->count()
			&& $this->finishSynchronizing !== true
		);

		$synchronization->end();
		$synchronization->setSuccess(true);
		$this->em->persist($synchronization);

		$this->onSynchronizationFinished();
		$this->em->flush(); //flush changes after event

		if ($entities->count()) {
			$this->logFinish($logger);
		}
	}

	/**
	 * @return IUploadDataGetter
	 */
	public function getDataGetter(): IUploadDataGetter
	{
		return $this->dataGetter;
	}

	/**
	 * @return IUploadDataHandler
	 */
	public function getDataHandler(): IUploadDataHandler
	{
		return $this->dataHandler;
	}

	/**
	 * @param int $itemsPerPage
	 */
	public function setItemsPerPage(int $itemsPerPage)
	{
		$this->itemsPerPage = $itemsPerPage;
	}

	/**
	 * @param Logger $logger
	 */
	private function logDataFetching(Logger $logger): void
	{
		$notice = 'Fetching data from local storage';
		if ($this->changesOnly()) {
			$notice .= '. Fetching changes only';
		}

		$logger->notice($notice);
	}

	/**
	 * @param Logger $logger
	 * @param int $entitiesCount
	 */
	private function logDataFetched(Logger $logger, int $entitiesCount): void
	{
		$logger->notice(sprintf(
			'%s items fetched%s',
			$entitiesCount,
			$entitiesCount ? '. Uploading data to remote system' : ''
		));
	}

	/**
	 * @param Logger $logger
	 */
	private function logFinish(Logger $logger): void
	{
		$logger->notice('Uploading data to remote system finished');
	}
}