<?php
namespace Sellastica\Connector\Model;

use Sellastica\Communication\IResponse;
use Sellastica\Communication\Response;

/**
 * @method static $this ignored()
 * @method static $this skipped(string $description = null)
 * @method static $this modified()
 * @method static $this created()
 * @method static $this removed()
 * @method static $this error(string $message = null, int $code = 400)
 * @method static $this badRequest(string $message = null)
 * @method static $this notFound(string $message = null)
 * @method static $this unprocessableEntity(string $message = null)
 * @method static $this fromResponse(IResponse $response): IResponse
 */
class ConnectorResponse extends Response implements IResponse
{
	/** @var string|null */
	private $objectClass;
	/** @var int|null */
	private $objectId;
	/** @var string|null */
	private $externalId;
	/** @var string|null External ID custom field */
	private $customField1;
	/** @var string|null External ID custom field */
	private $customField2;
	/** @var string|null External ID custom field */
	private $customField3;
	/** @var string|null */
	private $code;
	/** @var string|null */
	private $title;
	/** @var mixed */
	private $sourceData;
	/** @var mixed */
	private $resultData;


	/**
	 * @return null|string
	 */
	public function getObjectClass(): ?string
	{
		return $this->objectClass;
	}

	/**
	 * @param null|string $objectClass
	 * @return $this
	 */
	public function setObjectClass(string $objectClass)
	{
		$this->objectClass = $objectClass;
		return $this;
	}

	/**
	 * @return int|null
	 */
	public function getObjectId(): ?int
	{
		return $this->objectId;
	}

	/**
	 * @param int|null $objectId
	 * @return $this
	 */
	public function setObjectId(?int $objectId)
	{
		$this->objectId = $objectId;
		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getExternalId(): ?string
	{
		return $this->externalId;
	}

	/**
	 * @param null|string $externalId
	 * @return $this
	 */
	public function setExternalId(?string $externalId)
	{
		$this->externalId = $externalId;
		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getCustomField1(): ?string
	{
		return $this->customField1;
	}

	/**
	 * @param null|string $customField1
	 * @return $this
	 */
	public function setCustomField1(?string $customField1)
	{
		$this->customField1 = $customField1;
		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getCustomField2(): ?string
	{
		return $this->customField2;
	}

	/**
	 * @param null|string $customField2
	 * @return $this
	 */
	public function setCustomField2(?string $customField2)
	{
		$this->customField2 = $customField2;
		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getCustomField3(): ?string
	{
		return $this->customField3;
	}

	/**
	 * @param null|string $customField3
	 * @return $this
	 */
	public function setCustomField3(?string $customField3)
	{
		$this->customField3 = $customField3;
		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getCode(): ?string
	{
		return $this->code;
	}

	/**
	 * @param null|string $code
	 * @return $this
	 */
	public function setCode(?string $code)
	{
		$this->code = $code;
		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getTitle(): ?string
	{
		return $this->title;
	}

	/**
	 * @param null|string $title
	 * @return $this
	 */
	public function setTitle(?string $title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSourceData()
	{
		return $this->sourceData;
	}

	/**
	 * @param $sourceData
	 * @return $this
	 */
	public function setSourceData($sourceData)
	{
		$this->sourceData = $sourceData;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getResultData()
	{
		return $this->resultData;
	}

	/**
	 * @param $resultData
	 * @return $this
	 */
	public function setResultData($resultData)
	{
		$this->resultData = $resultData;
		return $this;
	}

	/**
	 * @param array $errors
	 * @return $this
	 */
	public function addErrors(array $errors)
	{
		foreach ($errors as $error) {
			$this->addError($error);
		}

		return $this;
	}
}