<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Connector\Entity\ISynchronizationLogRepository;
use Sellastica\DataGrid\Mapping\Dibi\TFilterRulesRepository;

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
	 * @param string $application
	 * @param string $identifier
	 * @param int $internalId
	 * @param \Sellastica\Entity\Configuration|null $configuration
	 * @return \Sellastica\Connector\Entity\SynchronizationLogCollection
	 */
	public function findByInternalId(
		string $application,
		string $identifier,
		int $internalId,
		\Sellastica\Entity\Configuration $configuration = null
	): \Sellastica\Connector\Entity\SynchronizationLogCollection
	{
		return $this->initialize(
			$this->dao->findByInternalId($application, $identifier, $internalId, $configuration)
		);
	}
}