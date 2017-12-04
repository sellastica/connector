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
	protected function getEmptyCollection(): EntityCollection
	{
		return new SynchronizationLogCollection;
	}
}