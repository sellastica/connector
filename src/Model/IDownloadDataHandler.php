<?php
namespace Sellastica\Connector\Model;

interface IDownloadDataHandler
{
	function initialize(): void;

	/**
	 * Target indentifier
	 * @return string
	 */
	function getTarget(): string;

	/**
	 * @param \Sellastica\Connector\Logger\ILogger $logger
	 */
	function setLogger(\Sellastica\Connector\Logger\ILogger $logger);

	/**
	 * @param $data
	 * @param \DateTime|null $sinceWhen
	 * @param OptionsRequest|null $params
	 * @return ConnectorResponse
	 */
	function modify($data, ?\DateTime $sinceWhen, OptionsRequest $params = null): ConnectorResponse;

	/**
	 * @param string $externalId
	 * @return ConnectorResponse
	 */
	function remove(string $externalId): ConnectorResponse;
}
