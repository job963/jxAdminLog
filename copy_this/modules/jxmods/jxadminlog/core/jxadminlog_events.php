<?php
/*
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
 * @copyright (C) Joachim Barthel 2015
 * 
 */

class jxadminlog_events
{ 
    public static function onActivate() 
    { 
        //$myConfig = oxRegistry::getConfig();
        $blAdminLog = oxRegistry::get("oxConfig")->getConfigParam('blLogChangesInAdmin');
        $sLogPath = oxRegistry::get("oxConfig")->getConfigParam("sShopDir") . '/log/';
        $fh = fopen($sLogPath.'jxmods.log', "a+");
        
        if ($blAdminLog == FALSE) {
            fputs( $fh, date("Y-m-d H:i:s ").'jxAdminLog: Setting "$this->blLogChangesInAdmin" in config.inc.php is deactivated!'."\n" );
            echo '<div style="border:2px solid #dd0000;margin:10px;padding:5px;background-color:#ffdddd;font-family:sans-serif;font-size:14px;">';
            echo '<b>Setting <i>blLogChangesInAdmin</i> in <i>config.inc.php</i> is deactivated!</b><br />Actually no new admin action will be logged.';
            echo '</div>';
            //return FALSE;
        }
        fclose($fh);
        
        return TRUE;
    }

    
    public static function onDeactivate() 
    { 
        // do nothing
        
        return TRUE; 
    }  
}