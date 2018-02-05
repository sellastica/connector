<?php
namespace Sellastica\Connector\UI;

class StatusLabelFactory
{
	/** @var \Nette\Localization\ITranslator */
	private $translator;


	/**
	 * @param \Nette\Localization\ITranslator $translator
	 */
	public function __construct(\Nette\Localization\ITranslator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * @param \Sellastica\Connector\Entity\SynchronizationLog $synchronizationLog
	 * @return \Nette\Utils\Html
	 */
	public function create(\Sellastica\Connector\Entity\SynchronizationLog $synchronizationLog): \Nette\Utils\Html
	{
		if ($synchronizationLog->getStatusCode() === \Sellastica\Connector\Model\ConnectorResponse::SKIPPED) {
			$class = 'hidden';
		} elseif ($synchronizationLog->getStatusCode() === \Sellastica\Connector\Model\ConnectorResponse::UPDATED) {
			$class = 'secondary';
		} elseif ($synchronizationLog->getStatusCode() < 400) {
			$class = 'success';
		} else {
			$class = 'alert';
		}

		return \Nette\Utils\Html::el('span')
			->setText(
				$synchronizationLog->getStatusTitle()
					? $this->translator->translate($synchronizationLog->getStatusTitle())
					: $synchronizationLog->getStatusCode()
			)->class("label $class");
	}
}