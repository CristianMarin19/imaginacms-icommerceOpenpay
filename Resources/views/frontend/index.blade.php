@extends('layouts.master')

@section('title')
  Openpay | @parent
@stop


@section('content')
<div class="icommerce_openpay icommerce_openpay_index py-4">
  <div class="container">

      <div class="row justify-content-center">
        <div class="col-md-8">
          {{--
          <h5>imagen prueba</h5>
          <img src="{{url('modules/icommerceopenpay/img/openpay.png')}}" alt="test-img">
          --}}
          
          @include('icommerceopenpay::frontend.partials.form')
          

        </div>

     </div>
     <div class="row justify-content-center">
        <div class="col-md-8">
           @include('icommerceopenpay::frontend.partials.loading')
        </div>
     </div>

  </div>
</div>
@stop


@section('scripts')
@parent
 
 
<script type="text/javascript" src="https://resources.openpay.co/openpay.v1.min.js"></script>
<script type='text/javascript' src="https://resources.openpay.co/openpay-data.v1.min.js"></script>

<script type="text/javascript">
$(document).ready(function () {


  initOpenpay();

  /*
  * Init Open Pay
  */
  function initOpenpay(){
    OpenPay.setId('{{$config->merchantId}}');
    OpenPay.setApiKey('{{$config->publicKey}}');
    OpenPay.setSandboxMode({{$config->sandboxMode}});

    var deviceSessionId = OpenPay.deviceData.setup("payment-form", "deviceIdHiddenFieldName");
  }

  /*
  * Event Click Pay Button
  */
  $('#pay-button').on('click', function(event) {
    event.preventDefault();
    $("#pay-button").prop( "disabled", true);
    OpenPay.token.extractFormAndCreate('payment-form', successCallback, errorCallback);
  });

  /*
  * Sucess
  */
  var successCallback = function(response) {
    //console.warn(response)
    var tokenId = response.data.id;
    processPayment(tokenId)
   
  };

  /*
  * Error
  */
  var errorCallback = function(response) {
    //console.warn(response)
    console.warn("============== ERROR CALL BACK")
    var desc = response.data.description != undefined ? response.data.description : response.message;
    alert("ERROR [" + response.status + "] " + desc);
    $("#pay-button").prop("disabled", false);

  };

  /*
  * API - Process Payment
  */
  async function processPayment(token){

    $("#pay-button").hide();
    $("#loadingPayment").show();

    let url = "{{route('icommerceopenpay.api.openpay.processPayment')}}"
    
    let data = {
        orderId:{{$config->order->id}},
        clientToken: token,
        deviceId: $('#deviceIdHiddenFieldName').val()
    }
    
    // FETCH
    let response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json;charset=utf-8'
      },
      body: JSON.stringify({attributes:data})
    });

    let result = await response.json();

    $("#loadingPayment").hide();
    
    // CHECK RESULT
    if(result){

      //let data = result.data;
      if(result.status=="success"){
        //console.warn(data.transaction)
        alert("PROCESO EXITOSO")

      }else{
        //$("#btnPay").show();
        alert("ERROR: "+result.error)
      }

      finishedPayment()

    }
   

   
  }


  /*
  * Reedirect to Order
  */
  function finishedPayment(){

    $('#dialogTitle').text("Reedireccionando...");
    $("#loadingPayment .alert").removeClass("alert-warning");
    $("#loadingPayment .alert").addClass("alert-success");

    $("#loadingPayment").show();

    let redirect = "{{$config->reedirectAfterPayment}}";
    window.location.href = redirect;

  }

});
</script>



@stop