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
                'callback' => [$this, 'paynow_handle_notification'],
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

        if(empty($payload)){
            error_log('[paynow_donations] received empty notification');
            return new WP_REST_Response(null, 400);
        }
    
        $notificationData = json_decode($payload, true);
    
        try {
            new Notification($this->signatureKey, $payload, $normalizedHeaders);
            $this->dbService->debugNewNotification([
                'internal_ref' => $notificationData['externalId'],
                'status' => $notificationData['status']
            ]);
            $this->dbService->updatePaymentStatus([
                'internal_ref' => $notificationData['externalId'],
                'new_status' => $notificationData['status']
            ]);

            return new WP_REST_Response(['message' => 'Accepted'], 202);
    
        } catch (Exception $exception) {
            return new WP_REST_Response(null, 400);
        }
    }
}