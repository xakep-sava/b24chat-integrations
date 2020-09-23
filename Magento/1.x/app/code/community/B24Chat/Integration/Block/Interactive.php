<?php

class B24Chat_Integration_Block_Interactive extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    protected function _construct()
    {
        // TODO: добавить кэш
//        parent::_construct();
//        $this->addData(array(
//            'cache_lifetime'    => $this->getData('cache_lifetime'),
//            'cache_tags'        => [Mage_Catalog_Model_Product::CACHE_TAG],
//            'cache_key'         => $this->getCacheKey(),
//        ));
    }

    public function getScenarioId()
    {
        return $this->_getData('scenario_id') ?: 0;
    }

    public function getRepeatScenario()
    {
        return $this->_getData('repeat_scenario') ?: 1;
    }

    public function getRandomSteps()
    {
        return $this->_getData('random_steps') ?: 0;
    }

    public function getTypingSpeed()
    {
        return $this->_getData('typing_speed') ?: 100;
    }

    public function getDeleteTypingSpeed()
    {
        return $this->_getData('delete_typing_speed') ?: 50;
    }

    public function getDelayBeforeDelete()
    {
        return $this->_getData('delay_before_delete') ?: 1000;
    }

    protected function _toHtml()
    {
        // TODO: учесть локализацию
        // TODO: избавится от 2х запросов и делать все через один api
        $helper = Mage::helper('b24chat_integration');
        $api = Mage::helper('b24chat_integration/api');

        if (!$helper->isEnabled()) {
            return '';
        }

        if ($scenarioId = $this->getScenarioId()) {
            if ($response = $api->get(sprintf('settings/%s', $helper->getToken()))) {
                if ($response->botId) {
                    $response = $api->get(sprintf('scenario/%d/%d/steps?token=%s&onlyText=true',
                        $response->botId, $scenarioId, $helper->getToken()));

                    if ($response) {
                        $this->setTemplate('b24chat/integration/interactive.phtml');
// TODO:                        $this->getLayout()->getBlock('head')->addJs('b24chat/interactive.js');
                        $this->setData('interactive_text', json_encode($response->result));
                    }
                }
            }
        }

        return $scenarioId && $this->getData('interactive_text') ? $this->renderView() : '';
    }
}
