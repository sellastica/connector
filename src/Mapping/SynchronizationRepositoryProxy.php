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

	public function getLastDownload(
		string $application,
		string $identifier,
		string $source,
		string $target
	): ?Synchronization
	{
		return $this->getRepository()->getLastDownload($application, $identifier, $source, $target);
	}

	public function getLastUpload(
		string $application,
		string $identifier,
		string $source,
		string $target,
		array $types = null
	): ?Synchronization
	{
		return $this->getRepository()->getLastUpload($application, $identifier, $source, $target, $types);
	}
}