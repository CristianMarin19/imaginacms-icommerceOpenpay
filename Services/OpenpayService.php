<?php

namespace Modules\Icommerceopenpay\Services;

class OpenpayService
{

	public function __construct(){

	}

    /**
    * Make configuration to view in JS
    * @param 
    * @return Object Configuration
    */
	public function makeConfiguration($paymentMethod,$order,$transaction){

		$conf['merchantId'] = $paymentMethod->options->merchantId ?? null;
	 	$conf['publicKey'] = $paymentMethod->options->publicKey ?? null;
	 	
	 	$conf['sandboxMode'] = true;
	 	if($paymentMethod->options->mode!='sandbox')
	 		$conf['sandboxMode'] = false;
	 	
	 	$conf['reedirectAfterPayment'] = $order->url;
	 	
	 	$conf['order'] = $order;
	 	$conf['transaction'] = $transaction;

	 	return json_decode(json_encode($conf));
               
	}

}