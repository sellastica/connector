<?php
namespace Sellastica\Connector\Mapping;

/**
 * @see \Sellastica\Connector\Entity\MongoSynchronizationItem
 */
class MongoSynchronizationItemMapper extends \Sellastica\MongoDB\Mapping\MongoMapper
{
	use \Sellastica\DataGrid\Mapping\MongoDB\TFilterRulesMongoMapper;

	/**
	 * @return \MongoDB\Collection
	 */
	protected function getCollectionName(): string
	{
		return '_synchronization_log_item';
	}

	/**
	 * @param \Sellastica\Entity\Configuration $configuration
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @return iterable
	 */
	public function findByFilterRules(
		\Sellastica\DataGrid\Model\FilterRuleCollection $rules,
		\Sellastica\Entity\Configuration $configuration
	): iterable
	{
		$match = $this->rulesToMatch($rules);

		//application
		if ($rules['application']) {
			$match['application'] = $rules['application']->getValue();
		}

		//status code
		if ($rules['status_code']) {
			$match['statusCode'] = (int)$rules['status_code']->getValue();
		}

		//synchronization_id
		try {
			if ($rules['synchronization_id']) {
				$match['synchronizationId'] = new \MongoDB\BSON\ObjectId($rules['synchronization_id']->getValue());
			}
		} catch (\MongoDB\Driver\Exception\InvalidArgumentException $e) {
		}

		//item_id
		if ($rules['item_id']) {
			$match['internalId'] = $rules['item_id']->getValue();
		}

		return $this->getCollection($configuration)->find($match, $this->getOptions($match, $configuration));
	}
}