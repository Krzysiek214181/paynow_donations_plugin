<?php

namespace Src\Base;

class FormShortcode
{
    public static function register(){
        add_shortcode("paynow_donation_form", [self::class, 'renderForm']);
    }

    public static function renderForm($atts = [], $content = null){
        $atts = shortcode_atts([
            'main_text' => 'Donate Here',
            'user_legend' => 'Donor Information',
            'payment_legend' => 'Payment Information',
            'name_label' => 'Name',
            'name_placeholder' => 'Full Name',
            'surname_label' => 'Surname',
            'surname_placeholder' => 'Surname',
            'email_label' => 'Email',
            'email_placeholder' => 'Email Address',
            'description_label' => 'Description',
            'description_placeholder' => 'Payment Description',
            'amount_label' => 'Amount',
            'amount_placeholder' => 'Amount',
            'button_text' => 'Donate'
        ], $atts);

        ob_start();
        ?>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php'));?>" class="paynow-donation-form">
            <?php wp_nonce_field('paynow-donation-form', 'paynow_nonce')?>
            <input type="hidden" name="action" value="paynow_submit_donation">

            <h1><?php echo esc_html($atts['main_text'])?></h1>
            <fieldset class="paynow-donation-form-fieldset">
                <legend><?php echo esc_html($atts['user_legend'])?></legend>

                <span style="display: flex; width: 100%; gap:26px;">
                    <p style="width:48%; align-self: flex-start;">
                        <label><?php echo esc_html($atts['name_label']); ?>:</label><br>
                        <input style="width:100%;" type="text" name="paynow_name"  placeholder="<?php echo esc_html($atts['name_placeholder'])?>" required>
                    </p>

                    <p style="width:48%; align-self: flex-end;">
                        <label><?php echo esc_html($atts['surname_label']); ?>:</label><br>
                        <input style="width:100%;" type="text" name="paynow_surname" placeholder="<?php echo esc_html($atts['surname_placeholder'])?>" required>
                    </p>
                </span>

                <p>
                    <label><?php echo esc_html($atts['email_label']); ?>:</label><br>
                    <input style="width:100%; "type="email" name="paynow_email" placeholder="<?php echo esc_html($atts['email_placeholder'])?>" required>
                </p>
            </fieldset>
            
            <fieldset class="paynow-donation-form-fieldset">
                <legend><?php echo esc_html($atts['payment_legend'])?></legend>
                
                <p>
                    <label><?php echo esc_html($atts['description_label']); ?>:</label><br>
                    <input style="width:100%;" type="text" name="paynow_description" placeholder="<?php echo esc_html($atts['description_placeholder'])?>" required>
                </p>

                <p>
                    <label><?php echo esc_html($atts['amount_label']); ?>:</label><br>
                    <input style="width:100%" type="number" name="paynow_amount" step="0.01" min="1" max="1000000" placeholder="<?php echo esc_html($atts['amount_placeholder'])?>" required>
                </p>
            </fieldset>
            <p style="width: 80%;">
                <button type="submit"><?php echo esc_html($atts['button_text']); ?></button>
            </p>
        </form>

        <?php

        return ob_get_clean();
    }
}