<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Connector\Entity\SynchronizationLogBuilder;
use Sellastica\Connector\Entity\SynchronizationLogCollection;
use Sellastica\DataGrid\Mapping\TFilterRulesDao;
use Sellastica\Entity\Entity\EntityCollection;
use Sellastica\Entity\IBuilder;
use Sellastica\Entity\Mapping\Dao;

/**
 * @see \Sellastica\Connector\Entity\SynchronizationLog
 * @property \Sellastica\Connector\Mapping\SynchronizationLogDibiMapper $mapper
 */
class SynchronizationLogDao extends Dao
{
	use TFilterRulesDao;


	/**
	 * @param array $filterValues
	 * @return int
	 */
	public function getNumberOfRecordsBy(array $filterValues): int
	{
		return $this->mapper->getNumberOfRecordsBy($filterValues);
	}

	/**
	 * @param \DateTime $dateTime
	 * @return void
	 */
	public function clearOldLogEntries(\DateTime $dateTime)
	{
		$this->mapper->clearOldLogEntries($dateTime);
	}

	/**
	 * @param string $application
	 * @param string $identifier
	 * @param int $internalId
	 * @param \Sellastica\Entity\Configuration|null $configuration
	 * @return SynchronizationLogCollection|EntityCollection
	 */
	public function findByInternalId(
		string $application,
		string $identifier,
		int $internalId,
		\Sellastica\Entity\Configuration $configuration = null
	): SynchronizationLogCollection
	{
		return $this->getEntitiesFromCacheOrStorage(
			$this->mapper->findByInternalId($application, $identifier, $internalId, $configuration)
		);
	}

	/**
	 * @inheritDoc
	 */
	protected function getBuilder(
		$data,
		$first = null,
		$second = null
	): IBuilder
	{
		return SynchronizationLogBuilder::create($data->synchronizationId, $data->dateTime)
			->hydrate($data);
	}

	/**
	 * @return \Sellastica\Entity\Entity\EntityCollection|\Sellastica\Connector\Entity\SynchronizationLogCollection
	 */
	public function getEmptyCollection(): EntityCollection
	{
		return new SynchronizationLogCollection;
	}
}