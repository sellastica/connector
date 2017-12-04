<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Entity\Configuration;
use Sellastica\Entity\Mapping\IRepository;

/**
 * @method SynchronizationLog find(int $id)
 * @method SynchronizationLog findOneBy(array $filterValues)
 * @method SynchronizationLog findOnePublishableBy(array $filterValues, Configuration $configuration = null)
 * @method SynchronizationLog[]|SynchronizationLogCollection findAll(Configuration $configuration = null)
 * @method SynchronizationLog[]|SynchronizationLogCollection findBy(array $filterValues, Configuration $configuration = null)
 * @method SynchronizationLog[]|SynchronizationLogCollection findByIds(array $idsArray, Configuration $configuration = null)
 * @method SynchronizationLog[]|SynchronizationLogCollection findPublishable(int $id)
 * @method SynchronizationLog[]|SynchronizationLogCollection findAllPublishable(Configuration $configuration = null)
 * @method SynchronizationLog[]|SynchronizationLogCollection findPublishableBy(array $filterValues, Configuration $configuration = null)
 * @see SynchronizationLog
 */
interface ISynchronizationLogRepository extends IRepository
{
	/**
	 * @param array $filterValues
	 * @return int
	 */
	function getNumberOfRecordsBy(array $filterValues): int;

	/**
	 * @param \DateTime $dateTime
	 * @return void
	 */
	function clearOldLogEntries(\DateTime $dateTime);
}