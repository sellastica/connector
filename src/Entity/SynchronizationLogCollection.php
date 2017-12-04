<?php
namespace Sellastica\Connector\Entity;

use Sellastica\Entity\Entity\EntityCollection;

/**
 * @property SynchronizationLog[] $items
 * @method SynchronizationLogCollection add($entity)
 * @method SynchronizationLogCollection remove($key)
 * @method SynchronizationLog|mixed getEntity(int $entityId, $default = null)
 * @method SynchronizationLog|mixed getBy(string $property, $value, $default = null)
 */
class SynchronizationLogCollection extends EntityCollection
{
}