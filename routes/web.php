<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\CategorymenuController;
use App\Http\Controllers\EmbeddingController;
use App\Http\Controllers\OptionmenuController;
use App\Http\Controllers\UsertypeController;
use App\Http\Controllers\WorkertypeController;
use App\Http\Controllers\AssistenceController;
use App\Http\Controllers\BranchofficeController;
use App\Http\Controllers\DashboardMainController;
use Illuminate\Support\Facades\Route;


Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/', [LoginController::class, 'in'])->name('in');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Other routes can be added here
Route::group(['middleware' => 'auth'], function () {
   Route::get('/dashboard', function () {
      return View::make('dashboard.home');
   });

    Route::get('dashboardmain/index', [DashboardMainController::class, 'index'])->name('dashboardmain.index');
    

   // Routes for CategorymenuController
    Route::post('categorymenu/search', [CategorymenuController::class,'search'])->name('categorymenu.search');
    Route::get('categorymenu/eliminar/{id}/{listarluego}', [CategorymenuController::class,'eliminar'])->name('categorymenu.eliminar');
    Route::resource('categorymenu', CategorymenuController::class, array('except' => array('show')));

    // Routes for OptionmenuController
     Route::post('optionmenu/search', [OptionmenuController::class,'search'])->name('optionmenu.search');
     Route::get('optionmenu/eliminar/{id}/{listarluego}', [OptionmenuController::class,'eliminar'])->name('optionmenu.eliminar');
     Route::resource('optionmenu', OptionmenuController::class, array('except' => array('show')));

    // Routes for usertypeController
    Route::post('usertype/search', [UsertypeController::class,'search'])->name('usertype.search');
    Route::get('usertype/eliminar/{id}/{listarluego}', [UsertypeController::class,'eliminar'])->name('usertype.eliminar');
    Route::get('usertype/obtenerpermisos/{listar}/{id}', [UsertypeController::class,'obtenerpermisos'])->name('usertype.obtenerpermisos');
    Route::post('usertype/guardarpermisos/{id}', [UsertypeController::class,'guardarpermisos'])->name('usertype.guardarpermisos');
    Route::resource('usertype', UsertypeController::class, array('except' => array('show')));


     // Routes for UserController
    Route::post('user/search', [UserController::class,'search'])->name('user.search');
    Route::get('user/eliminar/{id}/{listarluego}', [UserController::class,'eliminar'])->name('user.eliminar');
    Route::get('user/editpassword/{id}', [UserController::class,'editpassword'])->name('user.editpassword');
    Route::post('user/updatepassword/{id}', [UserController::class,'updatepassword'])->name('user.updatepassword');
    Route::resource('user', UserController::class, array('except' => array('show')));

   // Routes for EmployeeController
   Route::post('employee/search', [EmployeeController::class,'search'])->name('employee.search');
   Route::get('employee/eliminar/{id}/{listarluego}', [EmployeeController::class,'eliminar'])->name('employee.eliminar');
   Route::get('employee/indexsimple', [EmployeeController::class,'indexsimple'])->name('employee.indexsimple');
   Route::post('employee/searchsimple', [EmployeeController::class,'searchsimple'])->name('employee.searchsimple');
   Route::resource('employee',EmployeeController::class, array('except' => array('show')));

   // Routes for SupplierController
   Route::post('person/search', [PersonController::class,'search'])->name('person.search');
   Route::get('person/eliminar/{id}/{listarluego}', [PersonController::class,'eliminar'])->name('person.eliminar');
   Route::get('person/indexsimple', [PersonController::class,'indexsimple'])->name('person.indexsimple');
   Route::post('person/searchsimple', [PersonController::class,'searchsimple'])->name('person.searchsimple');
   Route::resource('person',PersonController::class, array('except' => array('show')));


   // Routes for WorkertypeController
   Route::post('workertype/search', [WorkertypeController::class,'search'])->name('workertype.search');
   Route::get('workertype/eliminar/{id}/{listarluego}', [WorkertypeController::class,'eliminar'])->name('workertype.eliminar');
   Route::resource('workertype', WorkertypeController::class, array('except' => array('show')));

    // Routes for EmbeddingController
   Route::post('embedding/search', [EmbeddingController::class,'search'])->name('embedding.search');
   Route::get('embedding/eliminar/{id}/{listarluego}', [EmbeddingController::class,'eliminar'])->name('embedding.eliminar');
   Route::resource('embedding', EmbeddingController::class, array('except' => array('show')));

    // Routes for AssistenceController
   Route::post('assistence/search', [AssistenceController::class,'search'])->name('assistence.search');
   Route::get('assistence/eliminar/{id}/{listarluego}', [AssistenceController::class,'eliminar'])->name('assistence.eliminar');
   Route::resource('assistence', AssistenceController::class, array('except' => array('show')));

    Route::post('branchoffice/search', [BranchofficeController::class,'search'])->name('branchoffice.search');
   Route::get('branchoffice/eliminar/{id}/{listarluego}', [BranchofficeController::class,'eliminar'])->name('branchoffice.eliminar');
   Route::resource('branchoffice', BranchofficeController::class, array('except' => array('show')));
});