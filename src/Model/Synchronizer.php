<?php
namespace Sellastica\Connector\Model;

use Sellastica\Connector\Exception\IErpConnectorException;
use Sellastica\Connector\Logger\Logger;

class Synchronizer implements \Sellastica\Connector\Model\ISynchronizer
{
	/** @var DownloadSynchronizer|null */
	private $downloadSynchronizer;
	/** @var UploadSynchronizer|null */
	private $uploadSynchronizer;
	/** @var \Sellastica\Connector\Logger\Logger */
	private $logger;
	/** @var bool */
	private $download = true;
	/** @var bool */
	private $upload = true;


	/**
	 * @param OptionsRequest|null $params
	 * @param bool $manual
	 * @throws \Exception
	 * @throws \InvalidArgumentException
	 * @throws \Throwable
	 * @throws \UnexpectedValueException
	 */
	public function synchronize(
		OptionsRequest $params = null, 
		bool $manual = false
	)
	{
		if ($this->download && isset($this->downloadSynchronizer)) {
			$this->downloadSynchronizer->synchronize($params, $manual);
		}

		if ($this->upload && isset($this->uploadSynchronizer)) {
			$this->uploadSynchronizer->synchronize($params, $manual);
		}
	}

	/**
	 * @param $data
	 * @return \Sellastica\Connector\Model\ConnectorResponse
	 * @throws \RuntimeException
	 * @throws \Exception
	 */
	public function upload($data): \Sellastica\Connector\Model\ConnectorResponse
	{
		if (!isset($this->uploadSynchronizer)) {
			throw new \RuntimeException('Cannot upload, upload synchronizer is not set');
		}

		$response = $this->uploadSynchronizer->upload($data);
		return $response;
	}

	/**
	 * @param int $batch
	 * @param OptionsRequest|null $params
	 * @param bool $manual
	 */
	public function batch(
		int $batch, 
		OptionsRequest $params = null, 
		bool $manual = false
	)
	{
		//download does not have batch
		if ($this->download && isset($this->downloadSynchronizer)) {
			$this->downloadSynchronizer->synchronize($params, $manual);
		}

		//upload does...
		if ($this->upload && isset($this->uploadSynchronizer)) {
			$this->uploadSynchronizer->setItemsPerPage($batch);
			$this->uploadSynchronizer->batch();
		}
	}

	/**
	 * @return DownloadSynchronizer|null
	 */
	public function getDownloadSynchronizer(): ?DownloadSynchronizer
	{
		return $this->downloadSynchronizer;
	}

	/**
	 * @param DownloadSynchronizer $downloadSynchronizer
	 * @return Synchronizer
	 */
	public function setDownloadSynchronizer(DownloadSynchronizer $downloadSynchronizer): Synchronizer
	{
		$this->downloadSynchronizer = $downloadSynchronizer;
		return $this;
	}

	/**
	 * @return UploadSynchronizer|null
	 */
	public function getUploadSynchronizer(): ?UploadSynchronizer
	{
		return $this->uploadSynchronizer;
	}

	/**
	 * @param UploadSynchronizer $uploadSynchronizer
	 * @return Synchronizer
	 */
	public function setUploadSynchronizer(UploadSynchronizer $uploadSynchronizer): Synchronizer
	{
		$this->uploadSynchronizer = $uploadSynchronizer;
		return $this;
	}

	/**
	 * @param bool $download
	 * @return Synchronizer
	 */
	public function down(bool $download): Synchronizer
	{
		$this->download = $download;
		return $this;
	}
	
	/**
	 * @param bool $upload
	 * @return Synchronizer
	 */
	public function up(bool $upload): Synchronizer
	{
		$this->upload = $upload;
		return $this;
	}
	
	/**
	 * @return Logger
	 */
	public function getLogger(): Logger
	{
		return $this->logger;
	}

	/**
	 * @return int
	 */
	public static function generateProcessId(): int
	{
		return round(microtime(true) * 10); //11 numbers, equals to tens microseconds
	}
}