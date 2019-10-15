<?php

namespace Forge\Modules\PaymentPaypal;

use \Forge\Modules\ForgePayment\Payment;
use \Forge\Core\App\App;
use \Forge\Core\Classes\Localization;
use \Forge\Core\Classes\Mail;
use \Forge\Core\Classes\Settings;
use \Forge\Core\Classes\User;
use \Forge\Core\Classes\Utils;

/**
 * Help the Translation Crawler
 * i('paypal', 'forge-payment')
 */

class Adapter {
    public static $id = 'paypal';
    private $orderId = null;
    private $item = null;
    private $payment = null;

    public function __construct($orderId) {
        $this->orderId = $orderId;
        $this->payment = Payment::getOrder($this->orderId);
    }

    public static function payView($parts) {

        App::instance()->db->where('token', $_GET['token']);
        $orders = App::instance()->db->get('forge_payment_orders');
        if(count($orders) == 1) {
            App::instance()->db->where('token', $_GET['token']);
            App::instance()->db->update('forge_payment_orders', [
                'payment_type' => 'paypal',
                "status" => "success",
                "order_confirmed" => App::instance()->db->now()
            ]);

            App::instance()->redirect($_SESSION['redirectSuccess']);
        } else {
            App::instance()->redirect($_SESSION['redirectCancel']);
        }

    }

    public function infos() {
        $token = 'PP-'.Utils::hash(microtime());

        // set the token on the database.
        App::instance()->db->where('id' , $this->orderId);
        App::instance()->db->update('forge_payment_orders', ['token' => $token]);

        return ['raw' => App::instance()->render(MOD_ROOT."forge-payment-paypal/templates/", 'paypal-button', [
            'env' => Settings::get('forge-payment-paypal-sandbox-mode') == 'on' ? 'sandbox' : 'production',
            'currency' => Payment::getCurrency(),
            'amount' => $this->payment->getTotalAmount(),
            'client_sandbox' => Settings::get('forge-payment-paypal-client-id-sandbox'),
            'client_production' => Settings::get('forge-payment-paypal-client-id-production'),
            'redirect_success' => Utils::getUrl(['pay', 'paypal', $this->orderId]),
            'token'=> $token
        ])];
    }
}

?>
