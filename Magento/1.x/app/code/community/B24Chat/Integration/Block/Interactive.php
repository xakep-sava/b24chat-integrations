<?php

class B24Chat_Integration_Block_Interactive extends B24Chat_Integration_Block_Base implements Mage_Widget_Block_Interface
{
    const CACHE_GROUP = 'B24Chat_Integration_Interactive';

    protected $entity = 'interactive';

    /**
     * @return int|mixed|null
     */
    public function getScenarioId()
    {
        return $this->_getData('scenario_id') ?: 0;
    }

    /**
     * @return int|mixed|null
     */
    public function getRepeatScenario()
    {
        return $this->_getData('repeat_scenario') ?: 1;
    }

    /**
     * @return int|mixed|null
     */
    public function getRandomSteps()
    {
        return $this->_getData('random_steps') ?: 0;
    }

    /**
     * @return int|mixed|null
     */
    public function getTypingSpeed()
    {
        return $this->_getData('typing_speed') ?: 100;
    }

    /**
     * @return int|mixed|null
     */
    public function getDeleteTypingSpeed()
    {
        return $this->_getData('delete_typing_speed') ?: 50;
    }

    /**
     * @return int|mixed|null
     */
    public function getDelayBeforeDelete()
    {
        return $this->_getData('delay_before_delete') ?: 1000;
    }

    /**
     * @return string
     * @throws Mage_Core_Exception
     */
    protected function _toHtml()
    {
        // TODO: учесть локализацию
        // TODO: избавится от 2х запросов и делать все через один api
        $helper = Mage::helper('b24chat_integration');
        $api = Mage::helper('b24chat_integration/api');

        if (!$helper->isEnabled()) {
            return '';
        }

        $cacheKey = $this->_getCacheKey('interactive_text');
        $cacheInteractiveText = Mage::app()->loadCache($cacheKey);

        $this->setTemplate('b24chat/integration/interactive.phtml');
        // TODO: $this->getLayout()->getBlock('head')->addJs('b24chat/interactive.js');

        $scenarioId = $this->getScenarioId();

        if (!empty($cacheInteractiveText)) {
            $this->setData('interactive_text', unserialize($cacheInteractiveText));
        } else {
            if ($scenarioId) {
                if ($response = $api->get(sprintf('settings/%s', $helper->getToken()))) {
                    if ($response->botId) {
                        $response = $api->get(sprintf('scenario/%d/%d/steps?token=%s&onlyText=true',
                            $response->botId, $scenarioId, $helper->getToken()));

                        if ($response) {
                            $this->setData('interactive_text', json_encode($response->result));
                            $this->_saveCache($cacheKey, $this->getData('interactive_text'));
                        }
                    }
                }
            }
        }

        return $scenarioId && $this->getData('interactive_text') ? $this->renderView() : '';
    }
}
