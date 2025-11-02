<?php

namespace Src\Paynow;

use Exception;
use Paynow\Notification;
use Src\Base\DbService;
use WP_REST_Response;
use WP_REST_Request;

class NotificationHandler
{
    public $dbService;
    private $signatureKey;

    public function __construct(){
        $this->dbService = new DbService();
        $this->signatureKey = get_option('paynow_signatureKey');
    }

    public function register(){
        add_action('rest_api_init', function() {
            register_rest_route('paynowdonations', '/notify', [
                'methods' => 'POST',
                'callback' => 'paynow_handle_notification',
                'permission_callback' => '__return_true'
            ]);
        });
    }

    public function paynow_handle_notification(WP_REST_Request $request) {
        
        $payload = $request->get_body();
        $headers = $request->get_headers();
        $normalizedHeaders = [];
        foreach ($headers as $key => $value) {
            $normalizedHeaders[$key] = is_array($value) ? $value[0] : $value;
        }
    
        $notificationData = json_decode($payload, true);
    
        try {
            new Notification($this->signatureKey, $payload, $normalizedHeaders);
            $this->dbService->updatePaymentStatus([
                'transaction_id' => $notificationData['paymentId'],
                'new_status' => $notificationData['status']
            ]);

            return new WP_REST_Response(['message' => 'Accepted'], 202);
    
        } catch (Exception $exception) {
            return new WP_REST_Response(null, 400);
        }
    }
}