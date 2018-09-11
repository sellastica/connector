<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Entity\Mapping\Repository;
use Sellastica\Connector\Entity\MongoSynchronizationItem;
use Sellastica\Connector\Entity\IMongoSynchronizationItemRepository;

/**
 * @property MongoSynchronizationItemDao $dao
 * @see MongoSynchronizationItem
 */
class MongoSynchronizationItemRepository extends Repository implements IMongoSynchronizationItemRepository
{
	use \Sellastica\DataGrid\Mapping\MongoDB\TFilterRulesRepository;
}