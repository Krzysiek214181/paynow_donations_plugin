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
        add_action('admin_post_donations_for_paynow_submit_donation', [$this, 'donations_for_paynow_handle_submit']);
        add_action('admin_post_nopriv_donations_for_paynow_submit_donation', [$this, 'donations_for_paynow_handle_submit']);
    }

    public function donations_for_paynow_handle_submit(){
        if(!isset($_POST['donations_for_paynow_nonce']) || !wp_verify_nonce($_POST['donations_for_paynow_nonce'], 'donations-for-paynow-form')){
            wp_die('Security check failed');
        }

        $data = [
            'name'        => sanitize_text_field($_POST['donations_for_paynow_name'] ?? ''),
            'surname'     => sanitize_text_field($_POST['donations_for_paynow_surname'] ?? ''),
            'email'       => sanitize_email($_POST['donations_for_paynow_email'] ?? ''),
            'description' => sanitize_text_field($_POST['donations_for_paynow_description'] ?? ''),
            'amount'      => floatval($_POST['donations_for_paynow_amount'] ?? 0),
        ];

        $redirectUlr = $this->paymentHandler->registerNewPayment($data);

        if(!empty($redirectUlr)){
            wp_safe_redirect($redirectUlr);
        }

        echo "Something went wrong, please try again";
    
        exit;
    }
}