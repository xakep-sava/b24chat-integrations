<?php

class B24Chat_Integration_Block_RecommendedProducts extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    protected $_columnCount = 4;

    protected function _construct()
    {
        // TODO: добавить кэш
//        parent::_construct();
//        $this->addData(array(
//            'cache_lifetime'    => $this->getData('cache_lifetime'),
//            'cache_tags'        => [Mage_Catalog_Model_Product::CACHE_TAG],
//            'cache_key'         => $this->getCacheKey(),
//        ));
    }

    public function getColumnCount()
    {
        $colCount = $this->_getData('col_count');
        return $colCount ? $colCount : $this->_columnCount;
    }

    protected function _toHtml()
    {
        $helper = Mage::helper('b24chat_integration');
        $api = Mage::helper('b24chat_integration/api');

        if (!$helper->isEnabled()) {
            return '';
        }

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
                    $this->setTemplate('b24chat/integration/recommended_products.phtml');

                    $productCollection = Mage::getModel('catalog/product')->getCollection()
                        ->addFieldToFilter('entity_id', ['in' => $response->result])
                        ->addAttributeToSelect('*')
                        ->load();

                    $this->setData('products', $productCollection->getItems());
                }
            }
        }

        return $this->getData('products') ? $this->renderView() : '';
    }
}
