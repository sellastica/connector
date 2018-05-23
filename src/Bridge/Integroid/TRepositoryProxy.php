<?php
namespace Sellastica\Connector\Bridge\Integroid;

/**
 * @method \Sellastica\Entity\Mapping\IRepository getRepository()
 */
trait TRepositoryProxy
{
	public function findModifiedItems(
		string $column,
		\Sellastica\Entity\Configuration $configuration = null
	): \Sellastica\Entity\Entity\EntityCollection
	{
		return $this->getRepository()->findModifiedItems($column, $configuration);
	}

	public function findLastModifiedItem(string $column): ?\Sellastica\Entity\Entity\IEntity
	{
		return $this->getRepository()->findLastModifiedItem($column);
	}
}