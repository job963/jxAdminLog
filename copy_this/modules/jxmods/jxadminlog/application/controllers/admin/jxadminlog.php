<?php
/**
 *    This file is part of the module jxAdminLog for OXID eShop Community Edition.
 *
 *    The module jxAdminLog for OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    The module jxAdminLog for OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      https://github.com/job963/jxAdminLog
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @copyright (C) 2015-2016 Joachim Barthel
 * @author    Joachim Barthel <jobarthel@gmail.com>
 *
 */

class jxadminlog extends oxAdminDetails {

    protected $_sThisTemplate = "jxadminlog.tpl";

    /**
     * Displays the latest admin log entries as full report
     */
    public function render() 
    {
        parent::render();

        $myConfig = oxRegistry::getConfig();
        
        if ($myConfig->getBaseShopId() == 'oxbaseshop') {
            // CE or PE shop
            $sWhereShopId = "";
        } else {
            // EE shop
            $sWhereShopId = "AND l.oxshopid = {$myConfig->getBaseShopId()} ";
        }
        $blAdminLog = $myConfig->getConfigParam('blLogChangesInAdmin');
        $sExcludeThis = $myConfig->getConfigParam( 'sJxAdminLogExcludeThis' );
        if ( !Empty($sExcludeThis) ) {
            $sExcludeThis = "AND l.oxsql NOT REGEXP '{$sExcludeThis}' ";
        }
        
        $cReportType = $this->getConfig()->getRequestParameter( 'jxadminlog_reporttype' );
        if (empty($cReportType))
            $cReportType = "all";

        if ($cReportType == "regexp")
            $sFreeRegexp = $this->getConfig()->getRequestParameter( 'jxadminlog_regexp' );

        $cAdminUser = $this->getConfig()->getRequestParameter( 'jxadminlog_adminuser' );
        if (empty($cAdminUser)) {
            $cAdminUser = "all";
            $sWhereUser = "";
        } else {
            $sWhereUser = "AND l.oxuserid = '{$cAdminUser}' ";
        }

        $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );

        $sSql = "SELECT DISTINCT l.oxuserid, u.oxusername, u.oxfname, u.oxlname "
                . "FROM oxadminlog l, oxuser u "
                . "WHERE l.oxuserid = u.oxid "
                    . $sExcludeThis
                . "ORDER BY u.oxfname, u.oxlname ";
        
        try {
            $rs = $oDb->Select($sSql);
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
        
        $aAdminUsers = array();
        if ($rs) {
            while (!$rs->EOF) {
                array_push($aAdminUsers, $rs->fields);
                $rs->MoveNext();
            }
        }
        
        $sSql = "SELECT l.oxtimestamp, u.oxusername, u.oxfname, u.oxlname, oxcompany, /*l.oxfnc,*/ l.oxsql "
                . "FROM oxadminlog l, oxuser u "
                . "WHERE l.oxuserid = u.oxid "
                    . $sExcludeThis
                    . $this->_createKeywordFilter($cReportType, $sFreeRegexp)
                    . $sWhereShopId
                    . $sWhereUser
                . "ORDER BY l.oxtimestamp DESC "
                . "LIMIT 0,200";

        try {
            $rs = $oDb->Select($sSql);
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
        
        $aAdminLogs = array();
        if ($rs) {
            while (!$rs->EOF) {
                array_push($aAdminLogs, $rs->fields);
                $rs->MoveNext();
            }
        }

        foreach ($aAdminLogs as $key => $aAdminLog) {
            $aAdminLogs[$key]['oxsql'] = $this->_keywordHighlighter( strip_tags( $aAdminLogs[$key]['oxsql'] ) );
        }

        $this->_aViewData["ReportType"] = $cReportType;
        $this->_aViewData["FreeRegexp"] = $sFreeRegexp;
        $this->_aViewData["AdminUser"] = $cAdminUser;
        $this->_aViewData["aAdminUsers"] = $aAdminUsers;
        $this->_aViewData["aAdminLogs"] = $aAdminLogs;
            
        $this->_aViewData["blAdminLog"] = $blAdminLog;

        $oModule = oxNew('oxModule');
        $oModule->load('jxadminlog');
        $this->_aViewData["sModuleId"] = $oModule->getId();
        $this->_aViewData["sModuleVersion"] = $oModule->getInfo('version');

        return $this->_sThisTemplate;
    }
	
	
    private function _createKeywordFilter( $sReport, $sFreeRegexp )
    {
        switch ( $sReport ) {

            case 'article':
                $aKeywords = array('oxarticles','oxartextends');
                break;

            case 'category':
                $aKeywords = array('oxarticles','oxartextends');
                break;

            case 'user':
                $aKeywords = array('oxuser','oxnewssubscribed','oxremark');
                break;

            case 'order':
                $aKeywords = array('oxorder','oxorderarticles');
                break;

            case 'payment':
                $aKeywords = array('oxpayments');
                break;

            case 'module':
                $aKeywords = array('oxconfig','oxconfigdisplay','oxtplblocks');
                break;

            case 'regexp':
                if (empty($sFreeRegexp)) {
                    $sFreeRegexp = '.';
                }
                $aKeywords = array( $sFreeRegexp );
                break;

            default:    // all
                return '';
                break;
        }
        
        if (count($aKeywords) > 1) {
            $sRegex = implode( '|', $aKeywords );
        } else {
            $sRegex = $aKeywords[0];
        }
        
        return "AND  l.oxsql REGEXP '" . $sRegex . "' ";
    }
    
    
    private function _keywordHighlighter( $sText ) 
    {
        $aSearch = array(
            'insert ',
            'update ',
            'delete '
        );
        $aReplace = array(
            '<span style="color:green;">insert </span>',
            '<span style="color:blue;">update </span>',
            '<span style="color:red;">delete </span>'
        );
        
        $sText = str_replace($aSearch, $aReplace, $sText);

        return $sText;
    }
    
	
}
