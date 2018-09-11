<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Entity\Mapping\RepositoryProxy;
use Sellastica\Connector\Entity\IMongoSynchronizationRepository;
use Sellastica\Connector\Entity\MongoSynchronization;

/**
 * @method MongoSynchronizationRepository getRepository()
 * @see MongoSynchronization
 */
class MongoSynchronizationRepositoryProxy extends RepositoryProxy implements IMongoSynchronizationRepository
{
	use \Sellastica\DataGrid\Mapping\MongoDB\TFilterRulesRepositoryProxy;
}