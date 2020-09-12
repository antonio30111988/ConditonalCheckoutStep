<?php
declare(strict_types=1);

namespace Antonio88\ConditionalCheckoutStep\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;

class PassProp65ProductsExistenceToCheckoutConfig
{
    /** @var SearchCriteriaBuilder  */
    private $searchCriteriaBuilder;

    /** @var FilterBuilder  */
    private $filterBuilder;

    /** @var ProductRepositoryInterface  */
    private $productRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder  $filterBuilder
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->productRepository = $productRepository;
    }

    public function afterGetConfig(DefaultConfigProvider $subject, $result)
    {
        $skus = $this->getSkusListInQuote($result);
        $productsInQuote = $this->getProductsInQuoteBySkus($skus);
        $quoteHasProp65Product = $this->checkProp65ProductExistence($productsInQuote);
        $result['anyItemWIthProp65Attribute'] = $quoteHasProp65Product;

        return $result;
    }

    private function getProductsInQuoteBySkus(array $skus)
    {
        $filter = $this->filterBuilder
            ->setField(ProductInterface::SKU)
            ->setConditionType('in')
            ->setValue([$skus])
            ->create();

        $this->searchCriteriaBuilder->addFilters([$filter]);
        $searchCriteria = $this->searchCriteriaBuilder->create();

        return $this->productRepository->getList($searchCriteria)->getItems();
    }

    private function checkProp65ProductExistence(array $productsInQuote): bool
    {
        $quoteHasProp65Product = false;
        foreach ($productsInQuote as $product) {
            /** @var Product $product */

            $extensionAttributes = $product->getExtensionAttributes();
            if ((string)$extensionAttributes->getProp65() === '1') {
                $quoteHasProp65Product = true;
                break;
            }
        }
        return $quoteHasProp65Product;
    }

    private function getSkusListInQuote($result): array
    {
        $skus = [];
        if (isset($result['quoteItemData'])) {
            foreach ($result['quoteItemData'] as $itemKey => $quoteItem) {
                $skus[] = $quoteItem['sku'];
            }
        }
        return array_unique($skus);
    }
}
