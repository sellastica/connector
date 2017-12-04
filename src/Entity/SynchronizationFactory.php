<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Connector\Model\ILogSummaryFactory;
use Sellastica\Entity\Entity\EntityFactory;
use Sellastica\Entity\Entity\IEntity;
use Sellastica\Entity\EntityManager;
use Sellastica\Entity\Event\IDomainEventPublisher;
use Sellastica\Entity\IBuilder;

/**
 * @method Synchronization build(IBuilder $builder, bool $initialize = true, int $assignedId = null)
 * @see Synchronization
 */
class SynchronizationFactory extends EntityFactory
{
	/** @var \Sellastica\Connector\Model\ILogSummaryFactory */
	private $logSummaryFactory;


	/**
	 * @param EntityManager $em
	 * @param \Sellastica\Entity\Event\IDomainEventPublisher $eventPublisher
	 * @param \Sellastica\Connector\Model\ILogSummaryFactory $logSummaryFactory
	 */
	public function __construct(
		EntityManager $em,
		IDomainEventPublisher $eventPublisher,
		ILogSummaryFactory $logSummaryFactory
	)
	{
		parent::__construct($em, $eventPublisher);
		$this->logSummaryFactory = $logSummaryFactory;
	}

	/**
	 * @param \Sellastica\Entity\Entity\IEntity|Synchronization $entity
	 */
	public function doInitialize(IEntity $entity)
	{
		$entity->setRelationService(new SynchronizationRelations($entity, $this->em));
		$entity->doInitialize($this->logSummaryFactory->create($entity->getId()));
	}

	/**
	 * @return string
	 */
	public function getEntityClass(): string
	{
		return Synchronization::class;
	}
}