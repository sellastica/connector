<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Connector\Entity\ISynchronizationRepository;
use Sellastica\Connector\Entity\Synchronization;
use Sellastica\DataGrid\Mapping\TFilterRulesRepositoryProxy;

/**
 * @method \Sellastica\Connector\Mapping\SynchronizationRepository getRepository()
 * @see Synchronization
 */
class SynchronizationRepositoryProxy extends \Sellastica\Entity\Mapping\RepositoryProxy implements ISynchronizationRepository
{
	use TFilterRulesRepositoryProxy;

	public function getLastDownload(string $application, string $identifier): ?Synchronization
	{
		return $this->getRepository()->getLastDownload($application, $identifier);
	}

	public function getLastUpload(string $application, string $identifier, array $types = null): ?Synchronization
	{
		return $this->getRepository()->getLastUpload($application, $identifier, $types);
	}
}