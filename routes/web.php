<?php
/**
 * Web Routes
 */

global $router;

// ============================================
// PUBLIC ROUTES
// ============================================

// Home
$router->get('/', 'HomeController@index');

// Menu / Cardápio
$router->get('/menu', 'MenuController@index');
$router->get('/menu/{slug}', 'MenuController@category');

// Cart & Checkout
$router->get('/checkout', 'CartController@checkout');
$router->post('/order', 'OrderController@store');

// Order Status
$router->get('/pedido/{id}', 'OrderController@status');

// Ready Orders Public Display
$router->get('/ready', 'OrderController@ready');

// API - Public
$router->get('/api/ready-orders', 'ApiController@readyOrders');
$router->get('/api/menu', 'ApiController@menu');

// ============================================
// AUTHENTICATION
// ============================================

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// ============================================
// PROTECTED ROUTES (Admin/Kitchen)
// ============================================

// Admin - Orders Management
$router->get('/admin/pedidos', 'AdminController@orders');
$router->get('/admin/pedidos/{id}', 'AdminController@orderDetails');

// Admin - Products Management
$router->get('/admin/produtos', 'ProductAdminController@index');
$router->get('/admin/produtos/novo', 'ProductAdminController@create');
$router->post('/admin/produtos', 'ProductAdminController@store');
$router->get('/admin/produtos/{id}/editar', 'ProductAdminController@edit');
$router->post('/admin/produtos/{id}', 'ProductAdminController@update');
$router->post('/admin/produtos/{id}/excluir', 'ProductAdminController@destroy');
$router->patch('/admin/produtos/{id}/toggle', 'ProductAdminController@toggleActive');
$router->post('/admin/produtos/{id}/toggle', 'ProductAdminController@toggleActive');

// Admin - Categories Management
$router->get('/admin/categorias', 'ProductAdminController@categories');
$router->get('/admin/categorias/nova', 'ProductAdminController@createCategory');
$router->post('/admin/categorias', 'ProductAdminController@storeCategory');
$router->get('/admin/categorias/{id}/editar', 'ProductAdminController@editCategory');
$router->post('/admin/categorias/{id}', 'ProductAdminController@updateCategory');
$router->post('/admin/categorias/{id}/excluir', 'ProductAdminController@destroyCategory');

// Admin - Financial
$router->get('/admin/financeiro', 'FinanceController@index');
$router->post('/admin/financeiro/toggle', 'FinanceController@togglePayment');

// Admin - Settings
$router->get('/admin/configuracoes', 'SettingsController@index');
$router->post('/admin/configuracoes/logo', 'SettingsController@updateLogo');
$router->post('/admin/configuracoes/salvar', 'SettingsController@updateSettings');

// Kitchen - KDS
$router->get('/cozinha', 'KitchenController@index');

// API - Protected (Kitchen/Admin)
$router->get('/api/kitchen/orders', 'ApiController@kitchenOrders');
$router->patch('/api/orders/{id}/status', 'ApiController@updateOrderStatus');
$router->get('/api/order/{id}', 'ApiController@orderDetails');
