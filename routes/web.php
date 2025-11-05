<?php

use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeePaymentController;
use App\Http\Controllers\TeacherDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/



Route::get('/', function () {
    return view('welcome');
});

Route::view('/about', 'about')->name('about');

Route::view('/features', 'features')->name('features');

Route::view('/contact', 'contact')->name('contact');

Route::view('/pricing', 'pricing')->name('pricing');




// Redirect root → students list
// Route::get('/', function () {
//     return redirect()->route('students.index');
// })->middleware(['auth'])->name('home');



// ===============================
// Students
// ===============================

// View (teachers + admins)
Route::middleware(['auth'])->group(function () {
    Route::get('students', [StudentController::class, 'index'])->name('students.index');

    // Put static routes before dynamic ones
    Route::get('students/create', [StudentController::class, 'create'])
        ->middleware('admin')
        ->name('students.create');
    Route::post('students', [StudentController::class, 'store'])
        ->middleware('admin')
        ->name('students.store');

    Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('students/{student}/edit', [StudentController::class, 'edit'])
        ->middleware('admin')
        ->name('students.edit');
    Route::put('students/{student}', [StudentController::class, 'update'])
        ->middleware('admin')
        ->name('students.update');
    Route::delete('students/{student}', [StudentController::class, 'destroy'])
        ->middleware('admin')
        ->name('students.destroy');

    Route::post('students/promote', [StudentController::class, 'promote'])
        ->middleware('admin')
        ->name('students.promote');
});

// ===============================
// Guardians
// ===============================

Route::middleware(['auth'])->group(function () {
    Route::get('guardians', [GuardianController::class, 'index'])->name('guardians.index');

    // Static routes first
    Route::get('guardians/create', [GuardianController::class, 'create'])
        ->middleware('admin')
        ->name('guardians.create');
    Route::post('guardians', [GuardianController::class, 'store'])
        ->middleware('admin')
        ->name('guardians.store');

    Route::get('guardians/{guardian}', [GuardianController::class, 'show'])->name('guardians.show');
    Route::get('guardians/{guardian}/edit', [GuardianController::class, 'edit'])
        ->middleware('admin')
        ->name('guardians.edit');
    Route::put('guardians/{guardian}', [GuardianController::class, 'update'])
        ->middleware('admin')
        ->name('guardians.update');
    Route::delete('guardians/{guardian}', [GuardianController::class, 'destroy'])
        ->middleware('admin')
        ->name('guardians.destroy');
});


// ===============================
// Teachers (Admin only)
// ===============================
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('teachers', TeacherController::class);
});

// ===============================
// Classes
// ===============================
Route::middleware(['auth'])->group(function () {
    Route::resource('classes', ClassController::class);

    // Promote class (only for admins ideally)
    Route::post('/classes/{id}/promote', [ClassController::class, 'promoteClass'])
        ->name('classes.promote');
});


// ===============================
// Dashboard
// ===============================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

// ===============================
// Fees
// ===============================
    
    Route::resource('fees', FeeController::class)->middleware('auth');
// ===============================
// Payment
// ===============================
    
Route::resource('payments', PaymentController::class);

// ----------------------------
//  Fee Payments (Admin Only)
// ----------------------------
Route::middleware(['auth'])->group(function () {

    // Resource routes for fee_payments
  
        
Route::resource('fee_payments', FeePaymentController::class);

    // AJAX: get students by class
    Route::get('students/by-class/{classId}', [FeePaymentController::class, 'getStudentsByClass'])
        ->name('students.byClass');
});

// Receipt


Route::middleware(['auth'])->group(function () {
    // View a specific student's receipt (web)
    Route::get('/receipts/view', [ReceiptController::class, 'view'])->name('receipts.view');

    // Download receipt as PDF
    Route::get('/receipts/{payment}/download', [ReceiptController::class, 'download'])->name('receipts.download');

    // Existing show method
    Route::get('/receipts/{payment}', [ReceiptController::class, 'show'])->name('receipts.show');
});



/// Schools

Route::resource('schools', SchoolController::class);

/// Results
// Route::resource('results', ResultController::class);


Route::prefix('results')->group(function () {
    Route::get('/', [ResultController::class, 'index'])->name('results.index');

    Route::get('/select-class', [ResultController::class, 'selectClass'])->name('results.selectClass');
    Route::get('/students/{class_id}', [ResultController::class, 'showStudents'])->name('results.showStudents');
    Route::get('/create/{student_id}', [ResultController::class, 'createResult'])->name('results.createResult');
    Route::post('/store', [ResultController::class, 'storeResult'])->name('results.storeResult');

    // 👇 New routes for managing results
    Route::get('/{id}/edit', [ResultController::class, 'edit'])->name('results.edit');
    Route::put('/{id}', [ResultController::class, 'update'])->name('results.update');
    Route::delete('/{id}', [ResultController::class, 'destroy'])->name('results.destroy');
    // In web.php
Route::get('/results/view/{student_id}/{term_id}/{session_id}', [ResultController::class, 'viewResult'])->name('results.view');
Route::get('/results/{student_id}/{term_id}/{session_id}/generate', [ResultController::class, 'generate'])
    ->name('results.generate');
    Route::get('/results/class/{class_id}/students', [ResultController::class, 'showStudents'])
    ->name('results.showStudents');

    Route::get('/results/edit-all/{student_id}/{term_id}/{session_id}', [ResultController::class, 'editAll'])
    ->name('results.editAll');
    Route::get('/results/view/{student_id}/{term_id}/{session_id}', [ResultController::class, 'view'])->name('results.view');

    Route::get('/results/view/{student_id}/{term_id}/{session_id}', [ResultController::class, 'view'])
    ->name('results.view');
    Route::get('/results/class/{class_id}/ranking', [ResultController::class, 'classRanking'])
    ->name('results.classRanking');





});
// AcademicSessions

Route::resource('sessions', \App\Http\Controllers\AcademicSessionController::class);

// Terms

Route::resource('terms', TermController::class);

// Subjects

Route::resource('subjects', SubjectController::class);

// Whatsapp

Route::get('/test-whatsapp', function (WhatsAppService $whatsapp) {
    return $whatsapp->sendMessage('2348012345678', 'Hello from mock mode!');
});



// ===============================
// Teacher Controller
// ===============================





Route::middleware(['auth', 'teacher'])->group(function () {
    Route::get('/dashboard/teachers', [TeacherDashboardController::class, 'index'])->name('teachers.dashboard');
    Route::get('/dashboard/students', [TeacherDashboardController::class, 'students'])->name('teachers.students');
    Route::get('/dashboard/results', [TeacherDashboardController::class, 'results'])->name('teachers.results');
});






// ===============================
// Profile
// ===============================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
