<?php
namespace Sellastica\Connector\Bridge\Integroid;

/**
 * @method \Sellastica\Entity\Entity\EntityCollection getEntitiesFromCacheOrStorage(array $idsArray, $first = null, $second = null)
 * @method \Sellastica\Entity\Entity\IEntity|null find($id)
 * @property \Sellastica\Entity\Mapping\IMapper $mapper
 */
trait TDao
{
	/**
	 * @param string $column
	 * @param \Sellastica\Entity\Configuration|null $configuration
	 * @return \Sellastica\Entity\Entity\EntityCollection
	 */
	public function findModifiedItems(
		string $column,
		\Sellastica\Entity\Configuration $configuration = null
	): \Sellastica\Entity\Entity\EntityCollection
	{
		return $this->getEntitiesFromCacheOrStorage(
			$this->mapper->findModifiedItems($column, $configuration)
		);
	}

	/**
	 * @param string $column
	 * @return \Sellastica\Entity\Entity\IEntity|null
	 */
	public function findLastModifiedItem(string $column): ?\Sellastica\Entity\Entity\IEntity
	{
		return $this->find($this->mapper->findLastModifiedItem($column));
	}
}