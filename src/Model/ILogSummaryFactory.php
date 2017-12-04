<?php
namespace Sellastica\Connector\Model;

interface ILogSummaryFactory
{
	/**
	 * @param int|null $synchronizationId
	 * @param int|null $processId
	 * @return \Sellastica\Connector\Model\LogSummary
	 */
	function create(
		int $synchronizationId = null,
		int $processId = null
	): \Sellastica\Connector\Model\LogSummary;
}