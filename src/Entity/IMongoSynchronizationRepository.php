<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Entity\Configuration;
use Sellastica\Entity\Mapping\IRepository;

/**
 * @method MongoSynchronization find(int $id)
 * @method MongoSynchronization findOneBy(array $filterValues)
 * @method MongoSynchronization findOnePublishableBy(array $filterValues, Configuration $configuration = null)
 * @method MongoSynchronization[]|MongoSynchronizationCollection findAll(Configuration $configuration = null)
 * @method MongoSynchronization[]|MongoSynchronizationCollection findBy(array $filterValues, Configuration $configuration = null)
 * @method MongoSynchronization[]|MongoSynchronizationCollection findByIds(array $idsArray, Configuration $configuration = null)
 * @method MongoSynchronization[]|MongoSynchronizationCollection findPublishable(int $id)
 * @method MongoSynchronization[]|MongoSynchronizationCollection findAllPublishable(Configuration $configuration = null)
 * @method MongoSynchronization[]|MongoSynchronizationCollection findPublishableBy(array $filterValues, Configuration $configuration = null)
 * @see MongoSynchronization
 */
interface IMongoSynchronizationRepository extends IRepository
{
}