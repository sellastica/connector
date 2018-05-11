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
	 * @param OptionsRequest|null $params
	 * @return ConnectorResponse
	 */
	function modify($data, ?\DateTime $sinceWhen, OptionsRequest $params = null): ConnectorResponse;

	/**
	 * @param \Sellastica\Entity\Entity\EntityCollection $contacts
	 * @return ConnectorResponse
	 * @throws NotImplementedException If batch upload is not implemented
	 */
	function batch(EntityCollection $contacts): ConnectorResponse;
}
