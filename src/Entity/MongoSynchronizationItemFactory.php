<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Entity\IBuilder;
use Sellastica\Entity\Entity\IEntity;
use Sellastica\Entity\Entity\EntityFactory;

/**
 * @method MongoSynchronizationItem build(IBuilder $builder, bool $initialize = true, int $assignedId = null)
 * @see MongoSynchronizationItem
 */
class MongoSynchronizationItemFactory extends EntityFactory
{
	/**
	 * @param IEntity|MongoSynchronizationItem $entity
	 */
	public function doInitialize(IEntity $entity)
	{
		$entity->setRelationService(new MongoSynchronizationItemRelations($entity, $this->em));
	}

	/**
	 * @return string
	 */
	public function getEntityClass(): string
	{
		return MongoSynchronizationItem::class;
	}
}