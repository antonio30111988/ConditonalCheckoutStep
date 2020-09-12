<?php
declare(strict_types=1);

namespace Antonio88\ConditionalCheckoutStep\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_CUSTOM_MESSAGE_IN_CONDITIONAL_CHECKOUT_STEP = 'conditional_checkout_step/general/custom_message';

    /** @var ScopeConfigInterface  */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getCustomerMessage(): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOM_MESSAGE_IN_CONDITIONAL_CHECKOUT_STEP,
            ScopeInterface::SCOPE_STORE
        );
    }
}
