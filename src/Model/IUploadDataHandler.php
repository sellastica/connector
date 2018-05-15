<?php
namespace Sellastica\Connector\Model;

interface IUploadDataHandler
{
	/**
	 * Target indentifier
	 * @return string
	 */
	function getTarget(): string;

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
	 * @throws \Sellastica\Connector\Exception\NotImplementedException If batch upload is not implemented
	 */
	function batch(\Sellastica\Entity\Entity\EntityCollection $contacts): ConnectorResponse;
}
