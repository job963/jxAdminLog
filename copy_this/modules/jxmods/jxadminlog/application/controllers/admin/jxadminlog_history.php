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
$aEditDates = $this->_getEditDates();
/*echo '<hr>EditDates:<pre>';
print_r($aEditDates);
echo '</pre>';/**/
            
        $this->_aViewData["blAdminLog"] = $blAdminLog;
        $this->_aViewData["aAdminLogs"] = $aAdminLogs;
        $this->_aViewData["aEditDates"] = $aEditDates;

        return $this->_sThisTemplate;
    }
    
    
    private function _getEditDates()
    {
        $aTables = $this->_getTables();
        $sObjectId = $this->getEditObjectId();
//echo count($aTables);        
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
//---echo "$sSql<br>";
                try {
                    $rs = $oDb->Select($sSql);
                }
                catch (Exception $e) {
                    echo $e->getMessage();
                }
                if ($rs) {
                    while (!$rs->EOF) {
                        array_push($aEditDates, $rs->fields);
                        $rs->MoveNext();
                    }
                }
            }
            $oDb = NULL;
        }
        return $aEditDates;
    }
	
	
    private function _getTables()
    {
        $sObjectType = $this->_getObjectType();
//--echo 'sObjectType='.$sObjectType.'<br>';        
        
        switch ( $sObjectType ) {

            case 'oxarticle':
                $aTables = array(
                            'oxarticles'   => array('oxinsert','oxtimestamp','oxid'),
                            'oxartextends' => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid'),
                            'oxobject2attribute' => array('"0000-00-00" AS oxinsert','MAX(oxtimestamp) AS oxtimestamp','oxobjectid'),
                            'oxobject2category' => array('"0000-00-00" AS oxinsert','oxtimestamp','oxobjectid'),
                            'oxobject2discount' => array('"0000-00-00" AS oxinsert','oxtimestamp','oxobjectid')
                            );
                break;

            case 'oxcategory':
                $aTables = array(
                            'oxcategories' => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid')
                            );
                break;

            case 'oxuser':
                $aTables = array(
                            'oxuser'           => array('oxcreate AS oxinsert','oxtimestamp','oxid'),
                            'oxnewssubscribed' => array('oxsubscribed AS oxinsert','oxtimestamp','oxuserid'),
                            'oxremark'         => array('oxcreate AS oxinsert','oxtimestamp','oxparentid')
                            );
                break;

            case 'oxorder':
                $aTables = array(
                            'oxorder'         => array('oxorderdate AS oxinsert','oxtimestamp','oxid'),
                            'oxorderarticles' => array('oxinsert','oxtimestamp','oxorderid')
                            );
                break;

            case 'oxpayment':
                $aTables = array(
                            'oxpayments' => array('"0000-00-00" AS oxinsert','oxtimestamp','oxid')
                            );
                break;

            case 'oxmodule':
                $aTables = array(
                            'oxconfig'        => array('"0000-00-00" AS oxinsert','oxtimestamp','oxmodule'),
                            'oxtplblocks'     => array('"0000-00-00" AS oxinsert','oxtimestamp','oxmodule')
                            );
                break;

            default:    // all
                return NULL;
                break;
        }
        
        return $aTables;
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
