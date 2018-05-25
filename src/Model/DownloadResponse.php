<?php
namespace Sellastica\Connector\Model;

class DownloadResponse
{
	/** @var mixed */
	private $clientReponse;
	/** @var iterable */
	private $data;
	/** @var array */
	private $externalIdsToRemove = [];


	/**
	 * @param mixed $clientReponse
	 * @param iterable $data
	 */
	public function __construct($clientReponse, iterable $data)
	{
		$this->clientReponse = $clientReponse;
		$this->data = $data;
	}

	/**
	 * @return mixed
	 */
	public function getClientReponse()
	{
		return $this->clientReponse;
	}

	/**
	 * @return iterable
	 */
	public function getData(): iterable
	{
		return $this->data;
	}

	/**
	 * @return array
	 */
	public function getExternalIdsToRemove(): array
	{
		return $this->externalIdsToRemove;
	}

	/**
	 * @param array $externalIdsToRemove
	 */
	public function setExternalIdsToRemove(array $externalIdsToRemove)
	{
		$this->externalIdsToRemove = $externalIdsToRemove;
	}
}
