<?php
namespace Sellastica\Connector\Logger;

use Sellastica\Entity\EntityManager;

class LoggerFactory
{
	/** @var \Sellastica\Entity\EntityManager */
	private $em;


	/**
	 * @param \Sellastica\Entity\EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	/**
	 * @param int $synchronizationId
	 * @return ILogger
	 */
	public function create(int $synchronizationId): ILogger
	{
		return new Logger($synchronizationId, $this->em);
	}

	/**
	 * @param \MongoDB\BSON\ObjectId $synchronizationId
	 * @return ILogger
	 */
	public function createMongoLogger(\MongoDB\BSON\ObjectId $synchronizationId): ILogger
	{
		return new MongoLogger($synchronizationId, $this->em);
	}
}