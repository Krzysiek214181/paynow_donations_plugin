<?php

namespace Src\Paynow;

use Src\Base\DbService;
use Paynow\Client;
use Paynow\Environment;
use Paynow\Exception\PaynowException;
use Paynow\Service\Payment;
use Ramsey\Uuid\Uuid;

class PaymentHandler
{
    public $dbService;
    public $paynowService;

    public function __construct(){
        $paynowApiKey = get_option('paynow_apiKey');
        $paynowSignatureKey =  get_option('paynow_signatureKey');
        $this->dbService = new DbService();
        $this->paynowService = new Client($paynowApiKey, $paynowSignatureKey, Environment::SANDBOX);
    }

    /**
     * register the payment to db and return redirectlink
     *  @param array{
     *      amount: float,
     *      description: string,
     *      user_email: string,
     *      user_name: string,
     *      user_surname:string
     * } $data
     * @return string
     */
    public function registerNewPayment($data){

        $internal_ref = Uuid::uuid4()->toString();
        $idempotencyKey = uniqid($internal_ref . '_');

        $newPaymentResult = $this->dbService->newPayment([
            'internal_ref'  => $internal_ref,
            'amount'           => $data['amount'],
            'description'      => $data['description'],
            'user_email'       => $data['email'],
            'user_name'        => $data['name'],
            'user_surname'     => $data['surname']
        ]);

        if (!$newPaymentResult) {
            $message = "Oops! We couldn't process your payment at the moment. Please try again later.";
            echo "<div class='alert alert-danger'>{$message}</div>";
            return "";
        }

        $payment_data = [
            'amount' => $data['amount']*100,
            'externalId' => $internal_ref,
            'description' => $data['description'],
            'buyer' => [
                'email' => $data['email'],
                'firstName' => $data['name'],
                'lastName' => $data['surname']
            ]
        ];

        try{
            $payment = new Payment($this->paynowService);
            $result = $payment->authorize($payment_data, $idempotencyKey);   
            echo json_encode($result);
        }catch(PaynowException $exception){
            //TODO
        }
        
        return "";
    }

}