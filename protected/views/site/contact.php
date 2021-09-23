<noindex>
  <div class="container">
    <div class="row">
      <div class="span10 offset1">
        <h2>Contact Us</h2>
        <h4>Mailing address</h4>
        <address>
          <strong>ActiveDNS dpt. of Vira Technologies Ltd</strong><br />
          7/6 Vozdvizhenka st, bld. 1, office 8<br />
          Moscow, 119019<br />
          Russian Federation<br />
        </address>
        <h4>E-mail</h4>
        <script type="text/javascript">
          <?php
            for ($i = 0; $i < strlen(Yii::app()->params['contactEmail']); $i++) {
              echo 'document.write("' . (Yii::app()->params['contactEmail'][$i]) . '");';
            }
          ?>
        </script>
      </div>
    </div>
  </div>
</noindex>
