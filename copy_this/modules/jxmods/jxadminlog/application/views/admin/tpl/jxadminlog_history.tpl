[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]


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
    .listitem, .listitem2 {
        padding-left: 4px;
        padding-right: 16px;
        white-space: nowrap;
    }
</style>


<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="jx_voucherserie_show">
</form>


<div style="height:92%;">

    <div id="liste" style="border:0px solid gray; padding:4px; width:99%; height:95%; overflow-y:scroll; float:left;">
        [{if $blAdminLog == FALSE }]
            <div style="border:2px solid #dd0000;margin:10px;padding:5px;background-color:#ffdddd;font-family:sans-serif;font-size:14px;">
                <b>Setting <i>blLogChangesInAdmin</i> in <i>config.inc.php</i> is deactivated!</b><br />Actually no new admin action will be logged.
            </div>
        [{/if}]
        <form name="jxadminlog_history" id="jxadminlog_history" action="[{ $oViewConf->getSelfLink() }]" method="post">
            [{ $oViewConf->getHiddenSid() }]
            <input type="hidden" name="oxid" value="[{ $oxid }]">
            <input type="hidden" name="cl" value="jxadminlog">
            <input type="hidden" name="fnc" value="">
            [{*<input type="hidden" name="voucherdelid" value="">*}]
            <table cellspacing="0" cellpadding="0" border="0" width="99%">
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
                        <td class="[{ $listclass }]" style="height: 20px;">&nbsp;<nobr>[{$aAdminLog.oxtimestamp}]</nobr></td>
                        <td class="[{ $listclass }]">[{$aAdminLog.oxusername}]</td>
                        <td class="[{ $listclass }]">[{$aAdminLog.oxfname}] [{$aAdminLog.oxlname}]</td>
                        <td class="[{ $listclass }]">[{$aAdminLog.oxcompany}]</td>
                        <td class="[{ $listclass }]" style="max-width: 400px;"><div title="[{$aAdminLog.oxsql|strip_tags}]" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">[{$aAdminLog.oxsql}]</div></td>
                    </tr>
                [{/foreach}]
            </table>
        </form>
    </div>

    <div style="float:right;[{*position:relative;bottom:-40px;*}]padding-right:10px;">
    <br />
            <a href="https://github.com/job963/jxAdminLog" target="_blank"><span style="color:gray;">jxAdminLog</span></a>
    </div>

</div>

[{*include file="bottomnaviitem.tpl"*}]
[{include file="bottomitem.tpl"}]

