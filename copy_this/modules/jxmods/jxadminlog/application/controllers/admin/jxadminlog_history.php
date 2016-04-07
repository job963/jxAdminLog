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
//echo '_getObjectType()='.$this->_getObjectType();        
            
        $this->_aViewData["blAdminLog"] = $blAdminLog;
        $this->_aViewData["aAdminLogs"] = $aAdminLogs;

        return $this->_sThisTemplate;
    }
	
	
    private function _getTables( $sReport )
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

            default:    // all
                return '';
                break;
        }
        
        return $aKeywords;
    }
	
	
    private function _getObjectType()
    {
        
        // --- main menu
        $oCountry = oxNew('oxcountry');
        if ($oCountry->load($this->getEditObjectId())) {
            return 'oxcountry';
        }
        
        $oVendor = oxNew('oxvendor');
        if ($oVendor->load($this->getEditObjectId())) {
            return 'mxvendor';
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
            return 'oxdelivery  ';
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
        
        $oCategory = oxNew('oxcategory');
        if ($oCategory->load($this->getEditObjectId())) {
            return 'oxcategory';
        }

        // --- Users
        $oUser = oxNew('oxuser');
        if ($oUser->load($this->getEditObjectId())) {
            return 'oxuser';
        }

        /*$oGroup = oxNew('oxgroups');
        if ($oGroup->load($this->getEditObjectId())) {
            return 'oxgroups';
        }*/
        
        // --- Orders
        $oOrder = oxNew('oxorder');
        if ($oOrder->load($this->getEditObjectId())) {
            return 'oxorder';
        }
        
        /*
        $oModule = oxNew('');
        if ($oModule->load($this->getEditObjectId())) {
            return 'oxmodule';
        }
        
        
        $oModule = oxNew('');
        if ($oModule->load($this->getEditObjectId())) {
            return 'oxmodule';
        }
        */
        
        return '';
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
