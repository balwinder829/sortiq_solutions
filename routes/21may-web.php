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
use App\Http\Controllers\ProductsRegistrationController;
use App\Http\Controllers\SingleProductRegistrationController;
use App\Http\Controllers\TrainerLetterController;
use App\Http\Controllers\SalesStaffLetterController;
use App\Http\Controllers\StudentCustomLetterController;

use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

use App\Http\Controllers\StudentCertificateController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Student\KeyTestController;
use App\Http\Controllers\Admin\OfflineTestController;
use App\Http\Controllers\Admin\OfficeOnlineQuestionController;


use App\Http\Controllers\FinanceTabsController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadCallController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CollegeEventController;
use App\Http\Controllers\StudentEventController;
use App\Http\Controllers\EmployeeEventController;
use App\Http\Controllers\EventNotificationController;
use App\Http\Controllers\BrochureController;
use App\Http\Controllers\PlacementController;
use App\Http\Controllers\CloseStudenController;
use App\Http\Controllers\TestCategoryController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\EnquiryOtpController;
use App\Http\Controllers\EnquiryFollowupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Sales\SalesEnquiryController;
use App\Http\Controllers\Sales\SalesDashboardController;
use App\Http\Controllers\OfficeExpenseController;
use App\Http\Controllers\PlacementCompanyController;
use App\Http\Controllers\PartTimeJobController;
use App\Http\Controllers\PgController;
use App\Http\Controllers\UpcomingEventController;
use App\Http\Controllers\PantryExpenseController;
use App\Http\Controllers\TeaPantryExpenseController;
use App\Http\Controllers\OfficePaperExpenseController;
use App\Http\Controllers\OfficeCleaningExpenseController;
use App\Http\Controllers\OfficeAccessoryExpenseController;
use App\Http\Controllers\EventExpenseController;
use App\Http\Controllers\TravelExpenseController;
use App\Http\Controllers\OfficeAssetController;
use App\Http\Controllers\BlockedNumberController;
use App\Http\Controllers\JoiningStudentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\ManagementsLetterController;
use App\Http\Controllers\RechargeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TutorialController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\DailyInterviewController;
use App\Http\Controllers\SalaryStructureController;
use App\Http\Controllers\SalarySlipController;
use App\Http\Controllers\StudentAdditionalLetterController;
use App\Http\Controllers\AcceptedLetterController;
use App\Http\Controllers\CompanyPptController;
use App\Http\Controllers\AdminPageController;
use App\Http\Controllers\FrontendPageController;
use App\Http\Controllers\InternshipRegistrationController;
use App\Http\Controllers\VisitingCardController;
use App\Http\Controllers\ServicesRegistrationController;
use App\Http\Controllers\HodController;
use App\Http\Controllers\InterviewQuestionController;
use App\Http\Controllers\TechnologyController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\InterviewRoundController;
use App\Http\Controllers\StudentEvaluationController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\ScannerShareController;
use App\Http\Controllers\MouController;
use App\Http\Controllers\FeeStatusController;
use App\Http\Controllers\CommonFilteredStudentController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\EmployeeLoginController;
use App\Http\Controllers\SystemActivityController;
use App\Http\Controllers\BlockedIpController;
use App\Http\Controllers\AllowedIpController;
use App\Http\Controllers\SalesStaffController;
use App\Http\Controllers\SalesStaffLoginController;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\StudentsAcceptedLetterController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\MentorsLoginController;
use App\Http\Controllers\StudentsLoginController;
use App\Http\Controllers\Students\StudentDashboardController;
use App\Http\Controllers\Helpdesk\HelpdeskTechnologyController;
use App\Http\Controllers\Helpdesk\HelpdeskArticleController;
use App\Http\Controllers\Helpdesk\HelpdeskAttachmentController;
use App\Http\Controllers\Helpdesk\HelpdeskFrontController;
use App\Http\Controllers\Admin\OfficeTestController;
use App\Http\Controllers\Admin\OfficeOnlineTestController;
use App\Http\Controllers\Admin\OfficeQuestionController;
use App\Http\Controllers\Admin\OfficeResultController;
use App\Http\Controllers\Admin\StudentPendingController;
use App\Http\Controllers\Student\OfficeOnlineExamController;
use App\Http\Controllers\Student\OfficeOnlineMCQTestController;
use App\Http\Controllers\PassoutController;
use App\Http\Controllers\StudentProjectController;
use App\Http\Controllers\StudentProjectAssignmentController;
use App\Http\Controllers\StudentProjectSubmissionController;
use App\Http\Controllers\StudentProjectReviewController;
use App\Http\Controllers\StudentCvController;
use App\Http\Controllers\StudentCvTemplateController;
use App\Http\Controllers\MentorsBatchController;
use App\Http\Controllers\ExternalAttendanceController;
use App\Http\Controllers\Student\AttendanceFormController;
use App\Http\Controllers\CollegeCallController;
use App\Http\Controllers\CollegeEmailController;
use App\Http\Controllers\ManualDataController;
use App\Http\Controllers\HardDataController;
use App\Http\Controllers\FormEntryController;
use App\Http\Controllers\JobDescriptionController;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\StudentPptController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\ProductsRegistrationFrontController;
use App\Http\Controllers\SingleProductRegistrationFrontController;
use App\Http\Controllers\WorkshopExpenseController;
use App\Http\Controllers\CollegeMistakeController;
use App\Http\Controllers\EmployeeLeaveController;
use App\Http\Controllers\Admin\EmployeeLeaveController as AdminEmployeeLeaveController;
use App\Http\Controllers\StudentLeaveController;
use App\Http\Controllers\Admin\StudentLeaveController as AdminStudentLeaveController;
use App\Http\Controllers\Admin\GmailController;
use App\Models\Student;
use App\Http\Controllers\Admin\AnalyticsController  as AdminAnalyticsController;




use App\Models\Test;
use App\Models\StudentTest;

use Spatie\Permission\PermissionRegistrar;



use App\Models\ExternalAttendanceTest;
use Illuminate\Http\Request;
use App\Http\Controllers\AdsAnalyticsController;
use App\Http\Controllers\Admin\TestAnalyticsController;


// EMployee Leave Form show
Route::get('/leave/apply', [EmployeeLeaveController::class, 'create'])
    ->name('employee.leave.apply');

// Form submit
Route::post('/leave/apply', [EmployeeLeaveController::class, 'store'])
    ->name('employee.leave.store')
    ->middleware('throttle:5,1'); // anti-spam

Route::get('/employee/find', function (Request $request) {
    return \App\Models\Employee::where('emp_code', $request->emp_code)->first();
})->name('employee.find');

// STudent Leave
// 🔍 Auto find student (AJAX)
Route::get('/student/find', function (Request $request) {
    return Student::where('sno', $request->sno)
        ->whereNull('deleted_at')
        ->first();
})->name('student.find');

// 📝 Form
Route::get('/student/leave/apply', [StudentLeaveController::class, 'create'])
    ->name('student.leave.apply');

// 💾 Submit
Route::post('/student/leave/apply', [StudentLeaveController::class, 'store'])
    ->name('student.leave.store')
    ->middleware('throttle:5,1');

Route::get('/register_student', [StudentRegistrationController::class, 'create'])->name('student.register.form');
Route::post('/join_student', [StudentRegistrationController::class, 'store'])->name('student.register');


Route::model('external_attendance', ExternalAttendanceTest::class);

Route::get('/fix-permissions', function () {
    app()[PermissionRegistrar::class]->forgetCachedPermissions();
    return "Permission cache cleared!";
});
/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/


Route::prefix('mentors')->group(function () {

    Route::get('/login',[MentorsLoginController::class,'showLoginForm'])->name('mentors.login');

    Route::post('/login',[MentorsLoginController::class,'login'])->name('mentors.login.submit');

});

// Route::prefix('students')->group(function () {

//     Route::get('/login',[StudentsLoginController::class,'showLoginForm'])->name('students.login');

//     Route::post('/login',[StudentsLoginController::class,'login'])->name('students.login.submit');

// });


Route::prefix('students')->name('students.')->group(function () {

    // Public routes
    Route::get('/login',[StudentsLoginController::class,'showLoginForm'])->name('login');
    Route::post('/login',[StudentsLoginController::class,'login'])->name('login.submit');

    // Protected routes
    Route::middleware('auth:student')->group(function () {

        Route::get('/dashboard',[StudentDashboardController::class,'index'])->name('dashboard');

        Route::get('/profile',[StudentDashboardController::class,'profile'])->name('profile');

        Route::get('/attendance',[StudentDashboardController::class,'attendance'])->name('attendance');

        Route::get('/assignments',[StudentDashboardController::class,'assignments'])->name('assignments');

        Route::get('/tests',[StudentDashboardController::class,'tests'])->name('tests');

        Route::get('/certificates',[StudentDashboardController::class,'certificates'])->name('certificates');

        Route::get('/assigned_projects',[StudentDashboardController::class,'projects'])->name('projects');

        Route::get('/fee_status',[StudentDashboardController::class,'fees'])->name('fee_status');

    });

});

Route::prefix('sales')->group(function () {

    Route::get('/login',[SalesStaffLoginController::class,'showLoginForm'])->name('sale_staff.login');

    Route::post('/login',[SalesStaffLoginController::class,'login'])->name('sale_staff.login.submit');

});

Route::prefix('employee')->group(function () {

    Route::get('/login',[EmployeeLoginController::class,'showLoginForm'])->name('employee.login');

    Route::post('/login',[EmployeeLoginController::class,'login'])->name('employee.login.submit');

});

Route::get('/job/{id}', [JobDescriptionController::class,'publicView'])->name('jd.public');
// Frontend
Route::get('/join', [JoiningStudentController::class, 'create'])->name('joining_student.front');
Route::post('/join', [JoiningStudentController::class, 'store'])->name('joining_student.store');
// Route::get('/share/scanners/{token}', [ScannerShareController::class, 'show'])
    // ->name('scanners.share');
Route::get('/scanners', [ScannerShareController::class, 'index'])
    ->name('frontend.scanners.index');

Route::get('/scanners/view/{token}', [ScannerShareController::class, 'show'])
    ->name('scanners.share');




Route::prefix('form')
    ->name('form.')
    ->group(function () {

        // ⚠️ Duplicate (PUT FIRST)
        Route::view('/already-submitted', 'student.attendance.form_already')
            ->name('already');

        // 🎉 Success
        Route::view('/thank-you', 'student.attendance.form_thank_you')
            ->name('thankyou');

        // 📝 Fill form
        Route::get('/fill/{slug}', [AttendanceFormController::class, 'showForm'])
            ->name('fill');

        // ✅ Submit
        Route::post('/submit', [AttendanceFormController::class, 'submit'])
            ->name('submit');

        // 🔗 Entry (KEEP LAST ALWAYS)
        Route::get('/{slug}', [AttendanceFormController::class, 'view'])
            ->name('view');
    });

Route::prefix('admin')->middleware(['auth'])->group(function () {

    Route::get('/employee-leave', [AdminEmployeeLeaveController::class, 'index'])
        ->name('admin.employee.leave.index');

    Route::get('/employee-leave/data', [AdminEmployeeLeaveController::class, 'data'])
        ->name('admin.employee.leave.data');

    Route::get('/employee-leave/show/{id}', [AdminEmployeeLeaveController::class, 'show'])
        ->name('admin.employee.leave.show');

    Route::get('/employee-leave/approve/{id}', [AdminEmployeeLeaveController::class, 'approve'])
        ->name('admin.employee.leave.approve');

    Route::get('/employee-leave/reject/{id}', [AdminEmployeeLeaveController::class, 'reject'])
        ->name('admin.employee.leave.reject');
});

Route::prefix('admin')->middleware(['auth'])->group(function () {

    Route::resource('trainer-letters', TrainerLetterController::class);

    Route::get(
        'trainer-letters/{trainer_letter}/download',
        [TrainerLetterController::class, 'download']
    )->name('trainer-letters.download');

    Route::post(
        'trainer-letters/{trainer_letter}/email',
        [TrainerLetterController::class, 'email']
    )->name('trainer-letters.email');


    Route::resource(
        'sales-staff-letters',
        SalesStaffLetterController::class
    );

    Route::get(
        'sales-staff-letters/{sales_staff_letter}/download',
        [SalesStaffLetterController::class, 'download']
    )->name('sales-staff-letters.download');

    Route::post(
        'sales-staff-letters/{sales_staff_letter}/email',
        [SalesStaffLetterController::class, 'email']
    )->name('sales-staff-letters.email');

    Route::get('/student-leave', [AdminStudentLeaveController::class, 'index'])
        ->name('admin.student.leave.index');

    Route::get('/student-leave/data', [AdminStudentLeaveController::class, 'data'])
        ->name('admin.student.leave.data');

    Route::get('/student-leave/show/{id}', [AdminStudentLeaveController::class, 'show'])
        ->name('admin.student.leave.show');

    Route::get('/student-leave/approve/{id}', [AdminStudentLeaveController::class, 'approve'])
        ->name('admin.student.leave.approve');

    Route::get('/student-leave/reject/{id}', [AdminStudentLeaveController::class, 'reject'])
        ->name('admin.student.leave.reject');
});
 

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {
Route::get('/ads-analytics', [AdsAnalyticsController::class, 'index'])
    ->name('ads.analytics');
Route::get('/test-analytics', [TestAnalyticsController::class, 'index'])
    ->name('test.analytics');
 Route::get('/form-entries', [FormEntryController::class, 'index'])
        ->name('form-entries.index');

    Route::get('/form-entries/data', [FormEntryController::class, 'data'])->name('form-entries.data');

    Route::get('/admin/form-entries/export', [FormEntryController::class, 'export'])
    ->name('form-entries.export');
    
     // Gmail (Sortiq & HR)
        Route::get('/gmail/{account?}', [GmailController::class, 'index'])
            ->name('gmail.index');
        Route::get('/gmail/{account}/{uid}/reply', [GmailController::class, 'replyForm'])
            ->name('gmail.reply.form');
        Route::post('/gmail/{account}/{uid}/reply', [GmailController::class, 'sendReply'])
            ->name('gmail.reply.send');
    
        // Route::prefix('college-emails')->name('college-emails.')->group(function () {

        //     Route::get('/', [CollegeEmailController::class, 'index'])->name('index');

        //     Route::get('/create', [CollegeEmailController::class, 'create'])->name('create');

        //     Route::post('/', [CollegeEmailController::class, 'store'])->name('store');

        //     Route::get('/campaigns', [CollegeEmailController::class, 'campaigns'])->name('campaigns');

        //     Route::post('/retry', [CollegeEmailController::class, 'retry'])->name('retry');

        // });


        Route::prefix('college-emails')->name('college-emails.')->group(function () {

            Route::get('/', [CollegeEmailController::class, 'index'])->name('index');

            Route::post('/store-selection', [CollegeEmailController::class, 'storeSelection'])->name('storeSelection');

            Route::get('/create', [CollegeEmailController::class, 'create'])->name('create');

            Route::post('/send', [CollegeEmailController::class, 'store'])->name('store');

            Route::get('/campaigns', [CollegeEmailController::class, 'campaigns'])->name('campaigns');

            Route::post('/retry', [CollegeEmailController::class, 'retry'])->name('retry');

            Route::post('/retry-college', [CollegeEmailController::class, 'retryByCollege'])->name('retryByCollege');

            Route::get('/logs/{college}', [CollegeEmailController::class, 'logs'])->name('logs');

            Route::get('/view/{recipient}', [CollegeEmailController::class, 'view'])->name('view');

        });


        Route::prefix('college-calls')->name('college-calls.')->group(function () {

            Route::get('/', [CollegeCallController::class, 'index'])->name('index');

            Route::post('/store-selection', [CollegeCallController::class, 'storeSelection'])->name('storeSelection');

            Route::get('/create', [CollegeCallController::class, 'create'])->name('create');

            Route::post('/store', [CollegeCallController::class, 'store'])->name('store');

            Route::post('/retry-college', [CollegeCallController::class, 'retryByCollege'])->name('retryByCollege');

            Route::get('/logs/{college}', [CollegeCallController::class, 'logs'])->name('logs');

            Route::get('/view/{id}', [CollegeCallController::class, 'view'])->name('view');

        });

    Route::get(
        'external-attendance/{test}/export-results',
        [ExternalAttendanceController::class, 'exportResults']
    )->name('external-attendance.export.results');

        Route::resource(
            'external-attendance',
            ExternalAttendanceController::class
        );

        // 🔗 Extra routes
        Route::get(
            'external-attendance/{external_attendance}/links',
            [ExternalAttendanceController::class, 'links']
        )->name('external-attendance.links');

        Route::post(
            'external-attendance/{external_attendance}/regenerate-link',
            [ExternalAttendanceController::class, 'regenerateLink']
        )->name('external-attendance.regenerate');

        Route::get('external-attendance/{external_attendance}/results', [ExternalAttendanceController::class,'results'])->name('external-attendance.results');
        Route::get('external-attendance/{external_attendance}/export-all', [ExternalAttendanceController::class, 'exportTestAll'])->name('external-attendance.export.all');
        Route::get('external-attendance/{external_attendance}/export-selected', [ExternalAttendanceController::class, 'exportSelectedStudents'])->name('external-attendance.export.selected');
        Route::get('external-attendance/{external_attendance}/export-finalized', [ExternalAttendanceController::class, 'exportFinalized'])->name('external-attendance.export.finalized');
        Route::post('external-attendance/{external_attendance}/move-to-enquiries', [ExternalAttendanceController::class, 'moveFinalizedToEnquiries'])->name('external-attendance.move.enquiries');

        Route::get('external-attendance/{external_attendance}/selected-students', [ExternalAttendanceController::class, 'selectedStudents'])->name('external-attendance.selected.students');

        Route::get('manual-data/export', [ManualDataController::class, 'exportExcel'])->name('manual_data.export');
        Route::get('manual-data/import', [ManualDataController::class, 'importForm'])->name('manual_data.import.form');
        Route::post('manual-data/import', [ManualDataController::class, 'import'])->name('manual_data.import');
        // Route::resource('manual-data', ManualDataController::class)->names('manual_data');
        Route::resource('manual-data', ManualDataController::class)->names('manual_data')->parameters(['manual-data' => 'manual_data']);
        Route::post('/manual-data/move-enquiries',
            [ManualDataController::class, 'moveManualToEnquiries']
        )->name('manual_data.move.enquiries');

        Route::get('hard-data/export', [HardDataController::class, 'exportExcel'])->name('hard_data.export');
        Route::get('hard-data/import', [HardDataController::class, 'importForm'])->name('hard_data.import.form');
        Route::post('hard-data/import', [HardDataController::class, 'import'])->name('hard_data.import');
        Route::resource('hard-data', HardDataController::class)->names('hard_data')->parameters(['manuhardal-data' => 'hard_data']);
        Route::post('/hard-data/move-enquiries',
            [HardDataController::class, 'moveManualToEnquiries']
        )->name('hard_data.move.enquiries');


        // Route::get('/external-attendance/{id}/export-all', ...)->name('admin.external-attendance.export.all');
        // Route::get('/external-attendance/{id}/export-finalized', ...)->name('admin.external-attendance.export.finalized');
    });





Route::prefix('admin/student')
->middleware(['auth'])
->group(function(){

    Route::resource('student-projects', StudentProjectController::class);

    Route::resource('student-project-assignments', StudentProjectAssignmentController::class)
        ->only(['index','create','store','show','destroy']);

    Route::resource('student-project-submissions', StudentProjectSubmissionController::class)
        ->only(['index','store']);

    Route::resource('student-project-reviews', StudentProjectReviewController::class)
        ->only(['index','store']);

});


Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('office-online-tests/{test}/questions', [OfficeOnlineQuestionController::class, 'index'])
        ->name('office-online-questions.index');

    Route::get('office-online-tests/{test}/questions/create', [OfficeOnlineQuestionController::class, 'create'])
        ->name('office-online-questions.create');

    Route::post('office-online-questions/store', [OfficeOnlineQuestionController::class, 'store'])
        ->name('office-online-questions.store');

    Route::get('office-online-questions/{id}/edit', [OfficeOnlineQuestionController::class, 'edit'])
        ->name('office-online-questions.edit');

    Route::post('office-online-questions/{id}/update', [OfficeOnlineQuestionController::class, 'update'])
        ->name('office-online-questions.update');

    Route::delete('office-online-questions/{id}', [OfficeOnlineQuestionController::class, 'destroy'])
        ->name('office-online-questions.destroy');
});

Route::prefix('admin')
->middleware('auth')
->name('admin.')
->group(function(){

    Route::get('student-cv',[StudentCvController::class,'index'])
    ->name('student.cv.index');

});

Route::prefix('student')
->name('student.')
->group(function(){

Route::get('cv/create',[StudentCvController::class,'create'])
->name('cv.create');

Route::post('cv/store',[StudentCvController::class,'store'])
->name('cv.store');

Route::get('cv/{id}/preview',[StudentCvController::class,'preview'])
->name('cv.preview');

Route::get('cv/{id}/download/{template}',
[StudentCvController::class,'download'])
->name('cv.download');

});
Route::prefix('admin/student')
    ->name('admin.student.')
    ->middleware('auth')
    ->group(function(){

    Route::get('cv/create',[StudentCvController::class,'create'])
    ->name('cv.create');

    Route::post('cv/store',[StudentCvController::class,'store'])
    ->name('cv.store');

    Route::get('cv/{id}/preview',[StudentCvController::class,'preview'])
    ->name('cv.preview');

    Route::get('cv/{id}/download/{template}',
    [StudentCvController::class,'download'])
    ->name('cv.download');

        Route::resource('cv-templates', StudentCvTemplateController::class);

    });
Route::prefix('office-online-exam')->name('student.office-online.')->group(function () {
    /* Result */
    Route::get(
        '/result',
        [OfficeOnlineMCQTestController::class, 'showResult']
    )->name('result');
    Route::get('/{slug}/exam_closed', function ($slug) {

        $test = \App\Models\OfficeOnlineTest::where('slug', $slug)
            ->firstOrFail();

        return view(
            'student.office-online.exam_closed',
            compact('test')
        );

    })->name('exam.closed');
   


    /* Enter details page */
    Route::get(
        '/{slug}/enter',
        [OfficeOnlineMCQTestController::class, 'enter']
    )->name('enter');


    /* Submit student details */
    Route::post(
        '/access',
        [OfficeOnlineMCQTestController::class, 'accessTest']
    )->name('access');


    /* Show exam */
    Route::get(
        '/{slug}/exam',
        [OfficeOnlineMCQTestController::class, 'showTest']
    )->name('exam');

    Route::get(
        '/{slug}/already-submitted',
        [OfficeOnlineMCQTestController::class, 'showTest']
    )->name('already.submitted');

    Route::get('/{slug}/already-submitted', function ($slug) {
        $test = \App\Models\OfficeOnlineTest::where('slug', $slug)
            ->firstOrFail();
        return view(
            'student.office-online.already_submitted',
            compact('test')
        );

    })->name('already.submitted');

    Route::get('{slug}/unavailable', function ($slug) {

        $test = \App\Models\OfficeOnlineTest::where('slug', $slug)
            ->firstOrFail();

        return view(
            'student.office-online.test_unavailable',
            compact('test')
        );

    })->name('unavailable');
   

    /* Autosave */
    Route::post(
        '/autosave',
        [OfficeOnlineMCQTestController::class, 'autoSave']
    )->name('autosave');


    /* Submit exam */
    Route::post(
        '/submit',
        [OfficeOnlineMCQTestController::class, 'submitTest']
    )->name('submit');

     /* Open test link */
    Route::get(
        '/{slug}',
        [OfficeOnlineMCQTestController::class, 'enter']
    )->name('view');
    

});

Route::prefix('office-exam')->name('student.office.')->group(function () {

    /* Result page */
    Route::get(
        '/result',
        [OfficeOnlineExamController::class, 'showResult']
    )->name('result.show');

    /* Student opens exam link */
    Route::get(
        '/{slug}',
        [OfficeOnlineExamController::class, 'studentView']
    )->name('view');


    /* Student info form */
    Route::get(
        '/{slug}/enter',
        [OfficeOnlineExamController::class, 'showForm']
    )->name('enter');


    /* Submit student info */
    Route::post(
        '/access',
        [OfficeOnlineExamController::class, 'accessTest']
    )->name('access');


    /* Show exam */
    Route::get(
        '/{slug}/exam',
        [OfficeOnlineExamController::class, 'showExam']
    )->name('exam.show');


    /* Autosave answers */
    Route::post(
        '/{slug}/autosave',
        [OfficeOnlineExamController::class, 'autoSave']
    )->name('autosave');


    /* Submit exam */
    Route::post(
        '/{slug}/submit',
        [OfficeOnlineExamController::class, 'submitExam']
    )->name('submit');


});

Route::get('office-exam-closed/{slug}', function ($slug) {

    $test = \App\Models\OfficeTest::where('slug', $slug)
        ->firstOrFail();

    return view(
        'student.office_exam.exam_closed',
        compact('test')
    );

})->name('student.office.exam.closed');

Route::get('office-exam-already-submitted/{slug}', function ($slug) {

    $test = \App\Models\OfficeTest::where('slug', $slug)
        ->firstOrFail();

    return view(
        'student.office_exam.already_submitted',
        compact('test')
    );

})->name('student.office.already.submitted');

Route::get('office-exam/unavailable/{slug}', function ($slug) {

    $test = \App\Models\OfficeTest::where('slug', $slug)
        ->firstOrFail();

    return view(
        'student.office-online.test_unavailable',
        compact('test')
    );

})->name('student.office.exam.unavailable');

Route::get(
    '/admin/office-test/{slug}/download',
    [OfficeTestController::class,'downloadAnswers']
)->name('admin.office-test.download');

Route::prefix('admin')
    ->middleware('auth')
    ->group(function () {
        Route::get('/pending-registration-students', [StudentPendingController::class, 'index'])->name('admin.pending_request.index');
        Route::post('/pending-send', [StudentPendingController::class, 'sendToSession'])
        ->name('admin.pending.send');

        Route::get('/enquiries/import', [EnquiryController::class, 'importForm'])
        ->name('enquiries.importForm');
        Route::get('/passouts/import', [PassoutController::class, 'importForm'])
        ->name('passouts.importForm');
        Route::resource('passouts', PassoutController::class);
        Route::post('passouts/import', [PassoutController::class, 'import'])
            ->name('passouts.import');  
        Route::get('/passouts-export', [PassoutController::class, 'export'])
            ->name('passouts.export');


        Route::prefix('jd')->group(function(){

            Route::get('/', [JobDescriptionController::class,'index'])->name('jd.index');
            Route::get('/data', [JobDescriptionController::class,'data'])->name('jd.data');

            Route::get('/create', [JobDescriptionController::class,'create'])->name('jd.create');
            Route::post('/store', [JobDescriptionController::class,'store'])->name('jd.store');

            Route::get('/edit/{id}', [JobDescriptionController::class,'edit'])->name('jd.edit');
            Route::post('/update/{id}', [JobDescriptionController::class,'update'])->name('jd.update');

            Route::get('/show/{id}', [JobDescriptionController::class,'show'])->name('jd.show');
            Route::delete('/delete/{id}', [JobDescriptionController::class,'destroy'])->name('jd.destroy');

        });

});

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {


    // Route::prefix('jd')->group(function(){

    //     Route::get('/', [JobDescriptionController::class,'index'])->name('admin.jd.index');
    //     Route::get('/data', [JobDescriptionController::class,'data'])->name('admin.jd.data');

    //     Route::get('/create', [JobDescriptionController::class,'create'])->name('admin.jd.create');
    //     Route::post('/store', [JobDescriptionController::class,'store'])->name('admin.jd.store');

    //     Route::get('/edit/{id}', [JobDescriptionController::class,'edit'])->name('admin.jd.edit');
    //     Route::post('/update/{id}', [JobDescriptionController::class,'update'])->name('admin.jd.update');

    //     Route::get('/show/{id}', [JobDescriptionController::class,'show'])->name('admin.jd.show');
    //     Route::delete('/delete/{id}', [JobDescriptionController::class,'destroy'])->name('admin.jd.destroy');

    // });

    // Route::prefix('admin')->group(function(){

        

    // });
        //Passout Module
        // Route::resource('passouts', PassoutController::class);


    Route::get(
    'office-online-tests/{office_test}/download-pdf',
        [OfficeOnlineTestController::class, 'downloadPdf']
    )->name('office-online-tests.download.pdf');

    Route::get(
    'office-online-tests/{office_test}/results',
        [OfficeOnlineTestController::class, 'results']
    )->name('office-online-tests.results');
    

    Route::resource('office-online-tests', OfficeOnlineTestController::class);




        Route::get(
    'office-tests/{office_test}/download-pdf',
    [OfficeTestController::class, 'downloadPdf']
)->name('office-tests.download.pdf');
        Route::resource('office-tests', OfficeTestController::class);

        Route::resource(
            'office-tests.office-questions',
            OfficeQuestionController::class
        );

        Route::get(
            'office-tests/{office_test}/results',
            [OfficeResultController::class,'index']
        )->name('office-tests.results');

        Route::post(
            'office-tests/{office_test}/results',
            [OfficeResultController::class,'store']
        )->name('office-tests.results.store');

});
    Route::get('/attachments/{id}', [HelpdeskAttachmentController::class, 'preview'])
    ->name('attachments.preview');
Route::prefix('admin/helpdesk')
    ->name('admin.helpdesk.')
    ->middleware('auth')
    ->group(function(){

    Route::resource('categories', HelpdeskTechnologyController::class);
    // Route::resource('technologies', HelpdeskTechnologyController::class);
    Route::resource('articles', HelpdeskArticleController::class);
    Route::get('/attachments/{id}', [HelpdeskAttachmentController::class, 'preview'])
    ->name('attachments.preview');
    Route::resource('attachments', HelpdeskAttachmentController::class)
        ->only(['index','create','store','destroy']);

});


Route::prefix('helpdesk')->group(function () {

    Route::get('/', [HelpdeskFrontController::class, 'index'])
        ->name('helpdesk.index');

    Route::get('/{techSlug}', [HelpdeskFrontController::class, 'technology'])
        ->name('helpdesk.technology');

    Route::get('/{techSlug}/{articleSlug}', [HelpdeskFrontController::class, 'article'])
        ->name('helpdesk.article');

});
Route::get('services-registrations/create', [ServicesRegistrationController::class, 'create'])
    ->name('services-registrations.create');

Route::post('services-registrations', [ServicesRegistrationController::class, 'store'])
    ->name('services-registrations.store');

Route::post('products-registrations', [ProductsRegistrationFrontController::class, 'store'])
    ->name('products-registrations.store');

Route::post('single-product-registrations', [SingleProductRegistrationFrontController::class, 'store'])
    ->name('single-product-registrations.store');

Route::resource(
    'internship-registrations',
    InternshipRegistrationController::class
)->only(['create', 'store']);

// Public preview via share token
    // Route::get(
    //     'student-ppt/preview/{token}',
    //     [StudentPptController::class, 'preview']
    // )->name('student_ppt.preview');

    Route::get(
    'ppt/preview/{token}',
    [StudentPptController::class, 'preview']
)->name('student_ppt.public.preview');

    Route::get(
    'ppt_company/preview/{token}',
    [CompanyPptController::class, 'preview']
)->name('ppt_company.public.preview');

    

Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->group(function () {

        Route::resource(
            'student-custom-letters',
            StudentCustomLetterController::class
    );

    Route::get(
        'student-custom-letters/{studentCustomLetter}/pdf',
        [StudentCustomLetterController::class, 'download']
    )->name('student-custom-letters.download');

    Route::post(
        'student-custom-letters/{StudentCustomLetter}/email',
        [StudentCustomLetterController::class, 'sendEmail']
    )->name('student-custom-letters.email');

         Route::resource('student-ppt', StudentPptController::class)
        ->names('student_ppt');

        Route::get('/all-analytics', [AdminAnalyticsController::class, 'index'])
    ->name('admin.analytics');
    // Public preview via share token
    Route::get(
        'student-ppt/preview/{token}',
        [StudentPptController::class, 'preview']
    )->name('student_ppt.preview');

    // Admin download
    Route::get(
        'student-ppt/{studentPpt}/admin-download',
        [StudentPptController::class, 'adminDownload']
    )->name('student_ppt.admin.download');

        Route::get('/workshops/analytics', [WorkshopController::class, 'analytics'])
        ->name('workshops.analytics');
        Route::get('workshops/export/excel', [WorkshopController::class, 'exportExcel'])->name('workshops.export.excel');
        Route::get('workshops/data', [WorkshopController::class, 'data'])->name('workshops.data');
        Route::resource('workshops', WorkshopController::class);

         Route::get('states/data', [StateController::class, 'data'])->name('states.data');
        Route::resource('states', StateController::class);

        Route::get('districts/data', [DistrictController::class, 'data'])->name('districts.data');
        Route::resource('districts', DistrictController::class);

        Route::resource('student-accepted-letters', StudentsAcceptedLetterController::class)
            ->parameters(['student-accepted-letters' => 'letter']);

        Route::get(
            'student-accepted-letters/{letter}/download',
            [StudentsAcceptedLetterController::class, 'download']
        )->name('student-accepted-letters.download');


        Route::get('filtered-students', [CommonFilteredStudentController::class, 'index'])
            ->name('common_filtered_student');
        Route::get('/students/export',
            [CommonFilteredStudentController::class,'export']
        )->name('students.export');
        Route::resource('pages', AdminPageController::class);
        Route::post('pages/{page}/toggle', [AdminPageController::class, 'toggle'])
            ->name('pages.toggle');

        Route::get(
            'internship-registrations/export',
            [InternshipRegistrationController::class, 'export']
        )->name('internship-registrations.export');
                
        Route::resource(
            'internship-registrations',
            InternshipRegistrationController::class
        )->except(['edit', 'update' , 'store','create']);

        // Status update (approve / reject)
        Route::patch(
            'internship-registrations/{internship_registration}/status',
            [InternshipRegistrationController::class, 'updateStatus']
        )->name('internship-registrations.status');

        Route::resource('visiting-cards', VisitingCardController::class);
        
        Route::resource('testimonials', TestimonialController::class);

        Route::get('services-registrations/export', [ServicesRegistrationController::class, 'export'])
            ->name('services-registrations.export');

        Route::resource('services-registrations', ServicesRegistrationController::class)
            ->only(['index', 'show', 'destroy']);

        Route::get('products-registrations/export', [ProductsRegistrationController::class, 'export'])
            ->name('products-registrations.export');

        Route::resource('products-registrations', ProductsRegistrationController::class)
            ->only(['index', 'show', 'destroy']);

         Route::get('single-product-registrations/export', [SingleProductRegistrationController::class, 'export'])
            ->name('single-product-registrations.export');

        Route::resource('single-product-registrations', SingleProductRegistrationController::class)
            ->only(['index', 'show', 'destroy']);

        Route::resource('hods', HodController::class);

        Route::get('interview-questions/listing', 
            [InterviewQuestionController::class, 'practice']
        )->name('interview-questions.practice');

        Route::resource('interview-questions', InterviewQuestionController::class);
        Route::get('technologies/data', [TechnologyController::class, 'data'])->name('technologies.data');
        Route::resource('technologies', TechnologyController::class);
        Route::resource('scanners', ScannerController::class);
        Route::resource('student-evaluations', StudentEvaluationController::class);
        // Route::get(
        //     'student-evaluations/{student_evaluation}/download',
        //     [StudentEvaluationController::class, 'downloadPdf']
        // )->name('student-evaluations.download');

        Route::get(
            'student-evaluations/{student_evaluation}/download-full',
            [StudentEvaluationController::class, 'downloadFull']
        )->name('student-evaluations.download.full');

        Route::get(
            'student-evaluations/{student_evaluation}/download-empty',
            [StudentEvaluationController::class, 'downloadEmpty']
        )->name('student-evaluations.download.empty');

        Route::post(
            'student-evaluations/{student_evaluation}/email',
            [StudentEvaluationController::class, 'sendEmail']
        )->name('student-evaluations.email');

        Route::resource('mous', MouController::class);
        Route::post('mous/{mou}/send-email', [MouController::class, 'sendEmail'])->name('mous.sendEmail');
        Route::post('mous/{mou}/upload-signed', [MouController::class, 'uploadSigned'])->name('mous.uploadSigned');

        Route::get('mous/{mou}/download', [MouController::class, 'download'])->name('mous.download');

        Route::post('/students/copy', [StudentController::class, 'copyStudents'])->name('students.copy');
        Route::post('/students/make_interns', [StudentController::class, 'makeInterns'])->name('students.make_interns');

        Route::post('/students/move-to-placement', [StudentController::class, 'moveToPlacement'])->name('students.moveToPlacement');
        
        Route::post('/students/toggle-certificate-sent', [CertificateController::class, 'toggleCertificateSent']
        )->name('students.toggleCertificateSent');

        Route::post('/students/bulk-certificate-status', [CertificateController::class, 'bulkCertificateStatus']
        )->name('students.bulkCertificateStatus');

        Route::post('/students/toggle-confirmation-sent', [StudentController::class, 'toggleConfirmationSent']
        )->name('students.toggleConfirmationSent');

        Route::post('/students/bulk-confirmation-status', [StudentController::class, 'bulkConfirmationStatus']
        )->name('students.bulkConfirmationStatus');

        Route::resource('workshop-expenses', WorkshopExpenseController::class);

        Route::get(
            'workshop-expenses-data',
            [WorkshopExpenseController::class, 'data']
        )->name('workshop-expenses.data');

        Route::resource('college-mistakes', CollegeMistakeController::class);

        Route::get(
            'college-mistakes-data',
            [CollegeMistakeController::class, 'data']
        )->name('college-mistakes.data');

        Route::get('/finance', [FinanceTabsController::class, 'index'])->name('finance.index');
        // Route::post(
        //     'interviews/{interview}/rounds',
        //     [InterviewController::class, 'storeRound']
        // )->name('interviews.rounds.store');

        // Route::get('interviews/{interview}/rounds/create',
        //     [InterviewController::class, 'createRound'])
        //     ->name('interviews.rounds.create');

        // Route::post('interviews/{interview}/rounds',
        //     [InterviewController::class, 'storeRound'])
        //     ->name('interviews.rounds.store');


        Route::resource('interviews', InterviewController::class);

        // ROUNDS
        Route::get(
            'interviews/rounds/{round}/edit',
            [InterviewRoundController::class, 'edit']
        )->name('interviews.rounds.edit');

        Route::put(
            'interviews/rounds/{round}',
            [InterviewRoundController::class, 'update']
        )->name('interviews.rounds.update');

        Route::get(
            'interviews/{interview}/rounds/create',
            [InterviewController::class, 'createRound']
        )->name('interviews.rounds.create');

        Route::post(
            'interviews/{interview}/rounds',
            [InterviewController::class, 'storeRound']
        )->name('interviews.rounds.store');

        Route::get('/fee-status', [FeeStatusController::class, 'index'])->name('fee.status');   
        Route::get('/fee-status/export', [FeeStatusController::class, 'export'])->name('fee.status.export');

        Route::get('/colleges/import', [CollegeController::class, 'showImport'])
            ->name('colleges.import.view');

        Route::post('/colleges/import', [CollegeController::class, 'importColleges'])
            ->name('colleges.import');

        // Route::resource('interviews', InterviewController::class);

        Route::post('sales_staff/inactive-all', [SalesStaffController::class, 'inactiveAll'])
    ->name('sales_staff.inactiveAll');
        Route::resource('sales_staff', SalesStaffController::class);

    });
});

Route::middleware(['auth'])->group(function () {
    
    Route::get('joined-students/export', [JoiningStudentController::class, 'export'])
    ->name('joined_students.export');

    Route::get('/admin/joining-students',
        [JoiningStudentController::class, 'index']
    )->name('joined_students.index');

    Route::get('/admin/joining-students/links',
        [JoiningStudentController::class, 'adminUrl']
    )->name('joined_students.adminUrl');

     Route::get('/admin/joining-students/{id}/edit',
        [JoiningStudentController::class, 'edit']
    )->name('joined_students.edit');

    // UPDATE
    Route::put('/joining-students/{id}',
        [JoiningStudentController::class, 'update']
    )->name('joined_students.update');

    // DELETE (Soft Delete)
    Route::delete('/joining-students/{id}',
        [JoiningStudentController::class, 'destroy']
    )->name('joined_students.destroy');

    Route::post('/admin/joining-students/send-to-session', 
        [JoiningStudentController::class, 'sendToSession']
    )->name('joined_students.sendToSession');

    Route::resource('letters', LetterController::class);

    Route::get(
        'letters/{letter}/download',
        [LetterController::class,'download']
    )->name('letters.download');

    Route::post(
        'letters/{letter}/email',
        [LetterController::class,'sendEmail']
    )->name('letters.email');

    Route::get(
        'managements_letters/download_empty',
        [ManagementsLetterController::class,'letterheaddownload']
    )->name('managements_letters.download_empty');


    Route::resource('managements_letters', ManagementsLetterController::class);

    
    Route::get(
        'managements_letters/{managements_letter}/download',
        [ManagementsLetterController::class,'download']
    )->name('managements_letters.download');

    Route::post(
        'managements_letters/{managements_letter}/email',
        [ManagementsLetterController::class,'sendEmail']
    )->name('managements_letters.email');

   
    Route::resource('projects', ProjectController::class);
    Route::resource('tutorials', TutorialController::class);
    Route::resource('cvs', CvController::class);
    Route::resource('daily-interviews', DailyInterviewController::class);

    Route::get('/students/import', [StudentController::class, 'importForm'])
        ->name('students.importForm');

    Route::post('/students/import', [StudentController::class, 'import'])
        ->name('students.import');

    Route::get('/students/importfee', [StudentController::class, 'importFeeForm'])
        ->name('students.importFeeForm');

    Route::post('/students/importfee', [StudentController::class, 'importFee'])
        ->name('students.importFee');

    Route::get(
            '/download-active-session-students',
            [StudentController::class, 'downloadActiveSessionStudents']
        )->name('students.download.active.session');

    Route::post('/students/bulk-delete', [StudentController::class, 'bulkDelete'])
     ->name('students.bulk.delete');

    //  Route::get('salary-structure/{employee}/create', [SalaryStructureController::class, 'create']);
    // Route::post('salary-structure/{employee}', [SalaryStructureController::class, 'store']);

    // Route::post('salary-slips/generate', [SalarySlipController::class, 'generate']);
    // Route::get('salary-slips/{salarySlip}/download', [SalarySlipController::class, 'download']);
    //  Route::resource('salary-structures', SalaryStructureController::class)
    //     ->only(['create', 'store']);

    // /* ============ SALARY SLIPS ============ */
    // Route::resource('salary-slips', SalarySlipController::class)
    //     ->only(['index']);

    // // extra actions
    // Route::get(
    //     'salary-slips-generate',
    //     [SalarySlipController::class, 'generateForm']
    // )->name('salary-slips.generate.form');

    // Route::post(
    //     'salary-slips-generate',
    //     [SalarySlipController::class, 'generate']
    // )->name('salary-slips.generate');

    // Route::get(
    //     'salary-slips/{salarySlip}/download',
    //     [SalarySlipController::class, 'download']
    // )->name('salary-slips.download');

    // Route::post('salary-slips/{salarySlip}/email', [SalarySlipController::class, 'sendEmail'])
    //     ->name('salary-slips.email');

          /* ===== Salary Structure ===== */
    Route::get(
        '/salary-structure/{employee}/create',
        [SalaryStructureController::class, 'create']
    )->name('salary-structure.create');

    Route::post(
        '/salary-structure/{employee}',
        [SalaryStructureController::class, 'store']
    )->name('salary-structure.store');

    /* ===== Salary Slips ===== */
    Route::get(
        '/salary-slips',
        [SalarySlipController::class, 'index']
    )->name('salary-slips.index');

    Route::get(
        '/salary-slips/generate',
        [SalarySlipController::class, 'generateForm']
    )->name('salary-slips.generate.form');

    Route::post(
        '/salary-slips/generate',
        [SalarySlipController::class, 'generate']
    )->name('salary-slips.generate');

    Route::get(
        '/salary-slips/{salarySlip}/download',
        [SalarySlipController::class, 'download']
    )->name('salary-slips.download');


        Route::post(
    '/salary-slips/download-bulk',
        [SalarySlipController::class, 'downloadBulk']
    )->name('salary-slips.download.bulk');

    // Bulk email
    Route::post(
        '/salary-slips/email-bulk',
        [SalarySlipController::class, 'emailBulk']
    )->name('salary-slips.email.bulk');

    // Single email
    Route::post(
        '/salary-slips/{salarySlip}/email',
        [SalarySlipController::class, 'sendEmail']
    )->name('salary-slips.email.single');
    
    Route::resource(
            'student-additional-letters',
            StudentAdditionalLetterController::class
    );

    Route::get(
        'student-additional-letters/{StudentAdditionalLetter}/pdf',
        [StudentAdditionalLetterController::class, 'download']
    )->name('student-additional-letters.pdf');

    Route::post(
        'student-additional-letters/{StudentAdditionalLetter}/email',
        [StudentAdditionalLetterController::class, 'sendEmail']
    )->name('student-additional-letters.email');

        // Resource routes (CRUD)
    Route::resource('company-ppt', CompanyPptController::class)
        ->names('company_ppt');

    // Public preview via share token
    Route::get(
        'company-ppt/preview/{token}',
        [CompanyPptController::class, 'preview']
    )->name('company_ppt.preview');

    // Admin download
    Route::get(
        'company-ppt/{companyPpt}/admin-download',
        [CompanyPptController::class, 'adminDownload']
    )->name('company_ppt.admin.download');



    


});

Route::prefix('sales')
    ->middleware(['auth:sales_staff'])
    ->group(function () {

        Route::get('/enquiries', [SalesEnquiryController::class, 'index'])
    ->name('sales.enquiries.index');

    Route::post('/enquiries/{enquiry}/register',
            [SalesEnquiryController::class, 'register']
        )->name('sales.enquiries.register');


Route::get('/enquiries/{enquiry}', [SalesEnquiryController::class, 'show'])
    ->name('sales.enquiries.show');

Route::post('/enquiries/{enquiry}/followup', 
    [SalesEnquiryController::class, 'storeFollowup'])
    ->name('sales.enquiries.followup.store');

    Route::get('/dashboard', [SalesDashboardController::class, 'index'])
    ->name('sales.dashboard');

});


Route::get('/enquiry-otp', [EnquiryOtpController::class, 'showOtpPage'])
    ->name('enquiry.otp.page');
Route::post('/enquiry-otp-send', [EnquiryOtpController::class, 'sendOtp'])
    ->name('enquiry.otp.send');
Route::post('/enquiry-otp-verify', [EnquiryOtpController::class, 'verifyOtp'])
    ->name('enquiry.otp.verify');

// Protected Enquiry CRUD
    Route::prefix('admin')
    ->middleware(['auth'])   // admin users only
    ->group(function () {
        Route::resource('enquiries', EnquiryController::class);
        Route::post('enquiries/import', [EnquiryController::class, 'import'])
        ->name('enquiries.import');

    Route::post('enquiries/assign', [EnquiryController::class, 'assign'])
        ->name('enquiries.assign');


    //Office expenses
    Route::resource('electricity-expenses', OfficeExpenseController::class)->names('office-expenses');
    Route::resource('pantry-expenses', PantryExpenseController::class);
    Route::resource('tea-pantry-expenses', TeaPantryExpenseController::class);
    Route::resource('office-paper-expenses', OfficePaperExpenseController::class);
    Route::resource('event-expenses', EventExpenseController::class);
    Route::resource('travel-expenses', TravelExpenseController::class);
    Route::resource('office-assets', OfficeAssetController::class);
    Route::resource('office-cleaning-expenses', OfficeCleaningExpenseController::class);
    Route::resource('office-accessories-expenses', OfficeAccessoryExpenseController::class);
     



    });
    Route::prefix('admin')
    ->middleware(['auth'])   // admin users only
    // ->middleware(['auth','role:1'])   // admin users only
    ->group(function () {

// Route::middleware(['auth', 'enquiry.otp','role:1'])->group(function () {
    Route::resource('recharges', RechargeController::class);
    // quick status update
    Route::post('recharges/{recharge}/set-status', [RechargeController::class, 'setStatus'])->name('recharges.setStatus');

    
    Route::get('/salespersons', [EnquiryController::class, 'salespersons'])->name('salespersons.list');
    Route::get('/salespersons/{id}', [EnquiryController::class, 'salespersonShow'])
    ->name('salespersons.show');
    Route::get('/assignment-report', [EnquiryController::class, 'assignmentReport'])
    ->name('assignments.report');

Route::post('/enquiries/{enquiry}/register',
    [EnquiryController::class, 'register']
)->name('enquiries.register');

Route::get('/followups', [EnquiryController::class, 'pendingFollowups'])
    ->name('admin.followups');
Route::get('/calls', [EnquiryController::class, 'callDashboard'])
    ->name('admin.calls');

    
    Route::post('enquiries/{enquiry}/convert', [EnquiryController::class, 'convert'])
        ->name('enquiries.convert');

    Route::post('enquiries/{enquiry}/followup', 
    [EnquiryFollowupController::class, 'store'])
    ->name('admin.enquiries.followup.store');

    Route::get('enquiries-pipeline', [EnquiryController::class, 'pipeline'])
    ->name('admin.enquiries.pipeline');

    Route::post('enquiries/update-status', [EnquiryController::class, 'updateStatus'])
        ->name('admin.enquiries.updateStatus');

    Route::get('enquiries-dashboard', [EnquiryController::class, 'dashboard'])
    ->name('admin.enquiries.dashboard');

    Route::get('sales-performance', [EnquiryController::class, 'performance'])
    ->name('admin.enquiries.performance');
});

Route::get('/', fn() => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout.post');
Route::post('/change-session', [DashboardController::class, 'changeSession'])
    ->name('admin.changeSession');

/*
|--------------------------------------------------------------------------
| ADMIN UTILITY (role 1 only)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::post('/admin/pending-fees/dismiss', function () {
        session(['dismiss_pending_fee' => true]);
        return response()->json(['status' => 'ok']);
    })->name('admin.pending_fees.dismiss');

    Route::get('/admin/pending-fees', [StudentController::class, 'pendingFees'])
        ->name('admin.pendingfees.list');

    Route::get('/admin/closinglists', [StudentController::class, 'closingList'])
        ->name('admin.closinglists');
    
    Route::post('/students/download-multiple', [StudentController::class, 'downloadconfirmMultiple'])
    ->name('students.downloadconfirmMultiple');

    Route::post('/students/download-receipts', [StudentController::class, 'downloadMultipleReceipts'])
    ->name('students.downloadMultipleReceipts');

    Route::post('/students/moveMultiple', [StudentController::class, 'moveMultipleToCertificate'])
    ->name('students.moveMultiple');

        Route::post('/students/moveMultipletoConfirmation', [StudentController::class, 'moveMultipleToConfirmation'])
    ->name('students.moveMultipleToConfirmation');

    // new routes for download certificate

    Route::post('/students/download-certificate-multiple', [StudentController::class, 'downloadCertificateMultiple'])
    ->name('students.downloadCertificateMultiple');

    Route::post('/students/update_issue-date', [StudentController::class, 'updateCertificateIssueDateMultiple'])
    ->name('students.updateCertificateIssueDateMultiple');

    Route::get('/admin/pending-students', [StudentController::class, 'pendingStudents'])
        ->name('admin.pendingstudents.list');

    Route::get('students/{student}/id-card', [StudentController::class, 'downloadIdStudentCard'])->name('students.idcard');

    // routes/web.php
    Route::get('/verify-students', [StudentController::class, 'verifyStudents'])->name('verify-students.index');
    Route::get('/verify-students-index', [StudentController::class, 'verifyStudentsLink'])->name('verify-students-index.index');


    // Route::resource('office-expenses', OfficeExpenseController::class);
    Route::get('/placement-companies/import', [PlacementCompanyController::class, 'importForm'])
            ->name('placement-companies.import.view');

    Route::post('/placement-companies/import', [PlacementCompanyController::class, 'import'])
            ->name('placement-companies.import');
    Route::resource('placement-companies', PlacementCompanyController::class);

    Route::get('/part-time-jobs/import', [PartTimeJobController::class, 'importForm'])
            ->name('part-time-jobs.import.view');

    Route::post('/part-time-jobs/import', [PartTimeJobController::class, 'import'])
            ->name('part-time-jobs.import');

    Route::resource('part-time-jobs', PartTimeJobController::class);

    Route::get('/pgs/import', [PgController::class, 'importForm'])
            ->name('pgs.import.view');

    Route::post('/pgs/import', [PgController::class, 'import'])
            ->name('pgs.import');
    Route::resource('pgs', PgController::class);

    Route::resource('upcoming-events', UpcomingEventController::class)
    ->parameters(['upcoming-events' => 'event']);

    Route::post(
        'upcoming-events/{event}/dismiss',
        [UpcomingEventController::class, 'dismiss']
    )->name('upcoming-events.dismiss');

    Route::post(
        'upcoming-events/{event}/enable',
        [UpcomingEventController::class, 'enable']
    )->name('upcoming-events.enable');

    Route::get(
        'upcoming-events-calendar',
        [UpcomingEventController::class, 'calendar']
    )->name('upcoming-events.calendar');


    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

    Route::resource('accepted-letters', AcceptedLetterController::class);
    Route::get(
        'accepted-letters/{accepted_letter}/download',
        [AcceptedLetterController::class, 'download']
    )->name('accepted-letters.download');
});


/*
|--------------------------------------------------------------------------
| Dashboard (ALL: admin, trainer, sales)
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth:web', 'role:1,3,4'])->get('/dashboard', [DashboardController::class, 'index'])
    // ->name('dashboard');

// Route::middleware(['auth:trainer', 'role:1,2,3,4'])->get('/dashboard', [DashboardController::class, 'index'])
//     ->name('dashboard');

// Route::middleware(['auth', 'role:1,2,3'])->group(function () {
Route::middleware(['auth:web,trainer,sales_staff'])->group(function () {
    // List all notifications
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    // View notification + redirect to destination
    Route::get('/notifications/view/{id}', [NotificationController::class, 'view'])
        ->name('notifications.view');

    // Optional: Show notification inside a page (not redirect)
    Route::get('/notifications/show/{id}', [NotificationController::class, 'show'])
        ->name('notifications.show');

    // Mark one notification as read (for AJAX dismiss)
    Route::post('/notifications/mark-read/{id}', [NotificationController::class, 'markRead'])
        ->name('notifications.markRead');

    // Mark all notifications as read
    Route::post('/notifications/mark-all', [NotificationController::class, 'markAll'])
        ->name('notifications.markAll');


        Route::get('/notifications/type/{type}', [NotificationController::class, 'byType'])
    ->name('notifications.byType');

Route::get('/notifications/clear/{id}', [NotificationController::class, 'clearOne'])
    ->name('notifications.clearOne');

Route::get('/notifications/clear-all', [NotificationController::class, 'clearAll'])
    ->name('notifications.clearAll');
Route::delete(
    '/notifications/clear/type/{templateKey}',
    [NotificationController::class, 'clearByTemplate']
)->name('notifications.clearByTemplate');

});

    Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/payroll/export/{month}/{year}',
            [PayrollController::class, 'export']
        )->name('payroll.export');

    //     Route::get('/payroll/process/{year}/{month}', [PayrollController::class, 'process'])
    // ->name('admin.payroll.process');

    //     Route::post('/payroll/load', [PayrollController::class, 'load'])
    // ->name('payroll.load');

    Route::post('/payroll/load', [PayrollController::class, 'load'])
    ->name('payroll.load');

Route::get('/payroll/process/{year}/{month}', [PayrollController::class, 'process'])
    ->name('payroll.process');

    
        Route::resource('payroll', PayrollController::class)
    ->only(['index', 'store']);


        Route::resource(
            'blocked-numbers',
            BlockedNumberController::class
        )->except(['edit', 'update']);

        Route::resource('blocked-ips', BlockedIpController::class)
            ->only(['index', 'create', 'store', 'destroy'])
            ->names('blocked-ips');

        Route::resource('allowed-ips', AllowedIpController::class)
            ->only(['index', 'create', 'store', 'destroy'])
            ->names('allowed-ips');
    });


/*
|--------------------------------------------------------------------------
| ADMIN MODULES (role = 1)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {



    // Route::get('/payroll', [PayrollController::class, 'index'])
    // ->name('payroll.index');

    // Route::post('/payroll/load', [PayrollController::class, 'load'])
    //     ->name('payroll.load');

    // Route::post('/payroll/store', [PayrollController::class, 'store'])
    //     ->name('payroll.store');


    Route::resource('sessions', SessionController::class);
    // routes/web.php
    Route::get('/courses/{course}/students', [CourseController::class, 'students'])
        ->name('courses.students');

    Route::get(
        '/courses/{course}/students/export-excel',
        [CourseController::class, 'exportStudentsExcel']
    )->name('courses.students.export.excel');

    Route::get('courses/data', [CourseController::class, 'data'])->name('courses.data');
    Route::resource('courses', CourseController::class);

    Route::get('/colleges/{college}/students', [CollegeController::class, 'students']);
    Route::get('/colleges/{college}/students/export-excel', [CollegeController::class, 'exportStudentsExcel']);
    // Route::post('/colleges/{id}/toggle-status', [CollegeController::class, 'toggleStatus']);
    Route::post('/colleges/{id}/toggle-status', [CollegeController::class, 'toggleStatus'])
    ->name('colleges.toggle.status');

    Route::get('colleges/export/excel', [CollegeController::class, 'exportExcel'])
    ->name('colleges.export.excel');
    Route::get('colleges/data', [CollegeController::class, 'data'])->name('colleges.data');
    Route::resource('colleges', CollegeController::class);
    // Endpoint to fetch districts by state (AJAX)
    Route::get('districts/by-state/{state}', [DistrictController::class, 'getByState']);

    // Route::resource('certificates', CertificateController::class);
    Route::resource('certificates', CertificateController::class)
     ->parameters(['certificates' => 'student']);

    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::get('users/data', [UserController::class, 'data'])->name('users.data');
    Route::resource('users', UserController::class);
    Route::get('departments/data', [DepartmentController::class, 'data'])->name('departments.data');
    Route::resource('departments', DepartmentController::class);
    Route::get('references/data', [ReferenceController::class, 'data'])->name('references.data');
    Route::resource('references', ReferenceController::class);
    Route::resource('student_certificates', StudentCertificateController::class);
    Route::resource('batches', BatchController::class);


    // Route::get('/manager/permissions',
    //     [\App\Http\Controllers\ManagerPermissionController::class, 'edit']
    // )->name('admin.manager.permissions.edit');

    // Route::post('/manager/permissions',
    //     [\App\Http\Controllers\ManagerPermissionController::class, 'update']
    // )->name('admin.manager.permissions.update');


// Show page
Route::get('/admin/manager-permissions', [\App\Http\Controllers\ManagerPermissionController::class, 'index'])->name('admin.manager.permissions.edit');

// Save permissions
Route::post('/admin/manager-permissions', [\App\Http\Controllers\ManagerPermissionController::class, 'store']);


    // Tests module
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('tests', TestController::class);
        // Regenerate test link
        Route::post('tests/{test}/regenerate-link',
            [TestController::class, 'regenerateLink']
        )->name('tests.regenerate-link');

        Route::delete('/tests/links/{id}', [TestController::class, 'destroyLink'])
        ->name('tests.links.destroy');

        Route::post(
            'tests/links/{link}/regenerate',
            [TestController::class, 'regenerateCollegeLink']
        )->name('tests.links.regenerate');

        Route::get('/tests/{test}/links',
            [TestController::class,'links']
        )->name('tests.links');

        Route::post('/admin/tests/{test}/download-certificates',
            [TestController::class, 'downloadCertificates'])
            ->name('tests.certificate.download');
         // OFFLINE (new)
        // Route::get('offline-tests', [OfflineTestController::class,'index'])
        //     ->name('offline.tests.index');
 Route::resource('offline-tests', OfflineTestController::class);

         // OFFLINE TEST LIST (same UI as online)
    // Route::get(
    //     'offline-tests',
    //     [OfflineTestController::class, 'index']
    // )->name('offline.tests.index');

    // OFFLINE TEST RESULTS

    Route::get(
        'offline-tests/{test}/results',
        [OfflineTestController::class, 'results']
    )->name('offline.tests.results');

    // UPLOAD EXCEL
    Route::post(
        'offline-tests/{test}/upload',
        [OfflineTestController::class, 'uploadExcel']
    )->name('offline.tests.upload');

    // ADD STUDENT MANUAL
    Route::post(
        'offline-tests/{test}/store-student',
        [OfflineTestController::class, 'storeStudent']
    )->name('offline.tests.store.student');

    // FINALIZE
    Route::post(
        'offline-tests/finalize',
        [OfflineTestController::class, 'bulkFinalize']
    )->name('offline.tests.finalize');

    // MOVE TO ENQUIRIES
    Route::post(
        'offline-tests/{test}/move-enquiries',
        [OfflineTestController::class, 'moveToEnquiries']
    )->name('offline.tests.moveToenquiries');

    Route::get('offline-tests/{test}/selected-students', [OfflineTestController::class, 'selectedStudents'])->name('offline-tests.selected.students');

    Route::get('/offline-tests/{test}/students/create',
    [OfflineTestController::class, 'createStudent']
)->name('offline.tests.create.student');

Route::get(
        '/offline-tests/{test}/download-mcq-paper',
        [OfflineTestController::class, 'downloadMcqPaper']
    )->name('offline.tests.download.mcq.paper');

Route::get(
        '/online-tests/{test}/download-mcq-paper',
        [TestController::class, 'downloadMcqPaper']
    )->name('online.tests.download.mcq.paper');


 Route::get('offline-tests/{test}/results', [OfflineTestController::class,'results'])->name('offline-tests.results');

        Route::post('tests/bulk-finalize', [TestController::class, 'bulkFinalize'])->name('tests.bulk.finalize');

        Route::post('/attendance/{test}/move-enquiries',
            [ExternalAttendanceController::class, 'moveAttendanceToEnquiries']
        )->name('attendance.move.enquiries');
         


        Route::get('tests/{test}/questions/create', [QuestionController::class,'create'])->name('questions.create');
        Route::get('questions/{question}/edit', [QuestionController::class,'edit'])->name('questions.edit');
        Route::put('questions/{question}', [QuestionController::class,'update'])->name('questions.update');
        Route::delete('questions/{question}', [QuestionController::class,'destroy'])->name('questions.destroy');
        Route::post('tests/{test}/questions', [QuestionController::class,'store'])->name('questions.store');
        Route::get('tests/{test}/results', [TestController::class,'results'])->name('tests.results');
        

        Route::get('tests/{test}/export-all', [TestController::class, 'exportAllStudents'])->name('tests.export.all');
        Route::get('tests/{test}/export-selected', [TestController::class, 'exportSelectedStudents'])->name('tests.export.selected');
        Route::get('tests/{test}/export-finalized', [TestController::class, 'exportFinalized'])->name('tests.export.finalized');
        Route::post('tests/{test}/move-to-enquiries', [TestController::class, 'moveFinalizedToEnquiries'])->name('tests.move.enquiries');

        Route::get('tests/{test}/selected-students', [TestController::class, 'selectedStudents'])->name('tests.selected.students');

    });



    Route::prefix('admin/tests')->middleware(['auth'])->group(function () {

        Route::get('export/overall/finalized', [TestController::class, 'exportOverallFinalized'])
            ->name('admin.tests.export.overall.finalized');

        Route::get('export/overall/attempted', [TestController::class, 'exportOverallAttempted'])
            ->name('admin.tests.export.overall.attempted');

        Route::get('export/online/finalized', [TestController::class, 'exportOnlineFinalized'])
            ->name('admin.tests.export.online.finalized');

        Route::get('export/offline/finalized', [TestController::class, 'exportOfflineFinalized'])
            ->name('admin.tests.export.offline.finalized');

        Route::get('export/category/{category}', [TestController::class, 'exportCategoryFinalized'])
            ->name('admin.tests.export.category.finalized');

        Route::get('admin/tests/export/online/attempted', [TestController::class, 'exportOnlineAttempted'])
    ->name('admin.tests.export.online.attempted');

         Route::get('{test}/export/all', [TestController::class, 'exportTestAll'])
        ->name('admin.tests.export.all');

    Route::get('{test}/export/finalized', [TestController::class, 'exportTestFinalized'])
        ->name('admin.tests.export.finalized');

        Route::get('admin/tests/export/offline/attempted',
            [TestController::class, 'exportOfflineAttempted']
        )->name('admin.tests.export.offline.attempted');

    });

    Route::post('/change-password', [ChangePasswordController::class, 'update'])
        ->name('change.password');

    Route::get('employees/{employee}/id-card',
    [EmployeeController::class, 'downloadIdCard'])->name('employees.idcard');

    Route::post('employees/{employee}/id-card-email',
    [EmployeeController::class, 'emailIdCard'])->name('employees.idcard.email');
    Route::get('employees/data', [EmployeeController::class, 'data'])->name('employees.data');
    Route::resource('employees', EmployeeController::class);


});


/*
|--------------------------------------------------------------------------
| TRAINER MODULES (role = 2)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // route to download skipped rows: type = txt|csv|xlsx
    Route::get('/trainers/import/skipped/download/{type}', [TrainerController::class, 'downloadSkipped'])
        ->name('trainers.skipped.download');
    Route::get('/trainers/data', [TrainerController::class, 'data'])->name('trainers.data');
    Route::get('/trainers/responsibilities_letter', [TrainerController::class, 'downloadResponsiblitiesLetter'])->name('trainers.responsibilities_letter');
    Route::resource('trainers', TrainerController::class)->except(['show']);
    

    Route::get('/trainers/import', [TrainerController::class, 'importForm'])->name('trainers.importForm');
    Route::post('/trainers/import', [TrainerController::class, 'import'])->name('trainers.import');

    Route::get('/trainers/{id}/batches-ajax', [TrainerController::class, 'batchesAjax'])
        ->name('trainers.batches.ajax');


    Route::get('/registrations', [EnquiryController::class, 'registeredIndex'])
     ->name('registrations.index');

    Route::post(
        '/registrations/{enquiry}/convert-to-student',
        [EnquiryController::class, 'convertToStudent']
    )->name('convert.to.student');

    Route::get('/registrations/export/all', [EnquiryController::class, 'exportAll'])
    ->name('registrations.export.all');

    Route::get('/registrations/export/pending', [EnquiryController::class, 'exportPending'])
    ->name('registrations.export.pending');

    Route::get('/enquiries-export', [EnquiryController::class, 'export'])
    ->name('enquiries.export');

    Route::post('/registrations/bulk-convert',
    [EnquiryController::class, 'bulkConvert'])
    ->name('registrations.bulk.convert');
    Route::get('students/export/excel', [StudentController::class, 'exportExcel'])
    ->name('students.export.excel');

    Route::resource('students', StudentController::class);

});

// Route::middleware(['auth', 'role:2'])->group(function () {
Route::middleware(['auth:trainer'])->group(function () {

    // Trainer can ONLY view the batch from notification
    Route::get('/mybatches/{batch}', [MentorsBatchController::class, 'show'])
        ->name('batch.show');


    Route::get('/mybatches', [MentorsBatchController::class, 'MyBatches'])
        ->name('batches.mybatches');

    Route::post('/trainer/send-batch-email',[MentorsBatchController::class, 'sendBatchEmail'])
    ->name('trainer.sendBatchEmail');

    Route::get('/attendance/{batch}', 
        [MentorsBatchController::class,'markAttendance']
    )->name('trainer.attendance.mark');

    Route::post('/attendance/save', 
        [MentorsBatchController::class,'saveAttendance']
    )->name('trainer.attendance.save');

    Route::get('/attendance/batch/{batch}',
    [MentorsBatchController::class,'batchAttendance'])
    ->name('trainer.attendance.batch');

    Route::get('/attendance/student/{student}',
    [MentorsBatchController::class,'studentAttendance'])
    ->name('trainer.attendance.student');
});

/*
|--------------------------------------------------------------------------
| SALES MODULES (role = 3)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:1,2,3'])->group(function () {

    Route::get('/sales/students', [StudentController::class,'salesIndex'])
        ->name('sales.students.index');



    // Route::resource('students', StudentController::class);
});


/*
|--------------------------------------------------------------------------
| MANAGER ROUTE (if role = ???)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->get('/manager/students', [StudentController::class, 'managerIndex'])
    ->name('manager.students.index');


/*
|--------------------------------------------------------------------------
| CERTIFICATE ISSUE (admin only)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::post('/students/issue-certificate/{id}', [StudentController::class,'issueCertificate'])
        ->name('students.issueCertificate');

    Route::post('/students/issue-multiple', [StudentController::class,'issueMultiple'])
        ->name('students.issueMultiple');

    Route::post('/students/issue-confirmation/{id}', [StudentController::class,'confirmStudent'])
        ->name('students.confirmStudent');

    Route::post('/students/confirm-multiple', [StudentController::class,'confirmMultiple'])
        ->name('students.confirmMultiple');

});


/*
|--------------------------------------------------------------------------
| Profile (all roles)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:web,trainer,employee'])->group(function () {

    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

});


/*
|--------------------------------------------------------------------------
| Student certificate upload
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:web,trainer,employee,sales_staff'])->group(function () {
    // Employee section
    Route::get('/attendance', [AttendanceController::class, 'employeePanel'])
        ->name('attendance.employee');

    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])
        ->name('attendance.checkIn');

    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])
        ->name('attendance.checkOut');

    Route::get('/attendance/my-detail', 
    [AttendanceController::class, 'monthlyDetail'])
    ->name('attendance.myDetail');

});

Route::middleware(['auth'])->group(function () {
    Route::get('student-certificates/upload', [StudentCertificateController::class, 'uploadForm'])
        ->name('student_certificates.upload_form');
    Route::post('student-certificates/upload', [StudentCertificateController::class, 'uploadFile'])
        ->name('student_certificates.upload');
});


/*
|--------------------------------------------------------------------------
| Public certificate verification
|--------------------------------------------------------------------------
*/

Route::get('/certificate-verify', [StudentCertificateController::class,'showForm'])
    ->name('certificate.form');

Route::post('/certificate-verify/check', [StudentCertificateController::class,'checkCertificate'])
    ->name('certificate.check');


/*
|--------------------------------------------------------------------------
| Training Check (ALL roles)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('check-training', [StudentTrainingController::class, 'checkForm'])->name('training.check.form');
    Route::post('check-training', [StudentTrainingController::class, 'checkTraining'])->name('training.check');
});


/*
|--------------------------------------------------------------------------
| Dashboard AJAX
|--------------------------------------------------------------------------
*/

// Route::get('/colleges-data', [DashboardController::class,'getCollegesData'])->name('colleges.data');
Route::get('/dashboard/session/{sessionName}/students', [DashboardController::class, 'getSessionStudents']);



/*
|--------------------------------------------------------------------------
| Student Test Routes (Frontend)
|--------------------------------------------------------------------------
*/

Route::get('test/{slug}', [KeyTestController::class,'studentView'])->name('student.test.slug');

// Route::prefix('student')->group(function () {

//     Route::get('enter-key', [KeyTestController::class,'showForm'])->name('student.enter.key');
//     Route::post('access-test', [KeyTestController::class,'accessTest'])->name('student.test.access');

//     Route::get('test/{test}', [KeyTestController::class,'showTest'])->name('student.test.show');
//     Route::post('test/{test}/submit', [KeyTestController::class,'submitTest'])->name('student.test.submit');

//     Route::post('test/{test}/autosave', [KeyTestController::class,'autoSave'])
//         ->name('student.test.autosave');

//     Route::get('student/exam-closed/{test}', function (Test $test) {
//         return view('student.exam_closed', compact('test'));
//     })->name('student.exam.closed');


//     Route::get('student/already-submitted/{test}', function (\App\Models\Test $test) {
//         return view('student.already_submitted', compact('test'));
//     })->name('student.already.submitted');

//     Route::get('student/result/{studentTest}', function (\App\Models\StudentTest $studentTest) {
//     return view('student.result', compact('studentTest'));
// })->name('student.result.show');




// });


Route::prefix('student')->group(function () {

    // Entry page
    Route::get('enter-key', [KeyTestController::class,'showForm'])
        ->name('student.enter.key');

    // Access test (POST only)
    Route::post('access-test', [KeyTestController::class,'accessTest'])
        ->name('student.test.access');

    /*
    |--------------------------------------------------------------------------
    | EXAM ROUTES (SLUG BASED)
    |--------------------------------------------------------------------------
    */

    // Show exam page (slug)
    Route::get('test/{slug}', [KeyTestController::class,'showTest'])
        ->name('student.test.show');

    // Submit exam (slug)
    Route::post('test/{slug}/submit', [KeyTestController::class,'submitTest'])
        ->name('student.test.submit');

    // Autosave answers (slug)
    Route::post('test/{slug}/autosave', [KeyTestController::class,'autoSave'])
        ->name('student.test.autosave');

    /*
    |--------------------------------------------------------------------------
    | STATUS PAGES (SLUG BASED)
    |--------------------------------------------------------------------------
    */

    // Exam closed page
    Route::get('exam-closed/{slug}', function ($slug) {
        $test = Test::where('slug', $slug)->firstOrFail();
        return view('student.exam_closed', compact('test'));
    })->name('student.exam.closed');

    // Already submitted page
    Route::get('already-submitted/{slug}', function ($slug) {
        $test = Test::where('slug', $slug)->firstOrFail();
        return view('student.already_submitted', compact('test'));
    })->name('student.already.submitted');

    // Result page (still by studentTest ID – INTERNAL ONLY)
    // Route::get('result/{studentTest}', function (StudentTest $studentTest) {
    //     return view('student.result', compact('studentTest'));
    // })->name('student.result.show');

    Route::get('student/result', [KeyTestController::class, 'showResult'])
    ->name('student.result.show');



    Route::get('test-unavailable', function () {
        return view('student.test_unavailable');
    })->name('student.test.unavailable');

});


Route::get('/sessions/{id}/batches', [SessionController::class, 'getBatches'])
     ->name('sessions.batches');

Route::get('/batches/{id}/students', [BatchController::class, 'getStudents'])
     ->name('batches.students');

Route::middleware(['auth', 'role:1,3'])->group(function () {

    // Route::prefix('sales')->group(function () {
    //      Route::get('/dashboard', [LeadController::class, 'salesDashboard'])->name('sales.dashboard');
    // });
});
Route::get('/b/{brochure}', [BrochureController::class, 'preview'])
    ->name('brochures.preview');

// Route::get('/c/{company_profile}', [CompanyProfileController::class, 'preview'])
//     ->name('company_profile.preview');
    Route::get('/company_profile/preview/{token}', 
    [CompanyProfileController::class, 'preview']
)->name('company_profile.preview');
Route::middleware(['auth'])->group(function () {

    Route::prefix('admin')->group(function () {
        Route::get('/activity', [ActivityController::class, 'index'])->name('admin.activity');
        Route::get('/activity/lead/{lead_id}', [ActivityController::class, 'leadTimeline'])->name('activity.lead');
        Route::get('/activity/user/{user_id}', [ActivityController::class, 'userTimeline'])->name('activity.user');
        Route::get('/system-activity/data', [SystemActivityController::class, 'data'])->name('admin.system-activity.data');
        Route::get('/system-activity', [SystemActivityController::class, 'index'])->name('admin.system-activity');
        // Route::resource('events', EventController::class);
        Route::prefix('college')->name('college.')->group(function () {

            Route::resource('events', CollegeEventController::class);

            Route::delete('event-image/{image}', [CollegeEventController::class,'deleteImage'])
                ->name('event-image.delete');

            Route::delete('event-video/{video}', [CollegeEventController::class,'deleteVideo'])
                ->name('event-video.delete');

            Route::post('event/set-cover/{eventImage}', [CollegeEventController::class,'setCover'])
                ->name('event.set.cover');
        });

        Route::prefix('student')->name('student.')->group(function () {

            Route::resource('events', StudentEventController::class);

            Route::delete('event-image/{image}', [StudentEventController::class,'deleteImage'])
                ->name('event-image.delete');

            Route::delete('event-video/{video}', [StudentEventController::class,'deleteVideo'])
                ->name('event-video.delete');

            Route::post('event/set-cover/{eventImage}', [StudentEventController::class,'setCover'])
                ->name('event.set.cover');
        });

        Route::prefix('employee')->name('employee.')->group(function () {

            Route::resource('events', EmployeeEventController::class);

            Route::delete('event-image/{image}', [EmployeeEventController::class,'deleteImage'])
                ->name('event-image.delete');

            Route::delete('event-video/{video}', [EmployeeEventController::class,'deleteVideo'])
                ->name('event-video.delete');

            Route::post('event/set-cover/{eventImage}', [EmployeeEventController::class,'setCover'])
                ->name('event.set.cover');
        }); 

        Route::post('/admin/event-notification/dismiss', fn() =>
            App\Models\EventNotification::today()->update(['dismissed'=>true])
        )->name('admin.event.notification.dismiss');

        Route::get('/admin/events/notifications', [EventNotificationController::class, 'list'])
        ->name('admin.events.notifications');


        Route::resource('brochures', BrochureController::class);
        
        Route::resource('company_profile', CompanyProfileController::class);
        Route::get('/company_profile/view/{company_profile}', [CompanyProfileController::class, 'view'])
         ->name('company_profile.view');
         Route::get('/company_profile/download/{company_profile}', 
    [CompanyProfileController::class, 'download']
)->name('company_profile.download');
//          Route::get('/company_profile/preview/{token}', 
//     [CompanyProfileController::class, 'preview']
// )->name('company_profile.preview');

         Route::get('/company_profile/{company_profile}/admin-view',
            [CompanyProfileController::class, 'adminView'])
            ->name('company_profile.admin.view');

        Route::get('/company_profile/{company_profile}/admin-download',
            [CompanyProfileController::class, 'adminDownload'])
            ->name('company_profile.admin.download');



        // regenerate token
        // Route::post('brochures/{brochure}/regenerate-token', [BrochureController::class,'regenerateToken'])
        //     ->name('brochures.regenerate-token');

        // public share (no auth)
        // Route::get('brochures/s/{token}', [BrochureController::class,'publicShow'])
        //     ->name('brochures.public.show');

        // public download (if you want separate endpoint)
        // Route::get('brochures/{brochure}/download', [BrochureController::class,'download'])
        //     ->name('brochures.download');

        Route::get('/brochure/view/{brochure}', [BrochureController::class, 'view'])
         ->name('brochures.view');

         Route::get('/brochures/{brochure}/admin-view',
            [BrochureController::class, 'adminView'])
            ->name('brochures.admin.view');

        Route::get('/brochures/{brochure}/admin-download',
            [BrochureController::class, 'adminDownload'])
            ->name('brochures.admin.download');




         //***** PLACEMENT*****//

        Route::resource('placements', PlacementController::class);
        

        // AJAX routes for media delete + cover selection
        Route::delete('placements/media/image/{id}', [PlacementController::class, 'deleteImage'])
             ->name('placements.media.image.delete');

        Route::delete('placements/media/video/{id}', [PlacementController::class, 'deleteVideo'])
             ->name('placements.media.video.delete');

        Route::post('placements/media/set-cover/{id}', [PlacementController::class, 'setCover'])
             ->name('placements.media.setCover');


        Route::resource('close_student', CloseStudenController::class)->parameters(['close_student' => 'student']);
        Route::resource('test-categories', TestCategoryController::class);
        Route::get('/attendance/trainer/{trainer}/detail', [AttendanceController::class, 'trainerAttendanceDetail'])->name('attendance.trainerDetail');
        Route::get('/attendance/{type}/{id}/detail',
            [AttendanceController::class,'attendanceDetail']
        )->name('attendance.detail');

         Route::get('/employees_attendece', [AttendanceController::class, 'employeeList'])
            ->name('attendance.employees');


        // Route::get('/employees/{id}', [AttendanceController::class, 'employeeDetail'])
            // ->name('attendance.employeeDetail');

        Route::get('/attendance/{id}/detail', [AttendanceController::class, 'monthlyDetail'])->name('attendance.employeeDetail');

        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');


        Route::get('/enquiry-otp', [EnquiryOtpController::class, 'showOtpPage'])->name('enquiry.otp.page');
        Route::post('/enquiry-otp-send', [EnquiryOtpController::class, 'sendOtp'])->name('enquiry.otp.send');
        Route::post('/enquiry-otp-verify', [EnquiryOtpController::class, 'verifyOtp'])->name('enquiry.otp.verify');

        
        // Route::resource('enquiries', EnquiryController::class);

    Route::post('enquiries/import', [EnquiryController::class, 'import'])
        ->name('enquiries.import');

    Route::post('enquiries/assign', [EnquiryController::class, 'assign'])
        ->name('enquiries.assign');

    Route::post('enquiries/{enquiry}/convert', [EnquiryController::class, 'convert'])
        ->name('enquiries.convert');


    });
});


Route::get('/brochure/view/{brochure}', [BrochureController::class, 'view'])
     ->name('brochure.view');
/* SECURE VIEW & DOWNLOAD */


Route::get('/b/{brochure}/download', [BrochureController::class, 'download'])
    ->name('brochures.secure.download');


Route::get('/company_profile/view/{company_profile}', [CompanyProfileController::class, 'view'])
     ->name('company_profile.view');

// Route::get('/c/{company_profile}', [CompanyProfileController::class, 'preview'])
//     ->name('company_profile.preview');

Route::get('/c/{company_profile}/download', [CompanyProfileController::class, 'download'])
    ->name('company_profile.secure.download');

// Route::get('/b/{token}', [BrochureController::class, 'publicShow'])
//     ->name('brochures.public.show');



Route::middleware(['auth', 'role:1,3'])->group(function () {

    Route::prefix('leads')->group(function () {

        // ---- IMPORT ROUTES MUST COME FIRST ----
        Route::get('/import', [LeadController::class, 'showImportForm'])->name('leads.import.form');
        Route::post('/import', [LeadController::class, 'import'])->name('leads.import');
        Route::get('/import/history', [LeadController::class, 'importHistory'])->name('leads.import.history');
        Route::get('/import/history/{batchId}', [LeadController::class, 'importBatchView'])->name('leads.import.batch');
        Route::post('/bulk-assign', [LeadController::class, 'bulkAssign'])->name('leads.bulk.assign');
        Route::post('/bulk-delete', [LeadController::class, 'bulkDelete'])->name('leads.bulk.delete');
       




        // ---- REGULAR ROUTES ----
        Route::get('/', [LeadController::class, 'index'])->name('leads.index');
        Route::get('/create', [LeadController::class, 'create'])->name('leads.create');
        Route::post('/', [LeadController::class, 'store'])->name('leads.store');

        // ---- THESE MUST COME LAST ----
        Route::get('/{lead}', [LeadController::class, 'show'])->name('leads.show');
        Route::get('/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
        Route::put('/{lead}', [LeadController::class, 'update'])->name('leads.update');
        Route::delete('/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');

        // Call logs
        Route::post('/{lead}/calls', [LeadCallController::class, 'store'])->name('leads.calls.store');

    });

});

Route::get('/{slug}', [FrontendPageController::class, 'show'])
    ->name('page.dynamic');



