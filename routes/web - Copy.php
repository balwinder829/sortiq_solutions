<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SessionController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentTrainingController;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\StudentCertificateController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Student\KeyTestController;
/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
// Route::middleware(['checksession'])->group(function () {
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// });

Route::get('/', function () {
    return redirect()->route('login'); // Home redirects to login
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (only after login)
|--------------------------------------------------------------------------
*/

Route::post('/admin/pending-fees/dismiss', function () {
    session(['dismiss_pending_fee' => true]); // store dismiss flag
    return response()->json(['status' => 'ok']);
})->middleware('auth')->name('admin.pending_fees.dismiss');

Route::get('/admin/pending-fees', [StudentController::class, 'pendingFees'])
     ->middleware('auth')
     ->name('admin.pendingfees.list');

     
Route::middleware(['auth'])->group(function () {
    Route::get('/trainers/import', [TrainerController::class, 'importForm'])->name('trainers.importForm');
Route::post('/trainers/import', [TrainerController::class, 'import'])->name('trainers.import');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Resource Routes
    Route::resource('sessions', SessionController::class);
    Route::resource('courses', CourseController::class);
    Route::resource('colleges', CollegeController::class);
    Route::resource('trainers', TrainerController::class);
    Route::resource('batches', BatchController::class);
   
    Route::resource('certificates', CertificateController::class);
    Route::resource('users', UserController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('references', ReferenceController::class);
    Route::resource('student_certificates', StudentCertificateController::class);

    Route::get('students/export', function() {
    return Excel::download(new StudentsExport, 'students.xlsx');
});

// Route::post('students/import', function (\Illuminate\Http\Request $request) {
//     Excel::import(new StudentsImport, $request->file('file'));
//     return redirect()->back()->with('success', 'Students imported successfully!');
// });
// Preview
    // 1) Show upload form
Route::get('/students/import', [StudentController::class, 'importForm'])->name('students.importForm');
/* Process Import (no preview) */
Route::post('/students/import', [StudentController::class, 'import'])
    ->name('students.import');
Route::get('/trainers/{id}/batches-ajax', [TrainerController::class, 'batchesAjax'])
     ->name('trainers.batches.ajax');

// 2) Show preview after uploading Excel
// Route::post('/students/import/preview', [StudentController::class, 'importPreview'])->name('students.importPreview');

// 3) Save data after confirmation
// Route::post('/students/import/save', [StudentController::class, 'importSave'])->name('students.importSave');

// Route::post('/students/import/preview', [StudentController::class, 'importPreview'])->name('students.importPreview');
// Route::get('/students/import', [StudentController::class, 'importForm'])->name('students.importForm');
// Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
 // Route::resource('students', StudentController::class);
Route::resource('students', StudentController::class)->except(['show']);
});


    Route::get('/manager/students', [App\Http\Controllers\StudentController::class, 'managerIndex'])
        ->name('manager.students.index');
    Route::get('/sales/students', [App\Http\Controllers\StudentController::class, 'salesIndex'])
        ->name('sales.students.index');
    Route::get('/colleges-data', [App\Http\Controllers\DashboardController::class, 'getCollegesData'])->name('colleges.data');
    Route::post('/students/issue-certificate/{id}', [StudentController::class, 'issueCertificate'])
    ->name('students.issueCertificate');
    
    // Bulk issue (new)
    Route::post('/students/issue-multiple', [App\Http\Controllers\StudentController::class, 'issueMultiple'])
    ->name('students.issueMultiple');

Route::middleware('auth')->group(function () {
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

});
Route::get('student-certificates/upload', [StudentCertificateController::class, 'uploadForm'])->name('student_certificates.upload_form');
Route::post('student-certificates/upload', [StudentCertificateController::class, 'uploadFile'])->name('student_certificates.upload');


Route::get('/certificate-verify', [StudentCertificateController::class, 'showForm'])->name('certificate.form');
Route::post('/certificate-verify/check', [StudentCertificateController::class, 'checkCertificate'])->name('certificate.check');

Route::middleware('auth')->group(function () {
    Route::get('check-training', [StudentTrainingController::class, 'checkForm'])->name('training.check.form');
    Route::post('check-training', [StudentTrainingController::class, 'checkTraining'])->name('training.check');
});
Route::get('/dashboard/session/{sessionName}/students', [DashboardController::class, 'getSessionStudents']);
Route::prefix('admin')->name('admin.')->group(function(){
    Route::resource('tests', TestController::class);

    Route::get('tests/{test}/questions/create',[QuestionController::class,'create'])->name('questions.create');
        Route::get('questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
    Route::post('tests/{test}/questions',[QuestionController::class,'store'])->name('questions.store');
    Route::get('tests/{test}/results', [TestController::class, 'results'])->name('tests.results');
});

Route::get('test/{slug}', [TestController::class, 'studentView'])->name('student.test.slug');

Route::prefix('student')->group(function () {

    // Show the form to enter a test key
    Route::get('enter-key', [KeyTestController::class, 'showForm'])
        ->name('student.enter.key');

    // Access test after entering key
    Route::post('access-test', [KeyTestController::class, 'accessTest'])
        ->name('student.test.access');

    // Show the test (using route model binding for Test)
    Route::get('test/{test}', [KeyTestController::class, 'showTest'])
        ->name('student.test.show');

    // Submit the test answers
    Route::post('test/{test}/submit', [KeyTestController::class, 'submitTest'])
        ->name('student.test.submit');
});