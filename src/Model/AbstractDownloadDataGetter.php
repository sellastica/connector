<?php
namespace Sellastica\Connector\Model;

abstract class AbstractDownloadDataGetter implements IDownloadCollectionDataGetter
{
	/** @var mixed */
	private $lastResponse;


	/**
	 * @param $response
	 * @param OptionsRequest|null $params
	 * @return mixed
	 * @throws \Sellastica\Connector\Exception\AbortException
	 */
	protected function handleResponse($response, OptionsRequest $params = null)
	{
		if (isset($this->lastResponse) && $this->lastResponse == $response) {
			throw new \Sellastica\Connector\Exception\AbortException('Cyclic data fetching');
		} elseif ($params && $params->isDumpResponse()) {
			\Sellastica\Dumper\Dumper::dump($response);
			exit;
		}

		return $this->lastResponse = $response;
	}
}
