<?php
namespace Sellastica\Connector\Model;

use Sellastica\Connector\Exception\AbortException;

abstract class AbstractDownloadDataGetter implements IDownloadCollectionDataGetter
{
	/** @var int */
	protected $synchronizationId;
	/** @var mixed */
	private $lastResponse;


	/**
	 * @param int $synchronizationId
	 */
	public function setProcessId(int $synchronizationId)
	{
		$this->synchronizationId = $synchronizationId;
	}

	/**
	 * @param $response
	 * @param OptionsRequest $params
	 * @return mixed
	 * @throws AbortException
	 */
	protected function handleResponse($response, OptionsRequest $params)
	{
		if (isset($this->lastResponse) && $this->lastResponse == $response) {
			throw new AbortException('Cyclic data fetching');
		} elseif ($params->isDump()) {
			\Sellastica\Dumper\Dumper::dump($response);
			exit;
		}

		return $this->lastResponse = $response;
	}
}
