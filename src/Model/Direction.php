<?php
namespace Sellastica\Connector\Model;

class Direction
{
	const DOWN = 'down',
		UP = 'up';

	/** @var string */
	private $direction;


	/**
	 * @param string $direction
	 */
	private function __construct(string $direction)
	{
		$this->direction = $direction;
	}

	/**
	 * @return string
	 */
	public function getDirection(): string
	{
		return $this->direction;
	}

	/**
	 * @return string
	 * @throws \UnexpectedValueException
	 */
	public function getTitle(): string
	{
		switch ($this->direction) {
			case self::DOWN:
				return 'core.connector.direction.down';
				break;
			case self::UP:
				return 'core.connector.direction.up';
				break;
			default:
				throw new \UnexpectedValueException();
				break;
		}
	}

	/**
	 * @return Direction
	 */
	public static function down(): Direction
	{
		return new self(self::DOWN);
	}

	/**
	 * @return Direction
	 */
	public static function up(): Direction
	{
		return new self(self::UP);
	}

	/**
	 * @param string $direction
	 * @return Direction
	 * @throws \InvalidArgumentException
	 */
	public static function from(string $direction): Direction
	{
		$rc = new \ReflectionClass(self::class);
		if (!in_array($direction, $rc->getConstants())) {
			throw new \InvalidArgumentException(sprintf('Invalid direction "%s"', $direction));
		}

		return new self($direction);
	}
}