<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="paymentModalLabel">Оплатить </h4>
      </div>
      <div class="modal-body">
          <div class="row"></div>
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

<div class="panel panel-default mb-1 hidden PM_protoModel">
    <div class="panel-heading"></div>
    <div class="panel-body">
        <img src="" width="100px"  class="thumbnail mb-0 d-inline" />
    </div>
    <ul class="list-group"></ul>
    <div class="panel-footer text-bold">
        <span class="label label-primary accrued hidden">Зачислено!</span>
        <span class="label label-default notPayed hidden">Не Оплачено!</span>
        <span class="label label-success paySuccess hidden">Оплачено!</span>
    </div>
</div>