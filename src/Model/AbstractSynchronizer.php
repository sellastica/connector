<?php
namespace Sellastica\Connector\Model;

use Sellastica\App\Entity\App;
use Sellastica\Connector\Entity\Synchronization;
use Sellastica\Connector\Entity\SynchronizationBuilder;
use Sellastica\Entity\EntityManager;

abstract class AbstractSynchronizer
{
	const UP = 'up',
		DOWN = 'down';

	/** @var App */
	protected $app;
	/** @var string */
	protected $sinceWhen = ISynchronizer::SINCE_LAST_SYNC;
	/** @var bool */
	protected $finishSynchronizing = false;
	/** @var string */
	protected $identifier;
	/** @var int */
	protected $processId;
	/** @var array */
	protected $params = [];
	/** @var \Sellastica\Entity\EntityManager */
	protected $em;
	/** @var IIdentifierFactory */
	private $identifierFactory;


	/**
	 * @param string $identifier
	 * @param int $processId
	 * @param App $app
	 * @param \Sellastica\Entity\EntityManager $em
	 * @param IIdentifierFactory $identifierFactory
	 */
	public function __construct(
		string $identifier,
		int $processId,
		App $app,
		EntityManager $em,
		IIdentifierFactory $identifierFactory
	)
	{
		$this->app = $app;
		$this->identifier = $identifier;
		$this->em = $em;
		$this->processId = $processId;
		$this->identifierFactory = $identifierFactory;
	}

	public function finishSynchronizing()
	{
		$this->finishSynchronizing = true;
	}

	/**
	 * @param string $sinceWhen
	 */
	public function setSinceWhen(string $sinceWhen)
	{
		$this->sinceWhen = $sinceWhen;
	}

	/**
	 * @return string
	 */
	public function getSinceWhen(): string
	{
		return $this->sinceWhen;
	}

	/**
	 * @return bool
	 */
	public function changesOnly(): bool
	{
		return $this->sinceWhen !== ISynchronizer::SINCE_EVER;
	}
	/**
	 * @param array $params
	 */
	public function setParams(array $params)
	{
		$this->params = $params;
	}

	/**
	 * @param \Sellastica\Connector\Model\SynchronizationType|string $type
	 * @param \Sellastica\Connector\Model\Direction $direction
	 * @return Synchronization
	 * @throws \UnexpectedValueException
	 */
	protected function createSynchronization(SynchronizationType $type, Direction $direction): Synchronization
	{
		$identifier = $this->identifierFactory->create($this->identifier);
		return SynchronizationBuilder::create(
			$this->processId,
			$this->app->getSlug(),
			$identifier,
			$direction,
			$type
		)
			->changesSince($this->getSinceWhenDate())
			->build();
	}

	/**
	 * @return \DateTime|null
	 * @throws \UnexpectedValueException
	 */
	protected function getSinceWhenDate(): ?\DateTime
	{
		switch ($this->sinceWhen) {
			case ISynchronizer::SINCE_EVER:
				return null;
				break;
			case ISynchronizer::SINCE_LAST_SYNC:
				/** @var Synchronization $lastSynchronization */
				$lastSynchronization = $this->em->getRepository(Synchronization::class)
					->getLastDownload($this->app->getSlug(), $this->identifier);
				return $lastSynchronization ? $lastSynchronization->getStart() : null;
				break;
			case ISynchronizer::SINCE_TODAY:
				return new \DateTime('today');
				break;
			case ISynchronizer::SINCE_YESTERDAY:
				return new \DateTime('yesterday');
				break;
			case ISynchronizer::TWO_DAYS_BEFORE:
				return new \DateTime('-2 day 0:00:00');
				break;
			case ISynchronizer::THREE_DAYS_BEFORE:
				return new \DateTime('-3 day 0:00:00');
				break;
			case ISynchronizer::WEEK_BEFORE:
				return new \DateTime('-7 day 0:00:00');
				break;
			case ISynchronizer::MONTH_BEFORE:
				return new \DateTime('-30 day 0:00:00');
				break;
			default:
				throw new \UnexpectedValueException("Unexpected value $this->sinceWhen");
				break;
		}
	}
}