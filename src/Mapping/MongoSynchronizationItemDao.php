<?php
namespace Sellastica\Connector\Mapping;

/**
 * @see \Sellastica\Connector\Entity\MongoSynchronizationItem
 * @property MongoSynchronizationItemMapper $mapper
 */
class MongoSynchronizationItemDao extends \Sellastica\MongoDB\Mapping\MongoDao
{
	use \Sellastica\DataGrid\Mapping\MongoDB\TFilterRulesDao;


	/**
	 * @inheritDoc
	 */
	protected function getBuilder(
		$data,
		$first = null,
		$second = null
	): \Sellastica\Entity\IBuilder
	{
		return \Sellastica\Connector\Entity\MongoSynchronizationItemBuilder::create($data->synchronizationId, $data->dateTime)
			->hydrate($data);
	}

	/**
	 * @param \Sellastica\Entity\Entity\IEntity|\Sellastica\Connector\Entity\MongoSynchronizationItem $mongoSynchronizationItem
	 * @param \Sellastica\MongoDB\Model\BSONDocument $data
	 */
	protected function completeEntity(
		\Sellastica\Entity\Entity\IEntity $mongoSynchronizationItem,
		\Sellastica\MongoDB\Model\BSONDocument $data
	): void
	{
	}

	/**
	 * @return \Sellastica\Connector\Entity\MongoSynchronizationItemCollection
	 */
	public function getEmptyCollection(): \Sellastica\Entity\Entity\EntityCollection
	{
		return new \Sellastica\Connector\Entity\MongoSynchronizationItemCollection;
	}
}