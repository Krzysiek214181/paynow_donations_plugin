<?php

namespace Kszkl\Donations\Base;

class FormShortcode
{
    public static function register(){
        add_shortcode("donations_for_paynow_form", [self::class, 'renderForm']);
    }

    public static function renderForm($atts = [], $content = null){
        $atts = shortcode_atts([
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
            'button_text' => 'Donate',
            'sug_amount_1' => '10',
            'sug_amount_2' => '25',
            'sug_amount_3' => '50',
            'sug_amount_4' => '100',
        ], $atts);

        ob_start();
        ?>
        <div class="donations-for-paynow-form-wrapper">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php'));?>" class="donations-for-paynow-form">
                <?php wp_nonce_field('donations-for-paynow-form', 'donations_for_paynow_nonce')?>
                <input type="hidden" name="action" value="donations_for_paynow_submit_donation">

                <fieldset class="donations-for-paynow-form-fieldset">
                    <legend><?php echo esc_html($atts['user_legend'])?></legend>

                    <div class="donations-for-paynow-form-row">
                        <div class="donations-for-paynow-form-group">
                            <label><?php echo esc_html($atts['name_label']); ?></label>
                            <input type="text" name="donations_for_paynow_name" placeholder="<?php echo esc_html($atts['name_placeholder'])?>" required>
                        </div>

                        <div class="donations-for-paynow-form-group">
                            <label><?php echo esc_html($atts['surname_label']); ?></label>
                            <input type="text" name="donations_for_paynow_surname" placeholder="<?php echo esc_html($atts['surname_placeholder'])?>" required>
                        </div>
                    </div>

                    <div class="donations-for-paynow-form-group">
                        <label><?php echo esc_html($atts['email_label']); ?></label>
                        <input 
                            style="width:100%;"
                            type="email" 
                            name="donations_for_paynow_email" 
                            pattern="[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+" 
                            title="Please enter a valid email address like example@domain.com" 
                            placeholder="<?php echo esc_html($atts['email_placeholder'])?>" 
                            required\
                        >
                    </div>
                </fieldset>
                
                <fieldset>
                    <legend><?php echo esc_html($atts['payment_legend'])?></legend>
                    
                    <div class="donations-for-paynow-form-group">
                        <label><?php echo esc_html($atts['description_label']); ?></label>
                        <input type="text" name="donations_for_paynow_description" placeholder="<?php echo esc_html($atts['description_placeholder'])?>" required>
                    </div>

                    <div class="donations-for-paynow-form-group">
                        <label><?php echo esc_html($atts['amount_label']); ?></label>
                        <div class="donations-for-paynow-amount-input-wrapper">
                            <input 
                                type="number" 
                                name="donations_for_paynow_amount" 
                                step="0.01" 
                                min="1" 
                                max="1000000" 
                                placeholder="<?php echo esc_html($atts['amount_placeholder'])?>" 
                                required
                            >
                            <span class="donations-for-paynow-currency-label">zł</span>
                        </div>
                    </div>
                    <div class="donations-for-paynow-form-suggested-amounts">
                        <button type="button" class="donations-for-paynow-form-suggested-amount" data-value="<?php echo esc_html($atts['sug_amount_1'])?>"><?php echo esc_html($atts['sug_amount_1'])?> zł</button>
                        <button type="button" class="donations-for-paynow-form-suggested-amount" data-value="<?php echo esc_html($atts['sug_amount_2'])?>"><?php echo esc_html($atts['sug_amount_2'])?> zł</button>
                        <button type="button" class="donations-for-paynow-form-suggested-amount" data-value="<?php echo esc_html($atts['sug_amount_3'])?>"><?php echo esc_html($atts['sug_amount_3'])?> zł</button>
                        <button type="button" class="donations-for-paynow-form-suggested-amount" data-value="<?php echo esc_html($atts['sug_amount_4'])?>"><?php echo esc_html($atts['sug_amount_4'])?> zł</button>
                    </div>
                </fieldset>
                
                <button type="submit" class="donations-for-paynow-form-submit-btn"><?php echo esc_html($atts['button_text']); ?></button>
            </form>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const buttons = document.querySelectorAll(".donations-for-paynow-form-suggested-amount");
                const amountInput = document.querySelector("input[name='donations_for_paynow_amount']");
                if (!buttons.length || !amountInput) return;

                buttons.forEach(btn => {
                    btn.addEventListener("click", () => {
                        buttons.forEach(b => b.classList.remove("active"));
                        btn.classList.add("active");
                        amountInput.value = btn.dataset.value;
                    });
                });
            });
        </script>

        <?php

        return ob_get_clean();
    }
}