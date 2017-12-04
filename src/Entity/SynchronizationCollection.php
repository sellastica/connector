<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Entity\Entity\EntityCollection;

/**
 * @property Synchronization[] $items
 * @method SynchronizationCollection add($entity)
 * @method SynchronizationCollection remove($key)
 * @method Synchronization|mixed getEntity(int $entityId, $default = null)
 * @method Synchronization|mixed getBy(string $property, $value, $default = null)
 */
class SynchronizationCollection extends EntityCollection
{
}