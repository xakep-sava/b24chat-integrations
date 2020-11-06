<?php

class B24Chat_Integration_Model_Api2_Products_Rest extends B24Chat_Integration_Model_Api2_Products
{
    /**
     * Get products
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $cache = Mage::getSingleton('core/cache');
        $key = 'B24Chat_Integration_products_' . Mage::helper('core')
                ->jsonEncode((Mage::app()->getRequest()->getParams()));

        if ($results = $cache->load($key)) {
            $results = unserialize($results);
        } else {
            $results = $this->_getCollectionForRetrieve()->load()->toArray();
            $cache->save(serialize($results), $key, [], 60 * 60);
        }

        return ['success' => true, 'count' => count($results), 'results' => [$results]];
    }

    /**
     * Retrieve collection instances
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection
            ->joinField(
                'category_id', 'catalog/category_product', 'category_id',
                'product_id = entity_id', null, 'left'
            )
            ->addAttributeToSelect('*')
            ->getSelect()
            ->group('e.entity_id');
        $this->_applyCollectionModifiers($collection);
        return $collection;
    }
}