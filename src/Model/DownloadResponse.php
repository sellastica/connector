<?php
namespace Sellastica\Connector\Model;

class DownloadResponse
{
	/** @var mixed */
	private $clientReponse;
	/** @var array */
	private $data;
	/** @var array */
	private $externalIdsToRemove = [];


	/**
	 * @param mixed $clientReponse
	 * @param array $data
	 */
	public function __construct($clientReponse, array $data)
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
	 * @return array
	 */
	public function getData(): array
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
