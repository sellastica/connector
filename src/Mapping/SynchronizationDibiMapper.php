<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Connector\Entity\Synchronization;
use Sellastica\DataGrid\Mapping\TFilterRulesDibiMapper;
use Sellastica\DataGrid\Model\FilterRuleCollection;
use Sellastica\Entity\Configuration;
use Sellastica\Entity\Mapping\DibiMapper;

/**
 * @see Synchronization
 */
class SynchronizationDibiMapper extends DibiMapper
{
	use TFilterRulesDibiMapper;

	/**
	 * @param string $application
	 * @param string $identifier
	 * @param string $source
	 * @param string $target
	 * @return int|null
	 */
	public function getLastDownload(
		string $application,
		string $identifier,
		string $source,
		string $target
	): ?int
	{
		return $this->getResourceWithIds()
			->where('application = %s', $application)
			->where('source = %s', $source)
			->where('target = %s', $target)
			->where('identifier = %s', $identifier)
			->orderBy('id', \dibi::DESC)
			->fetchSingle();
	}

	/**
	 * @param string $application
	 * @param string $identifier
	 * @param string $source
	 * @param string $target
	 * @param array|null $types
	 * @return int|null
	 */
	public function getLastUpload(
		string $application,
		string $identifier,
		string $source,
		string $target,
		array $types = null
	): ?int
	{
		$resource = $this->getResourceWithIds()
			->where('application = %s', $application)
			->where('source = %s', $source)
			->where('target = %s', $target)
			->where('identifier = %s', $identifier)
			->orderBy('id', \dibi::DESC);

		if (isset($types)) {
			$resource->where('type IN (%sN)', $types);
		}

		return $resource->fetchSingle();
	}

	/**
	 * @param Configuration $configuration
	 * @param FilterRuleCollection $rules
	 * @return \Dibi\Fluent
	 */
	protected function getAdminResource(
		Configuration $configuration = null,
		FilterRuleCollection $rules = null
	): \Dibi\Fluent
	{
		$resource = parent::getAdminResource($configuration)
			->leftJoin('synchronization_log')
			->on('synchronization_log.synchronizationId = %n.id', $this->getTableName());

		if (isset($rules)) {
			if ($rules['application']) {
				$resource->where('application = %s', $rules['application']->getValue());
			}
		}

		return $resource;
	}

	/**
	 * @param \DateTime $dateTime
	 * @param string|null $identifier
	 */
	public function clearOldLogEntries(\DateTime $dateTime, string $identifier = null): void
	{
		$resource = $this->database->delete($this->getTableName(true))
			->where('created < %t', $dateTime);

		if ($identifier) {
			$resource->where('identifier = %s', $identifier);
		}

		$resource->execute();
	}
}