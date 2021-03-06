<?php

class B24Chat_Integration_Model_Api2_Websites_Rest extends B24Chat_Integration_Model_Api2_Websites
{
    /**
     * Get websites
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $cache = Mage::getSingleton('core/cache');
        $key = 'B24Chat_Integration_websites_' . Mage::helper('core')
                ->jsonEncode((Mage::app()->getRequest()->getParams()));

        if ($results = $cache->load($key)) {
            $results = unserialize($results);
        } else {
            $results = $this->_getCollectionForRetrieve()->load()->toArray();
            $results = $results['items'] ? $results['items'] : [];
            $cache->save(serialize($results), $key, [], 60 * 60);
        }

        return ['success' => true, 'count' => count($results), 'results' => [$results]];
    }

    /**
     * Retrieve collection instances
     *
     * @return Mage_Core_Model_Resource_Website_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /** @var $collection Mage_Core_Model_Resource_Website_Collection */
        $collection = Mage::getResourceModel('core/website_collection');
        $this->_applyCollectionModifiers($collection);
        return $collection;
    }
}