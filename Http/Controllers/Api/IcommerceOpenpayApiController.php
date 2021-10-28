<?php

namespace Modules\Icommerceopenpay\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

//Request
use Modules\Icommerceopenpay\Http\Requests\InitRequest;

// Base Api
use Modules\Icommerce\Http\Controllers\Api\OrderApiController;
use Modules\Icommerce\Http\Controllers\Api\TransactionApiController;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Modules\Icommerceopenpay\Http\Controllers\Api\OpenpayApiController;

// Repositories
use Modules\Icommerce\Repositories\TransactionRepository;
use Modules\Icommerce\Repositories\OrderRepository;
use Modules\Icommerceopenpay\Repositories\IcommerceOpenpayRepository;

use Modules\Icommerce\Entities\Transaction as TransEnti;


class IcommerceOpenpayApiController extends BaseApiController
{

    private $icommerceopenpay;
    private $order;
    private $orderController;
    private $transaction;
    private $transactionController;
    private $openpayApi;
    
    public function __construct(
        IcommerceOpenpayRepository $icommerceopenpay,
        OrderRepository $order,
        OrderApiController $orderController,
        TransactionRepository $transaction,
        TransactionApiController $transactionController,
        OpenpayApiController $openpayApi
    ){
        $this->icommerceopenpay = $icommerceopenpay;
        $this->order = $order;
        $this->orderController = $orderController;
        $this->transaction = $transaction;
        $this->transactionController = $transactionController;
        $this->openpayApi = $openpayApi;
    }

    /**
    * Init Calculations (Validations to checkout)
    * @param Requests request
    * @return mixed
    */
    public function calculations(Request $request)
    {
      
      try {

        $paymentMethod = openpayGetConfiguration();
        $response = $this->icommerceopenpay->calculate($request->all(), $paymentMethod->options);
        
      } catch (\Exception $e) {
        //Message Error
        $status = 500;
        $response = [
          'errors' => $e->getMessage()
        ];
      }
      
      return response()->json($response, $status ?? 200);
    
    }

    

    /**
     * ROUTE - Init data
     * @param Requests request
     * @param Requests orderId
     * @return route
     */
    public function init(Request $request){
      
        try {
            
            
            $data = $request->all();
           
            $this->validateRequestApi(new InitRequest($data));

            $orderID = $request->orderId;
            //\Log::info('Module Icommerceopenpay: Init-ID:'.$orderID);

            // Payment Method Configuration
            $paymentMethod = openpayGetConfiguration();

            // Order
            $order = $this->order->find($orderID);
            $statusOrder = 1; // Processing

            // Validate minimum amount order
            if(isset($paymentMethod->options->minimunAmount) && $order->total<$paymentMethod->options->minimunAmount)
              throw new \Exception(trans("icommerceopenpay::icommerceopenpays.messages.minimum")." :".$paymentMethod->options->minimunAmount, 204);

            // Create Transaction
            $transaction = $this->validateResponseApi(
                $this->transactionController->create(new Request( ["attributes" => [
                    'order_id' => $order->id,
                    'payment_method_id' => $paymentMethod->id,
                    'amount' => $order->total,
                    'status' => $statusOrder
                ]]))
            );

            // Encri
            $eUrl = openpayEncriptUrl($order->id,$transaction->id);
        
            $redirectRoute = route('icommerceopenpay',[$eUrl]);

            // Response
            $response = [ 'data' => [
                  "redirectRoute" => $redirectRoute,
                  "external" => true
            ]];


        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            $status = 500;
            $response = [
                'errors' => $e->getMessage()
            ];
        }


        return response()->json($response, $status ?? 200);

    }


    /**
    * ROUTE - POST Process Payment
    * @param OrderId
    * @param clientToken
    * @param deviceId
    * @return response
    */
    public function processPayment(Request $request){

        \Log::info('Icommerceopenpay: Process Payment - INIT ==============');

        try {
             
            $data = $request['attributes'] ?? [];//Get data

            $orderId = $data['orderId'];
            $transactionId = $data['transactionId'];

            // Validate Exist Order Id
            $order = $this->order->find($orderId);
            \Log::info('Icommerceopenpay: OrderID: '.$order->id);

            // Validate that the transaction is associated with the order
            $transaction = $order->transactions->find($transactionId);
            \Log::info('Icommerceopenpay: transactionID: '.$transaction->id);
            

            $response = $this->openpayApi->createCharge($order,$transaction,$data['clientToken'],$data['deviceId']);

            
            if($response['status']=="success"){
                //Processed
                $this->updateInformation($orderId,$transactionId,13);
            }else{
                //failed
                $this->updateInformation($orderId,$transactionId,7,$response); 
            }
           
        }catch(\Exception $e){

            $status = 500;
            $response = [
              'errors' => $e->getMessage()
            ];
            \Log::error('Icommerceopenpay: Process Payment - Message: '.$e->getMessage());
            \Log::error('Icommerceopenpay: Process Payment - Code: '.$e->getCode());
        }

        \Log::info('Icommerceopenpay: Process Payment - END ==============');

        return response()->json($response, $status ?? 200);

    }

    /**
    * Update Information
    */
    public function updateInformation($orderId,$transactionId,$newStatusOrder,$response=null){

        \Log::info('Icommerceopenpay: Updating Information');

        \Log::info('Icommerceopenpay: New Status Order: '.$newStatusOrder);

        $externalStatus = $response["error"] ?? "";
        $externalCode = $response["code"] ?? "";

        // Update Transaction
        $transaction = $this->validateResponseApi(
            $this->transactionController->update($transactionId,new Request([
                'status' => $newStatusOrder,
                'external_status' => $externalStatus,
                'external_code' => $externalCode
            ]))
        );

        // Update Order
        $orderUP = $this->validateResponseApi(
            $this->orderController->update($orderId,new Request(
                ["attributes" =>[
                    'status_id' => $newStatusOrder
                ]
            ]))
        );

    }


   
}