<?php
namespace Sellastica\Connector\Mapping;

/**
 * @see \Sellastica\Connector\Entity\MongoSynchronization
 */
class MongoSynchronizationMapper extends \Sellastica\MongoDB\Mapping\MongoMapper
{
	use \Sellastica\DataGrid\Mapping\MongoDB\TFilterRulesMongoMapper;

	/**
	 * @return \MongoDB\Collection
	 */
	protected function getCollectionName(): string
	{
		return '_synchronization_log';
	}
}