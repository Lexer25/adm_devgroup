<?php defined('SYSPATH') OR die('No direct script access.');

defined('DEVGROUP_VERSION') OR define('DEVGROUP_VERSION', '1.0.1');

Kohana::$config->load('adm')
    ->set('devgroup', array(
        'title' => 'Группы устройств',
        'url' => 'devgroup',
        'icon' => 'fa-sitemap',
        'order' => 90,
    ));

// Основной маршрут
Route::set('devgroup', 'devgroup(/<action>(/<id>))', array('id' => '\d+'))
    ->defaults(array(
        'controller' => 'Devgroup',
        'action' => 'index',
    ));

// AJAX маршруты для дерева
Route::set('devgroup_getChildren', 'devgroup/getChildren/<id>', array('id' => '\d+'))
    ->defaults(array(
        'controller' => 'Devgroup',
        'action' => 'getChildren',
    ));

Route::set('devgroup_getDevices', 'devgroup/getDevices/<id>', array('id' => '\d+'))
    ->defaults(array(
        'controller' => 'Devgroup',
        'action' => 'getDevices',
    ));

// AJAX маршруты для управления устройствами в группе
Route::set('devgroup_addDevices', 'devgroup/addDevices')
    ->defaults(array('controller' => 'Devgroup', 'action' => 'addDevices'));

Route::set('devgroup_removeDevices', 'devgroup/removeDevices')
    ->defaults(array('controller' => 'Devgroup', 'action' => 'removeDevices'));
	
	// AJAX маршрут для сохранения режима отображения
Route::set('devgroup_setEditMode', 'devgroup/setEditMode')
    ->defaults(array('controller' => 'Devgroup', 'action' => 'setEditMode'));