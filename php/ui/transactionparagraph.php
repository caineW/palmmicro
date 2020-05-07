<?php
require_once('stockgroupparagraph.php');

function _echoTransactionTableItem($ref, $record, $bReadOnly)
{
    $strSymbol = $ref->GetSymbol();
    $strDate = GetSqlTransactionDate($record);
    $strPrice = $ref->GetPriceDisplay($record['price']);
    $strFees = strval_round(floatval($record['fees']), 2);
    if ($bReadOnly)
    {
        $strEdit = '';
        $strDelete = '';
    }
    else
    {
    	$strEdit = GetEditLink(STOCK_PATH.'editstocktransaction', $record['id']);
    	$strDelete = GetDeleteLink(STOCK_PHP_PATH.'_submittransaction.php?delete='.$record['id'], '交易记录');
    }
    
    echo <<<END
    <tr>
        <td class=c1>$strDate</td>
        <td class=c1>$strSymbol</td>
        <td class=c1>{$record['quantity']}</td>
        <td class=c1>$strPrice</td>
        <td class=c1>$strFees</td>
        <td class=c1>{$record['remark']}</td>
        <td class=c1>$strEdit $strDelete</td>
    </tr>
END;
}

function _echoSingleTransactionTableData($sql, $ref, $iStart, $iNum, $bReadOnly)
{
	if ($result = $sql->GetStockTransaction($ref->GetStockId(), $iStart, $iNum)) 
    {
        while ($record = mysql_fetch_assoc($result)) 
        {
            _echoTransactionTableItem($ref, $record, $bReadOnly);
        }
        @mysql_free_result($result);
    }
}

function _echoAllTransactionTableData($sql, $iStart, $iNum, $bReadOnly)
{
    $ar = array();
    if ($result = $sql->GetAllStockTransaction($iStart, $iNum)) 
    {
        while ($record = mysql_fetch_assoc($result)) 
        {
        	$strGroupItemId = $record['groupitem_id'];
        	if (array_key_exists($strGroupItemId, $ar))
        	{
        		$ref = $ar[$strGroupItemId];
        	}
        	else
        	{
        		$strStockId = $sql->GetStockId($strGroupItemId);
        		$strSymbol = SqlGetStockSymbol($strStockId);
        		$ref = new MyStockReference($strSymbol);
        		$ar[$strGroupItemId] = $ref;
        	}
            _echoTransactionTableItem($ref, $record, $bReadOnly);
        }
        @mysql_free_result($result);
    }
}

function _echoTransactionTableData($sql, $ref, $iStart, $iNum, $bReadOnly)
{
    if ($ref)
    {
    	_echoSingleTransactionTableData($sql, $ref, $iStart, $iNum, $bReadOnly);
    }
    else
    {
    	_echoAllTransactionTableData($sql, $iStart, $iNum, $bReadOnly);
    }
}

function EchoTransactionParagraph($strGroupId, $ref = false, $iStart = 0, $iNum = TABLE_COMMON_DISPLAY)
{
	$sql = new StockGroupItemSql($strGroupId);
    if (IsTableCommonDisplay($iStart, $iNum))
    {
    	$str = StockGetAllTransactionLink($strGroupId, $ref);
        $strNavLink = '';
    }
    else
    {
    	if ($ref)
    	{
            $iTotal = $sql->CountStockTransaction($ref->GetStockId());
           	$strNavLink = GetNavLink('groupid='.$strGroupId.'&symbol='.$ref->GetSymbol(), $iTotal, $iStart, $iNum);
    	}
    	else
    	{
            $iTotal = $sql->CountAllStockTransaction();
           	$strNavLink = GetNavLink('groupid='.$strGroupId, $iTotal, $iStart, $iNum);
        }
        $str = $strNavLink;
    }
    
    $arColumn = GetTransactionTableColumn();
    echo <<<END
    <p>$str
    <TABLE borderColor=#cccccc cellSpacing=0 width=640 border=1 class="text" id="transaction">
    <tr>
        <td class=c1 width=100 align=center>{$arColumn[0]}</td>
        <td class=c1 width=80 align=center>{$arColumn[1]}</td>
        <td class=c1 width=70 align=center>{$arColumn[2]}</td>
        <td class=c1 width=80 align=center>{$arColumn[3]}</td>
        <td class=c1 width=60 align=center>{$arColumn[4]}</td>
        <td class=c1 width=170 align=center>{$arColumn[5]}</td>
        <td class=c1 width=80 align=center>{$arColumn[6]}</td>
    </tr>
END;

    _echoTransactionTableData($sql, $ref, $iStart, $iNum, StockGroupIsReadOnly($strGroupId));
    EchoTableParagraphEnd($strNavLink);
}

?>
