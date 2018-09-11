<?php
namespace Sellastica\Connector\Entity;

class MongoSynchronizationItemRelations implements \Sellastica\Entity\Relation\IEntityRelations
{
	/** @var MongoSynchronizationItem */
	private $item;
	/** @var \Sellastica\Entity\EntityManager */
	private $em;

	/**
	 * @param MongoSynchronizationItem $item
	 * @param \Sellastica\Entity\EntityManager $em
	 */
	public function __construct(
		MongoSynchronizationItem $item,
		\Sellastica\Entity\EntityManager $em
	)
	{
		$this->item = $item;
		$this->em = $em;
	}

	/**
	 * @return MongoSynchronization
	 */
	public function getSynchronization(): MongoSynchronization
	{
		return $this->em->getRepository(MongoSynchronization::class)->find($this->item->getSynchronizationId());
	}
}