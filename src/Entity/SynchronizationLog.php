<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Connector\Model\ConnectorResponse;
use Sellastica\Entity\Entity\AbstractEntity;
use Sellastica\Entity\Entity\IEntity;
use Sellastica\Entity\Entity\TAbstractEntity;

/**
 * @generate-builder
 * @see SynchronizationLogBuilder
 *
 * @property SynchronizationLogRelations $relationService
 */
class SynchronizationLog extends AbstractEntity
{
	use TAbstractEntity;

	/** @var int @required */
	private $synchronizationId;
	/** @var \DateTime @required */
	private $dateTime;
	/** @var int|null @optional */
	private $statusCode;
	/** @var int|null @optional */
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
	 * @param \Sellastica\Connector\Entity\SynchronizationLogBuilder $builder
	 */
	public function __construct(\Sellastica\Connector\Entity\SynchronizationLogBuilder $builder)
	{
		$this->hydrate($builder);
	}

	/**
	 * @return int
	 */
	public function getAggregateId(): int
	{
		return $this->synchronizationId;
	}

	/**
	 * @return string
	 */
	public function getAggregateRootClass(): string
	{
		return Synchronization::class;
	}

	/**
	 * @return bool
	 */
	public static function isIdGeneratedByStorage(): bool
	{
		return true;
	}

	/**
	 * @return int
	 */
	public function getSynchronizationId(): int
	{
		return $this->synchronizationId;
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
	 * @return int|null
	 */
	public function getInternalId(): ?int
	{
		return $this->internalId;
	}

	/**
	 * @param int|null $internalId
	 */
	public function setInternalId(?int $internalId)
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
	 * @return null|string
	 */
	public function getSourceData(): ?string
	{
		return $this->sourceData;
	}

	/**
	 * @param null|string $sourceData
	 */
	public function setSourceData(?string $sourceData)
	{
		$this->sourceData = $sourceData;
	}

	/**
	 * @return null|string
	 */
	public function getResultData(): ?string
	{
		return $this->resultData;
	}

	/**
	 * @param null|string $resultData
	 */
	public function setResultData(?string $resultData)
	{
		$this->resultData = $resultData;
	}

	/**
	 * @return Synchronization
	 */
	public function getSynchronization(): Synchronization
	{
		return $this->relationService->getSynchronization();
	}

	/**
	 * @return \Sellastica\Entity\Entity\IEntity|null
	 */
	public function getEntity(): ?IEntity
	{
		return $this->relationService->getEntity();
	}

	/**
	 * @return bool
	 */
	public function isNotice(): bool 
	{
		return !$this->getInternalId()
			&& !$this->getRemoteId()
			&& !$this->getCode()
			&& $this->getDescription();
	}

	/**
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'synchronizationId' => $this->synchronizationId,
			'statusCode' => $this->statusCode,
			'dateTime' => $this->dateTime,
			'internalId' => $this->internalId,
			'remoteId' => $this->remoteId,
			'code' => $this->code,
			'title' => $this->title,
			'description' => $this->description,
			'sourceData' => $this->sourceData,
			'resultData' => $this->resultData,
		];
	}
}