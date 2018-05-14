<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Entity\IBuilder;
use Sellastica\Entity\TBuilder;

/**
 * @see Synchronization
 */
class SynchronizationBuilder implements IBuilder
{
	use TBuilder;

	/** @var int */
	private $processId;
	/** @var string */
	private $application;
	/** @var \Sellastica\Connector\Model\IIdentifier */
	private $identifier;
	/** @var string */
	private $source;
	/** @var string */
	private $target;
	/** @var \Sellastica\Connector\Model\SynchronizationType */
	private $type;
	/** @var \Sellastica\Connector\Model\SynchronizationStatus */
	private $status;
	/** @var \DateTime|null */
	private $changesSince;
	/** @var mixed */
	private $params;
	/** @var \DateTime|null */
	private $start;
	/** @var \DateTime|null */
	private $end;
	/** @var bool */
	private $manual = false;

	/**
	 * @param int $processId
	 * @param string $application
	 * @param \Sellastica\Connector\Model\IIdentifier $identifier
	 * @param string $source
	 * @param string $target
	 * @param \Sellastica\Connector\Model\SynchronizationType $type
	 * @param \Sellastica\Connector\Model\SynchronizationStatus $status
	 */
	public function __construct(
		int $processId,
		string $application,
		\Sellastica\Connector\Model\IIdentifier $identifier,
		string $source,
		string $target,
		\Sellastica\Connector\Model\SynchronizationType $type,
		\Sellastica\Connector\Model\SynchronizationStatus $status
	)
	{
		$this->processId = $processId;
		$this->application = $application;
		$this->identifier = $identifier;
		$this->source = $source;
		$this->target = $target;
		$this->type = $type;
		$this->status = $status;
	}

	/**
	 * @return int
	 */
	public function getProcessId(): int
	{
		return $this->processId;
	}

	/**
	 * @return string
	 */
	public function getApplication(): string
	{
		return $this->application;
	}

	/**
	 * @return \Sellastica\Connector\Model\IIdentifier
	 */
	public function getIdentifier(): \Sellastica\Connector\Model\IIdentifier
	{
		return $this->identifier;
	}

	/**
	 * @return string
	 */
	public function getSource(): string
	{
		return $this->source;
	}

	/**
	 * @return string
	 */
	public function getTarget(): string
	{
		return $this->target;
	}

	/**
	 * @return \Sellastica\Connector\Model\SynchronizationType
	 */
	public function getType(): \Sellastica\Connector\Model\SynchronizationType
	{
		return $this->type;
	}

	/**
	 * @return \Sellastica\Connector\Model\SynchronizationStatus
	 */
	public function getStatus(): \Sellastica\Connector\Model\SynchronizationStatus
	{
		return $this->status;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getChangesSince()
	{
		return $this->changesSince;
	}

	/**
	 * @param \DateTime|null $changesSince
	 * @return $this
	 */
	public function changesSince(\DateTime $changesSince = null)
	{
		$this->changesSince = $changesSince;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @param mixed $params
	 * @return $this
	 */
	public function params($params)
	{
		$this->params = $params;
		return $this;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getStart()
	{
		return $this->start;
	}

	/**
	 * @param \DateTime|null $start
	 * @return $this
	 */
	public function start(\DateTime $start = null)
	{
		$this->start = $start;
		return $this;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getEnd()
	{
		return $this->end;
	}

	/**
	 * @param \DateTime|null $end
	 * @return $this
	 */
	public function end(\DateTime $end = null)
	{
		$this->end = $end;
		return $this;
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
	 * @return $this
	 */
	public function manual(bool $manual)
	{
		$this->manual = $manual;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function generateId(): bool
	{
		return !Synchronization::isIdGeneratedByStorage();
	}

	/**
	 * @return Synchronization
	 */
	public function build(): Synchronization
	{
		return new Synchronization($this);
	}

	/**
	 * @param int $processId
	 * @param string $application
	 * @param \Sellastica\Connector\Model\IIdentifier $identifier
	 * @param string $source
	 * @param string $target
	 * @param \Sellastica\Connector\Model\SynchronizationType $type
	 * @param \Sellastica\Connector\Model\SynchronizationStatus $status
	 * @return self
	 */
	public static function create(
		int $processId,
		string $application,
		\Sellastica\Connector\Model\IIdentifier $identifier,
		string $source,
		string $target,
		\Sellastica\Connector\Model\SynchronizationType $type,
		\Sellastica\Connector\Model\SynchronizationStatus $status
	): self
	{
		return new self($processId, $application, $identifier, $source, $target, $type, $status);
	}
}