<?php
/**
 * phpMyAdmin configuration for Sinematix
 */

$cfg['blowfish_secret'] = 'sinematix_secret_key_1234567890abcdef';

$i = 0;
$i++;

// Server configuration
$cfg['Servers'][$i]['host'] = 'localhost';
$cfg['Servers'][$i]['port'] = '';
$cfg['Servers'][$i]['socket'] = '';
$cfg['Servers'][$i]['user'] = 'root';
$cfg['Servers'][$i]['password'] = '';
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['AllowNoPassword'] = true;

// Default database
$cfg['Servers'][$i]['only_db'] = '';
$cfg['Servers'][$i]['hide_db'] = '';

// Directories for saving/loading files
$cfg['UploadDir'] = '';
$cfg['SaveDir'] = '';

// Theme
$cfg['ThemeDefault'] = 'pmahomme';

// Language
$cfg['DefaultLang'] = 'tr';
