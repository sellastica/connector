<?php
namespace Sellastica\Connector\Model;

use Sellastica\Connector\Logger\Logger;

interface IDownloadDataHandler
{
	function initialize(): void;

	/**
	 * @param int $synchronizationId
	 */
	function setProcessId(int $synchronizationId);

	/**
	 * @param \Sellastica\Connector\Logger\Logger $logger
	 */
	function setLogger(Logger $logger);

	/**
	 * @param $data
	 * @param \DateTime|null $sinceWhen
	 * @param array $params
	 * @return \Sellastica\Connector\Model\ConnectorResponse
	 */
	function modify($data, ?\DateTime $sinceWhen, array $params = []): \Sellastica\Connector\Model\ConnectorResponse;

	/**
	 * @param string $externalId
	 * @return \Sellastica\Connector\Model\ConnectorResponse
	 */
	function remove(string $externalId): \Sellastica\Connector\Model\ConnectorResponse;
}
