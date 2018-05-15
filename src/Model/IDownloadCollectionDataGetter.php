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
	 * @param OptionsRequest|null $params
	 * @return DownloadResponse
	 */
	function getAll(int $limit, $offset, OptionsRequest $params = null): DownloadResponse;

	/**
	 * @param int $limit
	 * @param mixed $offset
	 * @param \DateTime $sinceWhen
	 * @param OptionsRequest|null $params
	 * @return DownloadResponse
	 */
	function getModified(int $limit, $offset, ?\DateTime $sinceWhen, OptionsRequest $params = null): DownloadResponse;

	/**
	 * @param $id
	 * @param OptionsRequest|null $params
	 * @return DownloadResponse
	 * @throws \Sellastica\Connector\Exception\IErpConnectorException If item not found
	 */
	function getOne($id, OptionsRequest $params = null): DownloadResponse;
}
