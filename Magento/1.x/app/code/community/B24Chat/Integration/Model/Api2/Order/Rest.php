<?php

class B24Chat_Integration_Model_Api2_Order_Rest extends B24Chat_Integration_Model_Api2_Order
{
//    public function dispatch()
//    {
//        $this->_filter = Mage::getModel('b24chat_integration/api2_order_filter', $this);
//        parent::dispatch();
//        $this->_render($this->_responseItems);
//    }

    /**
     * Get websites list
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $data = $this->_getCollectionForRetrieve()->load()->toArray();
//        return isset($data['items']) ? $data['items'] : $data;
        return ['test' => [$data]];
    }

//    /**
//     * Retrieve collection instances
//     *
////     * @return Mage_Customer_Model_Resource_Customer_Collection
//     */
    protected function _getCollectionForRetrieve()
    {
//        /** @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        $collection = Mage::getResourceModel('core/website_collection');
//        $collection->addAttributeToSelect(array_keys(
//            $this->getAvailableAttributes($this->getUserType(), Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
//        ));

        $this->_applyCollectionModifiers($collection);
        return $collection;
    }
}