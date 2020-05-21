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

namespace B24Chat\Integration\Block;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\App\Config\ScopeConfigInterface;

class Script extends Template
{
    protected $_scopeConfig;

    public function __construct(
        Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->_scopeConfig = $scopeConfig;

        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        if (!$this->_scopeConfig->getValue('b24chat_integration/general/enabled')
            || !$this->_scopeConfig->getValue('b24chat_integration/general/token')
        ) {
            return '';
        }

        return $this->getJS();
    }

    private function getJS()
    {
        $token = $this->_scopeConfig->getValue('b24chat_integration/general/token');
        return '<script src="https://b24chat.com/init.js" api="{&quot;url&quot;:&quot;https://b24chat.com/&quot;,' .
            '&quot;widgets&quot;:{&quot;token&quot;:&quot;' . $token . '&quot;}}"></script>';
    }
}
