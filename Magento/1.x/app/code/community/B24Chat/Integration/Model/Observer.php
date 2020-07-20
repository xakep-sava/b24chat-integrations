<?php

class B24Chat_Integration_Model_Observer extends Varien_Object
{
    public function AddOrUpdateCart($observer)
    {
//        $order = $observer->getEvent()->getOrder();
//        if ($order->getPayment()->getMethodInstance()->getCode() == 'payture_mobile') {
//            Mage::helper('kruko_payturemobile')->sendEmailByOrder($order);
//        }
    }

    public function CheckoutSuccess($observer)
    {

    }

    public function SuccessRegister($observer)
    {

    }
}
