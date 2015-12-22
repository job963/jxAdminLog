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


<div style="height:100%; overflow-y:no-scroll;">
    <div>
        <form name="jxadminlog" id="jxadminlog" action="[{ $oViewConf->getSelfLink() }]" method="post">
            [{ $oViewConf->getHiddenSid() }]
            <input type="hidden" name="oxid" value="[{ $oxid }]">
            <input type="hidden" name="cl" value="jxadminlog">
            <input type="hidden" name="fnc" value="">
            [{*<input type="hidden" name="voucherdelid" value="">*}]
            
            Filtern nach: 
            <select name="jxadminlog_reporttype" onchange="document.forms['jxadminlog'].elements['fnc'].value='';this.form.submit()">
                <option value="all" [{if $ReportType == "all"}]selected[{/if}]>[{ oxmultilang ident="CONTENT_LIST_ALL" }]&nbsp;</option>
                <option value="article" [{if $ReportType == "article"}]selected[{/if}]>[{ oxmultilang ident="GENERAL_ITEM" }]&nbsp;</option>
                <option value="category" [{if $ReportType == "category"}]selected[{/if}]>[{ oxmultilang ident="CONTENT_MAIN_CATEGORY" }]&nbsp;</option>
                <option value="user" [{if $ReportType == "user"}]selected[{/if}]>[{ oxmultilang ident="GENERAL_USER" }]&nbsp;</option>
                <option value="order" [{if $ReportType == "order"}]selected[{/if}]>[{ oxmultilang ident="order" }]&nbsp;</option>
                <option value="payment" [{if $ReportType == "payment"}]selected[{/if}]>[{ oxmultilang ident="tbcluser_payment" }]&nbsp;</option>
                <option value="module" [{if $ReportType == "module"}]selected[{/if}]>[{ oxmultilang ident="mxmodule" }]&nbsp;</option>
                <option value="regexp" [{if $ReportType == "regexp"}]selected[{/if}]>[{ oxmultilang ident="JXADMINLOG_REGEXP" }]&nbsp;</option>
            </select>
            <span style="margin-left:20px;[{if $ReportType != "regexp"}]color:#a0a0a0;[{/if}]">Filterbedingung:</span> 
            <input type="text" name="jxadminlog_regexp" value="[{$FreeRegexp}]" [{if $ReportType != "regexp"}]disabled="disabled"[{else}]size=40[{/if}]>
            <input style="margin-left:20px;" type="submit" 
               onClick="document.forms['jxadminlog'].elements['fnc'].value = '';" 
               value=" [{ oxmultilang ident="ORDER_MAIN_UPDATE_DELPAY" }] " />
        </form>
    </div>
    <div style="position:absolute;top:4px;right:8px;color:gray;font-size:0.9em;border:1px solid gray;border-radius:3px;">&nbsp;[{$sModuleId}]&nbsp;[{$sModuleVersion}]&nbsp;</div>
               
    <div id="liste" style="border:0px solid gray; padding:4px; width:99%; height:92%; overflow-y:scroll; float:left;">
            <div style="height: 12px;"></div>
            
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
    </div>

</div>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]

