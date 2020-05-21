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

namespace MageDev\Segmento\Observer;

use \Exception;
use \Magento\Framework\Event;
use \Magento\Customer\Model\Session;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \B24Chat\Integration\Helper\Data as Helper;

class AddOrUpdateCart implements Event\ObserverInterface
{
    protected $_customerSession;
    protected $_scopeConfig;
    protected $_helper;

    public function __construct(
        Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        Helper $helper
    ) {
        $this->_customerSession = $customerSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_helper = $helper;
    }

    public function execute(Event\Observer $observer)
    {
        if ($this->_scopeConfig->getValue('b24chat_integration/general/enabled')) {
            try {
                $event = $observer->getEvent();
                $product = $event->getData('product');

                // TODO: отличать добавление или обновление корзины
                if (!$product || !$product->getId()) {
                    $products = $event->getData('cart')->getQuote()->getItems();
                } else {
                    $products = [$product];
                }

                $this->_helper->sendData('cart.add', ['products' => $products]);
            } catch (Exception $exception) {
                return '';
            }
        }
    }
}
