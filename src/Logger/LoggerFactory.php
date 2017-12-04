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
	public function __construct(
		EntityManager $em
	)
	{
		$this->em = $em;
	}

	/**
	 * @param int $synchronizationId
	 * @return Logger
	 */
	public function create(int $synchronizationId): Logger
	{
		return new Logger($synchronizationId, $this->em);
	}
}