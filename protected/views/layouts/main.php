<?php
/**
  Project       : ActiveDNS
  Document      : views/layouts/main.php
  Document type : PHP script file
  Created at    : 29.11.2011
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Layout decorator
*/
$this->cs->registerCssFile($this->cssUrl('style'),'screen,projection');
?><!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $this->pageTitle?></title>
    <link href='//fonts.googleapis.com/css?family=Arimo:400,700,400italic,700italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <link href="<?php echo $this->cssUrl('font-awesome.min')?>" rel="stylesheet" media="screen,projection" />
    <!--[if lt IE 9]>
    <link rel="stylesheet" href="<?php echo $this->cssUrl('font-awesome-ie7.min')?>" type="text/css" media="screen,projection" />
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body>
    <?php echo $content?>
    <?php $this->renderPartial('//snippets/analytics')?>
    <?php if (Yii::app()->user->hasFlash('revertToSourceLanguage')){ $this->renderPartial('//snippets/revert'); }?>
  </body>
</html>
