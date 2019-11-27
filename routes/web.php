<?php
/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/admin', 'Auth\LoginController@showAdminLoginForm');
Route::get('/admin', 'Auth\LoginController@showAdminLoginForm')->name('admin.login');
Route::post('/admin', 'Auth\LoginController@adminLogin');
Route::match(['get', 'post'], '/logout', 'Auth\LoginController@userLogout')->name('logout');

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('admin')->group(function() {
    
    Route::match(['get', 'post'], '/logout', 'Auth\LoginController@admin_logout')->name('admin.logout');

    Route::get('/companies', 'CompaniesController@index')->name('admin.companies');
    Route::post('/companies/formdata', 'CompaniesController@formdata')->name('admin.companies.formdata');
    Route::post('/companies/save/{id}', 'CompaniesController@store')->name('admin.companies.store');
    Route::post('/companies/checkValidation', 'CompaniesController@formValidation')->name('admin.companies.formValidation');
    Route::match(['get', 'post'], '/companies/get_data', 'CompaniesController@get_data')->name('admin.companies.getdata');
    Route::post('/companies/delete/{id}', 'CompaniesController@delete')->name('admin.companies.delete');
    
    Route::get('/employees', 'EmployeesController@index')->name('admin.employees');
    Route::post('/employees/formdata', 'EmployeesController@formdata')->name('admin.employees.formdata');
    Route::post('/employees/save/{id}', 'EmployeesController@store')->name('admin.employees.store');
    Route::post('/employees/checkValidation', 'EmployeesController@formValidation')->name('admin.employees.formValidation');
    Route::match(['get', 'post'], '/employees/get_data', 'EmployeesController@get_data')->name('admin.employees.getdata');
    Route::post('/employees/delete/{id}', 'EmployeesController@delete')->name('admin.employees.delete');
    
});

    Route::get('/employees', 'EmployeesController@index')->name('employees');
    Route::post('/employees/formdata', 'EmployeesController@formdata')->name('employees.formdata');
    Route::post('/employees/save/{id}', 'EmployeesController@store')->name('employees.store');
    Route::post('/employees/checkValidation', 'EmployeesController@formValidation')->name('employees.formValidation');
    Route::match(['get', 'post'], '/employees/get_data', 'EmployeesController@get_data')->name('employees.getdata');
    Route::post('/employees/delete/{id}', 'EmployeesController@delete')->name('employees.delete');    

    

