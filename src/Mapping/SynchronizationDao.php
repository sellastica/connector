<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Connector\Entity\Synchronization;
use Sellastica\Connector\Entity\SynchronizationBuilder;
use Sellastica\Connector\Entity\SynchronizationCollection;
use Sellastica\Connector\Model\Direction;
use Sellastica\Connector\Model\SynchronizationType;
use Sellastica\DataGrid\Mapping\TFilterRulesDao;
use Sellastica\Entity\Entity\EntityCollection;
use Sellastica\Entity\Entity\IEntity;
use Sellastica\Entity\IBuilder;

/**
 * @see Synchronization
 * @property \Sellastica\Connector\Mapping\SynchronizationDibiMapper $mapper
 */
class SynchronizationDao extends \Sellastica\Entity\Mapping\Dao
{
	use TFilterRulesDao;
	/** @var \Sellastica\Connector\Model\IIdentifierFactory */
	private $identifierFactory;


	/**
	 * @param \Sellastica\Entity\Mapping\IMapper $mapper
	 * @param \Sellastica\Entity\Entity\EntityFactory $entityFactory
	 * @param \Nette\Caching\IStorage $storage
	 * @param \Sellastica\Connector\Model\IIdentifierFactory $identifierFactory
	 */
	public function __construct(
		\Sellastica\Entity\Mapping\IMapper $mapper,
		\Sellastica\Entity\Entity\EntityFactory $entityFactory,
		\Nette\Caching\IStorage $storage,
		\Sellastica\Connector\Model\IIdentifierFactory $identifierFactory
	)
	{
		parent::__construct($mapper, $entityFactory, $storage);
		$this->identifierFactory = $identifierFactory;
	}

	/**
	 * @param string $application
	 * @param string $identifier
	 * @return Synchronization|IEntity|null
	 */
	public function getLastDownload(string $application, string $identifier): ?Synchronization
	{
		return $this->find($this->mapper->getLastDownload($application, $identifier));
	}

	/**
	 * @param string $application
	 * @param string $identifier
	 * @param array|null $types
	 * @return \Sellastica\Connector\Entity\Synchronization|IEntity|null
	 */
	public function getLastUpload(string $application, string $identifier, array $types = null): ?Synchronization
	{
		return $this->find($this->mapper->getLastUpload($application, $identifier, $types));
	}

	/**
	 * @inheritDoc
	 */
	protected function getBuilder(
		$data,
		$first = null,
		$second = null
	): IBuilder
	{
		$data->params = $data->params ? unserialize($data->params) : [];
		return SynchronizationBuilder::create(
			$data->processId,
			$data->application,
			$this->identifierFactory->create($data->identifier),
			Direction::from($data->direction),
			SynchronizationType::from($data->type)
		)->hydrate($data);
	}

	/**
	 * @return \Sellastica\Entity\Entity\EntityCollection|\Sellastica\Connector\Entity\SynchronizationCollection
	 */
	protected function getEmptyCollection(): EntityCollection
	{
		return new SynchronizationCollection;
	}
}