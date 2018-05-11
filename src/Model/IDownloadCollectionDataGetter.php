<?php
namespace Sellastica\Connector\Model;

interface IDownloadCollectionDataGetter
{
	/**
	 * Source indentifier
	 * @return string
	 */
	function getSource(): string;

	/**
	 * @param int $limit
	 * @param mixed $offset
	 * @param array $params
	 * @return \Sellastica\Connector\Model\DownloadResponse
	 */
	function getAll(int $limit, $offset, $params = null): \Sellastica\Connector\Model\DownloadResponse;

	/**
	 * @param int $limit
	 * @param mixed $offset
	 * @param \DateTime $sinceWhen
	 * @param array $params
	 * @return \Sellastica\Connector\Model\DownloadResponse
	 */
	function getModified(int $limit, $offset, ?\DateTime $sinceWhen, $params = null): \Sellastica\Connector\Model\DownloadResponse;

	/**
	 * @param int $synchronizationId
	 */
	function setProcessId(int $synchronizationId);
}
