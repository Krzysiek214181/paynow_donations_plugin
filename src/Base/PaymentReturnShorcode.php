<?php

namespace Src\Base;

class PaymentReturnShorcode
{
    public static function register(){
        add_shortcode("paynow_return", [self::class, 'render']);
    }

    public static function render($atts){
        $atts = shortcode_atts([
            'buttonText' => 'Main Page',
            'buttonUrl' => '',
            'successMsg' => 'Thank you for your contribution',
            'failMsg' => 'Something went wrong with your payment',
            'showId' => true,
            'transactionIdMsg'=> 'Your transaction ID is'
        ], $atts);

        $transaction_id = isset($_GET['paymentId']) ? sanitize_text_field($_GET['paymentId']) : '';
        $status = isset($_GET['paymentStatus']) ? sanitize_text_field($_GET['paymentStatus']) : '';

        $message = "";
        $transacionIdMessage = "";

        if($status == "CONFIRMED"){
            $message = $atts['successMsg'];
        }else{
            $message = $atts['failMsg'];
        }

        if (filter_var($atts['showId'], FILTER_VALIDATE_BOOLEAN) && !empty($transaction_id)) {
            $transacionIdMessage .= esc_html($atts['transactionIdMsg']) . ' <strong>' . esc_html($transaction_id) . '</strong>';
        }

        ob_start();
        ?>

        <div id="paynow_return_main_div">
            <h1 id="paynow_return_main_message">
                <?php echo esc_html($message)?>
            </h1>

            <p id="paynow_return_transaction_id">
                <?php echo $transacionIdMessage?>
            </p>
        </div>


        <?php
        return ob_get_clean();
    }
}