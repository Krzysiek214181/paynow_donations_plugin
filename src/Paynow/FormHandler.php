<?php

namespace Src\Paynow;

use Src\Paynow\PaymentHandler;

class FormHandler
{

    private $paymentHandler;

    public function __construct(){
        $this->paymentHandler = new PaymentHandler();
    }
    //
    public function register(){
        add_action('admin_post_paynow_submit_donation', [$this, 'paynow_handle_donation_submit']);
        add_action('admin_post_nopriv_paynow_submit_donation', [$this, 'paynow_handle_donation_submit']);
    }

    public function paynow_handle_donation_submit(){
        if(!isset($_POST['paynow_nonce']) || !wp_verify_nonce($_POST['paynow_nonce'], 'paynow-donation-form')){
            wp_die('Security check failed');
        }

        $data = [
            'name'        => sanitize_text_field($_POST['paynow_name'] ?? ''),
            'surname'     => sanitize_text_field($_POST['paynow_surname'] ?? ''),
            'email'       => sanitize_email($_POST['paynow_email'] ?? ''),
            'description' => sanitize_text_field($_POST['paynow_description'] ?? ''),
            'amount'      => floatval($_POST['paynow_amount'] ?? 0),
        ];

        $redirectUlr = $this->paymentHandler->registerNewPayment($data);

        if(!empty($redirectUlr)){
            wp_safe_redirect($redirectUlr);
        }

        echo "Something went wrong, please try again";
    
        exit;
    }
}