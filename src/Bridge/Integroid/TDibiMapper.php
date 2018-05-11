<?php
namespace Sellastica\Connector\Bridge\Integroid;

/**
 * @method \Dibi\Fluent getResourceWithIds(\Sellastica\Entity\Configuration $configuration = null)
 */
trait TDibiMapper
{
	/**
	 * @param string $column
	 * @return array
	 */
	public function findModifiedItems(string $column): array
	{
		return $this->getResourceWithIds()
			->where('modified > %n', $column)
			->fetchPairs();
	}

	/**
	 * @param string $column
	 * @return int|false
	 */
	public function findLastModifiedItem(string $column)
	{
		return $this->getResourceWithIds()
			->orderBy($column, \dibi::DESC)
			->fetchSingle();
	}
}