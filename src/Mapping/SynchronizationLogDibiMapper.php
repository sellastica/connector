<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Connector\Entity\SynchronizationLog;
use Sellastica\DataGrid\Mapping\TFilterRulesDibiMapper;
use Sellastica\DataGrid\Model\FilterRuleCollection;
use Sellastica\Entity\Configuration;

/**
 * @see SynchronizationLog
 */
class SynchronizationLogDibiMapper extends \Sellastica\Entity\Mapping\DibiMapper
{
	use TFilterRulesDibiMapper;


	/**
	 * @param array $filterValues
	 * @return int
	 */
	public function getNumberOfRecordsBy(array $filterValues): int
	{
		return $this->database->select('COUNT(*)')
			->from('synchronization_log sl')
			->innerJoin('synchronization s')
			->on('s.id = sl.synchronizationId')
			->where($filterValues)
			->where('statusCode IS NOT NULL')
			->fetchSingle();
	}

	/**
	 * @param int $statusCode
	 * @param int $processId
	 * @param array $filterValues
	 * @return int
	 */
	public function getCountByStatusCodeAndProcessId(
		int $statusCode,
		int $processId,
		array $filterValues
	): int
	{
		return $this->database->select('COUNT(*)')
			->from('synchronization_log sl')
			->innerJoin('synchronization s')
			->on('s.id = sl.synchronizationId')
			->where($filterValues)
			//->where('s.processId = %i', $processId)
			//->where('sl.statusCode = %i', $statusCode)
			->fetchSingle();
	}

	/**
	 * @param int $processId
	 * @return int
	 */
	public function getNumberOfErrorsByProcessId(int $processId): int
	{
		return $this->database->select('COUNT(*)')
			->from('synchronization_log sl')
			->innerJoin('synchronization s')
			->on('s.id = sl.synchronizationId')
			->where('s.processId = %i', $processId)
			->where('sl.statusCode >= 400')
			->fetchSingle();
	}

	/**
	 * @param \DateTime $dateTime
	 * @return void
	 */
	public function clearOldLogEntries(\DateTime $dateTime)
	{
		$this->database->delete($this->getTableName(true))
			->where('dateTime < %t', $dateTime)
			->execute();
	}

	/**
	 * @param string $application
	 * @param string $identifier
	 * @param int $internalId
	 * @param \Sellastica\Entity\Configuration|null $configuration
	 * @return array
	 */
	public function findByInternalId(
		string $application,
		string $identifier,
		int $internalId,
		Configuration $configuration = null
	): array
	{
		$resource = $this->getResourceWithIds()
			->innerJoin('synchronization')->as('s')
			->on('s.id = %n.synchronizationId', $this->getTableName())
			->where('application = %s', $application)
			->where('identifier = %s', $identifier)
			->where('internalId = %i', $internalId);

		if ($configuration) {
			$this->applyConfiguration($resource);
		}

		return $resource->fetchPairs();
	}

	/**
	 * @param \Sellastica\Entity\Configuration $configuration
	 * @param FilterRuleCollection $rules
	 * @return \Dibi\Fluent
	 */
	protected function getAdminResource(
		Configuration $configuration = null,
		FilterRuleCollection $rules = null
	): \Dibi\Fluent
	{
		$resource = $this->database->select('%n.*', $this->getTableName())
			->from($this->getTableName())
			->innerJoin('synchronization s')
			->on('s.id = %n.synchronizationId', $this->getTableName());

		//display notices only if it is sorted by date
		if (!$configuration->getSorter()->isSortedBy('id')) {
			$resource->where('statusCode IS NOT NULL');
		}

		if (isset($rules)) {
			if ($rules['application']) {
				$resource->where('s.application = %s', $rules['application']->getValue());
			}

			if ($rules['success']) {
				if ($rules['success']->getValue()) {
					$resource->where('%n.statusCode < 400', $this->getTableName());
				} else {
					$resource->where('%n.statusCode >= 400', $this->getTableName());
				}
			}

			if ($rules['status_code']) {
				$resource->where('%n.statusCode = %i', $this->getTableName(), $rules['status_code']->getValue());
			}
		}

		return $resource;
	}
}