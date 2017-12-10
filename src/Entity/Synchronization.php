<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Connector\Model\Direction;
use Sellastica\Connector\Model\LogSummary;
use Sellastica\Connector\Model\SynchronizationType;
use Sellastica\Entity\Entity\AbstractEntity;
use Sellastica\Entity\Entity\IAggregateRoot;
use Sellastica\Entity\Entity\TAbstractEntity;

/**
 * @generate-builder
 * @see SynchronizationBuilder
 *
 * @property SynchronizationRelations $relationService
 */
class Synchronization extends AbstractEntity implements IAggregateRoot
{
	use TAbstractEntity;

	/** @var int @required */
	private $processId;
	/** @var string @required */
	private $application;
	/** @var \Sellastica\Connector\Model\IIdentifier @required */
	private $identifier;
	/** @var \Sellastica\Connector\Model\Direction @required */
	private $direction;
	/** @var \Sellastica\Connector\Model\SynchronizationType @required */
	private $type;
	/** @var \DateTime|null @optional */
	private $changesSince;
	/** @var array @optional */
	private $params = [];
	/** @var \DateTime|null @optional */
	private $start;
	/** @var \DateTime|null @optional */
	private $end;
	/** @var bool|null @optional */
	private $success;
	/** @var bool @optional */
	private $manual = false;
	/** @var bool @optional */
	private $break = false;
	/** @var LogSummary */
	private $logSummary;


	/**
	 * @param \Sellastica\Connector\Entity\SynchronizationBuilder $builder
	 */
	public function __construct(\Sellastica\Connector\Entity\SynchronizationBuilder $builder)
	{
		$this->hydrate($builder);
	}

	/**
	 * @param LogSummary $logSummary
	 */
	public function doInitialize(LogSummary $logSummary)
	{
		$this->logSummary = $logSummary;
	}

	/**
	 * @return LogSummary
	 */
	public function getLogSummary(): LogSummary
	{
		return $this->logSummary;
	}

	/**
	 * @return string
	 */
	public function getApplication(): string
	{
		return $this->application;
	}

	/**
	 * @param string $application
	 */
	public function setApplication(string $application)
	{
		$this->application = $application;
	}

	/**
	 * @return \Sellastica\Connector\Model\IIdentifier
	 */
	public function getIdentifier(): \Sellastica\Connector\Model\IIdentifier
	{
		return $this->identifier;
	}

	/**
	 * @param \Sellastica\Connector\Model\IIdentifier $identifier
	 */
	public function setIdentifier(\Sellastica\Connector\Model\IIdentifier $identifier)
	{
		$this->identifier = $identifier;
	}

	/**
	 * @return \Sellastica\Connector\Model\Direction
	 */
	public function getDirection(): Direction
	{
		return $this->direction;
	}

	/**
	 * @param \Sellastica\Connector\Model\Direction $direction
	 */
	public function setDirection(Direction $direction)
	{
		$this->direction = $direction;
	}

	/**
	 * @return \Sellastica\Connector\Model\SynchronizationType
	 */
	public function getType(): SynchronizationType
	{
		return $this->type;
	}

	/**
	 * @param SynchronizationType $type
	 */
	public function setType(SynchronizationType $type)
	{
		$this->type = $type;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getChangesSince(): ?\DateTime
	{
		return $this->changesSince;
	}

	/**
	 * @param \DateTime|null $changesSince
	 */
	public function setChangesSince(?\DateTime $changesSince)
	{
		$this->changesSince = $changesSince;
	}

	/**
	 * @return array
	 */
	public function getParams(): array
	{
		return $this->params;
	}

	/**
	 * @param array $params
	 */
	public function setParams(array $params)
	{
		$this->params = $params;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getStart(): ?\DateTime
	{
		return $this->start;
	}

	public function start()
	{
		$this->start = new \DateTime();
	}

	/**
	 * @return \DateTime|null
	 */
	public function getEnd(): ?\DateTime
	{
		return $this->end;
	}

	public function end()
	{
		$this->end = new \DateTime();
	}

	/**
	 * @return \DateInterval|null
	 */
	public function getDuration(): ?\DateInterval
	{
		if (!$this->start || !$this->end) {
			return null;
		}

		return $this->end->diff($this->start);
	}

	/**
	 * @return bool|null
	 */
	public function getSuccess(): ?bool
	{
		return $this->success;
	}

	/**
	 * @param bool $success
	 */
	public function setSuccess(?bool $success)
	{
		$this->success = $success;
	}

	/**
	 * @return bool
	 */
	public function getManual(): bool
	{
		return $this->manual;
	}

	/**
	 * @param bool $manual
	 */
	public function setManual(bool $manual)
	{
		$this->manual = $manual;
	}

	/**
	 * @return bool
	 */
	public function isBreak(): bool
	{
		return $this->relationService->isBreak();
	}

	/**
	 * @param bool $break
	 */
	public function setBreak(bool $break)
	{
		$this->break = $break;
	}

	/**
	 * @return array
	 */
	public function toArray(): array
	{
		return array_merge($this->parentToArray(), [
			'application' => $this->application,
			'identifier' => $this->identifier->getCode(),
			'processId' => $this->processId,
			'direction' => $this->direction->getDirection(),
			'type' => $this->type->getType(),
			'changesSince' => $this->changesSince,
			'params' => $this->params ? serialize($this->params) : null,
			'start' => $this->start,
			'end' => $this->end,
			'success' => $this->success,
			'manual' => $this->manual,
		]);
	}
}