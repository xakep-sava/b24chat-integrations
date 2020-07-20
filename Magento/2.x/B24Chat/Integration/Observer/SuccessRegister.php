<?php
/**
 * Copyright © B24 Chat. All rights reserved.
 *
 * PHP version 7
 *
 * @category B24Chat
 * @package  B24Chat_Integration
 * @author   Vladimir Savrov <i@sava.team>
 */

namespace B24Chat\Integration\Observer;

use \Magento\Framework\Event;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \B24Chat\Integration\Helper\Data as Helper;

class SuccessRegister implements Event\ObserverInterface
{
    protected $_scopeConfig;
    protected $_helper;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Helper $helper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_helper = $helper;
    }

    public function execute(Event\Observer $observer)
    {
        if ($this->_scopeConfig->getValue('b24chat_integration/general/enabled')) {
            $customer = $observer->getEvent()->getCustomer();

            if ($customer->getId() > 0) {
                $this->_helper->sendData('register', ['customer' => $customer]);
            }
        }
    }
}
