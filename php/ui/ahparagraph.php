<?php

function GetSortString($strQuery)
{
    $ar = array('hshare' => '按H股排序',
                  'ratio' => '按比价排序',
                 );
	return isset($ar[$strQuery]) ? $ar[$strQuery] : '';
}

function _getSortLink($strQuery = 'hshare')
{
    return CopyPhpLink(UrlAddQuery('sort='.$strQuery), GetSortString($strQuery));
}

function _echoAhItem($ref)
{
	$ar = array();
	
   	$ar[] = RefGetMyStockLink($ref);
   	
    $strSymbolA = $ref->GetSymbolA();
    $ar[] = GetMyStockLink($strSymbolA);
    
    if ($fAhRatio = $ref->GetAhPriceRatio())
    {
    	$ar[] = GetRatioDisplay($fAhRatio);
    	$ar[] = GetRatioDisplay(1.0 / $fAhRatio);
    }
    
    EchoTableColumn($ar);
}

function _refSortByRatio($arRef, $bAh = true)
{
    $ar = array();
    $arRatio = array();
    foreach ($arRef as $ref)
    {
        $strSymbol = $ref->GetSymbol();
        $ar[$strSymbol] = $ref;
        $arRatio[$strSymbol] = ($bAh ? $ref->GetAhPriceRatio() : $ref->GetAdrhPriceRatio()); 
    }
    asort($arRatio, SORT_NUMERIC);
    
    $arSort = array();
    foreach ($arRatio as $strSymbol => $fRatio)
    {
        $arSort[] = $ar[$strSymbol];
    }
    return $arSort;
}

function _refSortBySymbol($arRef)
{
    $ar = array();
    foreach ($arRef as $ref)
    {
        $strSymbol = $ref->GetSymbol();
        $ar[$strSymbol] = $ref; 
    }
    ksort($ar);
    
    $arSort = array();
    foreach ($ar as $str => $ref)
    {
        $arSort[] = $ref;
    }
    return $arSort;
}

function EchoAhParagraph($arRef)
{
	$str = GetAhCompareLink();
	$iCount = count($arRef);
	if ($iCount == 1)
	{
		$str .= ' '.GetAhHistoryLink($arRef[0]->GetSymbolA());
	}
	else if ($iCount > 2)
	{
		if ($strSort = UrlGetQueryValue('sort'))
		{
			if ($strSort == 'hshare')
			{
				$arRef = _refSortBySymbol($arRef);
			}
			else if ($strSort == 'ratio')
			{
				$arRef = _refSortByRatio($arRef);
			}
		}
		else
		{
			$str .= ' '._getSortLink();
			$str .= ' '._getSortLink('ratio');
		}
	}

	EchoTableParagraphBegin(array(new TableColumnSymbol(),
								   new TableColumnSymbol('A股'),
								   new TableColumnAhRatio(),
								   new TableColumnHaRatio()
								   ), 'ah', $str);

	foreach ($arRef as $ref)
	{
		_echoAhItem($ref);
	}
    EchoTableParagraphEnd();
}

function _echoAdrhItem($ref)
{
	$ar = array();
	
   	$ar[] = RefGetMyStockLink($ref);
   	
   	$adr_ref = $ref->adr_ref;
    $ar[] = GetMyStockLink($adr_ref->GetSymbol());
    $ar[] = $adr_ref->GetPriceDisplay($ref->GetUsdPrice());
    
    if ($fAdrhRatio = $ref->GetAdrhPriceRatio())
    {
    	$ar[] = GetRatioDisplay($fAdrhRatio);
    	$ar[] = GetRatioDisplay(1.0 / $fAdrhRatio);
    }
    
    EchoTableColumn($ar);
}

function EchoAdrhParagraph($arRef)
{
	$str = GetAdrhCompareLink();
	if (count($arRef) > 2)
	{
		if ($strSort = UrlGetQueryValue('sort'))
		{
			if ($strSort == 'hshare')
			{
				$arRef = _refSortBySymbol($arRef);
			}
			else if ($strSort == 'ratio')
			{
				$arRef = _refSortByRatio($arRef, false);
			}
		}
		else
		{
			$str .= ' '._getSortLink();
		}
	}

	EchoTableParagraphBegin(array(new TableColumnSymbol(),
								   new TableColumnSymbol('ADR'),
								   new TableColumnUSD(),
								   new TableColumnRatio('ADRH'),
								   new TableColumnRatio('HADR')
								   ), 'adrh', $str);

	foreach ($arRef as $ref)
	{
		_echoAdrhItem($ref);
	}
    EchoTableParagraphEnd();
}

?>
