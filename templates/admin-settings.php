<?php
$apiKey = get_option('paynow_apiKey', '');
$signatureKey = get_option('paynow_signatureKey', '');
$environment = get_option('paynow_environment', '');
$debug = get_option('paynow_debug', '');
?>

<div class="wrap">
    <h1>Paynow Settings</h1>
    <?php settings_errors(); ?>
    <form method="post" action="options.php">
        <?php settings_fields('paynow_donations_settings_group'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">API Key</th>
                <td><input id="paynow_apiKey" type="password" name="paynow_apiKey" value="<?php echo esc_attr($apiKey); ?>" class="regular-text" style="margin-right: 8px" /><button type="button" class="button toggle-password" data-target="paynow_apiKey">Show</button></td>
            </tr>
            <tr valign="top">
                <th scope="row">Signature Key</th>
                <td><input id="paynow_signatureKey" type="password" name="paynow_signatureKey" value="<?php echo esc_attr($signatureKey); ?>" class="regular-text" style="margin-right: 8px" /><button type="button" class="button toggle-password" data-target="paynow_signatureKey">Show</button></td>
            </tr>
            <tr valing="top">
                <th scope="row">Environment</th>
                <td>
                    <select id="paynow_environment" name="paynow_environment">
                        <option value="0" <?php selected($environment, '0') ?>>SANDBOX</option>
                        <option value="1" <?php selected($environment, '1')?>>PRODUCTION</option>
                    </select>
                </td>
            </tr>
            <tr valing="top">
                <th scope="row">Debug</th>
                <td>
                    <select id="paynow_debug" name="paynow_debug">
                        <option value="1" <?php selected($debug, '1') ?>>ON</option>
                        <option value="0" <?php selected($debug, '0')?>>OFF</option>
                    </select>
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.toggle-password');

            buttons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const inputId = btn.getAttribute('data-target');
                    const input = document.getElementById(inputId);

                    if (input.type === 'password') {
                        input.type = 'text';
                        btn.textContent = 'Hide';
                    } else {
                        input.type = 'password';
                        btn.textContent = 'Show';
                    }
                });
            });
        });
    </script>
</div>