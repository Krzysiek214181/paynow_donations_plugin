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

        $errorMessage = "Oops! We couldn't process your payment at the moment. Please try again later.";
        $internal_ref = Uuid::uuid4()->toString();

        $newPaymentResult = $this->dbService->newPayment([
            'internal_ref'  => $internal_ref,
            'amount'           => $data['amount'],
            'description'      => $data['description'],
            'user_email'       => $data['email'],
            'user_name'        => $data['name'],
            'user_surname'     => $data['surname']
        ]);

        if (!$newPaymentResult) {
            echo "<div class='alert alert-danger'>{$errorMessage}</div>";
            return "";
        }

        $payment_data = [
            'amount' => (string) ($data['amount']*100),
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
            $result = $payment->authorize($payment_data);   

            $insertIdResult = $this->dbService->addPaymentTransactionId([
                'transaction_id' => $result->getPaymentId(),
                'internal_ref' => $internal_ref
            ]);

            if(!$insertIdResult){
                echo "<div class='alert alert-danger'>{$errorMessage}</div>";
                return "";
            }

            return $result->getRedirectUrl();

        }catch(PaynowException $exception){
            echo "<div class='alert alert-danger'>{$errorMessage}</div>";
            return "";
        }
    }

}