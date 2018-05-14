<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Connector\Entity\Synchronization;
use Sellastica\Connector\Entity\SynchronizationBuilder;
use Sellastica\Connector\Entity\SynchronizationCollection;
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
	 * @param string $source
	 * @param string $target
	 * @return Synchronization|IEntity|null
	 */
	public function getLastDownload(
		string $application,
		string $identifier,
		string $source,
		string $target
	): ?Synchronization
	{
		return $this->find($this->mapper->getLastDownload($application, $identifier, $source, $target));
	}

	/**
	 * @param string $application
	 * @param string $identifier
	 * @param string $source
	 * @param string $target
	 * @param array|null $types
	 * @return \Sellastica\Connector\Entity\Synchronization|IEntity|null
	 */
	public function getLastUpload(
		string $application,
		string $identifier,
		string $source,
		string $target,
		array $types = null
	): ?Synchronization
	{
		return $this->find($this->mapper->getLastUpload($application, $identifier, $source, $target, $types));
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
			$data->source,
			$data->target,
			\Sellastica\Connector\Model\SynchronizationType::from($data->type),
			\Sellastica\Connector\Model\SynchronizationStatus::from($data->status)
		)->hydrate($data);
	}

	/**
	 * @return \Sellastica\Entity\Entity\EntityCollection|\Sellastica\Connector\Entity\SynchronizationCollection
	 */
	public function getEmptyCollection(): EntityCollection
	{
		return new SynchronizationCollection;
	}
}