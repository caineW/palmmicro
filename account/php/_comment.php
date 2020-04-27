<?php
require_once('_account.php');
require_once('/php/ui/commentparagraph.php');

function EchoUserComment($bChinese = true)
{
    global $acct;
    $strMemberId = $acct->GetLoginId();
    
    if ($str = UrlGetQueryValue('blog_id'))
    {
        $strQuery = 'blog_id='.$str;
        $strLink = GetBlogLink($str);
    }
    else if ($str = UrlGetQueryValue('member_id'))
    {
        $strQuery = 'member_id='.$str;
        $strLink = GetMemberLink($str, $bChinese);
    }
    else if ($str = UrlGetQueryValue('ip'))
    {
        $strQuery = 'ip='.$str;
        $strLink = GetIpLink($str, $bChinese);
    }
    else
    {
        $strQuery = false;
        $strLink = '';
    }

    $strWhere = SqlWhereFromUrlQuery($strQuery);
    $iTotal = SqlCountBlogComment($strWhere);
    $iStart = $acct->GetStart();
    $iNum = $acct->GetNum();
    $strNavLink = GetNavLink($strQuery, $iTotal, $iStart, $iNum, $bChinese);
    
    EchoParagraph($strLink.' '.$strNavLink);
    EchoCommentParagraphs($strMemberId, $strWhere, $iStart, $iNum, $bChinese);
    EchoParagraph($strNavLink);
}

   	$acct = new AcctStart();
    
?>
