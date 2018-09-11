<?php
namespace Sellastica\Connector\Mapping;

/**
 * @see \Sellastica\Connector\Entity\MongoSynchronization
 * @property MongoSynchronizationMapper $mapper
 */
class MongoSynchronizationDao extends \Sellastica\MongoDB\Mapping\MongoDao
{
	use \Sellastica\DataGrid\Mapping\MongoDB\TFilterRulesDao;

	/** @var \Sellastica\Connector\Model\IIdentifierFactory */
	private $identifierFactory;


	/**
	 * @param \Sellastica\Entity\Mapping\IMapper $mapper
	 * @param \Sellastica\Entity\Entity\EntityFactory $entityFactory
	 * @param \Sellastica\Connector\Model\IIdentifierFactory $identifierFactory
	 */
	public function __construct(
		\Sellastica\Entity\Mapping\IMapper $mapper,
		\Sellastica\Entity\Entity\EntityFactory $entityFactory,
		\Sellastica\Connector\Model\IIdentifierFactory $identifierFactory
	)
	{
		parent::__construct($mapper, $entityFactory);
		$this->identifierFactory = $identifierFactory;
	}

	/**
	 * @inheritDoc
	 */
	protected function getBuilder(
		$data,
		$first = null,
		$second = null
	): \Sellastica\Entity\IBuilder
	{
		$data->params = !empty($data->params) ? unserialize($data->params) : [];
		return \Sellastica\Connector\Entity\MongoSynchronizationBuilder::create(
			$data->processId,
			$data->application,
			$this->identifierFactory->create($data->identifier),
			$data->source,
			$data->target,
			\Sellastica\Connector\Model\SynchronizationType::from($data->type),
			\Sellastica\Connector\Model\SynchronizationStatus::from($data->status)
		)->hydrate($data);
	}

	/**
	 * @param \Sellastica\Entity\Entity\IEntity|\Sellastica\Connector\Entity\MongoSynchronization $mongoSynchronization
	 * @param \Sellastica\MongoDB\Model\BSONDocument $data
	 */
	protected function completeEntity(
		\Sellastica\Entity\Entity\IEntity $mongoSynchronization,
		\Sellastica\MongoDB\Model\BSONDocument $data
	): void
	{
	}

	/**
	 * @return \Sellastica\Connector\Entity\MongoSynchronizationCollection
	 */
	public function getEmptyCollection(): \Sellastica\Entity\Entity\EntityCollection
	{
		return new \Sellastica\Connector\Entity\MongoSynchronizationCollection;
	}
}