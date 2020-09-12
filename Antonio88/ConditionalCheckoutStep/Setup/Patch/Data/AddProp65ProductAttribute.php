<?php
declare(strict_types=1);

namespace Antonio88\ConditionalCheckoutStep\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\AttributeInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddProp65ProductAttribute implements DataPatchInterface
{
    const PROP65_ATTRIBUTE_CODE = 'prop65';

    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /** @var Config */
    private $eavModelConfig;

    /** @var EavSetupFactory */
    private $eavSetupFactory;

    public function __construct(
        Config $eavModelConfig,
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavModelConfig = $eavModelConfig;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        if (!$this->isProductAttributeExists()) {
            $eavSetup->addAttribute(Product::ENTITY, self::PROP65_ATTRIBUTE_CODE, [
                'type' => 'int',
                'label' => __('Prop 65'),
                'input' => 'boolean',
                'source' => Boolean::class,
                'default' => '0',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'used_in_product_listing' => false,
                'user_defined' => true,
                'visible_on_front' => false,
                'required' => false,
                'sort_order' => 70,
                'group' => 'General'
            ]);
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    private function isProductAttributeExists(): bool
    {
        $attribute = $this->getAttribute();
        return $attribute && $attribute->getId() ? true : false;
    }

    private function getAttribute(): ?AttributeInterface
    {
        return $this->eavModelConfig->getAttribute(Product::ENTITY, self::PROP65_ATTRIBUTE_CODE);
    }
}
