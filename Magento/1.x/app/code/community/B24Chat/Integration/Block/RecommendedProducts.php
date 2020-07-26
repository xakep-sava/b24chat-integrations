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
        $customerId = 0;
        if ($customer = $helper->getCustomer()) {
            $customerId = $customer->getId();
        }

        $this->setTemplate('b24chat/integration/recommended_products.phtml');

        // TODO: кэш
        $ch = curl_init('http://192.168.1.7:5000' . '/api/v1/recommendation?userId=' . $customerId . '&modelName=bot_20'); // $this->getApiUrl()
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);

        $productCollection = Mage::getModel('catalog/product')->getCollection()
            ->addFieldToFilter('entity_id', ['in' => $response->result])
            ->addAttributeToSelect('*')
            ->load();

        $this->setData('products', $productCollection->getItems());

        return $this->renderView();
    }
}
