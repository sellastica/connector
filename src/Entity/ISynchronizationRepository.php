<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Entity\Configuration;
use Sellastica\Entity\Mapping\IRepository;

/**
 * @method Synchronization find(int $id)
 * @method Synchronization findOneBy(array $filterValues)
 * @method Synchronization findOnePublishableBy(array $filterValues, Configuration $configuration = null)
 * @method Synchronization[]|SynchronizationCollection findAll(Configuration $configuration = null)
 * @method Synchronization[]|SynchronizationCollection findBy(array $filterValues, Configuration $configuration = null)
 * @method Synchronization[]|SynchronizationCollection findByIds(array $idsArray, Configuration $configuration = null)
 * @method Synchronization[]|SynchronizationCollection findPublishable(int $id)
 * @method Synchronization[]|SynchronizationCollection findAllPublishable(Configuration $configuration = null)
 * @method Synchronization[]|SynchronizationCollection findPublishableBy(array $filterValues, Configuration $configuration = null)
 * @see Synchronization
 */
interface ISynchronizationRepository extends IRepository
{
	/**
	 * @param string $application
	 * @param string $identifier
	 * @param string $source
	 * @param string $target
	 * @return Synchronization|null
	 */
	function getLastDownload(
		string $application,
		string $identifier,
		string $source,
		string $target
	): ?Synchronization;

	/**
	 * @param string $application
	 * @param string $identifier
	 * @param string $source
	 * @param string $target
	 * @param array|null $types
	 * @return Synchronization|null
	 */
	function getLastUpload(
		string $application,
		string $identifier,
		string $source,
		string $target,
		array $types = null
	): ?Synchronization;
}