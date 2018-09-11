<?php
namespace Sellastica\Connector\Logger;

interface ILogger
{
	/**
	 * @param int $statusCode
	 * @param $internalId
	 * @param string|null $remoteId
	 * @param string|null $code
	 * @param string|null $title
	 * @param array|string|null $description
	 * @param string|null $sourceData
	 * @param string|null $resultData
	 * @return \Sellastica\Connector\Entity\ISynchronizationItem
	 */
	function add(
		int $statusCode = null,
		$internalId = null,
		string $remoteId = null,
		string $code = null,
		string $title = null,
		$description = null,
		$sourceData = null,
		$resultData = null
	): \Sellastica\Connector\Entity\ISynchronizationItem;

	/**
	 * @param \Sellastica\Connector\Model\ConnectorResponse $response
	 * @param int $count Used in batch synchronizations
	 */
	function fromResponse(
		\Sellastica\Connector\Model\ConnectorResponse $response,
		int $count = 1
	): void;

	/**
	 * @param \Throwable $e
	 */
	function fromException(\Throwable $e): void;

	/**
	 * @param string|array $notice
	 * @return \Sellastica\Connector\Entity\ISynchronizationItem
	 */
	function notice($notice): \Sellastica\Connector\Entity\ISynchronizationItem;

	/**
	 * @param string $lookupHistory
	 * @param string|null $identifier
	 */
	function clearOldLogEntries(string $lookupHistory, string $identifier = null): void;

	function save(): void;
}