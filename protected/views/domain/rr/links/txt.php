<a class="modal-btn modal-create" href="<?php echo $this->createUrl('ajax',array('id'=>$id,'ajax'=>'createRR','type'=>'txt'))?>"><?php echo Yii::t('domain','Add resource record type {type}',array('{type}'=>'TXT'))?></a>
&nbsp;
<a class="modal-btn modal-create" href="<?php echo $this->createUrl('ajax',array('id'=>$id,'ajax'=>'createRR','type'=>'spf'))?>"><?php echo Yii::t('domain','Add SPF record')?></a>
