<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Connector\Entity\Synchronization;
use Sellastica\Connector\Model\Direction;
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
	 * @return int|null
	 */
	public function getLastDownload(string $application, string $identifier): ?int
	{
		return $this->getResourceWithIds()
			->where('application = %s', $application)
			->where('direction = %s', Direction::DOWN)
			->where('identifier = %s', $identifier)
			->orderBy('id', \dibi::DESC)
			->fetchSingle();
	}

	/**
	 * @param string $application
	 * @param string $identifier
	 * @param array|null $types
	 * @return int|null
	 */
	public function getLastUpload(string $application, string $identifier, array $types = null): ?int
	{
		$resource = $this->getResourceWithIds()
			->where('application = %s', $application)
			->where('direction = %s', Direction::UP)
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
		$resource = parent::getAdminResource($configuration);
		if (isset($rules)) {
			if ($rules['application']) {
				$resource->where('application = %s', $rules['application']->getValue());
			}
		}

		return $resource;
	}
}