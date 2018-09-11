<?php
namespace Sellastica\Connector\Mapping;

use Sellastica\Entity\Mapping\Repository;
use Sellastica\Connector\Entity\MongoSynchronization;
use Sellastica\Connector\Entity\IMongoSynchronizationRepository;

/**
 * @property MongoSynchronizationDao $dao
 * @see MongoSynchronization
 */
class MongoSynchronizationRepository extends Repository implements IMongoSynchronizationRepository
{
	use \Sellastica\DataGrid\Mapping\MongoDB\TFilterRulesRepository;
}