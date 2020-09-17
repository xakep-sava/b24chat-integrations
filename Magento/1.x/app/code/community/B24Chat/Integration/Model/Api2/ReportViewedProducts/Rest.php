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
        $key = 'B24Chat_Integration';

        if (!$results = $cache->load($key)) {
            $results = unserialize($results);
        } else {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');

            $reportsViewedProductIndexTable = $resource->getTableName('reports/viewed_product_index');
            $catalogProductEntityIntTable = $resource->getTableName('catalog_product_entity_int');
            $catalogProductEntityTable = $resource->getTableName('catalog_product_entity');
            $eavAttributeTable = $resource->getTableName('eav/attribute');
            $catalogInventoryStockItemTable = $resource->getTableName('cataloginventory/stock_item');
            $catalogInventoryStockStatusTable = $resource->getTableName('cataloginventory/stock_status');

            $query = 'SELECT customer_id AS userId, Viewed.product_id AS productId, rating, timestamp FROM '
                . '(SELECT customer_id, product_id, UNIX_TIMESTAMP(added_at) AS timestamp FROM '
                . $reportsViewedProductIndexTable . ' WHERE customer_id IS NOT NULL ) AS Viewed '
                . 'JOIN (SELECT product_id, count(product_id) AS rating FROM '
                . $reportsViewedProductIndexTable . ' '

                // Active
                . 'JOIN (SELECT * FROM ' . $catalogProductEntityIntTable . ' '
                . 'WHERE attribute_id = (SELECT attribute_id FROM ' . $eavAttributeTable . ' '
                . 'WHERE attribute_code LIKE "status") AND value = 1) AS ACTIVE ON product_id = ACTIVE.entity_id '

                // Stock
                . 'JOIN (SELECT ' . $catalogProductEntityTable . '.entity_id, ' . $catalogInventoryStockItemTable
                . '.qty, stock_status FROM ' . $catalogProductEntityTable . ' '
                . 'JOIN ' . $catalogInventoryStockItemTable . ' ON ' . $catalogProductEntityTable . '.entity_id = '
                . $catalogInventoryStockItemTable . '.product_id ' . 'JOIN ' . $catalogInventoryStockStatusTable . ' '
                . 'ON ' . $catalogProductEntityTable . '.entity_id = ' . $catalogInventoryStockStatusTable
                . '.product_id WHERE ' . $catalogInventoryStockItemTable . '.qty > 0 AND stock_status = 1) AS STOCK '
                . 'ON product_id = STOCK.entity_id '

                . 'GROUP BY product_id) AS Rating ON Viewed.product_id = Rating.product_id';

            $results = $readConnection->fetchAll($query);
            $cache->save(serialize($results), $key, ['report_viewed_products'], 60 * 60);
        }

        return $results;
    }
}