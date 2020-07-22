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

namespace B24Chat\Integration\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Framework\App\Area;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use B24Chat\Integration\Api\GetProductImage;

class GetProductImageUrl implements GetProductImage
{
    /**
     * @var Emulation
     */
    protected $_appEmulation;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;
    /**
     * @var Image
     */
    protected $_imageHelper;

    /**
     * @param Emulation $appEmulation
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param Image $imageHelper
     */
    public function __construct(
        Emulation $appEmulation,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        Image $imageHelper,
        JsonFactory $jsonResultFactory
    )
    {
        $this->_appEmulation = $appEmulation;
        $this->_storeManager = $storeManager;
        $this->_productRepository = $productRepository;
        $this->_imageHelper = $imageHelper;
    }

    public function getProductImageUrl($sku)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $product = $this->_productRepository->get($sku);

        $this->_appEmulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);

        if ($product) {
            $imageBaseUrl = $this->_imageHelper->init($product, 'product_base_image')->getUrl();
            $imageThumbnailUrl = $this->_imageHelper->init($product, 'product_page_image_small')->getUrl();

            return [['success' => true, 'base' => $imageBaseUrl, 'thumbnail' => $imageThumbnailUrl]];
        } else {
            return [['success' => false]];
        }

        $this->_appEmulation->stopEnvironmentEmulation();
    }
}