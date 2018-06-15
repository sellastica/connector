<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Connector\Entity\ISynchronizationRepository;
use Sellastica\Connector\Entity\Synchronization;
use Sellastica\Entity\Mapping\Repository;

/**
 * @property \Sellastica\Connector\Mapping\SynchronizationDao $dao
 * @see \Sellastica\Connector\Entity\Synchronization
 */
class SynchronizationRepository extends Repository implements ISynchronizationRepository
{
	use \Sellastica\DataGrid\Mapping\Dibi\TFilterRulesRepository;

	/**
	 * @param string $application
	 * @param string $identifier
	 * @param string $source
	 * @param string $target
	 * @return \Sellastica\Connector\Entity\Synchronization|null
	 */
	public function getLastDownload(
		string $application,
		string $identifier,
		string $source,
		string $target
	): ?Synchronization
	{
		return $this->initialize($this->dao->getLastDownload($application, $identifier, $source, $target));
	}

	/**
	 * @param string $application
	 * @param string $identifier
	 * @param string $source
	 * @param string $target
	 * @param array|null $types
	 * @return \Sellastica\Connector\Entity\Synchronization|null
	 */
	public function getLastUpload(
		string $application,
		string $identifier,
		string $source,
		string $target,
		array $types = null
	): ?Synchronization
	{
		return $this->initialize($this->dao->getLastUpload($application, $identifier, $source, $target, $types));
	}

	/**
	 * @param \DateTime $dateTime
	 * @param string|null $identifier
	 * @return void
	 */
	public function clearOldLogEntries(\DateTime $dateTime, string $identifier = null): void
	{
		$this->dao->clearOldLogEntries($dateTime, $identifier);
	}
}