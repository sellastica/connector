<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Entity\Entity\IEntity;
use Sellastica\Entity\EntityManager;
use Sellastica\Entity\Relation\IEntityRelations;

/**
 * @property \Sellastica\Connector\Entity\Synchronization $entity
 */
class SynchronizationRelations implements IEntityRelations
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
	 * @return bool
	 */
	public function isBreak(): bool
	{
		return (bool)$this->em->getRepository(\Sellastica\Connector\Entity\Synchronization::class)->findField($this->entity->getId(), 'break');
	}
}