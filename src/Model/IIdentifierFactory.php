<?php
namespace Sellastica\Connector\Model;

interface IIdentifierFactory
{
	/**
	 * @param string $identifier
	 * @return IIdentifier
	 */
	function create(string $identifier): IIdentifier;
}