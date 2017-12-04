<?php
namespace Sellastica\Connector\Model;

use Sellastica\Connector\Exception\IErpConnectorException;
use Sellastica\Connector\Logger\Logger;

class Synchronizer implements \Sellastica\Connector\Model\ISynchronizer
{
	/** @var \Sellastica\Connector\Model\DownloadSynchronizer|null */
	private $downloadSynchronizer;
	/** @var \Sellastica\Connector\Model\UploadSynchronizer|null */
	private $uploadSynchronizer;
	/** @var \Sellastica\Connector\Logger\Logger */
	private $logger;
	/** @var bool */
	private $download = true;
	/** @var bool */
	private $upload = true;


	public function synchronize()
	{
		if ($this->download && isset($this->downloadSynchronizer)) {
			$this->downloadSynchronizer->synchronize();
		}

		if ($this->upload && isset($this->uploadSynchronizer)) {
			$this->uploadSynchronizer->synchronize();
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
	 * @param string|null $logType
	 * @throws IErpConnectorException
	 * @throws \Exception
	 */
	public function batch(int $batch, string $logType = null)
	{
		//download does not have batch
		if ($this->download && isset($this->downloadSynchronizer)) {
			$this->downloadSynchronizer->synchronize();
		}

		//upload does...
		if ($this->upload && isset($this->uploadSynchronizer)) {
			$this->uploadSynchronizer->setItemsPerPage($batch);
			$this->uploadSynchronizer->batch();
		}
	}

	/**
	 * @return \Sellastica\Connector\Model\DownloadSynchronizer|null
	 */
	public function getDownloadSynchronizer(): ?\Sellastica\Connector\Model\DownloadSynchronizer
	{
		return $this->downloadSynchronizer;
	}

	/**
	 * @param \Sellastica\Connector\Model\DownloadSynchronizer $downloadSynchronizer
	 * @return Synchronizer
	 */
	public function setDownloadSynchronizer(\Sellastica\Connector\Model\DownloadSynchronizer $downloadSynchronizer): Synchronizer
	{
		$this->downloadSynchronizer = $downloadSynchronizer;
		return $this;
	}

	/**
	 * @return \Sellastica\Connector\Model\UploadSynchronizer|null
	 */
	public function getUploadSynchronizer(): ?\Sellastica\Connector\Model\UploadSynchronizer
	{
		return $this->uploadSynchronizer;
	}

	/**
	 * @param \Sellastica\Connector\Model\UploadSynchronizer $uploadSynchronizer
	 * @return Synchronizer
	 */
	public function setUploadSynchronizer(\Sellastica\Connector\Model\UploadSynchronizer $uploadSynchronizer): Synchronizer
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