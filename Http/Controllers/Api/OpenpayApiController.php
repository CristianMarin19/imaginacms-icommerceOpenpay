<?php

namespace Modules\Icommerceopenpay\Http\Controllers\Api;

// Requests & Response
use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Base Api
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;

use Openpay\Data\Client as Openpay;

class OpenpayApiController extends BaseApiController
{

    private $gateway;
    private $openpayService;

    public function __construct(){
        $this->gateway = $this->getGateway();
        $this->openpayService = app("Modules\Icommerceopenpay\Services\OpenpayService");
    }

    /**
    * Get gateway
    * @param 
    * @return gateway
    */
    public function getGateway(){

        // Payment Method Configuration
        $paymentMethod = openpayGetConfiguration();

        $isProduction = openpayIsProductionMode($paymentMethod->options->mode);

        Openpay::setProductionMode($isProduction);

        $gateway = Openpay::getInstance(
            $paymentMethod->options->merchantId, 
            $paymentMethod->options->privateKey,
            'CO'
        );

        return $gateway;

    }

    
    /**
    * Create Charge
    * @param 
    * @return result
    */
    public function createCharge($order,$token,$deviceId){
        
       
        try {
            \Log::info('Icommerceopenpay: Create Charge');

             // create object customer
            $customer = array(
                'name' => $order->first_name,
                'last_name' => $order->last_name,
                'email' => $order->email
            );
            
            $chargeData = array(
                'method' => 'card',
                'source_id' => $token,
                'amount' => $order->total,
                'currency' => $order->currency_code,
                'description' => openpayGetOrderDescription($order),
                'order_id' => openpayGetOrderRefCommerce($order),
                'device_session_id' => $deviceId,
                'customer' => $customer
            );

            $charge = $this->gateway->charges->create($chargeData);

            \Log::info('Icommerceopenpay: Charge Status: '.$charge->status);

            // Format response
            $response = [
                'status'=> 'success',
                'charge' => [
                    'id' => $charge->id,
                    'status' => $charge->status 
                ]
            ];


        } catch (\OpenpayApiTransactionError | \OpenpayApiRequestError | \OpenpayApiConnectionError | \OpenpayApiAuthError | \OpenpayApiError | \Exception $e) {

            \Log::info('Icommerceopenpay: Create Charge - ERROR: '.$e->getMessage().' Code:'.$e->getErrorCode());
            //error_log('ERROR ' . $e->getCategory() . ': ' . $e->getMessage(), 0);
            $response = [
                'status' => 'error',
                'error' => $e->getMessage(),
                'code' => $e->getErrorCode(),
            ];
        }

        return $response;
    }

 
}