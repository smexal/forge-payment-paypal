<?php

namespace Forge\Modules\PaymentPaypal;

use \Forge\Core\Abstracts\Module as AbstractModule;
use \Forge\Core\App\App;
use \Forge\Core\App\Auth;
use \Forge\Core\App\ModifyHandler;
use \Forge\Core\Classes\Fields;
use \Forge\Core\Classes\Logger;
use \Forge\Core\Classes\Settings;
use \Forge\Loader;


class Module extends AbstractModule {

    public function setup() {
        $this->settings = Settings::instance();
        $this->version = '1.0.0';
        $this->id = "forge-payment-paypal";
        $this->name = i('Paypal Payment', 'forge-payment-paypal');
        $this->description = i('Payment Adapter as extension for the forge payment to add Paypal Payment Methods.', 'forge-payment-paypal');
        $this->image = $this->url().'images/module-image.png';
    }

    public function start() {
        /**
         * The "forge-payment" plugin is required
         * i('paypal', 'forge-payment')
         */
        if(! App::instance()->mm->isActive('forge-payment')) {
            Logger::error('forge-payment Plugin required for forge-payment-paypal');
            return;
        }

        App::instance()->tm->theme->addScript("https://www.paypalobjects.com/api/checkout.js", true);
        App::instance()->tm->theme->addStyle(MOD_ROOT."forge-payment-paypal/assets/forge-payment-paypal.less");

        //App::instance()->tm->theme->addStyle(MOD_ROOT."forge-payment-stripe/css/forge-payment-paypal.less");

        ModifyHandler::instance()->add(
            'modify_forge_payment_adapters', 
            [$this, 'addPaypalAdapter']
        );

        $this->settings();
    }

    public function addPaypalAdapter($data) {
        $data[] = '\Forge\Modules\PaymentPaypal\Adapter';
        return $data;
    }

    private function settings() {
        if (! Auth::allowed("manage.settings", true)) {
            return;
        }

        $this->settings->registerField(
            Fields::text(array(
            'key' => 'forge-payment-paypal-client-id-production',
            'label' => i('Paypal Client ID Production', 'forge-payment'),
            'hint' => i('Check official Paypal Developer Page for more information: https://developer.paypal.com/developer/applications/', 'forge-payment')
        ), Settings::get('forge-payment-paypal-client-id-production')), 'forge-payment-paypal-client-id-production', 'left', 'forge-payment');

        $this->settings->registerField(
            Fields::text(array(
            'key' => 'forge-payment-paypal-client-id-sandbox',
            'label' => i('Paypal Client ID Sandbox', 'forge-payment-paypal'),
            'hint' => ''
        ), Settings::get('forge-payment-paypal-client-id-sandbox')), 'forge-payment-paypal-client-id-sandbox', 'left', 'forge-payment');

        $this->settings->registerField(
            Fields::checkbox(array(
            'key' => 'forge-payment-paypal-sandbox-mode',
            'label' => i('Use Sandbox Mode'),
            'hint' => i('If this setting is enabled, paypal sandbox domain will be used.'),
        ), Settings::get('forge-payment-paypal-sandbox-mode')), 'forge-payment-paypal-sandbox-mode', 'left', 'forge-payment');
    }

}

?>
