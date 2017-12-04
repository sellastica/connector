<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Connector\Entity\ISynchronizationLogRepository;
use Sellastica\DataGrid\Mapping\TFilterRulesRepository;

/**
 * @property \Sellastica\Connector\Mapping\SynchronizationLogDao $dao
 * @see \Sellastica\Connector\Entity\SynchronizationLog
 */
class SynchronizationLogRepository extends \Sellastica\Entity\Mapping\Repository implements ISynchronizationLogRepository
{
	use TFilterRulesRepository;


	/**
	 * @param array $filterValues
	 * @return int
	 */
	public function getNumberOfRecordsBy(array $filterValues): int
	{
		return $this->dao->getNumberOfRecordsBy($filterValues);
	}

	/**
	 * @param \DateTime $dateTime
	 * @return void
	 */
	public function clearOldLogEntries(\DateTime $dateTime)
	{
		$this->dao->clearOldLogEntries($dateTime);
	}
}