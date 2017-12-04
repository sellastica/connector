<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Connector\Entity\ISynchronizationRepository;
use Sellastica\Connector\Entity\Synchronization;
use Sellastica\DataGrid\Mapping\TFilterRulesRepository;
use Sellastica\Entity\Mapping\Repository;

/**
 * @property \Sellastica\Connector\Mapping\SynchronizationDao $dao
 * @see \Sellastica\Connector\Entity\Synchronization
 */
class SynchronizationRepository extends Repository implements ISynchronizationRepository
{
	use TFilterRulesRepository;

	/**
	 * @param string $identifier
	 * @return \Sellastica\Connector\Entity\Synchronization|null
	 */
	public function getLastDownload(string $application, string $identifier): ?Synchronization
	{
		return $this->initialize($this->dao->getLastDownload($application, $identifier));
	}

	/**
	 * @param string $identifier
	 * @param array|null $types
	 * @return \Sellastica\Connector\Entity\Synchronization|null
	 */
	public function getLastUpload(string $application, string $identifier, array $types = null): ?Synchronization
	{
		return $this->initialize($this->dao->getLastUpload($application, $identifier, $types));
	}
}