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

class jxadminlog_history extends oxAdminDetails {

    protected $_sThisTemplate = "jxadminlog_history.tpl";

    /**
     * Displays the latest log entries of selected object
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

        $sObjectId = $this->getEditObjectId();
		
		
        $sSql = "SELECT l.oxtimestamp, u.oxusername, u.oxfname, u.oxlname, oxcompany, /*l.oxfnc,*/ l.oxsql "
                . "FROM oxadminlog l, oxuser u "
                . "WHERE l.oxuserid = u.oxid "
                    . "AND l.oxsql LIKE '%{$sObjectId}%' "
                    . $sWhereShopId
                . "ORDER BY oxtimestamp DESC "
                . "LIMIT 0,100";

        $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
        //$rs = $oDb->Execute($sSql);
        try {
            $rs = $oDb->Select($sSql);
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
        $oDb = NULL;

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

        $aEditDates = $this->_getEditDates();
            
        $this->_aViewData["blAdminLog"] = $blAdminLog;
        $this->_aViewData["aAdminLogs"] = $aAdminLogs;
        $this->_aViewData["aEditDates"] = $aEditDates;

        return $this->_sThisTemplate;
    }
    
    
    /*
     * Returns the related tables with first, last modification and username
     */
    private function _getEditDates()
    {
        $aTables = $this->_getTables();
        $sObjectId = $this->getEditObjectId();

        if( count($aTables) > 0 ) {
            $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
            $aEditDates = array();
            foreach ($aTables as $sTable => $aColumns) {
                $sColumns = implode( ',', $aColumns );
                if ($sTable == "oxconfig") {
                    $sSql = "SELECT '$sTable' AS jxtable, $sColumns FROM $sTable WHERE {$aColumns[2]} = 'module:$sObjectId' ";
                } else {
                    $sSql = "SELECT '$sTable' AS jxtable, $sColumns FROM $sTable WHERE {$aColumns[2]} = '$sObjectId' ";
                }

                try {
                    $rs = $oDb->Select($sSql);
                }
                catch (Exception $e) {
                    echo $e->getMessage();
                }
                if ($rs) {
                    while (!$rs->EOF) {
                        if ($rs->fields['oxtimestamp'] != '') {
                            array_push($aEditDates, $rs->fields);
                        }
                        $rs->MoveNext();
                    }
                }
                foreach ($aEditDates as $key => $aEditDate) {
                    $aEditDates[$key]['jxusername'] = $this->_getLogUsername( $sObjectId, $aEditDate['oxtimestamp'] );
                }
            }
            $oDb = NULL;
        }
        return $aEditDates;
    }
    
    
    /*
     * Returns the username of the last modification of an object
     */
    private function _getLogUsername( $sObjectId, $sTimestamp ) 
    {
        $sSql = "SELECT u.oxusername, u.oxfname, u.oxlname "
                . "FROM oxadminlog l, oxuser u "
                . "WHERE l.oxuserid = u.oxid "
                    . "AND l.oxsql LIKE '%{$sObjectId}%' "
                    . "AND l.oxtimestamp = '{$sTimestamp}' "
                . "LIMIT 0,1";
        $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );

        try {
            $rs = $oDb->Select($sSql);
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
        $oDb = NULL;

        $aAdminLogs = array();
        if ($rs) {
            if ($rs->_numOfRows > 0) {
                return $rs->fields['oxusername'];
            } else {
                return '';
            }
        }
    }
	
	
    /*
     * Returns array with tablename and related fields for the actual object
     */
    private function _getTables()
    {
        $sObjectType = $this->_getObjectType();
//echo $sObjectType;        
        
        switch ( $sObjectType ) {

            case 'oxcountry':
                $aTables = array(
                            'oxcountry'         => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxstates'          => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxcountryid')
                            );
                break;

            case 'oxvendor':
                $aTables = array(
                            'oxvendor'          => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxseo'             => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxobjectid')
                            );
                break;

            case 'oxmanufacturer':
                $aTables = array(
                            'oxmanufacturers'   => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxseo'             => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxobjectid')
                            );
                break;

            case 'oxpayment':
                $aTables = array(
                            'oxpayments'        => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxobject2payment'  => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxpaymentid')
                            );
                break;

            case 'oxdiscount':
                $aTables = array(
                            'oxdiscount'        => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxobject2discount' => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxdiscountid')
                            );
                break;

            case 'oxdeliveryset':
                $aTables = array(
                            'oxdeliveryset'     => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxdel2delset'      => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxdelsetid'),
                            'oxobject2delivery' => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxdeliveryid'),
                            'oxobject2payment'  => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxobjectid')
                            );
                break;

            case 'oxdelivery':
                $aTables = array(
                            'oxdelivery' => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxobject2delivery' => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxdeliveryid')
                            );
                break;

            case 'oxvoucherserie':
                $aTables = array(
                            'oxvoucherseries'   => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxvouchers'        => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxvoucherserieid'),
                            'oxobject2discount' => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxdiscountid'),
                            'oxobject2group'    => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxobjectid')
                            );
                break;

            case 'oxwrapping':
                $aTables = array(
                            'oxwrapping'        => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid')
                            );
                break;

            case 'oxarticle':
                $aTables = array(
                            'oxarticles'        => array('oxinsert','oxtimestamp','oxid'),
                            'oxartextends'      => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxobject2attribute' => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxobjectid'),
                            'oxmediaurls'       => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxobjectid'),
                            'oxfiles'           => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxartid'),
                            'oxobject2category' => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxobjectid'),
                            'oxobject2discount' => array('"0000-00-00" AS oxinsert','oxtimestamp','oxobjectid'),
                            'oxseo'             => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxobjectid')
                            );
                break;

            case 'oxattribute':
                $aTables = array(
                            'oxattribute' => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxcategory2attribute' => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxattrid'),
                            'oxobject2attribute' => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxattrid')
                            );
                break;

            case 'oxcategory':
                $aTables = array(
                            'oxcategories'      => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxobject2category' => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxcatnid'),
                            'oxobject2discount' => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxobjectid'),
                            'oxseo'             => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxobjectid')
                            );
                break;

            case 'oxselectlist':
                $aTables = array(
                            'oxselectlist'      => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxobject2selectlist' => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxselnid')
                            );
                break;

            case 'oxuser':
                $aTables = array(
                            'oxuser'           => array('oxcreate AS oxinsert','oxtimestamp','oxid'),
                            'oxnewssubscribed' => array('oxsubscribed AS oxinsert','oxtimestamp','oxuserid'),
                            'oxremark'         => array('oxcreate AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxparentid'),
                            'oxobject2group'   => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxobjectid')
                            );
                break;

            case 'oxgroups':
                $aTables = array(
                            'oxgroups'        => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxobject2group'  => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxgroupsid')
                            );
                break;

            case 'oxorder':
                $aTables = array(
                            'oxorder'         => array('oxorderdate AS oxinsert','oxtimestamp','oxid'),
                            'oxorderarticles' => array('oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxorderid')
                            );
                break;

            case 'oxnews':
                $aTables = array(
                            'oxnews'          => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxobject2group'   => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxobjectid')
                            );
                break;

            case 'oxnewsletter':
                $aTables = array(
                            'oxnewsletter'     => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxobject2group'   => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxobjectid')
                            );
                break;

            case 'oxlinks':
                $aTables = array(
                            'oxlinks'          => array('oxinsert','oxtimestamp','oxid')
                            );
                break;

            case 'oxcontent':
                $aTables = array(
                            'oxcontents'       => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxseo'            => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxobjectid')
                            );
                break;

            case 'oxactions':
                $aTables = array(
                            'oxactions'         => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxactions2article' => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxactionid')
                            );
                break;

            case 'oxmodule':
                $aTables = array(
                            'oxconfig'        => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxmodule'),
                            'oxtplblocks'     => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxmodule')
                            );
                break;

            default:    // all
                return NULL;
                break;
        }
        
        return $aTables;
    }
	
	
    /*
     * Determines the class / object type of the actual object
     */
    private function _getObjectType()
    {
        
        // --- main menu
        $oCountry = oxNew('oxcountry');
        if ($oCountry->load($this->getEditObjectId())) {
            return 'oxcountry';
        }
        
        $oVendor = oxNew('oxvendor');
        if ($oVendor->load($this->getEditObjectId())) {
            return 'oxvendor';
        }
        
        $oManufacturer = oxNew('oxmanufacturer');
        if ($oManufacturer->load($this->getEditObjectId())) {
            return 'oxmanufacturer';
        }
        
        /*$oLanguage = oxNew('oxlang');
        if ($oLanguage->load($this->getEditObjectId())) {
            return 'oxlang';
        }*/
        
        // --- shop settings
        $oPayment = oxNew('oxpayment');
        if ($oPayment->load($this->getEditObjectId())) {
            return 'oxpayment';
        }

        $oDiscount = oxNew('oxdiscount');
        if ($oDiscount->load($this->getEditObjectId())) {
            return 'oxdiscount';
        }

        $oDeliveryset = oxNew('oxdeliveryset');
        if ($oDeliveryset->load($this->getEditObjectId())) {
            return 'oxdeliveryset';
        }

        $oDelivery = oxNew('oxdelivery');
        if ($oDelivery->load($this->getEditObjectId())) {
            return 'oxdelivery';
        }

        $oVoucherserie = oxNew('oxvoucherserie');
        if ($oVoucherserie->load($this->getEditObjectId())) {
            return 'oxvoucherserie';
        }

        $oWrapping = oxNew('oxwrapping');
        if ($oWrapping->load($this->getEditObjectId())) {
            return 'oxwrapping';
        }

        // --- Extensions
        $oModule = oxNew('oxmodule');
        if ($oModule->load($this->getEditObjectId())) {
            return 'oxmodule';
        }
        
        // --- Products
        $oArticle = oxNew('oxarticle');
        if ($oArticle->load($this->getEditObjectId())) {
            return 'oxarticle';
        }
        
        $oAttribute = oxNew('oxattribute');
        if ($oAttribute->load($this->getEditObjectId())) {
            return 'oxattribute';
        }
        
        $oCategory = oxNew('oxcategory');
        if ($oCategory->load($this->getEditObjectId())) {
            return 'oxcategory';
        }
        
        $oSelectlist = oxNew('oxselectlist');
        if ($oSelectlist->load($this->getEditObjectId())) {
            return 'oxselectlist';
        }

        // --- Users
        $oUser = oxNew('oxuser');
        if ($oUser->load($this->getEditObjectId())) {
            return 'oxuser';
        }

        $oGroup = oxNew('oxgroups');
        if ($oGroup->load($this->getEditObjectId())) {
            return 'oxgroups';
        }
        
        // --- Orders
        $oOrder = oxNew('oxorder');
        if ($oOrder->load($this->getEditObjectId())) {
            return 'oxorder';
        }
        
        // --- Customer Info
        $oNews = oxNew('oxnews');
        if ($oNews->load($this->getEditObjectId())) {
            return 'oxnews';
        }
        
        $oNewsletter = oxNew('oxnewsletter');
        if ($oNewsletter->load($this->getEditObjectId())) {
            return 'oxnewsletter';
        }
        
        $oLinks = oxNew('oxlinks');
        if ($oLinks->load($this->getEditObjectId())) {
            return 'oxlinks';
        }
        
        $oContent = oxNew('oxcontent');
        if ($oContent->load($this->getEditObjectId())) {
            return 'oxcontent';
        }
        
        $oActions = oxNew('oxactions');
        if ($oActions->load($this->getEditObjectId())) {
            return 'oxactions';
        }
        
        return '';
    }
    
    
    /*
     * Checks if the OxId exists in the given table
     */
    private function _existsObject( $sObject, $sOxId )
    {
        $sSql = "SELECT * FROM $sObject WHERE oxid = '$sOxId' ";

        $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
        try {
            $rs = $oDb->Select($sSql);
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
        $oDb = NULL;
        
        if ($rs) {
            if ($rs->_numOfRows > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
        
    }
    
    
    /*
     * Highlights MySQL Keywords by using different colors
     */
    private function _keywordHighlighter( $sText ) 
    {
        $aSearch = array(
            'insert',
            'update',
            'delete'
        );
        $aReplace = array(
            '<span style="color:green;">insert</span>',
            '<span style="color:blue;">update</span>',
            '<span style="color:red;">delete</span>'
        );
        
        $sText = str_replace($aSearch, $aReplace, $sText);

        return $sText;
    }
    
	
}
