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
 * @copyright (C) 2015 Joachim Barthel
 * @author    Joachim Barthel <jobarthel@gmail.com>
 *
 */

class jxadminlog extends oxAdminDetails {

    protected $_sThisTemplate = "jxadminlog.tpl";

    /**
     * Displays the latest log entries
     */
    public function render() 
    {
        parent::render();

        $myConfig = oxRegistry::getConfig();
        $blAdminLog = $myConfig->getConfigParam('blLogChangesInAdmin');
        
        if ($blAdminLog == TRUE) {
            $sSql = "SELECT l.oxtimestamp, u.oxusername, u.oxfname, u.oxlname, oxcompany, /*l.oxfnc,*/ l.oxsql "
                    . "FROM oxadminlog l, oxuser u "
                    . "WHERE l.oxuserid = u.oxid "
                    . "ORDER BY oxtimestamp DESC "
                    . "LIMIT 0,200";

            $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
            $rs = $oDb->Execute($sSql);
            $aAdminLogs = array();
            while (!$rs->EOF) {
                array_push($aAdminLogs, $rs->fields);
                $rs->MoveNext();
            }
            
            foreach ($aAdminLogs as $key => $aAdminLog) {
                $aAdminLogs[$key]['oxsql'] = $this->_keywordHighlighter( strip_tags( $aAdminLogs[$key]['oxsql'] ) );
            }
                
            $this->_aViewData["aAdminLogs"] = $aAdminLogs;
        }

        return $this->_sThisTemplate;
    }
    
    
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
    
    
    /*
    public function deleteVoucher() 
    {
        $sVoucherId = oxRegistry::getConfig()->getRequestParameter( 'voucherdelid' );
        
        //echo 'deleteVoucher='.$sVoucherId;
        $sSql = "DELETE FROM oxvouchers WHERE oxid = '{$sVoucherId}' ";
        $oDb = oxDb::getDb();
        $oDb->Execute($sSql);
        $oDb = null;
        
        return;
    }
    */
	
}
