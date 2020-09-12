<?php
declare(strict_types=1);

namespace Antonio88\ConditionalCheckoutStep\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\AttributeInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddNicknameProductAttribute implements DataPatchInterface
{
    const NICKNAME_ATTRIBUTE_CODE = 'nickname';

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
            $eavSetup->addAttribute(Product::ENTITY, self::NICKNAME_ATTRIBUTE_CODE, [
                'type' => 'text',
                'label' => 'Nickname',
                'input' => 'text',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'used_in_product_listing' => false,
                'user_defined' => true,
                'visible_on_front' => false,
                'required' => false,
                'sort_order' => 65,
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
        return $this->eavModelConfig->getAttribute(Product::ENTITY, self::NICKNAME_ATTRIBUTE_CODE);
    }
}
