<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Entity\Configuration;
use Sellastica\Entity\Mapping\IRepository;

/**
 * @method MongoSynchronizationItem find(int $id)
 * @method MongoSynchronizationItem findOneBy(array $filterValues)
 * @method MongoSynchronizationItem findOnePublishableBy(array $filterValues, Configuration $configuration = null)
 * @method MongoSynchronizationItem[]|MongoSynchronizationItemCollection findAll(Configuration $configuration = null)
 * @method MongoSynchronizationItem[]|MongoSynchronizationItemCollection findBy(array $filterValues, Configuration $configuration = null)
 * @method MongoSynchronizationItem[]|MongoSynchronizationItemCollection findByIds(array $idsArray, Configuration $configuration = null)
 * @method MongoSynchronizationItem[]|MongoSynchronizationItemCollection findPublishable(int $id)
 * @method MongoSynchronizationItem[]|MongoSynchronizationItemCollection findAllPublishable(Configuration $configuration = null)
 * @method MongoSynchronizationItem[]|MongoSynchronizationItemCollection findPublishableBy(array $filterValues, Configuration $configuration = null)
 * @see MongoSynchronizationItem
 */
interface IMongoSynchronizationItemRepository extends IRepository
{
}