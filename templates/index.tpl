<div class="owl-carousel owl-theme">
    <div class="item"><a href="#"><img src="img/image-min.jpg"></a></div>
    <div class="item"><a href="#"><img src="img/image2-min.jpg"></a></div>
    <div class="item"><a href="#"><img src="img/image3-min.jpg"></a></div>
    <div class="item"><h4>4</h4></div>
    <div class="item"><h4>5</h4></div>
    <div class="item"><h4>6</h4></div>
</div>
<h4> Популярные товары:</h4><br>
<div id="items"  class="row">
    {foreach from=$row item=str}
        <div  class="col-12 col-md-6 col-lg-4">
            <a data-toggle="modal"  data-target="#my1Modal"><img  width="130" height="130" src="img\goods\crop\{$str.img}"></a>
                                                                  <div id="des">{$str.name} <br> 
                <span id="price">
                    {$str.price} <f class="rubl">о</f> 
                </span>   
        </div>
    </div>
{/foreach}
<div id="my1Modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><button class="close" type="button" data-dismiss="modal">×</button>
                Всего товаров: 200000<div>Сумма заказа: 100000 <f class="rubl">о</f>    
                </div></div>
            <div class="modal-body">Текст уведомления</div>
            <div class="modal-body">Текст уведомления</div>
            <div class="modal-body">Текст уведомления</div>
            <div class="modal-body">Текст уведомления</div>
            <div class="modal-footer">
                <button class="btn btn-default" type="button" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
</div>



