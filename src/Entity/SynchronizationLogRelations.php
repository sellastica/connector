<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Entity\Entity\IEntity;
use Sellastica\Entity\EntityManager;
use Sellastica\Entity\Relation\IEntityRelations;

/**
 * @property \Sellastica\Connector\Entity\SynchronizationLog $entity
 */
class SynchronizationLogRelations implements IEntityRelations
{
	/** @var IEntity */
	private $entity;
	/** @var EntityManager */
	private $em;


	/**
	 * @param IEntity $entity
	 * @param EntityManager $em
	 */
	public function __construct(
		IEntity $entity,
		EntityManager $em
	)
	{
		$this->entity = $entity;
		$this->em = $em;
	}

	/**
	 * @return Synchronization
	 */
	public function getSynchronization(): Synchronization
	{
		return $this->em->getRepository(Synchronization::class)->find($this->entity->getSynchronizationId());
	}

	/**
	 * @return \Sellastica\Entity\Entity\IEntity|null
	 */
	public function getEntity(): ?IEntity
	{
		if ($this->getSynchronization()->getIdentifier()->getEntityClassName()) {
			return $this->em->getRepository($this->getSynchronization()->getIdentifier()->getEntityClassName())
				->find($this->entity->getInternalId());
		} else {
			return null;
		}
	}
}