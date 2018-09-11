<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Entity\IBuilder;
use Sellastica\Entity\TBuilder;

/**
 * @see MongoSynchronizationItem
 */
class MongoSynchronizationItemBuilder implements IBuilder
{
	use TBuilder;

	/** @var \MongoDB\BSON\ObjectId */
	private $synchronizationId;
	/** @var \DateTime */
	private $dateTime;
	/** @var int|null */
	private $statusCode;
	/** @var mixed|null */
	private $internalId;
	/** @var string|null */
	private $remoteId;
	/** @var string|null */
	private $code;
	/** @var string|null */
	private $title;
	/** @var string|null */
	private $description;
	/** @var string|null */
	private $sourceData;
	/** @var string|null */
	private $resultData;

	/**
	 * @param \MongoDB\BSON\ObjectId $synchronizationId
	 * @param \DateTime $dateTime
	 */
	public function __construct(
		\MongoDB\BSON\ObjectId $synchronizationId,
		\DateTime $dateTime
	)
	{
		$this->synchronizationId = $synchronizationId;
		$this->dateTime = $dateTime;
	}

	/**
	 * @return \MongoDB\BSON\ObjectId
	 */
	public function getSynchronizationId(): \MongoDB\BSON\ObjectId
	{
		return $this->synchronizationId;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateTime(): \DateTime
	{
		return $this->dateTime;
	}

	/**
	 * @return int|null
	 */
	public function getStatusCode()
	{
		return $this->statusCode;
	}

	/**
	 * @param int|null $statusCode
	 * @return $this
	 */
	public function statusCode(int $statusCode = null)
	{
		$this->statusCode = $statusCode;
		return $this;
	}

	/**
	 * @return mixed|null
	 */
	public function getInternalId()
	{
		return $this->internalId;
	}

	/**
	 * @param mixed|null $internalId
	 * @return $this
	 */
	public function internalId($internalId = null)
	{
		$this->internalId = $internalId;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getRemoteId()
	{
		return $this->remoteId;
	}

	/**
	 * @param string|null $remoteId
	 * @return $this
	 */
	public function remoteId(string $remoteId = null)
	{
		$this->remoteId = $remoteId;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param string|null $code
	 * @return $this
	 */
	public function code(string $code = null)
	{
		$this->code = $code;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param string|null $title
	 * @return $this
	 */
	public function title(string $title = null)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string|null $description
	 * @return $this
	 */
	public function description(string $description = null)
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getSourceData()
	{
		return $this->sourceData;
	}

	/**
	 * @param string|null $sourceData
	 * @return $this
	 */
	public function sourceData(string $sourceData = null)
	{
		$this->sourceData = $sourceData;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getResultData()
	{
		return $this->resultData;
	}

	/**
	 * @param string|null $resultData
	 * @return $this
	 */
	public function resultData(string $resultData = null)
	{
		$this->resultData = $resultData;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function generateId(): bool
	{
		return !MongoSynchronizationItem::isIdGeneratedByStorage();
	}

	/**
	 * @return MongoSynchronizationItem
	 */
	public function build(): MongoSynchronizationItem
	{
		return new MongoSynchronizationItem($this->toArray());
	}

	/**
	 * @param \MongoDB\BSON\ObjectId $synchronizationId
	 * @param \DateTime $dateTime
	 * @return self
	 */
	public static function create(
		\MongoDB\BSON\ObjectId $synchronizationId,
		\DateTime $dateTime
	): self
	{
		return new self($synchronizationId, $dateTime);
	}
}