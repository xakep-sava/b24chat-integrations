<?php

class B24Chat_Integration_Model_Api2_Contents_Rest extends B24Chat_Integration_Model_Api2_Contents
{
    /**
     * Get contents
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $cache = Mage::getSingleton('core/cache');
        $key = 'B24Chat_Integration_contents_' . Mage::helper('core')
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
     * @return Mage_Cms_Model_Resource_Page_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /** @var $collection Mage_Cms_Model_Resource_Page_Collection */
        $collection = Mage::getResourceModel('cms/page_collection');
        $this->_applyCollectionModifiers($collection);
        return $collection;
    }
}