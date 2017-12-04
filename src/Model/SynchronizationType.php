<?php
namespace Sellastica\Connector\Model;

class SynchronizationType
{
	const FULL = 'full',
		BATCH = 'batch',
		SINGLE = 'single';

	/** @var string */
	private $type;


	/**
	 * @param string $type
	 */
	private function __construct(string $type)
	{
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @return string
	 * @throws \UnexpectedValueException
	 */
	public function getTitle(): string
	{
		switch ($this->type) {
			case self::FULL:
				return 'core.connector.type.full';
				break;
			case self::BATCH:
				return 'core.connector.type.batch';
				break;
			case self::SINGLE:
				return 'core.connector.type.single';
				break;
			default:
				throw new \UnexpectedValueException();
				break;
		}
	}

	/**
	 * @return SynchronizationType
	 */
	public static function full(): SynchronizationType
	{
		return new self(self::FULL);
	}

	/**
	 * @return SynchronizationType
	 */
	public static function batch(): SynchronizationType
	{
		return new self(self::BATCH);
	}

	/**
	 * @return SynchronizationType
	 */
	public static function single(): SynchronizationType
	{
		return new self(self::SINGLE);
	}

	/**
	 * @param string $type
	 * @return SynchronizationType
	 * @throws \InvalidArgumentException
	 */
	public static function from(string $type): SynchronizationType
	{
		$rc = new \ReflectionClass(self::class);
		if (!in_array($type, $rc->getConstants())) {
			throw new \InvalidArgumentException(sprintf('Invalid type "%s"', $type));
		}

		return new self($type);
	}
}