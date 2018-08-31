<?php
namespace Sellastica\Connector\Model;

class DownloadResponse
{
	/** @var mixed */
	private $clientReponse;
	/** @var mixed */
	private $data;
	/** @var array */
	private $externalIdsToRemove = [];


	/**
	 * @param mixed $clientReponse
	 * @param $data
	 */
	public function __construct($clientReponse, $data)
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
	 * @return mixed
	 */
	public function getData()
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
