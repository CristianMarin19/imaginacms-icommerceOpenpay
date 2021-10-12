<form action="#" method="POST" id="payment-form">
   <input type="hidden" name="token_id" id="token_id">

    <div class="card">

      <div class="card-header">
        Openpay - Bienvenido
      </div>

      <div class="card-body bg-light">

        {{--
        <div class="row">

          <div class="col-xs-12 col-md-6"><h6>Tarjetas de crédito</h6> </div>
          <div class="col-xs-12 col-md-6"><h6>Tarjetas de débito</h6></div>

        </div>
        --}}

        <div class="form-row">
           <div class="form-group col-md-6">
              <label >Nombre del titular</label>
              <input type="text" class="form-control" placeholder="Como aparece en la tarjeta" autocomplete="off" data-openpay-card="holder_name">
            </div>
            <div class="form-group col-md-6">
              <label>Número de tarjeta</label>
              <input type="text" class="form-control" autocomplete="off" data-openpay-card="card_number">
            </div>
        </div>

        <div class="form-row">
           
            <div class="form-group col-md-6">
              {{--
              <label>Fecha de expiración</label>
              --}}
              <div class="row">
                <div class="col-xs-12 col-md-6">
                  {{--
                  <input type="text" class="form-control" placeholder="Mes" data-openpay-card="expiration_month">
                  --}}
                  <label>Mes</label>
                  <select class="custom-select" data-openpay-card="expiration_month">
                    <option value="01" selected>01</option>
                    <option value="02">02</option>
                    <option value="03">03</option>
                    <option value="04">04</option>
                    <option value="05">05</option>
                    <option value="06">06</option>
                    <option value="07">07</option>
                    <option value="08">08</option>
                    <option value="09">09</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                  </select>
                </div>
                <div class="col-xs-12 col-md-6">
                  <label>Año</label>
                  <input type="number" min="21" max="30" class="form-control" placeholder="Ultimos 2 digitos" data-openpay-card="expiration_year">
                </div>
              </div>
            </div>
            <div class="form-group col-md-6">
              <label>Código de seguridad</label>
              <input type="text" class="form-control w-50" placeholder="3 dígitos" autocomplete="off" data-openpay-card="cvv2">
            </div>

        </div>
        {{--
        <div class="d-flex justify-content-end">
            <div class="logo">Transacciones realizadas vía:</div>
                        
            <div class="shield">Tus pagos se realizan de forma segura con encriptación de 256 bits</div>
        </div>
        --}}

        <div class="d-flex justify-content-end">
          <a id="pay-button" class="btn btn-primary cursor-pointer">Pagar: {{formatMoney($config->order->total)}}</a>
        </div>

        

      </div>
    </div>
</form>