<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Connector\Entity\ISynchronizationLogRepository;
use Sellastica\DataGrid\Mapping\Dibi\TFilterRulesRepositoryProxy;
use Sellastica\Entity\Mapping\RepositoryProxy;

/**
 * @method \Sellastica\Connector\Mapping\SynchronizationLogRepository getRepository()
 * @see \Sellastica\Connector\Entity\SynchronizationLog
 */
class SynchronizationLogRepositoryProxy extends RepositoryProxy implements ISynchronizationLogRepository
{
	use TFilterRulesRepositoryProxy;


	public function getNumberOfRecordsBy(array $filterValues): int
	{
		return $this->getRepository()->getNumberOfRecordsBy($filterValues);
	}

	public function findByInternalId(
		string $application,
		string $identifier,
		int $internalId,
		\Sellastica\Entity\Configuration $configuration = null
	): \Sellastica\Connector\Entity\SynchronizationLogCollection
	{
		return $this->getRepository()->findByInternalId($application, $identifier, $internalId, $configuration);
	}
}