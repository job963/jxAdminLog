<?php
/**
 * Metadata version
 */
$sMetadataVersion = '1.1';
 
/**
 * Module information
 */
$aModule = array(
    'id'           => 'jxadminlog',
    'title'        => 'jxAdminLog - Display of Logged Admin Actions',
    'description'  => array(
                        'de' => 'Anzeige der protokollierten Admin Aktionen an jedem Objekt und als Gesamtbericht.<br /><br />'
                                . '(um das Logging zu aktivieren muss in der Datei config.inc.php die Einstellung<br />'
                                . '<code>$this->blLogChangesInAdmin = false;</code> auf <code>True</code> geändert werden)<br /><br />',
                        'en' => 'Display of Logged Administrative Actions for each Object and as full Report.<br /><br />'
                                . '(for enabling the logging you have to change the setting<br />'
                                . '<code>$this->blLogChangesInAdmin = false;</code> to <code>True</code>)<br /><br />'
                        ),
    'thumbnail'    => 'jxadminlog.png',
    'version'      => '0.3.1',
    'author'       => 'Joachim Barthel',
    'url'          => 'https://github.com/job963/jxAdminLog',
    'email'        => 'jobarthel@gmail.com',
    'extend'       => array(
                        ),
    'files'        => array(
                        'jxadminlog'     	=> 'jxmods/jxadminlog/application/controllers/admin/jxadminlog.php',
                        'jxadminlog_history' 	=> 'jxmods/jxadminlog/application/controllers/admin/jxadminlog_history.php',
                        'jxadminlog_events' 	=> 'jxmods/jxadminlog/core/jxadminlog_events.php'
                        ),
    'templates'    => array(
                        'jxadminlog.tpl'            => 'jxmods/jxadminlog/application/views/admin/tpl/jxadminlog.tpl',
                        'jxadminlog_history.tpl'    => 'jxmods/jxadminlog/application/views/admin/tpl/jxadminlog_history.tpl'
                        ),
    'events'       => array(
                        'onActivate'   => 'jxadminlog_events::onActivate', 
                        'onDeactivate' => 'jxadminlog_events::onDeactivate'
                        ),
    'settings' => array(
                        array(
                                'group' => 'JXADMINLOG_WHERE', 
                                'name'  => 'sJxAdminLogExcludeThis', 
                                'type'  => 'str', 
                                'value' => ''
                                )
                        )
);