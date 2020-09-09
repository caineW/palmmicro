<?php
require_once('/php/layout.php');
require_once('/php/_navpalmmicro.php');

function _getMenuArray($bChinese)
{
    if ($bChinese)
    {
        return array('login' => '登录',
                      'profile' => '资料',
                     );
    }
    else
    {
         return array('login' => 'Login',
                      'profile' => 'Profile',
                     );
    }
}

function AccountMenu($bChinese)
{
	NavBegin();
	NavMenu1('account', $bChinese);
	NavContinueNewLine();
    NavMenuSet(_getMenuArray($bChinese));
	NavContinueNewLine();
    NavSwitchLanguage($bChinese);
    NavEnd();
}

function _LayoutTopLeft($bChinese = true, $bAdsense = true)
{
    LayoutTopLeft('AccountMenu', true, $bChinese, $bAdsense);
}

?>
