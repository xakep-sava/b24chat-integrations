<?php
/**
 * Copyright © B24 Chat. All rights reserved.
 *
 * PHP version 7
 *
 * @category B24Chat
 * @package  B24Chat_Integration
 * @author   Vladimir Savrov <i@sava.team>
 */

namespace B24Chat\Integration\Api;

interface GetProductImage
{
    /**
     * @param string $sku
     * @return array
     * @api
     */
    public function getProductImageUrl($sku);
}