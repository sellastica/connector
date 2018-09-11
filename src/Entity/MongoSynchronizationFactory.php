<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Entity\IBuilder;
use Sellastica\Entity\Entity\IEntity;
use Sellastica\Entity\Entity\EntityFactory;

/**
 * @method MongoSynchronization build(IBuilder $builder, bool $initialize = true, int $assignedId = null)
 * @see MongoSynchronization
 */
class MongoSynchronizationFactory extends EntityFactory
{
	/**
	 * @param IEntity|MongoSynchronization $entity
	 */
	public function doInitialize(IEntity $entity)
	{
	}

	/**
	 * @return string
	 */
	public function getEntityClass(): string
	{
		return MongoSynchronization::class;
	}
}