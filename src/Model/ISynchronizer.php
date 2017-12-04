<?php
namespace Sellastica\Connector\Model;

use Sellastica\Connector\Logger\Logger;

interface ISynchronizer
{
	const SINCE_LAST_SYNC = 'since_last_sync',
		SINCE_TODAY = 'since_today',
		SINCE_YESTERDAY = 'since_yesterday',
		TWO_DAYS_BEFORE = 'two_days_before',
		THREE_DAYS_BEFORE = 'three_days_before',
		WEEK_BEFORE = 'week_before',
		MONTH_BEFORE = 'month_before',
		SINCE_EVER = 'since_ever';


	/**
	 * @return void
	 */
	function synchronize();

	/**
	 * @return \Sellastica\Connector\Logger\Logger
	 */
	function getLogger(): Logger;
}
