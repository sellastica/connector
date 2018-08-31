<?php
namespace Sellastica\Connector\Model;

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
	 * @return ConnectorResponse
	 * @throws \Exception
	 * @throws \Exception
	 */
	public function upload($data): ConnectorResponse
	{
		if (!isset($this->uploadSynchronizer)) {
			throw new \Exception('Cannot upload, upload synchronizer is not set');
		}

		return $this->uploadSynchronizer->upload($data);
	}

	/**
	 * @param $id
	 * @param OptionsRequest|null $params
	 * @return ConnectorResponse
	 * @throws \Exception
	 */
	public function downloadOne($id, OptionsRequest $params = null): ConnectorResponse
	{
		if (!isset($this->downloadSynchronizer)) {
			throw new \Exception('Cannot download, download synchronizer is not set');
		}

		return $this->downloadSynchronizer->downloadOne($id, $params);
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
	 * @return \Sellastica\Connector\Logger\Logger
	 */
	public function getLogger(): \Sellastica\Connector\Logger\Logger
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