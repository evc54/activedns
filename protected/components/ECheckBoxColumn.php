<?php
/**
  Project       : ActiveDNS
  Document      : ECheckBoxColumn.php
  Document type : PHP script file
  Created at    : 15.09.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Extended checkbox column for grid view
*/
class ECheckBoxColumn extends CCheckBoxColumn
{
  public $readonly = false;

  protected function renderDataCellContent($row,$data)
  {
    $readonly = $this->evaluateExpression($this->readonly, array('row' => $row, 'data' => $data));

    if (!$readonly) {
      $this->selectableRows = 0;
      parent::renderDataCellContent($row, $data);
    }
  }
}
