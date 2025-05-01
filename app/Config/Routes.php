<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// landing
$routes->get('/auth/(:any)', 'Landing::auth/$1');
$routes->get('/', 'Landing::index');

// home
$routes->get('/home', 'Home::index');
$routes->post('/home/delete', 'Home::delete');
$routes->get('logout', 'Home::logout');
$routes->post('/home/switch_tema', 'Home::switch_tema');
$routes->post('/home/statistik', 'Home::statistik');
$routes->post('/home/bisyaroh', 'Home::bisyaroh');
$routes->post('/home/update_bisyaroh', 'Home::update_bisyaroh');
$routes->post('/home/koperasi', 'Home::koperasi');
$routes->post('/home/update_koperasi', 'Home::update_koperasi');
$routes->post('/home/add_koperasi', 'Home::add_koperasi');

// menu
$routes->get('/menu', 'Menu::index');
$routes->post('/menu/add', 'Menu::add');
$routes->post('/menu/update', 'Menu::update');

// options
$routes->get('/options', 'Options::index');
$routes->post('/options/add', 'Options::add');
$routes->post('/options/update', 'Options::update');

// options
$routes->get('/user', 'User::index');
$routes->post('/user/add', 'User::add');
$routes->post('/user/update', 'User::update');

// options
$routes->get('/barang', 'Barang::index');
$routes->post('/barang/add', 'Barang::add');
$routes->post('/barang/update', 'Barang::update');

// penjualan
$routes->get('/penjualan', 'Penjualan::index');
$routes->post('/penjualan/no_nota', 'Penjualan::no_nota');
$routes->post('/penjualan/cari_barang', 'Penjualan::cari_barang');
$routes->post('/penjualan/transaksi', 'Penjualan::transaksi');
$routes->post('/penjualan/user', 'Penjualan::user');
$routes->post('/penjualan/update', 'Penjualan::update');

// pengeluaran
$routes->get('/pengeluaran', 'Pengeluaran::index');
$routes->post('/pengeluaran/cari_barang', 'Pengeluaran::cari_barang');
$routes->post('/pengeluaran/transaksi', 'Pengeluaran::transaksi');
$routes->post('/pengeluaran/user', 'Pengeluaran::user');
$routes->post('/pengeluaran/update', 'Pengeluaran::update');

$routes->get('/inventaris', 'Pengeluaran::inventaris');
$routes->post('/inventaris/add', 'Pengeluaran::add');
$routes->post('/inventaris/update', 'Pengeluaran::update');

// guest
$routes->get('guest/laporan/(:any)/(:num)', 'Guest::laporan/$1/$2');
$routes->get('/guest/cetak_nota/(:any)', 'Guest::cetak_nota/$1');
// hutang
$routes->get('hutang', 'Hutang::index');
$routes->post('hutang/bayar', 'Hutang::bayar');
