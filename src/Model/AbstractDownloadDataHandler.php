<?php
namespace Sellastica\Connector\Model;

use Sellastica\App\Entity\App;
use Sellastica\Connector\Logger\Logger;
use Sellastica\Entity\EntityManager;

abstract class AbstractDownloadDataHandler implements IDownloadDataHandler
{
	/** @var App */
	protected $app;
	/** @var EntityManager */
	protected $em;
	/** @var int */
	protected $synchronizationId;
	/** @var \Sellastica\Connector\Logger\Logger */
	protected $logger;


	/**
	 * @param EntityManager $em
	 * @param \Sellastica\App\Entity\App $app
	 */
	public function __construct(
		App $app,
		EntityManager $em
	)
	{
		$this->app = $app;
		$this->em = $em;
	}

	public function initialize(): void
	{
	}

	/**
	 * @param int $synchronizationId
	 */
	public function setProcessId(int $synchronizationId)
	{
		$this->synchronizationId = $synchronizationId;
	}

	/**
	 * @param \Sellastica\Connector\Logger\Logger $logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * @param string $string
	 * @return string
	 */
	protected function stripTags(string $string): string
	{
		return strip_tags($string, '<p><a><img><ul><ol><li><i><em><strong><b><br><h1<h2><h3><h4><h5><h6>');
	}
}
