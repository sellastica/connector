<?php
namespace Sellastica\Connector\Model;

abstract class AbstractDownloadDataHandler implements IDownloadDataHandler
{
	/** @var \Sellastica\App\Entity\App */
	protected $app;
	/** @var \Sellastica\Entity\EntityManager */
	protected $em;
	/** @var \Sellastica\Connector\Logger\Logger */
	protected $logger;


	/**
	 * @param \Sellastica\App\Entity\App $app
	 * @param \Sellastica\Entity\EntityManager $em
	 */
	public function __construct(
		\Sellastica\App\Entity\App $app,
		\Sellastica\Entity\EntityManager $em
	)
	{
		$this->app = $app;
		$this->em = $em;
	}

	public function initialize(): void
	{
	}

	/**
	 * @param \Sellastica\Connector\Logger\ILogger $logger
	 */
	public function setLogger(\Sellastica\Connector\Logger\ILogger $logger)
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
