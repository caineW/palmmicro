<?php
require_once('stocktable.php');

function _echoNvCloseItem($csv, $shares_sql, $strDate, $strNetValue, $ref, $strStockId, $strFundId)
{
	$his_sql = $ref->GetHistorySql();
	$strClose = $his_sql->GetClose($strDate);
	$strClosePrev = $his_sql->GetClosePrev($strDate);
	if (($strClose === false) || ($strClosePrev === false))	return;
	
   	if ($csv)	$csv->Write($strDate, $ref->GetPercentageString($strClosePrev, $strClose), $ref->GetPercentageString($strNetValue, $strClose), $strNetValue);

   	$ar = array($strDate);
   	$ar[] = $ref->GetPriceDisplay($strClose, $strNetValue);
   	
    if ($strFundId)
    {
    	$ar[] = GetOnClickLink('/php/_submitdelete.php?'.TABLE_NETVALUE_HISTORY.'='.$strFundId, '确认删除'.$strDate.'净值记录'.$strNetValue.'?', $strNetValue);
    }
    else
    {
    	$ar[] = $strNetValue;
    }
    
	$ar[] = $ref->GetPercentageDisplay($strNetValue, $strClose);
   	$ar[] = $ref->GetPercentageDisplay($strClosePrev, $strClose);
    
    if ($strShares = $shares_sql->GetClose($strStockId, $strDate))
    {
    	$ar[] = rtrim0($strShares);
    	$fVolume = floatval($his_sql->GetVolume($strDate));
    	$ar[] = strval_round(100.0 * $fVolume / (floatval($strShares * 10000.0)));
    }
    
    EchoTableColumn($ar);
}

function _echoNvCloseData($sql, $ref, $strStockId, $csv, $iStart, $iNum, $bAdmin)
{
	$strSymbol = $ref->GetSymbol();
	if (in_arrayQdii($strSymbol))
	{
		$bSameDayNetValue = false;
	}
	else
	{
		$bSameDayNetValue = true;
	}
	
	$shares_sql = new SharesHistorySql();
    if ($result = $sql->GetAll($iStart, $iNum)) 
    {
        while ($record = mysql_fetch_assoc($result)) 
        {
        	$strNetValue = rtrim0($record['close']);
        	if (empty($strNetValue) == false)
        	{
        		$strDate = $record['date'];
        		if ($bSameDayNetValue == false)
        		{
        			$strDate = GetNextTradingDayYMD($strDate);
        		}
   				_echoNvCloseItem($csv, $shares_sql, $strDate, $strNetValue, $ref, $strStockId, ($bAdmin ? $record['id'] : false));
        	}
        }
        @mysql_free_result($result);
    }
}

function EchoNvCloseHistoryParagraph($ref, $str = false, $csv = false, $iStart = 0, $iNum = TABLE_COMMON_DISPLAY, $bAdmin = false)
{
	$strStockId = $ref->GetStockId();
	$sql = new NetValueHistorySql($strStockId);
	$iTotal = $sql->Count();
	if ($iTotal == 0)		return;

    $strSymbol = $ref->GetSymbol();
   	$strNavLink = IsTableCommonDisplay($iStart, $iNum) ? '' : StockGetNavLink($strSymbol, $iTotal, $iStart, $iNum);
   	if ($str == false)	$str = GetNvCloseHistoryLink($strSymbol);

	$str .= ' '.$strNavLink;
	EchoTableParagraphBegin(array(new TableColumnDate(),
								   new TableColumnClose(),
								   new TableColumnNetValue(),
								   new TableColumnPremium('y'),
								   new TableColumnChange('x'),
								   new TableColumn('流通股数(万)', 100),
								   new TableColumn('换手率(%)', 90)
								   ), $strSymbol.NVCLOSE_HISTORY_PAGE, $str);

    _echoNvCloseData($sql, $ref, $strStockId, $csv, $iStart, $iNum, $bAdmin);
    EchoTableParagraphEnd($strNavLink);
}

?>
