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
	/** @var mixed */
	protected $params;
	/** @var \Sellastica\Entity\EntityManager */
	protected $em;
	/** @var IIdentifierFactory */
	private $identifierFactory;
	/** @var string e.g. 128M */
	private $defaultMemoryLimit;
	/** @var int Memory limit in bytes */
	protected $memoryLimit;
	/** @var \Nette\Localization\ITranslator */
	protected $translator;


	/**
	 * @param string $identifier
	 * @param int $processId
	 * @param App $app
	 * @param \Sellastica\Entity\EntityManager $em
	 * @param IIdentifierFactory $identifierFactory
	 * @param \Nette\Localization\ITranslator $translator
	 */
	public function __construct(
		string $identifier,
		int $processId,
		App $app,
		EntityManager $em,
		IIdentifierFactory $identifierFactory,
		\Nette\Localization\ITranslator $translator
	)
	{
		$this->app = $app;
		$this->identifier = $identifier;
		$this->em = $em;
		$this->processId = $processId;
		$this->identifierFactory = $identifierFactory;
		$this->translator = $translator;

		//memory limit
		$this->defaultMemoryLimit = ini_get('memory_limit');
		$configuration = new \BrandEmbassy\Memory\MemoryConfiguration();
		$limitProvider = new \BrandEmbassy\Memory\MemoryLimitProvider($configuration);
		$this->memoryLimit = $limitProvider->getLimitInBytes();
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
	 * @param $params
	 */
	public function setParams($params)
	{
		$this->params = $params;
	}

	/**
	 * @param int $mb
	 */
	public function setMemoryLimit(int $mb): void
	{
		$this->memoryLimit = $mb * 1024 * 1024;
		ini_set('memory_limit', $mb . 'M');
	}

	public function restoreMemoryLimit(): void
	{
		ini_set('memory_limit', $this->defaultMemoryLimit);
	}

	/**
	 * @param \Sellastica\Connector\Model\SynchronizationType|string $type
	 * @param string $source
	 * @param string $target
	 * @return Synchronization
	 * @throws \UnexpectedValueException
	 */
	protected function createSynchronization(
		SynchronizationType $type,
		string $source,
		string $target
	): Synchronization
	{
		$identifier = $this->identifierFactory->create($this->identifier);
		return SynchronizationBuilder::create(
			$this->processId,
			$this->app->getSlug(),
			$identifier,
			$source,
			$target,
			$type,
			SynchronizationStatus::running()
		)->changesSince($this->getSinceWhenDate($source, $target))
			->build();
	}

	/**
	 * @param string $source
	 * @param string $target
	 * @return \DateTime|null
	 * @throws \UnexpectedValueException
	 */
	protected function getSinceWhenDate(string $source, string $target): ?\DateTime
	{
		switch ($this->sinceWhen) {
			case ISynchronizer::SINCE_EVER:
				return null;
				break;
			case ISynchronizer::SINCE_LAST_SYNC:
				/** @var Synchronization $lastSynchronization */
				$lastSynchronization = $this->em->getRepository(Synchronization::class)
					->getLastDownload($this->app->getSlug(), $this->identifier, $source, $target);
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

	/**
	 * @throws \Sellastica\Connector\Exception\AbortException
	 */
	protected function checkMemoryLimit()
	{
		if ($this->memoryLimit > 0 && $this->memoryLimit * 0.85 < memory_get_usage()) {
			throw new \Sellastica\Connector\Exception\AbortException(
				$this->translator->translate('core.connector.notices.memory_usage_exceeded')
			);
		}
	}
}