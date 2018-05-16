<?php
namespace Sellastica\Connector\Model;

class SynchronizationStatus
{
	const PENDING = 'pending',
		RUNNING = 'running',
		SUCCESS = 'success',
		FAIL = 'fail',
		INTERRUPTED = 'interrupted';

	/** @var string */
	private $status;


	/**
	 * @param string $status
	 */
	private function __construct(string $status)
	{
		$this->status = $status;
	}

	/**
	 * @return string
	 */
	public function getStatus(): string
	{
		return $this->status;
	}

	/**
	 * @return string
	 * @throws \UnexpectedValueException
	 */
	public function getTitle(): string
	{
		switch ($this->status) {
			case self::PENDING:
				return 'core.connector.status.pending';
				break;
			case self::RUNNING:
				return 'core.connector.status.running';
				break;
			case self::SUCCESS:
				return 'core.connector.status.success';
				break;
			case self::FAIL:
				return 'core.connector.status.fail';
				break;
			case self::INTERRUPTED:
				return 'core.connector.status.interrupted';
				break;
			default:
				throw new \UnexpectedValueException();
				break;
		}
	}

	/**
	 * @return bool
	 */
	public function isPending(): bool
	{
		return $this->status === self::PENDING;
	}

	/**
	 * @return bool
	 */
	public function isRunning(): bool
	{
		return $this->status === self::RUNNING;
	}

	/**
	 * @return bool
	 */
	public function isSuccess(): bool
	{
		return $this->status === self::SUCCESS;
	}

	/**
	 * @return bool
	 */
	public function isFail(): bool
	{
		return $this->status === self::FAIL;
	}

	/**
	 * @return bool
	 */
	public function isInterrupted(): bool
	{
		return $this->status === self::INTERRUPTED;
	}

	/**
	 * @param SynchronizationStatus $status
	 * @return bool
	 */
	public function equals(SynchronizationStatus $status): bool
	{
		return $this->status === $status->getStatus();
	}

	/**
	 * @return SynchronizationStatus
	 */
	public static function pending(): SynchronizationStatus
	{
		return new self(self::PENDING);
	}

	/**
	 * @return SynchronizationStatus
	 */
	public static function running(): SynchronizationStatus
	{
		return new self(self::RUNNING);
	}

	/**
	 * @return SynchronizationStatus
	 */
	public static function success(): SynchronizationStatus
	{
		return new self(self::SUCCESS);
	}

	/**
	 * @return SynchronizationStatus
	 */
	public static function fail(): SynchronizationStatus
	{
		return new self(self::FAIL);
	}

	/**
	 * @return SynchronizationStatus
	 */
	public static function interrupted(): SynchronizationStatus
	{
		return new self(self::INTERRUPTED);
	}

	/**
	 * @param string $status
	 * @return SynchronizationStatus
	 * @throws \InvalidArgumentException
	 */
	public static function from(string $status): SynchronizationStatus
	{
		$rc = new \ReflectionClass(self::class);
		if (!in_array($status, $rc->getConstants())) {
			throw new \InvalidArgumentException(sprintf('Invalid status "%s"', $status));
		}

		return new self($status);
	}
}