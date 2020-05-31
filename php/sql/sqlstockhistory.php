<?php
require_once('sqlstockdaily.php');

// ****************************** StockHistorySql class *******************************************************
class StockHistorySql extends DailyStockValSql
{
    function StockHistorySql($strStockId) 
    {
        parent::DailyStockValSql(TABLE_STOCK_HISTORY, $strStockId);
    }

    function Create()
    {
    	$str = ' `stock_id` INT UNSIGNED NOT NULL ,'
         	  . ' `date` DATE NOT NULL ,'
         	  . ' `open` DOUBLE(10,3) NOT NULL ,'
         	  . ' `high` DOUBLE(10,3) NOT NULL ,'
         	  . ' `low` DOUBLE(10,3) NOT NULL ,'
         	  . ' `close` DOUBLE(10,3) NOT NULL ,'
         	  . ' `volume` BIGINT UNSIGNED NOT NULL ,'
         	  . ' `adjclose` DOUBLE(13,6) NOT NULL ,'
         	  . ' FOREIGN KEY (`stock_id`) REFERENCES `stock`(`id`) ON DELETE CASCADE ,'
         	  . ' UNIQUE ( `date`, `stock_id` )';
    	return $this->CreateIdTable($str);
    }

    function WriteHistory($strDate, $strOpen, $strHigh, $strLow, $strClose, $strVolume, $strAdjClose)
    {
    	$ar = array('date' => $strDate,
    				   'open' => $strOpen,
    				   'high' => $strHigh,
    				   'low' => $strLow,
    				   'close' => $strClose,
    				   'volume' => $strVolume,
    				   'adjclose' => $strAdjClose);
    	
    	if ($record = $this->GetRecord($strDate))
    	{
    		unset($ar['date']);
    		if (abs(floatval($record['open']) - floatval($strOpen)) < 0.001)				unset($ar['open']);
    		if (abs(floatval($record['high']) - floatval($strHigh)) < 0.001)				unset($ar['high']);
    		if (abs(floatval($record['low']) - floatval($strLow)) < 0.001)					unset($ar['low']);
    		if (abs(floatval($record['close']) - floatval($strClose)) < 0.001)				unset($ar['close']);
    		if ($record['volume'] == $strVolume)												unset($ar['volume']);
    		if (abs(floatval($record['adjclose']) - floatval($strAdjClose)) < 0.000001)	unset($ar['adjclose']);
    		
    		$iCount = count($ar);
    		if ($iCount > 0)
    		{
    			if ($iCount == 1 && isset($ar['adjclose']))
    			{	// adjclose might have been changed manually
    				return false;
    			}
    			return $this->UpdateById($ar, $record['id']);
    		}
    	}
    	else
    	{
    		return $this->InsertArray(array_merge($this->MakeFieldKeyId(), $ar));
    	}
    	return false;
    }
    
    function UpdateClose($strId, $strClose)
    {
		return $this->UpdateById(array('close' => $strClose, 'adjclose' => $strClose), $strId);
    }

    function UpdateAdjClose($strId, $strAdjClose)
    {
		return $this->UpdateById(array('adjclose' => $strAdjClose), $strId);
    }

    function DeleteByZeroVolume()
    {
    	return $this->DeleteRecord("volume = '0' AND ".$this->BuildWhere_key());
    }

    function GetVolume($strDate)
    {
    	if ($record = $this->GetRecord($strDate))
    	{
    		return $record['volume'];
    	}
    	return '0';
    }
}

?>
