<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Entity\Entity\EntityCollection;

/**
 * @property MongoSynchronization[] $items
 * @method MongoSynchronizationCollection add($entity)
 * @method MongoSynchronizationCollection remove($key)
 * @method MongoSynchronization|mixed getEntity(int $entityId, $default = null)
 * @method MongoSynchronization|mixed getBy(string $property, $value, $default = null)
 */
class MongoSynchronizationCollection extends EntityCollection
{
}