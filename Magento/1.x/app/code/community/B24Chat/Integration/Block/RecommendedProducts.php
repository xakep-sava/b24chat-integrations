<?php

class B24Chat_Integration_Block_RecommendedProducts extends B24Chat_Integration_Block_Base implements Mage_Widget_Block_Interface
{
    const CACHE_GROUP = 'B24Chat_Integration_RecommendedProducts';

    protected $_columnCount = 4;
    protected $entity = 'recommended';

    /**
     * @return int|mixed
     */
    public function getColumnCount()
    {
        $colCount = $this->_getData('col_count');
        return $colCount ? $colCount : $this->_columnCount;
    }

    /**
     * @return string
     * @throws Mage_Core_Exception
     */
    protected function _toHtml()
    {
        $helper = Mage::helper('b24chat_integration');
        $api = Mage::helper('b24chat_integration/api');

        if (!$helper->isEnabled()) {
            return '';
        }

        $cacheKey = $this->_getCacheKey('products');
        $cacheProducts = Mage::app()->loadCache($cacheKey);

        $this->setTemplate('b24chat/integration/recommended_products.phtml');

        if (!empty($cacheProducts)) {
            $this->setData('products', unserialize($cacheProducts));
        } else {
            $customerId = 0;
            if ($customer = $helper->getCustomer()) {
                $customerId = $customer->getId();
            }

            // TODO: избавится от 2х запросов и делать все через один api
            if ($response = $api->get(sprintf('settings/%s', $helper->getToken()))) {
                if ($response->botId) {
                    $response = $api->get(sprintf('recommendation?userId=%d&modelName=bot_%s',
                        $customerId, $response->botId), 'https://ai.b24chat.com'); // TODO: take from settings

                    if ($response) {
                        $productCollection = Mage::getModel('catalog/product')->getCollection()
                            ->addFieldToFilter('entity_id', ['in' => $response->result])
                            ->addAttributeToSelect('*')
                            ->load();

                        $this->setData('products', $productCollection->getItems());
                        $this->_saveCache($cacheKey, $this->getData('products'));
                    }
                }
            }
        }

        return $this->getData('products') ? $this->renderView() : '';
    }
}
