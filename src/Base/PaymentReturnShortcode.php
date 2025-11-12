<?php

namespace Kszkl\Donations\Base;

class PaymentReturnShortcode
{
    public static function register(){
        add_shortcode("donations_for_paynow_return", [self::class, 'render']);
    }

    public static function render($atts){
        $atts = shortcode_atts([
            'button_text' => 'Main Page',
            'button_url' => home_url(),
            'success_msg' => 'Thank you for your contribution',
            'fail_msg' => 'Something went wrong with your payment',
            'sub_msg'=> 'A message with details has been sent to your email'
        ], $atts);

        $status = isset($_GET['paymentStatus']) ? sanitize_text_field($_GET['paymentStatus']) : '';

        $message = ($status == "CONFIRMED") ? $atts['success_msg'] : $atts['fail_msg'];

        $animationHTML = ($status == "CONFIRMED") ? 
            '<svg 
                class="donations-for-paynow__checkmark" 
                xmlns="http://www.w3.org/2000/svg" 
                viewBox="0 0 52 52" 
                style="width: 90px; height: 90px; margin-top: 25px;"
            >
                <circle 
                    class="donations-for-paynow__checkmark__circle" 
                    cx="26" 
                    cy="26" 
                    r="25" 
                    fill="none" 
                    stroke="#383278" 
                    stroke-width="2"
                />
                <path 
                    class="donations-for-paynow__checkmark__check" 
                    fill="none" 
                    stroke="#383278" 
                    stroke-width="2" 
                    d="M14 27l7 7 16-16"
                />
            </svg>' :
            '
            <svg
                class="donations-for-paynow__crossmark"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 52 52"
                style="width: 90px; height: 90px; margin-top: 25px;"
            >
                <circle
                    class="donations-for-paynow__crossmark__circle"
                    cx="26"
                    cy="26"
                    r="25"
                    fill="none"
                    stroke="#383278"
                    stroke-width="2"
                />
                <line
                    class="donations-for-paynow__crossmark__line donations-for-paynow__crossmark__line--1"
                    x1="16"
                    y1="36"
                    x2="36"
                    y2="16"
                    stroke="#383278"
                    stroke-width="2"
                />
                <line
                    class="donations-for-paynow__crossmark__line donations-for-paynow__crossmark__line--2"
                    x1="16"
                    y1="16"
                    x2="36"
                    y2="36"
                    stroke="#383278"
                    stroke-width="2"
                />
        </svg>';

        ob_start();
        ?>

        <div class="donations-for-paynow-return-wrapper">
            <?php echo $animationHTML?>
            <h1 id="donations_for_paynow_return_main_message" style="font-size: 1.8rem; color: #333; margin-bottom: 10px;">
                <?php echo esc_html($message)?>
            </h1>

            <p id="donations_for_paynow_return_transaction_id" style="font-size: 1rem; color: #666; max-width: 400px;">
                <?php echo esc_html($atts['sub_msg'])?>
            </p>

            <a href="<?php echo esc_html($atts['button_url'])?>" class="donations-for-paynow-return-button">
                <?php echo esc_html($atts['button_text'])?>
            </a>
        </div>


        <?php
        return ob_get_clean();
    }
}