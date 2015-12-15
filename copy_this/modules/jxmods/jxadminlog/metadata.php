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
                        'de' => 'Anzeige der protokollierten Admin Aktionen an jedem Objekt und als Gesamtbericht.',
                        'en' => 'Display of Logged Administrative Actions for each Object and as complete Report.'
                        ),
    'thumbnail'    => 'jxadminlog.png',
    'version'      => '0.1.0',
    'author'       => 'Joachim Barthel',
    'url'          => 'https://github.com/job963/jxAdminLog',
    'email'        => 'jobarthel@gmail.com',
    'extend'       => array(
                        ),
    'files'        => array(
                        'jxadminlog'     	=> 'jxmods/jxadminlog/application/controllers/admin/jxadminlog.php',
                        'jxadminlog_history' 	=> 'jxmods/jxadminlog/application/controllers/admin/jxadminlog_history.php'
                        ),
    'templates'    => array(
                        'jxadminlog.tpl'            => 'jxmods/jxadminlog/application/views/admin/tpl/jxadminlog.tpl',
                        'jxadminlog_history.tpl'    => 'jxmods/jxadminlog/application/views/admin/tpl/jxadminlog_history.tpl'
                        )
);