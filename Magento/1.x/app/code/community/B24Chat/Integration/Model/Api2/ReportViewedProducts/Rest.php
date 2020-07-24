<?php

class B24Chat_Integration_Model_Api2_ReportViewedProducts_Rest extends B24Chat_Integration_Model_Api2_ReportViewedProducts
{
    /**
     * Get report viewed products
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $cache = Mage::getSingleton('core/cache');
        $key = 'B24Chat_';

        if ($results = $cache->load($key)) {
            $results = unserialize($results);
        } else {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');

            $query = 'SELECT customer_id as userId, Viewed.product_id as productId, rating, timestamp FROM '
                . '(SELECT customer_id, product_id, UNIX_TIMESTAMP(added_at) as timestamp FROM '
                . $resource->getTableName('reports/viewed_product_index') . ' WHERE customer_id IS NOT NULL ) as Viewed '
                . 'JOIN (SELECT product_id, count(product_id) as rating FROM '
                . $resource->getTableName('reports/viewed_product_index') . ' GROUP BY product_id) AS Rating ON '
                . ' Viewed.product_id = Rating.product_id';

            $results = $readConnection->fetchAll($query);
            $cache->save(serialize($results), $key, ['report_viewed_products'], 60 * 60 * 24);
        }

        return $results;
    }
}