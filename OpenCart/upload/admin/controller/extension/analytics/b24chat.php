<?php
class ControllerExtensionAnalyticsB24Chat extends Controller {
	private $error = [];

	public function index() {
		$this->load->language('extension/analytics/b24chat');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('analytics_b24chat', $this->request->post, $this->request->get['store_id']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=analytics', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=analytics', true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/analytics/b24chat', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true)
		];

		$data['action'] = $this->url->link('extension/analytics/b24chat', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=analytics', true);

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['analytics_b24chat_code'])) {
			$data['analytics_b24chat_code'] = $this->request->post['analytics_b24chat_code'];
		} else {
			$data['analytics_b24chat_code'] = $this->model_setting_setting->getSettingValue('analytics_b24chat_code', $this->request->get['store_id']);
		}

		if (isset($this->request->post['analytics_b24chat_status'])) {
			$data['analytics_b24chat_status'] = $this->request->post['analytics_b24chat_status'];
		} else {
			$data['analytics_b24chat_status'] = $this->model_setting_setting->getSettingValue('analytics_b24chat_status', $this->request->get['store_id']);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/analytics/b24chat', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/analytics/b24chat')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

// TODO: валидация на ввод
// 		if (!$this->request->post['analytics_b24chat_code']) {
// 			$this->error['code'] = $this->language->get('error_code');
// 		}

		return !$this->error;
	}

    public function install() {
        $this->load->model('setting/setting');
        $this->load->model('setting/event');


        $this->model_setting_event->addEvent('b24chat_order_status','catalog/model/checkout/order/addOrderHistory/after','extension/analytics/b24chat/eventAddOrderHistory');
        $this->model_setting_event->addEvent('b24chat_add_cart','catalog/controller/checkout/cart/add/before','extension/analytics/b24chat/eventAddCart');
        $this->model_setting_event->addEvent('b24chat_remove_cart','catalog/controller/checkout/cart/remove/after','extension/analytics/b24chat/eventRemoveCart');
//         $this->model_setting_event->addEvent('b24chat_add_cart_soconfig','catalog/controller/extension/soconfig/cart/add/after','extension/analytics/b24chat/eventAddCart');

//         $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/analytics/b24chat');
//         $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/analytics/b24chat');

//         $settings = $this->model_setting_setting->getSetting('feed_openbaypro');
//         $settings['feed_openbaypro_status'] = 1;
//         $settings['feed_openbaypro_language'] = "en_GB";
//         $this->model_setting_setting->editSetting('feed_openbaypro', $settings);

//         $this->model_setting_event->addEvent('openbay_product_del_after', 'admin/model/catalog/product/deleteProduct/after', 'marketplace/openbay/eventDeleteProduct');

//         $this->model_setting_event->addEvent('openbay_product_edit_after', 'admin/model/catalog/product/editProduct/after', 'marketplace/openbay/eventEditProduct');

//         $this->model_setting_event->addEvent('openbay_menu', 'admin/view/common/column_left/before', 'marketplace/openbay/eventMenu');
    }

    public function uninstall() {
        $this->load->model('setting/setting');
        $this->load->model('setting/event');

//         $settings = $this->model_setting_setting->getSetting('feed_openbaypro');
//         $settings['feed_openbaypro_status'] = 0;
//         $this->model_setting_setting->editSetting('feed_openbaypro', $settings);

        $this->model_setting_event->deleteEventByCode('b24chat_order_status');
        $this->model_setting_event->deleteEventByCode('b24chat_add_cart');
        $this->model_setting_event->deleteEventByCode('b24chat_remove_cart');
//         $this->model_setting_event->deleteEventByCode('b24chat_add_cart_soconfig');
//         $this->model_setting_event->deleteEventByCode('b24chat_add_cart_soconfig_after');
//         $this->model_setting_event->deleteEventByCode('openbay_product_edit_after');
//         $this->model_setting_event->deleteEventByCode('openbay_menu');
    }
}
