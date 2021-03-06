<?php

namespace Pim\Bundle\CatalogBundle\Doctrine\MongoDBODM;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\MongoDB\Collection;
use Pim\Bundle\CatalogBundle\ProductQueryUtility;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ChannelInterface;
use Pim\Component\Catalog\Model\CurrencyInterface;
use Pim\Component\Catalog\Model\LocaleInterface;

/**
 * Makes sure that the indexes links to entity are removed.
 *
 * @author    Benoit Jacquemont <benoit@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class IndexPurger
{
    /** @var Collection */
    protected $collection;

    /** @var ManagerRegistry */
    protected $managerRegistry;

    /** @var string */
    protected $productClass;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param string          $productClass
     */
    public function __construct(ManagerRegistry $managerRegistry, $productClass)
    {
        $this->managerRegistry = $managerRegistry;
        $this->productClass    = $productClass;
    }

    /**
     * Remove all indexes
     */
    public function purgeIndexes()
    {
        $collection = $this->getCollection();
        $collection->deleteIndexes();
    }

    /**
     * Remove indexes associated with the provided locale
     *
     * @param LocaleInterface $locale
     */
    public function purgeIndexesFromLocale(LocaleInterface $locale)
    {
        $localePattern = sprintf(
            '/^%s\..+-%s/',
            ProductQueryUtility::NORMALIZED_FIELD,
            $locale->getCode()
        );

        $indexesToRemove = $this->getIndexesMatching($localePattern);

        $this->removeIndexes($indexesToRemove);
    }

    /**
     * Remove indexes associated with the provided channel
     *
     * @param ChannelInterface $channel
     */
    public function purgeIndexesFromChannel(ChannelInterface $channel)
    {
        $channelPattern = sprintf(
            '/^%s\..+-%s/',
            ProductQueryUtility::NORMALIZED_FIELD,
            $channel->getCode()
        );

        $indexesToRemove = $this->getIndexesMatching($channelPattern);

        $this->removeIndexes($indexesToRemove);
    }

    /**
     * Remove indexes associated with the provided currency
     *
     * @param CurrencyInterface $currency
     */
    public function purgeIndexesFromCurrency(CurrencyInterface $currency)
    {
        $currencyPattern = sprintf(
            '/%s\..+\.%s\.data/',
            ProductQueryUtility::NORMALIZED_FIELD,
            $currency->getCode()
        );

        $indexesToRemove = $this->getIndexesMatching($currencyPattern);

        $this->removeIndexes($indexesToRemove);
    }

    /**
     * Remove indexes associated with the provided attribute
     *
     * @param AttributeInterface $attribute
     */
    public function purgeIndexesFromAttribute(AttributeInterface $attribute)
    {
        $attributePattern = sprintf(
            '/^%s\.%s([\.-].+)?$/',
            ProductQueryUtility::NORMALIZED_FIELD,
            $attribute->getCode()
        );

        $indexesToRemove = $this->getIndexesMatching($attributePattern);

        $this->removeIndexes($indexesToRemove);
    }

    /**
     * Get the MongoDB collection object
     *
     * @return Collection
     */
    protected function getCollection()
    {
        if (null === $this->collection) {
            $documentManager = $this->managerRegistry->getManagerForClass($this->productClass);
            $this->collection = $documentManager->getDocumentCollection($this->productClass);
        }

        return $this->collection;
    }

    /**
     * Get indexes names that contains the specified string
     *
     * @param string $pattern
     *
     * @return array
     */
    protected function getIndexesMatching($pattern)
    {
        $collection = $this->getCollection();

        $indexes = $collection->getIndexInfo();
        $matchingIndexes = [];

        foreach ($indexes as $index) {
            $indexKeys = array_keys($index['key']);
            $key = reset($indexKeys);
            if (0 !== preg_match($pattern, $key)) {
                $matchingIndexes[] = $key;
            }
        }

        return $matchingIndexes;
    }

    /**
     * Remove indexes with names provided in the array parameter
     *
     * @param array $indexes
     */
    protected function removeIndexes(array $indexes)
    {
        $collection = $this->getCollection();

        foreach ($indexes as $key) {
            $collection->deleteIndex($key);
        }
    }
}
