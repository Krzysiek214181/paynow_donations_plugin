<?php
$apiKey = get_option('donations_for_paynow_apiKey', '');
$signatureKey = get_option('donations_for_paynow_signatureKey', '');
$environment = get_option('donations_for_paynow_environment', '');
$debug = get_option('donations_for_paynow_debug', '');
?>

<div class="wrap">
    <h1>Donations for Paynow Settings</h1>
    <?php settings_errors(); ?>
    <form method="post" action="options.php">
        <?php settings_fields('donations_for_paynow_settings_group'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">API Key</th>
                <td><input id="donations_for_paynow_apiKey" type="password" name="donations_for_paynow_apiKey" value="<?php echo esc_attr($apiKey); ?>" class="regular-text" style="margin-right: 8px" /><button type="button" class="button toggle-password" data-target="donations_for_paynow_apiKey">Show</button></td>
            </tr>
            <tr valign="top">
                <th scope="row">Signature Key</th>
                <td><input id="donations_for_paynow_signatureKey" type="password" name="donations_for_paynow_signatureKey" value="<?php echo esc_attr($signatureKey); ?>" class="regular-text" style="margin-right: 8px" /><button type="button" class="button toggle-password" data-target="donations_for_paynow_signatureKey">Show</button></td>
            </tr>
            <tr valing="top">
                <th scope="row">Environment</th>
                <td>
                    <select id="donations_for_paynow_environment" name="donations_for_paynow_environment">
                        <option value="0" <?php selected($environment, false) ?>>SANDBOX</option>
                        <option value="1" <?php selected($environment, true)?>>PRODUCTION</option>
                    </select>
                </td>
            </tr>
            <tr valing="top">
                <th scope="row">Debug</th>
                <td>
                    <select id="donations_for_paynow_debug" name="donations_for_paynow_debug">
                        <option value="1" <?php selected($debug, true) ?>>ON</option>
                        <option value="0" <?php selected($debug, false)?>>OFF</option>
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