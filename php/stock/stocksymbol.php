<?php

define ('SINA_FUND_PREFIX', 'f_');

define ('SHANGHAI_PREFIX', 'SH');
define ('SHENZHEN_PREFIX', 'SZ');

define ('YAHOO_INDEX_CHAR', '^');

define ('INDEXSP_PREFIX', 'INDEXSP:');
define ('INDEXDJX_PREFIX', 'INDEXDJX:');
define ('INDEXCBOE_PREFIX', 'INDEXCBOE:');

define ('STOCK_TIME_ZONE_CN', 'PRC');
define ('STOCK_TIME_ZONE_US', 'America/New_York');

function IsChineseStockDigit($strDigit)
{
    if (is_numeric($strDigit))
    {
        if (strlen($strDigit) == 6)
        {
            return true;
        }
    }
    return false;
}

function _isShenzhenFundDigit($iDigit)
{
    if ($iDigit >= 100000 && $iDigit < 200000)  return true;
    return false;
}

function _isShanghaiFundDigit($iDigit)
{
    if ($iDigit >= 500000 && $iDigit < 600000)  return true;
    return false;
}

// ****************************** StockSymbol Class *******************************************************

class StockSymbol
{
    var $strSymbol;              // Stock symbol
    
    // Index start with ^
    var $strFirstChar = false;
    var $strOthers;
    
    // Chinese market
    var $strDigitA = false;     // 162411
    var $iDigitA;
    var $strPrefixA;            // SH or SZ

    // Hongkong
    var $iDigitH = -1;
    
    function _getFirstChar()
    {
        if ($this->strFirstChar == false)
        {
            $strSymbol = $this->strSymbol;
            $this->strFirstChar = substr($strSymbol, 0, 1);
            $this->strOthers = substr($strSymbol, 1);
        }
    }
    
    function IsIndex()
    {
        $this->_getFirstChar();
        if ($this->strFirstChar == YAHOO_INDEX_CHAR)
        {
            return true;
        }
        return false;
    }
    
    function IsSymbolUS()
    {
        if ($this->IsSymbolA())     return false;
        if ($this->IsSymbolH())     return false;
        return true;
    }
    
    function IsSymbolH()
    {
        if ($this->iDigitH >= 0)   return true;
        
        $strSymbol = $this->strSymbol;
        if ($this->IsIndex())
        {
            if ($strSymbol == '^HSI' || $strSymbol == '^HSCE' || $strSymbol == '^SPHCMSHP')
            {
                $this->iDigitH = 0;
                return true;
            }
        }
        else if ($this->strFirstChar == '0')
        {
            if (is_numeric($strSymbol))
            {
                if (strlen($strSymbol) == 5)
                {
                    $this->iDigitH = intval($strSymbol);
                    return true;
                }
            }
        }
        return false;
    }
    
    function IsSymbolA()
    {
        if ($this->strDigitA)   return true;
        
        $strSymbol = $this->strSymbol;
        $strDigit = substr($strSymbol, 2);
        if (IsChineseStockDigit($strDigit))
        {
            $strPrefix = strtoupper(substr($strSymbol, 0, 2));
            if ($strPrefix == SHANGHAI_PREFIX || $strPrefix == SHENZHEN_PREFIX)
            {
                $this->strPrefixA = $strPrefix;
                $this->strDigitA = $strDigit;
                $this->iDigitA = intval($strDigit);
                return true;
            }
        }
        return false;
    }
    
    function IsFundA()
    {
        if ($this->strDigitA == false)
        {
            if ($this->IsSymbolA() == false)    return false;
        }
        
        if ($this->strPrefixA == SHENZHEN_PREFIX && _isShenzhenFundDigit($this->iDigitA))   return true;
        if ($this->strPrefixA == SHANGHAI_PREFIX && _isShanghaiFundDigit($this->iDigitA))   return true;
        return false;
    }
    
    function IsForex()
    {
        $strSymbol = $this->strSymbol;
        if ($strSymbol == 'USCNY' || $strSymbol == 'HKCNY')
        {
            return true;
        }
        return false;
    }
    
    // AUO, AG1712
    function IsFutureCn()
    {
        if ($this->IsSymbolA())                   return false;

        $this->_getFirstChar();
        if (is_numeric($this->strFirstChar))    return false;
        
        if (is_numeric(substr($this->strSymbol, -1, 1)))  return true;
        return false;
    }

    function SetTimeZone()
    {
        if ($this->IsSymbolA() || $this->IsSymbolH() || $this->IsForex())
        {
            $strTimeZone = STOCK_TIME_ZONE_CN;
        }
        else
        {
            $strTimeZone = STOCK_TIME_ZONE_US;
        }
        date_default_timezone_set($strTimeZone);
    }

    function GetSinaFundSymbol()
    {
        if ($this->IsFundA())   return SINA_FUND_PREFIX.$this->strDigitA;
        DebugString('GetSinaFundSymbol() exception '.$this->strSymbol);
        return false;
    }
    
    function GetSinaSymbol()
    {
        $strSymbol = str_replace('.', '', $this->strSymbol);
        $strLower = strtolower($strSymbol);
        if ($this->IsIndex())
        {
            if ($strSymbol == '^HSI')           return 'rt_hkHSI';
            else if ($strSymbol == '^HSCE')    return 'rt_hkHSCEI';  
            else if ($strSymbol == '^DJI')     return 'gb_dji';  
            else if ($strSymbol == '^GSPC')    return 'gb_inx';  
            else if ($strSymbol == '^NDX')     return 'gb_ndx';  
            else                                  return false;
        }
        else if ($this->IsSymbolH())
        {   // Hongkong market
            return 'rt_hk'.$strSymbol;    
        }
        else if ($this->IsSymbolA())
        {
            return $strLower;
        }
        return 'gb_'.$strLower;
    }

/*
上证综指代码：000001.ss，深证成指代码：399001.SZ，沪深300代码：000300.ss
下面就是世界股票交易所的网址和缩写，要查找哪个股票交易所的数据，就按照上面的格式以此类推。
上海交易所=cn.finance.yahoo.com,.SS,Chinese,sl1d1t1c1ohgv
深圳交易所=cn.finance.yahoo.com,.SZ,Chinese,sl1d1t1c1ohgv
美国交易所=finance.yahoo.com,,United States,sl1d1t1c1ohgv
加拿大=ca.finance.yahoo.com,.TO,Toronto,sl1d1t1c1ohgv
新西兰=au.finance.yahoo.com,.NZ,sl1d1t1c1ohgv
新加坡=sg.finance.yahoo.com,.SI,Singapore,sl1d1t1c1ohgv
香港=hk.finance.yahoo.com,.HK,Hong Kong,sl1d1t1c1ohgv
台湾=tw.finance.yahoo.com,.TW,Taiwan,sl1d1t1c1ohgv
印度=in.finance.yahoo.com,.BO,Bombay,sl1d1t1c1ohgv
伦敦=uk.finance.yahoo.com,.L,London,sl1d1t1c1ohgv
澳洲=au.finance.yahoo.com,.AX,Sydney,sl1d1t1c1ohgv
巴西=br.finance.yahoo.com,.SA,Sao Paulo,sl1d1t1c1ohgv
瑞典=se.finance.yahoo.com,.ST,Stockholm,sl1d1t1c1ohgv
*/
    function GetYahooSymbol()
    {
        $strSymbol = str_replace('.', '-', $this->strSymbol);
        if ($this->IsIndex())
        {
            return '%5E'.$this->strOthers;   // index ^HSI
        }
        else if ($this->IsSymbolH())
        {
            return $this->strOthers.'.hk';   // Hongkong market
        }
        else if ($this->IsSymbolA())
        {
            if ($this->strPrefixA == SHANGHAI_PREFIX)
            {
                return $this->strDigitA.'.ss';   // Shanghai market
            }
            else if ($this->strPrefixA == SHENZHEN_PREFIX)
            {
                return $this->strDigitA.'.sz';   // Shenzhen market
            }
        }
        return $strSymbol;
    }
 
    function GetGoogleSymbol()
    {
        $strSymbol = $this->strSymbol;
        if ($this->IsIndex())
        {
            $str = $this->strOthers;
            if ($strSymbol == '^GSPC')  return INDEXSP_PREFIX.'.INX';
            else if ($strSymbol == '^SPSIOP' || $strSymbol == '^SPGOGUP' || $strSymbol == '^SPBRICNTR' || $strSymbol == '^IXY')  return INDEXSP_PREFIX.$str;
            else if ($strSymbol == '^VIX')      return INDEXCBOE_PREFIX.$str;
            else if ($strSymbol == '^DJSOEP')   return INDEXDJX_PREFIX.$str;
        }
//        return $strSymbol;
        return false;
    }
    
    // constructor 
    function StockSymbol($strSymbol)
    {
        $this->strSymbol = $strSymbol;
    }
}

?>