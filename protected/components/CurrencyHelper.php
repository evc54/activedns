<?php
/**
  Project       : ActiveDNS
  Document      : CurrencyHelper.php
  Document type : PHP script file
  Created at    : 10.04.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Currency renderer helper class
*/
class CurrencyHelper
{
  public static function render($value,$params = array(),$currency = null)
  {
    $return = '';
    $options = self::getCurrencyOptions($currency);
    $value = self::value($value);
    $sign = $options['sign'];

    if (!empty($params['signTag'])) {
      $sign = CHtml::tag($params['signTag'], empty($params['signTagHtmlOptions']) ? array() : $params['signTagHtmlOptions'], $sign);
    }

    if (!empty($params['decimalsTag']) || !empty($params['integerTag'])) {
      $pointPosition = mb_strpos($value, $options['point'], 0, Yii::app()->charset);
      $integer = mb_substr($value, 0, $pointPosition, Yii::app()->charset);
      $decimals = mb_substr($value, $pointPosition + 1, mb_strlen($value, Yii::app()->charset) - $pointPosition, Yii::app()->charset);
      $value = '';
      if (!empty($params['integerTag'])) {
        $value .= CHtml::tag($params['integerTag'], empty($params['integerTagHtmlOptions']) ? array() : $params['integerTagHtmlOptions'], $integer);
      }
      else {
        $value .= $integer ? $integer : '0';
      }
      if (!empty($params['decimalsTag'])) {
        $value .= CHtml::tag($params['decimalsTag'], empty($params['decimalsTagHtmlOptions']) ? array() : $params['decimalsTagHtmlOptions'], $options['point'] . $decimals);
      }
      else {
        $value .= $options['point'] . $decimals;
      }
    }

    if ($options['position'] == 'left') {
      $return = $sign . $options['delimiter'] . $value;
    }
    else {
      $return = $value . $options['delimiter'] . $sign;
    }

    if (!empty($params['tag'])) {
      $return = CHtml::tag($params['tag'], empty($params['htmlOptions']) ? array() : $params['htmlOptions'], $return);
    }
    
    return $return;
  }

  public static function getCurrencySign($currency = null)
  {
    $options = self::getCurrencyOptions($currency);

    return empty($options['sign']) ? '$' : $options['sign'];
  }

  public static function value($value,$currency = null)
  {
    $options = self::getCurrencyOptions($currency);

    return number_format(floatval($value), $options['fraction'], $options['point'], $options['thousandSeparator']);
  }
  
  public static function getCurrencyOptions($currency = null)
  {
    if (empty($currency)) {
      $currency = self::getPrimaryCurrency();
    }

    $options = Yii::app()->params['currencyOptions'];

    return empty($options[$currency]) ? array(
      'sign'              => '$',
      'position'          => 'left',
      'delimiter'         => '',
      'fraction'          => 2,
      'point'             => '.',
      'thousandSeparator' => ',',
    ) : $options[$currency];
  }

  public static function getPrimaryCurrency()
  {
    $currency = Config::get('PrimaryCurrency');

    if (empty($currency)) {
      $currency = Yii::app()->params['currencies'];
      $currency = current($currency);
    }

    return $currency;
  }
}
