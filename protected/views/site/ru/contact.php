<noindex>
  <div class="container">
    <div class="row">
      <div class="span10 offset1">
        <h2>Контактная информация</h2>
      </div>
    </div>
    <div class="row">
      <div class="span4 offset1">
        <h4>Электронная почта</h4>
        <script type="text/javascript">
          <?php
            for ($i = 0; $i < strlen(Yii::app()->params['contactEmail']); $i++) {
              echo 'document.write("' . (Yii::app()->params['contactEmail'][$i]) . '");';
            }
          ?>
        </script>
        <h4>Почтовый адрес</h4>
        <address>
          <strong>ООО &laquo;ВираТех&raquo;</strong><br />
          ул. Воздвиженка, 7/6, стр. 1<br />
          Москва, 119019<br />
          Российская Федерация
        </address>
      </div>
      <div class="span5">
      </div>
    </div>
  </div>
</noindex>
