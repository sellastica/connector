<?php
namespace Sellastica\Connector\Logger;

use Sellastica\Connector\Entity\SynchronizationLog;
use Sellastica\Connector\Entity\SynchronizationLogBuilder;
use Sellastica\Connector\Model\ConnectorResponse;
use Sellastica\Entity\EntityManager;

class Logger
{
	/** @var int */
	private $synchronizationId;
	/** @var EntityManager */
	private $em;
	/** @var \Sellastica\Connector\Entity\SynchronizationLog[] */
	private $items = [];


	/**
	 * @param int $synchronizationId
	 * @param EntityManager $em
	 */
	public function __construct(
		int $synchronizationId,
		EntityManager $em
	)
	{
		$this->synchronizationId = $synchronizationId;
		$this->em = $em;
	}

	/**
	 * @param int $statusCode
	 * @param int|null $internalId
	 * @param string|null $remoteId
	 * @param string|null $code
	 * @param string|null $title
	 * @param array|string|null $description
	 * @param string|null $sourceData
	 * @param string|null $resultData
	 * @return SynchronizationLog
	 */
	public function add(
		int $statusCode = null,
		int $internalId = null,
		string $remoteId = null,
		string $code = null,
		string $title = null,
		$description = null,
		$sourceData = null,
		$resultData = null
	): SynchronizationLog
	{
		if (is_array($description)) {
			$description = implode(PHP_EOL, array_filter($description));
		}

		$this->items[] = $item = SynchronizationLogBuilder::create($this->synchronizationId, new \DateTime())
			->statusCode($statusCode)
			->internalId($internalId)
			->remoteId($remoteId)
			->code($code)
			->title($title)
			->description($description)
			->sourceData(isset($sourceData) ? serialize($sourceData) : null)
			->resultData(isset($resultData) ? serialize($resultData) : null)
			->build();

		return $item;
	}

	/**
	 * @param ConnectorResponse $response
	 * @param int $count Used in batch synchronizations
	 */
	public function fromResponse(ConnectorResponse $response, int $count = 1): void
	{
		$description = array_merge([$response->getDescription()], $response->getErrors());
		$this->add(
			$response->getStatusCode(),
			$response->getObjectId(),
			$response->getExternalId(),
			$response->getCode(),
			$response->getTitle(),
			$description,
			$response->getSourceData(),
			$response->getResultData()
		);
	}

	/**
	 * @param \Throwable $e
	 */
	public function fromException(\Throwable $e): void
	{
		$this->add(
			$e->getCode() ?: null, //default exception code is "0", it is "skipped" in connector terminology
			null,
			null,
			null,
			null,
			$e->getMessage()
		);
	}

	/**
	 * @param string|array $notice
	 * @return SynchronizationLog
	 */
	public function notice($notice): SynchronizationLog
	{
		return $this->add(null, null, null, null, null, $notice);
	}

	public function clearOldLogEntries(): void
	{
		$dateTime = new \DateTime('-1 week');
		$this->em->getRepository(SynchronizationLog::class)->clearOldLogEntries($dateTime);
	}

	public function save(): void
	{
		foreach ($this->items as $item) {
			$this->em->persist($item);
		}

		$this->em->flush();
		$this->items = [];
	}
}