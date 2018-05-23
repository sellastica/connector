<?php
namespace Sellastica\Connector\Bridge\Integroid;

/**
 * @method initialize($entities, $first = null, $second = null)
 * @property \Sellastica\Entity\Mapping\IDao $dao
 */
trait TRepository
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
		return $this->initialize(
			$this->dao->findModifiedItems($column, $configuration)
		);
	}

	/**
	 * @param string $column
	 * @return \Sellastica\Entity\Entity\IEntity|null
	 */
	public function findLastModifiedItem(string $column): ?\Sellastica\Entity\Entity\IEntity
	{
		return $this->initialize($this->dao->findLastModifiedItem($column));
	}
}