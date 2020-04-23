<?php
class ControllerApiB24Chat extends Controller
{
    public function story()
    {
        // TODO: посмотреть что еще можно стянуть по стору (тел., описание и т.д.)
        $data = $this->cache->get('b24chat_stores');
        if (!$data) {
            $this->load->model('setting/setting');

            $data[] = [
                'entityId' => '0',
                'name' => $this->config->get('config_name'),
                'url' => $this->config->get('config_secure') ? HTTPS_SERVER : HTTP_SERVER,
                'image' => $this->getImage($this->config->get('config_image'))
            ];

            $query = $this->db->query('SELECT store_id as entityId, name, url FROM ' . DB_PREFIX . 'store');

            foreach ($query->rows as $store) {
                $store['image'] = $this->getImage($this->config->get('config_image', $store->entityId), $store->entityId);
                $data[] = $store;
            }

            $this->cache->set('b24chat_stores', $data);
        }

        $this->sendResponse($data);
    }

    public function category()
    {
        $data = $this->cache->get('b24chat_categories');
        if (!$data) {
            $query = $this->db->query('SELECT c.category_id as entityId, c.parent_id as parentId, c.image, c.status, '
                . 'c.date_added as createdAt, c.date_modified as updatedAt, SUBSTRING(l.code, 1, 2) as language, '
                . 'cd.name, cd.description as content, cts.store_id as storeId FROM ' . DB_PREFIX . 'category as c '
                . 'LEFT JOIN ' . DB_PREFIX . 'category_description as cd ON c.category_id = cd.category_id '
                . 'LEFT JOIN ' . DB_PREFIX . 'language as l ON l.language_id = cd.language_id '
                . 'LEFT JOIN ' . DB_PREFIX . 'category_to_store as cts ON cts.category_id = c.category_id');

            foreach ($query->rows as $category) {
                $category['image'] = $this->getImage($category['image']);
                $category['status'] = $category['status'] === '1';
                $category['content'] = trim(strip_tags(html_entity_decode($category['content'], ENT_QUOTES, 'UTF-8')));

                $data[] = $category;
            }

            $this->cache->set('b24chat_categories', $data);
        }

        $this->sendResponse($data);
    }

    public function product()
    {
        // TODO: есть еще много других данных, проверить может что еще нужно, учесть валюту
        $this->load->language('api/cart');
        $this->load->model('catalog/product');

        $data = $this->cache->get('b24chat_products');
        if (!$data) {
            $query = $this->db->query('SELECT * FROM ' . DB_PREFIX . 'product as p '
                . 'LEFT JOIN ' . DB_PREFIX . 'product_description as pd ON pd.product_id = p.product_id '
                . 'LEFT JOIN ' . DB_PREFIX . 'product_to_category as ptc ON ptc.product_id = p.product_id '
                . 'LEFT JOIN ' . DB_PREFIX . 'product_to_store as pts ON pts.product_id = p.product_id ');

            foreach ($query->rows as $product) {
                $product['image'] = $this->getImage($product['image'], 0, 'no_image.png');

//                $price = false;
//                if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
//                    $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'],
//                        $this->config->get('config_tax')), $this->session->data['currency']
//                    );
//                }

                $special = false;
                if ((float)$product['special']) {
                    $special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'],
                        $this->config->get('config_tax')), $this->session->data['currency']);
                }

                $tax = false;
                if ($this->config->get('config_tax')) {
                    $tax = $this->currency->format((float)$product['special'] ? $product['special'] : $product['price'],
                        $this->session->data['currency']);
                }

                $rating = $this->config->get('config_review_status') ? (int)$product['rating'] : false;

                // TODO: может что-то левое
                $data[] = [
                    'image' => $product['image'],
                    'categoryId' => $product['category_id'],
                    'storeId' => $product['store_id'],
                    'name' => $product['name'],
                    'content' => trim(strip_tags(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8'))),
                    'price' => $product['price'],
                    'special' => $special,
                    'tax' => $tax,
                    'minimum' => $product['minimum'] ?: 1,
                    'rating' => $rating,
                    'url' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                    'tag' => $product['tag'],
                    'model' => $product['model'],
                    'sku' => $product['sku'],
                    'upc' => $product['upc'],
                    'ean' => $product['ean'],
                    'jan' => $product['jan'],
                    'isbn' => $product['isbn'],
                    'mpn' => $product['mpn'],
                    'location' => $product['location'],
                    'quantity' => $product['quantity'],
                    'stockStatus' => $product['stock_status'],
                    'manufacturer' => $product['manufacturer'],
                    'entityId' => $product['product_id'],
                    'reward' => $product['reward'],
                    'points' => $product['points'],
                    'weight' => $product['weight'],
                    'length' => $product['length'],
                    'subtract' => $product['subtract'],
                    'reviews' => $product['reviews'],
                    'active' => $product['status'] === '1',
                    'viewed' => $product['viewed'],
                    'dateAvailable' => $product['date_available'],
                    'updatedAt' => $product['date_modified'],
                ];
            }

            $this->cache->set('b24chat_products', $data);
        }

        $this->sendResponse($data);
    }

    public function customer()
    {
        // TODO: стянуть все что можно
        $data = $this->cache->get('b24chat_customers');
        if (!$data) {
            $query = $this->db->query('SELECT * FROM ' . DB_PREFIX . 'customer');
            $data = $query->rows;

            $this->cache->set('b24chat_customers', $query->rows);
        }

        $this->sendResponse($data);
    }

    public function order()
    {
        // TODO: стянуть все что можно
        $data = $this->cache->get('b24chat_orders');
        if (!$data) {
            $query = $this->db->query('SELECT * FROM ' . DB_PREFIX . 'order as o '
                . 'LEFT JOIN ' . DB_PREFIX . 'order_status as os ON os.order_status_id = o.order_status_id');

            $this->cache->set('b24chat_orders', $query->rows);
        }

        $this->sendResponse($data);
    }

    public function coupon()
    {
        // TODO: coupon_category, coupon_history, coupon_product, привязки к стору и языку нет (возможно через категории)
        $data = $this->cache->get('b24chat_coupons');
        if (!$data) {
            $query = $this->db->query('SELECT coupon_id as entityId, name, code, type, discount, logged, shipping, total, '
                . 'date_start as dateStart, date_end as dateEnd, uses_total as usesTotal, uses_customer as usesCustomer, '
                . 'status as active, date_added as createdAt FROM ' . DB_PREFIX . 'coupon');

            foreach ($query->rows as $coupon) {
                $coupon['active'] = $coupon['active'] === '1';

                $data[] = $coupon;
            }

            $this->cache->set('b24chat_coupons', $query->rows);
        }

        $this->sendResponse($data);
    }

    public function statistic()
    {
        // TODO: стянуть все что можно

        $data = $this->cache->get('b24chat_statistics');
        if (!$data) {
            $query = $this->db->query('');

            foreach ($query->rows as $statistic) {
                $data[] = $statistic;
            }

            $this->cache->set('b24chat_statistics', $query->rows);
        }

        $this->sendResponse($data);
    }

    public function information()
    {
        $query = $this->db->query('SELECT i.information_id as entityId, i.status as active, id.title as name, '
            . 'SUBSTRING(l.code, 1, 2) as language, its.store_id as storeId, id.description as content FROM '
            . DB_PREFIX . 'information as i '
            . 'LEFT JOIN ' . DB_PREFIX . 'information_description as id ON id.information_id = i.information_id '
            . 'LEFT JOIN ' . DB_PREFIX . 'information_to_store as its ON its.information_id = i.information_id '
            . 'LEFT JOIN ' . DB_PREFIX . 'language as l ON l.language_id = id.language_id');

        $data = $this->cache->get('b24chat_informations');
        if (!$data) {
            foreach ($query->rows as $information) {
                $information['content'] = trim(strip_tags(html_entity_decode($information['content'],
                    ENT_QUOTES, 'UTF-8')));
                $information['active'] = $information['active'] === '1';

                $data[] = $information;
            }

            $this->cache->set('b24chat_informations', $data);
        }

        $this->sendResponse($data);
    }

    private function getImage($image, $storeId = 0, $defaultName = 'placeholder.png')
    {
        $this->load->model('tool/image');

        $image = $image ?: $defaultName;

        return $this->model_tool_image->resize($image, $this->config->get('theme_' .
            $this->config->get('config_theme', $storeId) . '_image_product_width', $storeId),
            $this->config->get('theme_' . $this->config->get('config_theme', $storeId) . '_image_product_height',
                $storeId));
    }

    private function sendResponse($data = [])
    {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }
}
