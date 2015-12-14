[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{*
<script type="text/javascript">
<!--
function changeFnc( fncName )
{
    var langvar = document.myedit.elements['fnc'];
    if (langvar != null )
        langvar.value = fncName;
}
//-->
</script>
*}]
<style>
    #liste tr:hover td{
        background-color: #e0e0e0;
    }

    #liste td.activetime {
        background-image: url(bg/ico_activetime.png);
        min-width: 17px;
        background-position: center center;
        background-repeat: no-repeat;
    }
</style>


<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="jx_voucherserie_show">
</form>


<div style="height:100%;">

    <div id="liste" style="border:0px solid gray; padding:4px; width:99%; height:96%; overflow-y:scroll; float:left;">
        <form name="adminlog" id="adminlog" action="[{ $oViewConf->getSelfLink() }]" method="post">
            [{ $oViewConf->getHiddenSid() }]
            <input type="hidden" name="oxid" value="[{ $oxid }]">
            <input type="hidden" name="cl" value="jxadminlog">
            <input type="hidden" name="fnc" value="">
            [{*<input type="hidden" name="voucherdelid" value="">*}]
            <table cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td class="listheader">[{ oxmultilang ident="JXADMINLOG_TIME" }]</td>
                    <td class="listheader">[{ oxmultilang ident="USER_MAIN_EMAILLOGIN" }]</td>
                    <td class="listheader">[{ oxmultilang ident="GENERAL_NAME" }]</td>
                    <td class="listheader">[{ oxmultilang ident="GENERAL_COMPANY" }]</td>
                    <td class="listheader">[{ oxmultilang ident="JXADMINLOG_SQL" }]</td>
                </tr>
                [{foreach item=aAdminLog from=$aAdminLogs}]
                    [{ cycle values="listitem,listitem2" assign="listclass" }]
                    <tr>
                        <td class="[{ $listclass }]" style="height: 20px;">&nbsp;<nobr>[{$aAdminLog.oxtimestamp}]&nbsp;</nobr></td>
                        <td class="[{ $listclass }]">&nbsp;[{$aAdminLog.oxusername}]&nbsp;&nbsp;</td>
                        <td class="[{ $listclass }]">&nbsp;<nobr>[{$aAdminLog.oxfname}] [{$aAdminLog.oxlname}]&nbsp;&nbsp;</nobr></td>
                        <td class="[{ $listclass }]">&nbsp;<nobr>[{$aAdminLog.oxcompany}]&nbsp;&nbsp;</nobr></td>
                        <td class="[{ $listclass }]">&nbsp;[{$aAdminLog.oxsql}]</td>
                    </tr>
                [{/foreach}]
            </table>
        </form>
    </div>

    <div style="float:right;position:relative;bottom:-40px;padding-right:10px;">
            <a href="https://github.com/job963/jxAdminLog" target="_blank"><span style="color:gray;">jxAdminLog</span></a>
    </div>

</div>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]

