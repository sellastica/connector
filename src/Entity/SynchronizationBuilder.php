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
	/** @var \Sellastica\Connector\Model\Direction */
	private $direction;
	/** @var \Sellastica\Connector\Model\SynchronizationType */
	private $type;
	/** @var \DateTime|null */
	private $changesSince;
	/** @var array */
	private $params = [];
	/** @var \DateTime|null */
	private $start;
	/** @var \DateTime|null */
	private $end;
	/** @var bool|null */
	private $success;
	/** @var bool */
	private $manual = false;
	/** @var bool */
	private $break = false;

	/**
	 * @param int $processId
	 * @param string $application
	 * @param \Sellastica\Connector\Model\IIdentifier $identifier
	 * @param \Sellastica\Connector\Model\Direction $direction
	 * @param \Sellastica\Connector\Model\SynchronizationType $type
	 */
	public function __construct(
		int $processId,
		string $application,
		\Sellastica\Connector\Model\IIdentifier $identifier,
		\Sellastica\Connector\Model\Direction $direction,
		\Sellastica\Connector\Model\SynchronizationType $type
	)
	{
		$this->processId = $processId;
		$this->application = $application;
		$this->identifier = $identifier;
		$this->direction = $direction;
		$this->type = $type;
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
	 * @return \Sellastica\Connector\Model\Direction
	 */
	public function getDirection(): \Sellastica\Connector\Model\Direction
	{
		return $this->direction;
	}

	/**
	 * @return \Sellastica\Connector\Model\SynchronizationType
	 */
	public function getType(): \Sellastica\Connector\Model\SynchronizationType
	{
		return $this->type;
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
	 * @return array
	 */
	public function getParams(): array
	{
		return $this->params;
	}

	/**
	 * @param array $params
	 * @return $this
	 */
	public function params(array $params)
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
	 * @return bool|null
	 */
	public function getSuccess()
	{
		return $this->success;
	}

	/**
	 * @param bool|null $success
	 * @return $this
	 */
	public function success(bool $success = null)
	{
		$this->success = $success;
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
	public function getBreak(): bool
	{
		return $this->break;
	}

	/**
	 * @param bool $break
	 * @return $this
	 */
	public function break(bool $break)
	{
		$this->break = $break;
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
	 * @param \Sellastica\Connector\Model\Direction $direction
	 * @param \Sellastica\Connector\Model\SynchronizationType $type
	 * @return self
	 */
	public static function create(
		int $processId,
		string $application,
		\Sellastica\Connector\Model\IIdentifier $identifier,
		\Sellastica\Connector\Model\Direction $direction,
		\Sellastica\Connector\Model\SynchronizationType $type
	): self
	{
		return new self($processId, $application, $identifier, $direction, $type);
	}
}