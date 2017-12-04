<?php
namespace Sellastica\Connector\Model;

interface IIdentifier
{
	/**
	 * @return string
	 */
	function getCode(): string;

	/**
	 * @return string
	 * @throws \UnexpectedValueException
	 */
	function getTitle(): string;

	/**
	 * @return string|null
	 */
	function getEntityClassName(): ?string;
}