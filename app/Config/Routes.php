<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
//$routes->setDefaultController('Login');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false); 
$routes->set404Override();
$routes->setAutoRoute(true);
 
/*     
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */ 

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
//$routes->get('/', 'Home::index'); 
$routes->get('/', 'Login::index',['filter'=>'noauth']);  
$routes->post('/', 'Login::index',['filter'=>'noauth']);  

$routes->get('/login', 'Login::index',['filter'=>'noauth']);  
$routes->post('/login', 'Login::index',['filter'=>'noauth']);   


//$routes->get('admin', 'Admin\Oc::index',['filter'=>'auth']);  

$routes->get('admin/logout', 'Admin\Admin::logout',['filter'=>'auth']);  
$routes->get('admin/personal', 'Admin\Personal::index',['filter'=>'auth:1,2,3']);  
$routes->get('admin/key', 'Admin\Key::index',['filter'=>'auth']);  

$routes->get('admin', 'Admin\Oc::index',['filter'=>'auth']);  

$routes->get('admin/oc', 'Admin\Oc::index',['filter'=>'auth']);  
$routes->get('admin/oc/editar/(:any)', 'Admin\Oc::editar/$1',['filter'=>'auth']);  
$routes->get('admin/oc/agregar', 'Admin\Oc::agregar',['filter'=>'auth']);  
$routes->get('admin/oc/reporteOrdenes', 'Admin\Oc::reporteOrdenes',['filter'=>'auth']);  
$routes->get('admin/oc/reporteFinanzas', 'Admin\Oc::reporteFinanzas',['filter'=>'auth']);  
$routes->get('admin/oc/reporteTesoreria', 'Admin\Oc::reporteTesoreria',['filter'=>'auth']);  

$routes->get('admin/empresa', 'Admin\Empresa::index',['filter'=>'auth']);  
$routes->get('admin/banco', 'Admin\Banco::index',['filter'=>'auth']);  
$routes->get('admin/clasecosto', 'Admin\Clasecosto::index',['filter'=>'auth']);  


 
/* 
$routes->post('admin/clasecosto/ajaxAgregar', 'Admin\Clasecosto::ajaxAgregar',['filter'=>'auth']);  */

  
 
 
/* 
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it. 
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
