<?php

class B24Chat_Integration_Model_Api2_ReportViewedProducts_Filter extends Mage_Api2_Model_Acl_Filter
{
    public function collectionIn($items)
    {
        $nodeName = key($items);

        if (!is_numeric(key($items[$nodeName]))) {
            $items[$nodeName] = [$items[$nodeName]];
        }

        if (is_array($items[$nodeName])) {
            foreach ($items[$nodeName] as &$item) {
                $item = $this->in($item);
            }
        }

        return $items[$nodeName];
    }
}