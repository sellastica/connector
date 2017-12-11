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
	 * @param int $synchronizationId
	 */
	function setProcessId(int $synchronizationId);

	/**
	 * @param int $limit
	 * @param int $offset
	 * @param array $params
	 * @return EntityCollection
	 */
	function getAll(int $limit, int $offset, array $params = []): EntityCollection;

	/**
	 * @param int $limit
	 * @param int $offset
	 * @param array $params
	 * @return EntityCollection
	 */
	function getChanges(int $limit, int $offset, array $params = []): EntityCollection;
}
