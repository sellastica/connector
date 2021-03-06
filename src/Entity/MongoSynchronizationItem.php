<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Connector\Model\ConnectorResponse;

/**
 * @generate-builder
 * @see MongoSynchronizationItemBuilder
 *
 * @property MongoSynchronizationItemRelations $relationService
 */
class MongoSynchronizationItem extends \Sellastica\Entity\Entity\AbstractEntity
	implements \Sellastica\MongoDB\Entity\IMongoObject, \Sellastica\Entity\Entity\IEntity, ISynchronizationItem
{
	use \Sellastica\MongoDB\Model\THydrator;
	use \Sellastica\MongoDB\Entity\TMongoObject;

	/** @var \MongoDB\BSON\ObjectId @required */
	private $synchronizationId;
	/** @var \DateTime @required */
	private $dateTime;
	/** @var int|null @optional */
	private $statusCode;
	/** @var mixed|null @optional */
	private $internalId;
	/** @var string|null @optional */
	private $remoteId;
	/** @var string|null @optional */
	private $code;
	/** @var string|null @optional */
	private $title;
	/** @var string|null @optional */
	private $description;
	/** @var string|null @optional */
	private $sourceData;
	/** @var string|null @optional */
	private $resultData;


	/**
	 * @param iterable $data
	 */
	public function __construct(iterable $data)
	{
		$this->hydrate($data);
	}

	/**
	 * @return \MongoDB\BSON\ObjectId
	 */
	public function getSynchronizationId(): \MongoDB\BSON\ObjectId
	{
		return $this->synchronizationId;
	}

	/**
	 * @param \MongoDB\BSON\ObjectId $synchronizationId
	 */
	public function setSynchronizationId(\MongoDB\BSON\ObjectId $synchronizationId): void
	{
		$this->synchronizationId = $synchronizationId;
	}

	/**
	 * @return MongoSynchronization
	 */
	public function getSynchronization(): MongoSynchronization
	{
		return $this->relationService->getSynchronization();
	}

	/**
	 * @return int|null
	 */
	public function getStatusCode(): ?int
	{
		return $this->statusCode;
	}

	/**
	 * @param int|null $statusCode
	 */
	public function setStatusCode(?int $statusCode)
	{
		$this->statusCode = $statusCode;
	}

	/**
	 * @return string|null
	 */
	public function getStatusTitle(): ?string
	{
		switch ($this->statusCode) {
			case ConnectorResponse::SKIPPED:
				return 'core.connector.status.skipped';
				break;
			case ConnectorResponse::UPDATED:
				return 'core.connector.status.updated';
				break;
			case ConnectorResponse::CREATED:
				return 'core.connector.status.created';
				break;
			case ConnectorResponse::REMOVED:
				return 'core.connector.status.removed';
				break;
			case ConnectorResponse::BAD_REQUEST:
				return 'core.connector.status.bad_request';
				break;
			case ConnectorResponse::NOT_FOUND:
				return 'core.connector.status.not_found';
				break;
			case ConnectorResponse::INTERNAL_SERVER_ERROR:
				return 'core.connector.status.internal_server_error';
				break;
			default:
				break;
		}

		return null;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateTime(): \DateTime
	{
		return $this->dateTime;
	}

	/**
	 * @param \DateTime $dateTime
	 */
	public function setDateTime(\DateTime $dateTime)
	{
		$this->dateTime = $dateTime;
	}

	/**
	 * @return mixed|null
	 */
	public function getInternalId()
	{
		return $this->internalId;
	}

	/**
	 * @param $internalId
	 */
	public function setInternalId($internalId)
	{
		$this->internalId = $internalId;
	}

	/**
	 * @return null|string
	 */
	public function getRemoteId(): ?string
	{
		return $this->remoteId;
	}

	/**
	 * @param null|string $remoteId
	 */
	public function setRemoteId(?string $remoteId)
	{
		$this->remoteId = $remoteId;
	}

	/**
	 * @return null|string
	 */
	public function getCode(): ?string
	{
		return $this->code;
	}

	/**
	 * @param null|string $code
	 */
	public function setCode(?string $code)
	{
		$this->code = $code;
	}

	/**
	 * @return null|string
	 */
	public function getTitle(): ?string
	{
		return $this->title;
	}

	/**
	 * @param null|string $title
	 */
	public function setTitle(?string $title)
	{
		$this->title = $title;
	}

	/**
	 * @return null|string
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @param null|string $description
	 */
	public function setDescription(?string $description)
	{
		$this->description = $description;
	}

	/**
	 * @param bool $unserialize
	 * @return mixed
	 */
	public function getSourceData(bool $unserialize = true)
	{
		if (!isset($this->sourceData)) {
			return null;
		} else {
			return $unserialize
				? unserialize($this->sourceData)
				: $this->sourceData;
		}
	}

	/**
	 * @param null|string $sourceData
	 */
	public function setSourceData(?string $sourceData)
	{
		$this->sourceData = $sourceData;
	}

	/**
	 * @param bool $unserialize
	 * @return mixed
	 */
	public function getResultData(bool $unserialize = true)
	{
		if (!isset($this->resultData)) {
			return null;
		} else {
			return $unserialize
				? unserialize($this->resultData)
				: $this->resultData;
		}
	}

	/**
	 * @param null|string $resultData
	 */
	public function setResultData(?string $resultData)
	{
		$this->resultData = $resultData;
	}

	/**
	 * @return bool
	 */
	public function isNotice(): bool
	{
		return !$this->getInternalId()
			&& !$this->getRemoteId()
			&& !$this->getCode()
			&& $this->getDescription()
			&& $this->statusCode === null;
	}

	/**
	 * @param bool $filter
	 * @return array
	 */
	public function toArray(bool $filter = true): array
	{
		$array = array_merge(
			$this->mongoTraitToArray(),
			[
				'synchronizationId' => $this->synchronizationId,
				'statusCode' => $this->statusCode,
				'dateTime' => \Sellastica\MongoDB\Utils\DateTime::toUTCDateTime($this->dateTime),
				'internalId' => $this->internalId,
				'remoteId' => $this->remoteId,
				'code' => $this->code,
				'title' => $this->title,
				'description' => $this->description,
				'sourceData' => $this->sourceData,
				'resultData' => $this->resultData,
			]
		);
		return $filter
			? \Sellastica\Utils\Arrays::filterNulls($array)
			: $array;
	}
}