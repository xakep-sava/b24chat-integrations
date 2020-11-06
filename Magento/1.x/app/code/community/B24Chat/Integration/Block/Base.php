<?php

class B24Chat_Integration_Block_Base extends Mage_Core_Block_Template
{
    const CACHE_KEY = 'B24Chat_Integration_';
    const CACHE_GROUP = 'B24Chat_Integration_Base';
    protected $entity = 'widget';
    private $cacheLifeTime = 60;

    /**
     * B24Chat_Integration_Block_Base constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $helper = Mage::helper('b24chat_integration');
        $this->cacheLifeTime = $helper->getCacheTime($this->entity);

        echo $this->cacheLifeTime;
    }

    /**
     * @param $keyValue
     * @return string
     * @throws Mage_Core_Exception
     */
    public function _getCacheKey($keyValue)
    {
        $websiteId = Mage::app()->getWebsite()->getId();
        $cacheKey = self::CACHE_KEY . $websiteId . '_' . $keyValue;
        unset($keyValue);
        return $cacheKey;
    }

    /**
     * @param string $cacheKey
     * @param $value
     * @return B24Chat_Integration_Block_Base|false|void
     */
    public function _saveCache($cacheKey, $value)
    {
        Mage::app()->saveCache(
            serialize($value),
            $cacheKey,
            [self::CACHE_GROUP],
            $this->cacheLifeTime
        );
    }
}
