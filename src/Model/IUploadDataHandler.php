<?php
namespace Sellastica\Connector\Model;

use Nette\NotImplementedException;
use Sellastica\Entity\Entity\EntityCollection;

interface IUploadDataHandler
{
	/**
	 * Target indentifier
	 * @return string
	 */
	function getTarget(): string;

	/**
	 * @param int $processId
	 */
	function setProcessId(int $processId);

	/**
	 * @param $data
	 * @param \DateTime|null $sinceWhen
	 * @param array $params
	 * @return \Sellastica\Connector\Model\ConnectorResponse
	 */
	function modify($data, ?\DateTime $sinceWhen, array $params = []): \Sellastica\Connector\Model\ConnectorResponse;

	/**
	 * @param \Sellastica\Entity\Entity\EntityCollection $contacts
	 * @return \Sellastica\Connector\Model\ConnectorResponse
	 * @throws NotImplementedException If batch upload is not implemented
	 */
	function batch(EntityCollection $contacts): \Sellastica\Connector\Model\ConnectorResponse;
}
