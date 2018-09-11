<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Entity\Mapping\RepositoryProxy;
use Sellastica\Connector\Entity\IMongoSynchronizationItemRepository;
use Sellastica\Connector\Entity\MongoSynchronizationItem;

/**
 * @method MongoSynchronizationItemRepository getRepository()
 * @see MongoSynchronizationItem
 */
class MongoSynchronizationItemRepositoryProxy extends RepositoryProxy implements IMongoSynchronizationItemRepository
{
	use \Sellastica\DataGrid\Mapping\MongoDB\TFilterRulesRepositoryProxy;
}