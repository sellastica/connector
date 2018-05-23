<?php
namespace Sellastica\Connector\Bridge\Integroid;

/**
 * @method \Dibi\Fluent getResourceWithIds(\Sellastica\Entity\Configuration $configuration = null)
 */
trait TDibiMapper
{
	/**
	 * @param string $column
	 * @param \Sellastica\Entity\Configuration|null $configuration
	 * @return array
	 */
	public function findModifiedItems(string $column, \Sellastica\Entity\Configuration $configuration = null): array
	{
		$resource = $this->getResourceWithIds()
			->where('modified > %n', $column);

		if ($configuration) {
			$this->applyConfiguration($resource, $configuration);
		}

		return $resource->fetchPairs();
	}

	/**
	 * @param string $column
	 * @return int|false
	 */
	public function findLastModifiedItem(string $column)
	{
		return $this->getResourceWithIds()
			->orderBy($column, \dibi::DESC)
			->fetchSingle();
	}
}