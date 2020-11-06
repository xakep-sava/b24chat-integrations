<?php

class B24Chat_Integration_Model_Api2_Products extends Mage_Api2_Model_Resource
{
    const RESULT_ERROR_NOT_FOUND = 404;
    const RESULT_ERROR_IMPORT = 500;
    const RESULT_SUCCESS = 200;
    protected $_responseItems = [];

    protected function _addResult($item, $errorCode, $errorMessage)
    {
        $result = ['result' => $errorCode, 'id' => $item['id']];

        if ($errorMessage) {
            $result['error'] = $errorMessage;
        }

        $this->_responseItems[] = $result;
    }
}