<?php
class ControllerExtensionAnalyticsB24Chat extends Controller {
    public function eventAddOrderHistory($route,$data) {
        $log = new Log('EDIT.log');
        $log->write('Route: ' . $route);
        $log->write('DATA: ' . print_r($data,true));
    }

    public function eventAddCart($route,$data) {
        $log = new Log('EDIT.log');
        $log->write('add Route: ' . $route);
        $log->write('DATA: ' . print_r($data,true));
    }

    public function eventRemoveCart($route,$data) {
        $log = new Log('EDIT.log');
        $log->write('remove Route: ' . $route);
        $log->write('DATA: ' . print_r($data));
    }
}