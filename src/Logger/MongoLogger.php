<?php
namespace Sellastica\Connector\Logger;

class MongoLogger implements ILogger
{
	/** @var \Sellastica\Entity\EntityManager */
	private $em;
	/** @var \Sellastica\Connector\Entity\MongoSynchronizationItem[] */
	private $items = [];
	/** @var \MongoDB\BSON\ObjectId */
	private $synchronizationId;


	/**
	 * @param \MongoDB\BSON\ObjectId $synchronizationId
	 * @param \Sellastica\Entity\EntityManager $em
	 */
	public function __construct(
		\MongoDB\BSON\ObjectId $synchronizationId,
		\Sellastica\Entity\EntityManager $em
	)
	{
		$this->em = $em;
		$this->synchronizationId = $synchronizationId;
	}

	/**
	 * @param int $statusCode
	 * @param $internalId
	 * @param string|null $remoteId
	 * @param string|null $code
	 * @param string|null $title
	 * @param array|string|null $description
	 * @param string|null $sourceData
	 * @param string|null $resultData
	 * @return \Sellastica\Connector\Entity\ISynchronizationItem
	 */
	public function add(
		int $statusCode = null,
		$internalId = null,
		string $remoteId = null,
		string $code = null,
		string $title = null,
		$description = null,
		$sourceData = null,
		$resultData = null
	): \Sellastica\Connector\Entity\ISynchronizationItem
	{
		if (is_array($description)) {
			$description = implode(PHP_EOL, array_filter($description));
		}

		$item = \Sellastica\Connector\Entity\MongoSynchronizationItemBuilder::create(
			$this->synchronizationId, new \DateTime()
		)->build();
		$item->setStatusCode($statusCode);
		$item->setInternalId($internalId);
		$item->setRemoteId($remoteId);
		$item->setCode($code);
		$item->setTitle($title);
		$item->setDescription($description);

		try {
			$item->setSourceData(isset($sourceData) ? serialize($sourceData) : null);
			$item->setResultData(isset($resultData) ? serialize($resultData) : null);
		} catch (\Exception $e) {
		}

		$this->items[] = $item;

		return $item;
	}

	/**
	 * @param \Sellastica\Connector\Model\ConnectorResponse $response
	 * @param int $count Used in batch synchronizations
	 */
	public function fromResponse(
		\Sellastica\Connector\Model\ConnectorResponse $response,
		int $count = 1
	): void
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
	 * @return \Sellastica\Connector\Entity\ISynchronizationItem
	 */
	public function notice($notice): \Sellastica\Connector\Entity\ISynchronizationItem
	{
		return $this->add(null, null, null, null, null, $notice);
	}

	/**
	 * @param string $lookupHistory
	 * @param string|null $identifier
	 */
	public function clearOldLogEntries(string $lookupHistory, string $identifier = null): void
	{
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