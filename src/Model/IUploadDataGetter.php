<?php
namespace Sellastica\Connector\Model;

use Sellastica\Entity\Entity\EntityCollection;

interface IUploadDataGetter
{
	/**
	 * Source indentifier
	 * @return string
	 */
	function getSource(): string;

	/**
	 * @param int $limit
	 * @param int $offset
	 * @param OptionsRequest|null $params
	 * @return EntityCollection
	 */
	function getAll(int $limit, int $offset, OptionsRequest $params = null): EntityCollection;

	/**
	 * @param int $limit
	 * @param int $offset
	 * @param OptionsRequest|null $params
	 * @return EntityCollection
	 */
	function getChanges(int $limit, int $offset, OptionsRequest $params = null): EntityCollection;
}
