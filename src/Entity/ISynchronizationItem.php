<?php
namespace Sellastica\Connector\Entity;

interface ISynchronizationItem
{
	/**
	 * @return mixed
	 */
	function getSynchronizationId();

	/**
	 * @return int|null
	 */
	function getStatusCode(): ?int;

	/**
	 * @param int|null $statusCode
	 */
	function setStatusCode(?int $statusCode);

	/**
	 * @return string|null
	 */
	function getStatusTitle(): ?string;

	/**
	 * @return \DateTime
	 */
	function getDateTime(): \DateTime;

	/**
	 * @param \DateTime $dateTime
	 */
	function setDateTime(\DateTime $dateTime);

	/**
	 * @return mixed|null
	 */
	function getInternalId();

	/**
	 * @param $internalId
	 */
	function setInternalId($internalId);

	/**
	 * @return null|string
	 */
	function getRemoteId(): ?string;

	/**
	 * @param null|string $remoteId
	 */
	function setRemoteId(?string $remoteId);

	/**
	 * @return null|string
	 */
	function getCode(): ?string;

	/**
	 * @param null|string $code
	 */
	function setCode(?string $code);

	/**
	 * @return null|string
	 */
	function getTitle(): ?string;

	/**
	 * @param null|string $title
	 */
	function setTitle(?string $title);

	/**
	 * @return null|string
	 */
	function getDescription(): ?string;

	/**
	 * @param null|string $description
	 */
	function setDescription(?string $description);

	/**
	 * @param bool $unserialize
	 * @return mixed
	 */
	function getSourceData(bool $unserialize = true);

	/**
	 * @param null|string $sourceData
	 */
	function setSourceData(?string $sourceData);

	/**
	 * @param bool $unserialize
	 * @return mixed
	 */
	function getResultData(bool $unserialize = true);

	/**
	 * @param null|string $resultData
	 */
	function setResultData(?string $resultData);

	/**
	 * @return bool
	 */
	function isNotice(): bool; 
}