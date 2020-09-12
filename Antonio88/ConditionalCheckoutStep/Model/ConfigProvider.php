<?php
declare(strict_types=1);

namespace Antonio88\ConditionalCheckoutStep\Model;

use Antonio88\ConditionalCheckoutStep\Helper\Config;
use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /** @var Config  */
    private $helper;

    public function __construct(Config $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $customerMessage = $this->helper->getCustomerMessage() ?? '-';
        $result['customerMessage'] = $customerMessage;

        return $result;
    }
}
