<?php

class B24Chat_Integration_Model_Api2_Categories_Rest extends B24Chat_Integration_Model_Api2_Categories
{
    /**
     * Get categories
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $cache = Mage::getSingleton('core/cache');
        $key = 'B24Chat_Integration_categories_' . Mage::helper('core')
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
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /** @var $collection Mage_Catalog_Model_Resource_Category_Collection */
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('*');
        $this->_applyCollectionModifiers($collection);
        return $collection;
    }
}