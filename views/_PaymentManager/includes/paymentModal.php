<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="paymentModalLabel">Оплатить </h4>
      </div>
      <div class="modal-body">
          <div class="row">
              <div class="col-sm-12 pl-1 pr-1 columnFirst"></div>
              <div class="col-sm-12 pl-1 pr-1 columnSecond"></div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
            <span class="glyphicon glyphicon-triangle-left" aria-hidden="true"></span>
            Отмена
        </button>
        <button type="button" class="btn btn-primary pull-right payButton">
            <span class="glyphicon glyphicon-usd" aria-hidden="true"></span>
            Оплатить
        </button>
      </div>
    </div>
  </div>
</div>

<div id="alertResponse" data-izimodal-group="alerts"></div>

<div class="panel panel-success mb-1 hidden PM_protoModel">
    <div class="panel-heading cursorPointer relative" role="tab" id="">
        <a class="collapsed panel-title modelInfo" role="button" data-toggle="collapse" href="" aria-expanded="false" aria-controls=""></a>
    </div>
    <div id="" class="panel-collapse collapse" role="tabpanel" aria-labelledby="" aria-expanded="false">
        <div class="panel-body">
            <img src="" width="100px" class="thumbnail mb-0 d-inline" />
        </div>
        <ul class="list-group"></ul>
        <div class="panel-footer text-bold">
            <span class="label label-primary accrued hidden">Зачислено!</span>
            <span class="label label-default notPayed hidden">Не Оплачено!</span>
            <span class="label label-success paySuccess hidden">Оплачено!</span>
        </div>
    </div>
</div>

