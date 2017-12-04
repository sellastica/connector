<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Entity\Entity\EntityFactory;
use Sellastica\Entity\Entity\IEntity;
use Sellastica\Entity\IBuilder;

/**
 * @method SynchronizationLog build(IBuilder $builder, bool $initialize = true, int $assignedId = null)
 * @see SynchronizationLog
 */
class SynchronizationLogFactory extends EntityFactory
{
	/**
	 * @param \Sellastica\Entity\Entity\IEntity|SynchronizationLog $entity
	 */
	public function doInitialize(IEntity $entity)
	{
		$entity->setRelationService(new SynchronizationLogRelations($entity, $this->em));
	}

	/**
	 * @return string
	 */
	public function getEntityClass(): string
	{
		return SynchronizationLog::class;
	}
}