<?php 
require('php/_lof.php');

function EchoLofRelated($ref)
{
	$strGroup = GetLofLinks($ref);
	$strOil = GetOilSoftwareLinks();
	$strCommodity = GetCommoditySoftwareLinks();
	$strCompany = GetFortuneSoftwareLinks();
	
	echo <<< END
	<p><font color=red>已知问题:</font></p>
	<ol>
	    <li>2019年9月20日星期五, XOP季度分红除权. 因为现在采用XOP净值替代SPSIOP做华宝油气估值, 23日的估值不准, 要等华宝油气20日实际净值出来自动校准后恢复正常.</li>
	    <li>2016年12月21日星期三, CL期货换月. 因为CL和USO要等当晚美股开盘才会自动校准, 白天按照CL估算的实时净值不准.</li>
	    <li>2016年12月17日星期五, XOP季度分红除权. 这里显示的涨跌幅跟新浪和Yahoo等不一致, 它们都已经把昨天的收盘价减掉分红的0.08美元了. SMA均线数值也需要手工调整.</li>
    </ol>
    <p>
    	<a href="http://www.fsfund.com/funds/162411/index.shtml" target=_blank>华宝油气官网</a>
       	<a href="https://www.ssga.com/us/en/individual/etfs/funds/spdr-sp-oil-gas-exploration-production-etf-xop" target=_blank>XOP官网</a>
    	<a href="https://xueqiu.com/2244868365/60702370" target=_blank>华宝油气和XOP套利交易Q&A</a>
    	<a href="https://xueqiu.com/2244868365/81340659" target=_blank>一种根据XOP均线进行华宝油气和XOP联动交易的方法</a>
    	<a href="https://www.hedgewise.com/blog/investmentstrategy/the-right-way-to-invest-in-oil.php" target=_blank>原油投资比较</a>
    </p> 
	<p> $strGroup
		$strOil
		$strCommodity
		$strCompany
	</p>
END;
}

require('/php/ui/_dispcn.php');
?>
