<?php
namespace Sellastica\Connector\Model;

use Sellastica\Connector\Entity\SynchronizationLog;
use Sellastica\Entity\EntityManager;

class LogSummary
{
	/** @var int|null */
	private $synchronizationId;
	/** @var int|null */
	private $processId;
	/** @var EntityManager */
	private $em;


	/**
	 * @param int|null $synchronizationId
	 * @param int|null $processId
	 * @param EntityManager $em
	 */
	public function __construct(
		int $synchronizationId = null,
		int $processId = null,
		EntityManager $em
	)
	{
		$this->synchronizationId = $synchronizationId;
		$this->processId = $processId;
		$this->em = $em;
	}

	/**
	 * @param int $statusCode
	 * @return int
	 */
	public function getCountByStatusCode(int $statusCode): int
	{
		return $this->em->getRepository(SynchronizationLog::class)
			->getNumberOfRecordsBy($this->getConditions($statusCode));
	}

	/**
	 * @return int
	 */
	public function getNumberOfErrors(): int
	{
		return $this->em->getRepository(SynchronizationLog::class)
			->getNumberOfRecordsBy($this->getConditions() + ['statusCode >= 400']);
	}

	/**
	 * @return int
	 */
	public function getTotalCount(): int
	{
		return $this->em->getRepository(SynchronizationLog::class)
			->getNumberOfRecordsBy($this->getConditions());
	}

	/**
	 * @param int|null $statusCode
	 * @return array
	 */
	private function getConditions(int $statusCode = null): array
	{
		$conditions = [];
		if (isset($statusCode)) {
			$conditions['statusCode'] = $statusCode;
		}

		if (isset($this->synchronizationId)) {
			$conditions['synchronizationId'] = $this->synchronizationId;
		}

		if (isset($this->processId)) {
			$conditions['processId'] = $this->processId;
		}

		return $conditions;
	}
}
