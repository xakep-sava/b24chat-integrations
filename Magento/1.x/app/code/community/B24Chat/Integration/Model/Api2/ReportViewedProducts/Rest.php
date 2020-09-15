<?php

clASs B24Chat_Integration_Model_Api2_ReportViewedProducts_Rest extends B24Chat_Integration_Model_Api2_ReportViewedProducts
{
    /**
     * Get report viewed products
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $cache = Mage::getSingleton('core/cache');
        $key = 'B24Chat_Integration';

        if ($results = $cache->load($key)) {
            $results = unserialize($results);
        } else {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');

            $query = 'SELECT customer_id AS userId, Viewed.product_id AS productId, rating, timestamp FROM '
                . '(SELECT customer_id, product_id, UNIX_TIMESTAMP(added_at) AS timestamp FROM '
                . $resource->getTableName('reports/viewed_product_index') . ' WHERE customer_id IS NOT NULL ) AS Viewed '
                . 'JOIN (SELECT product_id, count(product_id) AS rating FROM '
                . $resource->getTableName('reports/viewed_product_index') . ' '

                // Active
                . 'JOIN (SELECT * FROM catalog_product_entity_int WHERE attribute_id = (SELECT attribute_id FROM '
                . 'eav_attribute WHERE attribute_code LIKE "status") AND value = 1) AS ACTIVE ON product_id = ACTIVE.entity_id '

                // Stock
                . 'JOIN (SELECT catalog_product_entity.entity_id, cataloginventory_stock_item.qty, stock_status FROM catalog_product_entity '
                . 'JOIN cataloginventory_stock_item ON catalog_product_entity.entity_id = cataloginventory_stock_item.product_id '
                . 'JOIN cataloginventory_stock_status ON catalog_product_entity.entity_id = cataloginventory_stock_status.product_id '
                . 'WHERE cataloginventory_stock_item.qty > 0 AND stock_status = 1) AS STOCK ON product_id = STOCK.entity_id '

                . 'GROUP BY product_id) AS Rating ON Viewed.product_id = Rating.product_id';

            $results = $readConnection->fetchAll($query);
            $cache->save(serialize($results), $key, ['report_viewed_products'], 60 * 60);
        }

        return $results;
    }
}