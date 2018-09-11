<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Entity\Entity\EntityCollection;

/**
 * @property MongoSynchronizationItem[] $items
 * @method MongoSynchronizationItemCollection add($entity)
 * @method MongoSynchronizationItemCollection remove($key)
 * @method MongoSynchronizationItem|mixed getEntity(int $entityId, $default = null)
 * @method MongoSynchronizationItem|mixed getBy(string $property, $value, $default = null)
 */
class MongoSynchronizationItemCollection extends EntityCollection
{
}