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
use App\Http\Controllers\Admin\OfflineTestController;


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
use App\Http\Controllers\EventExpenseController;
use App\Http\Controllers\TravelExpenseController;
use App\Http\Controllers\OfficeAssetController;
use App\Http\Controllers\BlockedNumberController;
use App\Http\Controllers\JoiningStudentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\LetterController;
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


use App\Models\Test;
use App\Models\StudentTest;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Frontend
Route::get('/join', [JoiningStudentController::class, 'create'])->name('joining_student.front');
Route::post('/join', [JoiningStudentController::class, 'store'])->name('joining_student.store');
// Route::get('/share/scanners/{token}', [ScannerShareController::class, 'show'])
    // ->name('scanners.share');
Route::get('/scanners', [ScannerShareController::class, 'index'])
    ->name('frontend.scanners.index');

Route::get('/scanners/view/{token}', [ScannerShareController::class, 'show'])
    ->name('scanners.share');
// Route::get('/page/{slug}', [FrontendPageController::class, 'show'])->name('page.show');

// Route::get('/ads-landing-page', [FrontendPageController::class, 'show_ads'])->name('page.show_ads');
Route::middleware(['auth', 'permission'])->group(function () {
    Route::prefix('admin')->group(function () {
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
        )->except(['create', 'edit', 'update']);

        // Status update (approve / reject)
        Route::patch(
            'internship-registrations/{internship_registration}/status',
            [InternshipRegistrationController::class, 'updateStatus']
        )->name('internship-registrations.status');

        Route::resource('visiting-cards', VisitingCardController::class);

        Route::get('services-registrations/export', [ServicesRegistrationController::class, 'export'])
            ->name('services-registrations.export');

        Route::resource('services-registrations', ServicesRegistrationController::class)
            ->only(['index', 'create', 'store', 'show', 'destroy']);

        Route::resource('hods', HodController::class);

        Route::get('interview-questions/listing', 
            [InterviewQuestionController::class, 'practice']
        )->name('interview-questions.practice');

        Route::resource('interview-questions', InterviewQuestionController::class);
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

        Route::resource('mous', MouController::class);
        Route::post('mous/{mou}/send-email', [MouController::class, 'sendEmail'])->name('mous.sendEmail');
        Route::post('mous/{mou}/upload-signed', [MouController::class, 'uploadSigned'])->name('mous.uploadSigned');

        Route::get('mous/{mou}/download', [MouController::class, 'download'])->name('mous.download');

        Route::post('/students/copy', [StudentController::class, 'copyStudents'])->name('students.copy');




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

        

    });
});

Route::middleware(['auth', 'permission'])->group(function () {
    
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


    Route::resource('letters', LetterController::class);

    Route::get(
        'letters/{letter}/download',
        [LetterController::class,'download']
    )->name('letters.download');

    Route::post(
        'letters/{letter}/email',
        [LetterController::class,'sendEmail']
    )->name('letters.email');

   
    Route::resource('projects', ProjectController::class);
    Route::resource('tutorials', TutorialController::class);
    Route::resource('cvs', CvController::class);
    Route::resource('daily-interviews', DailyInterviewController::class);

    Route::get('/students/import', [StudentController::class, 'importForm'])
        ->name('students.importForm');

    Route::post('/students/import', [StudentController::class, 'import'])
        ->name('students.import');

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
    ->middleware(['auth', 'role:3'])  // only sales employees
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

// Enquiry CRUD (protected with OTP middleware)
        // Route::middleware(['auth', 'enquiry.otp','role:1'])->group(function () {
        //     Route::resource('enquiries', EnquiryController::class);
        // });
// OTP Screens (no prefix)
Route::get('/enquiry-otp', [EnquiryOtpController::class, 'showOtpPage'])
    ->name('enquiry.otp.page');
Route::post('/enquiry-otp-send', [EnquiryOtpController::class, 'sendOtp'])
    ->name('enquiry.otp.send');
Route::post('/enquiry-otp-verify', [EnquiryOtpController::class, 'verifyOtp'])
    ->name('enquiry.otp.verify');

// Protected Enquiry CRUD
    Route::prefix('admin')
    ->middleware(['auth', 'enquiry.otp','role:1'])   // admin users only
    ->group(function () {
        Route::resource('enquiries', EnquiryController::class);
        Route::post('enquiries/import', [EnquiryController::class, 'import'])
        ->name('enquiries.import');

    Route::post('enquiries/assign', [EnquiryController::class, 'assign'])
        ->name('enquiries.assign');


    //Office expenses
    Route::resource('electricity-expenses', OfficeExpenseController::class)->names('office-expenses');
    Route::resource('pantry-expenses', PantryExpenseController::class);
    Route::resource('event-expenses', EventExpenseController::class);
    Route::resource('travel-expenses', TravelExpenseController::class);
    Route::resource('office-assets', OfficeAssetController::class);
     Route::resource('recharges', RechargeController::class);
    // quick status update
    Route::post('recharges/{recharge}/set-status', [RechargeController::class, 'setStatus'])->name('recharges.setStatus');




    });
    Route::prefix('admin')
    ->middleware(['auth','permission'])   // admin users only
    // ->middleware(['auth','role:1'])   // admin users only
    ->group(function () {
// Route::middleware(['auth', 'enquiry.otp','role:1'])->group(function () {
    
    
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

Route::middleware(['auth', 'permission'])->group(function () {

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

    // new routes for download certificate

    Route::post('/students/download-certificate-multiple', [StudentController::class, 'downloadCertificateMultiple'])
    ->name('students.downloadCertificateMultiple');

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

Route::middleware(['auth', 'role:1,2,3,4'])->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::middleware(['auth', 'role:1,2,3'])->group(function () {

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

        Route::resource(
            'blocked-numbers',
            BlockedNumberController::class
        )->except(['edit', 'update']);
    });


/*
|--------------------------------------------------------------------------
| ADMIN MODULES (role = 1)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission'])->group(function () {

    Route::resource('sessions', SessionController::class);
    Route::resource('courses', CourseController::class);
    Route::get('colleges/export/excel', [CollegeController::class, 'exportExcel'])
    ->name('colleges.export.excel');
    Route::resource('colleges', CollegeController::class);
    // Endpoint to fetch districts by state (AJAX)
    Route::get('districts/by-state/{state}', [DistrictController::class, 'getByState']);

    // Route::resource('certificates', CertificateController::class);
    Route::resource('certificates', CertificateController::class)
     ->parameters(['certificates' => 'student']);

    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::resource('users', UserController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('references', ReferenceController::class);
    Route::resource('student_certificates', StudentCertificateController::class);
    Route::resource('batches', BatchController::class);


     Route::get('/manager/permissions',
        [\App\Http\Controllers\ManagerPermissionController::class, 'edit']
    )->name('admin.manager.permissions.edit');

    Route::post('/manager/permissions',
        [\App\Http\Controllers\ManagerPermissionController::class, 'update']
    )->name('admin.manager.permissions.update');



    // Tests module
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('tests', TestController::class);
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


 Route::get('offline-tests/{test}/results', [OfflineTestController::class,'results'])->name('offline-tests.results');

        Route::post('tests/bulk-finalize', [TestController::class, 'bulkFinalize'])->name('tests.bulk.finalize');


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
    
    Route::resource('employees', EmployeeController::class);


});


/*
|--------------------------------------------------------------------------
| TRAINER MODULES (role = 2)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission'])->group(function () {

    // route to download skipped rows: type = txt|csv|xlsx
    Route::get('/trainers/import/skipped/download/{type}', [TrainerController::class, 'downloadSkipped'])
        ->name('trainers.skipped.download');
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

Route::middleware(['auth', 'role:2'])->group(function () {

    // Trainer can ONLY view the batch from notification
    Route::get('/batches/{batch}', [BatchController::class, 'show'])
        ->name('batches.show');


    Route::get('/mybatches', [BatchController::class, 'MyBatches'])
        ->name('batches.mybatches');


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

Route::middleware(['auth', 'permission'])->get('/manager/students', [StudentController::class, 'managerIndex'])
    ->name('manager.students.index');


/*
|--------------------------------------------------------------------------
| CERTIFICATE ISSUE (admin only)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'permission'])->group(function () {

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

Route::middleware(['auth'])->group(function () {

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

Route::middleware(['auth'])->group(function () {
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

Route::middleware(['auth', 'permission'])->group(function () {
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

Route::get('/colleges-data', [DashboardController::class,'getCollegesData'])->name('colleges.data');
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

Route::middleware(['auth', 'permission'])->group(function () {

    Route::prefix('admin')->group(function () {
        Route::get('/activity', [ActivityController::class, 'index'])->name('admin.activity');
        Route::get('/activity/lead/{lead_id}', [ActivityController::class, 'leadTimeline'])->name('activity.lead');
        Route::get('/activity/user/{user_id}', [ActivityController::class, 'userTimeline'])->name('activity.user');

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
         Route::get('/company_profile/preview/{token}', 
    [CompanyProfileController::class, 'preview']
)->name('company_profile.preview');

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
Route::get('/b/{brochure}', [BrochureController::class, 'preview'])
    ->name('brochures.preview');

Route::get('/b/{brochure}/download', [BrochureController::class, 'download'])
    ->name('brochures.secure.download');


Route::get('/company_profile/view/{company_profile}', [CompanyProfileController::class, 'view'])
     ->name('company_profile.view');

Route::get('/c/{company_profile}', [CompanyProfileController::class, 'preview'])
    ->name('company_profile.preview');

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



