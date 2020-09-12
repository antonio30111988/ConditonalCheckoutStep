<?php
declare(strict_types=1);

namespace Antonio88\ConditionalCheckoutStep\Plugin;

use Magento\Catalog\Api\Data\ProductExtensionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductExtension;
use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Api\SearchResults;
use Magento\Catalog\Api\ProductRepositoryInterface;

class HydrateDataFromProductRepository
{
    /** @var ProductExtensionFactory */
    private $productExtensionFactory;

    /** @var ProductFactory  */
    private $productFactory;

    public function __construct(
        ProductFactory $productFactory,
        ProductExtensionFactory $productExtensionFactory
    ) {
        $this->productFactory = $productFactory;
        $this->productExtensionFactory = $productExtensionFactory;
    }

    public function afterGet(ProductRepositoryInterface $subject, ProductInterface $product)
    {
        $this->addNickname($product);
        $this->addProp65($product);

        return $product;
    }

    public function afterGetList(ProductRepositoryInterface $subject, SearchResults $results)
    {
        $products = [];
        foreach ($results->getItems() as $product) {
            $this->afterGet($subject, $product);
            $products[] = $product;
        }
        $results->setItems($products);

        return $results;
    }

    public function afterGetById(ProductRepositoryInterface $subject, ProductInterface $product)
    {
        return $this->afterGet($subject, $product);
    }

    public function afterGetExtensionAttributes(
        ProductInterface $product,
        ProductExtensionInterface $extension = null
    ) {
        if ($extension === null) {
            /** @var ProductExtension $extension */
            $extension = $this->productExtensionFactory->create();
        }

        return $extension;
    }

    private function addNickname(ProductInterface $product)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->productExtensionFactory->create();
        }
        $productModel = $this->productFactory->create()->load($product->getId());
        $nickname = $productModel->getData('nickname') ?: '';
        $extensionAttributes->setNickname($nickname);
        $product->setExtensionAttributes($extensionAttributes);
    }

    private function addProp65(ProductInterface $product)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->productExtensionFactory->create();
        }
        $productModel = $this->productFactory->create()->load($product->getId());
        $prop65 = $productModel->getData('prop65') ?: false;
        $extensionAttributes->setProp65($prop65);
        $product->setExtensionAttributes($extensionAttributes);
    }
}
