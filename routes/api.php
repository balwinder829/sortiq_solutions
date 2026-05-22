<?php



use App\Http\Controllers\AuthController;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\TechnologyController;

use App\Http\Controllers\StateController;

use App\Http\Controllers\DistrictController;

use App\Http\Controllers\CollegeController;

use App\Http\Controllers\MouController;

use App\Http\Controllers\HodController;

use App\Http\Controllers\CollegeEmailController;

use App\Http\Controllers\CollegeCallController;

use App\Http\Controllers\WorkshopController;

use App\Http\Controllers\TestCategoryController;

use App\Http\Controllers\Admin\TestController;

use App\Http\Controllers\ExternalAttendanceController;

use App\Http\Controllers\ManualDataController;

use App\Http\Controllers\HardDataController;

// use App\Http\Controllers\WordPressInternshipController;

use App\Http\Controllers\TrainerController;

use App\Http\Controllers\BatchController;

use App\Http\Controllers\EnquiryController;

use App\Http\Controllers\LeadController;

use App\Http\Controllers\PassoutController;

use App\Http\Controllers\JoiningStudentController;

use App\Http\Controllers\Helpdesk\HelpdeskArticleController;

use App\Http\Controllers\ServicesRegistrationController;

use App\Http\Controllers\StudentController;

use App\Http\Controllers\CloseStudenController;

use App\Http\Controllers\CertificateController;

use App\Http\Controllers\StudentCertificateController;

use App\Http\Controllers\StudentEvaluationController;

use App\Http\Controllers\FeeStatusController;

use App\Http\Controllers\StudentAdditionalLetterController;

use App\Http\Controllers\StudentsAcceptedLetterController;

use App\Http\Controllers\Admin\OfficeTestController;

use App\Http\Controllers\Admin\StudentPendingController;

use App\Http\Controllers\EmployeeController;

use App\Http\Controllers\AttendanceController;

use App\Http\Controllers\LetterController;

use App\Http\Controllers\AcceptedLetterController;

use App\Http\Controllers\SalarySlipController;
use App\Http\Controllers\Sales\SalesDashboardController;

use App\Http\Controllers\EnquiryFollowupController;



use Illuminate\Support\Facades\Route;



Route::middleware('web')->post('/login', [AuthController::class, 'apiLogin']);

Route::middleware('web')->post('/sales/login', [AuthController::class, 'apiSalesLogin']);

Route::middleware('web')->post('/mentor/login', [AuthController::class, 'apiMentorLogin']);

Route::middleware('web')->post('/logout', [AuthController::class, 'apiLogout']);

Route::get('/sessions', [AuthController::class, 'apiSessions']);

Route::middleware('web')->get('/dashboard', [DashboardController::class, 'apiDashboard']);



// API Endpoints requiring Login & Permissions

Route::middleware('web')->group(function () {

    // Technologies

    Route::get('/technologies', [TechnologyController::class, 'apiIndex']);

    Route::post('/technologies', [TechnologyController::class, 'apiStore']);

    Route::put('/technologies/{id}', [TechnologyController::class, 'apiUpdate']);

    Route::delete('/technologies/{id}', [TechnologyController::class, 'apiDestroy']);



    // States

    Route::get('/states', [StateController::class, 'apiIndex']);

    Route::post('/states', [StateController::class, 'apiStore']);

    Route::put('/states/{id}', [StateController::class, 'apiUpdate']);

    Route::delete('/states/{id}', [StateController::class, 'apiDestroy']);



    // Districts

    Route::get('/districts', [DistrictController::class, 'apiIndex']);

    Route::post('/districts', [DistrictController::class, 'apiStore']);

    Route::put('/districts/{id}', [DistrictController::class, 'apiUpdate']);

    Route::delete('/districts/{id}', [DistrictController::class, 'apiDestroy']);



    // Colleges

    Route::get('/colleges', [CollegeController::class, 'apiIndex']);

    Route::get('/colleges/{id}', [CollegeController::class, 'apiShow']);

    Route::post('/colleges', [CollegeController::class, 'apiStore']);

    Route::put('/colleges/{id}', [CollegeController::class, 'apiUpdate']);

    Route::delete('/colleges/{id}', [CollegeController::class, 'apiDestroy']);



    // College MoU

    Route::get('/mous', [MouController::class, 'apiIndex']);

    Route::get('/mous/{id}', [MouController::class, 'apiShow']);

    Route::post('/mous', [MouController::class, 'apiStore']);

    Route::put('/mous/{id}', [MouController::class, 'apiUpdate']);

    Route::delete('/mous/{id}', [MouController::class, 'apiDestroy']);



    // College Authority (HOD/TPO)

    Route::get('/hods', [HodController::class, 'apiIndex']);

    Route::get('/hods/{id}', [HodController::class, 'apiShow']);

    Route::post('/hods', [HodController::class, 'apiStore']);

    Route::put('/hods/{id}', [HodController::class, 'apiUpdate']);

    Route::delete('/hods/{id}', [HodController::class, 'apiDestroy']);



    // College Emails Records

    Route::get('/college-emails', [CollegeEmailController::class, 'apiIndex']);

    Route::get('/college-emails/{collegeId}/logs', [CollegeEmailController::class, 'apiLogs']);



    // College Calls Records

    Route::get('/college-calls', [CollegeCallController::class, 'apiIndex']);

    Route::get('/college-calls/{collegeId}/logs', [CollegeCallController::class, 'apiLogs']);



    // Workshops

    Route::get('/workshops', [WorkshopController::class, 'apiIndex']);

    Route::get('/workshops/{id}', [WorkshopController::class, 'apiShow']);

    Route::post('/workshops', [WorkshopController::class, 'apiStore']);

    Route::put('/workshops/{id}', [WorkshopController::class, 'apiUpdate']);

    Route::delete('/workshops/{id}', [WorkshopController::class, 'apiDestroy']);



    // Exam Categories

    Route::get('/exam-categories', [TestCategoryController::class, 'apiIndex']);

    Route::post('/exam-categories', [TestCategoryController::class, 'apiStore']);

    Route::put('/exam-categories/{id}', [TestCategoryController::class, 'apiUpdate']);

    Route::delete('/exam-categories/{id}', [TestCategoryController::class, 'apiDestroy']);



    // Online Exams

    Route::get('/online-exams', [TestController::class, 'apiIndex']);

    Route::get('/online-exams/{id}', [TestController::class, 'apiShow']);



    // Attendance Forms

    Route::get('/attendance-forms', [ExternalAttendanceController::class, 'apiIndex']);

    Route::get('/attendance-forms/{id}', [ExternalAttendanceController::class, 'apiShow']);



    // Manual Upload Data

    Route::get('/manual-data', [ManualDataController::class, 'apiIndex']);

    Route::get('/manual-data/{id}', [ManualDataController::class, 'apiShow']);

    Route::post('/manual-data', [ManualDataController::class, 'apiStore']);

    Route::put('/manual-data/{id}', [ManualDataController::class, 'apiUpdate']);

    Route::delete('/manual-data/{id}', [ManualDataController::class, 'apiDestroy']);



    // Hard Data

    Route::get('/hard-data', [HardDataController::class, 'apiIndex']);

    Route::get('/hard-data/{id}', [HardDataController::class, 'apiShow']);

    Route::post('/hard-data', [HardDataController::class, 'apiStore']);

    Route::put('/hard-data/{id}', [HardDataController::class, 'apiUpdate']);

    Route::delete('/hard-data/{id}', [HardDataController::class, 'apiDestroy']);



    /* =========================================

       PHASE 1, 2, & 3: ADMINISTRATIVE MODULES

       ========================================= */



    // Mentors (Trainers)

    Route::get('/trainers', [TrainerController::class, 'apiIndex']);

    Route::get('/trainers/{id}', [TrainerController::class, 'apiShow']);

    Route::post('/trainers', [TrainerController::class, 'apiStore']);

    Route::put('/trainers/{id}', [TrainerController::class, 'apiUpdate']);

    Route::delete('/trainers/{id}', [TrainerController::class, 'apiDestroy']);



    // Batches

    Route::get('/batches', [BatchController::class, 'apiIndex']);

    Route::get('/batches/{id}', [BatchController::class, 'apiShow']);

    Route::post('/batches', [BatchController::class, 'apiStore']);

    Route::put('/batches/{id}', [BatchController::class, 'apiUpdate']);

    Route::delete('/batches/{id}', [BatchController::class, 'apiDestroy']);



    // Enquiries

    Route::get('/enquiries', [EnquiryController::class, 'apiIndex']);

    Route::get('/enquiries/{id}', [EnquiryController::class, 'apiShow']);

    Route::post('/enquiries', [EnquiryController::class, 'apiStore']);

    Route::put('/enquiries/{id}', [EnquiryController::class, 'apiUpdate']);

    Route::delete('/enquiries/{id}', [EnquiryController::class, 'apiDestroy']);



    // Leads

    Route::get('/leads', [LeadController::class, 'apiIndex']);

    Route::get('/leads/{id}', [LeadController::class, 'apiShow']);

    Route::post('/leads', [LeadController::class, 'apiStore']);

    Route::put('/leads/{id}', [LeadController::class, 'apiUpdate']);

    Route::delete('/leads/{id}', [LeadController::class, 'apiDestroy']);



    // Passouts

    Route::get('/passouts', [PassoutController::class, 'apiIndex']);

    Route::get('/passouts/{id}', [PassoutController::class, 'apiShow']);

    Route::post('/passouts', [PassoutController::class, 'apiStore']);

    Route::put('/passouts/{id}', [PassoutController::class, 'apiUpdate']);

    Route::delete('/passouts/{id}', [PassoutController::class, 'apiDestroy']);



    // Joining Students

    Route::get('/joining-students', [JoiningStudentController::class, 'apiIndex']);

    Route::get('/joining-students/{id}', [JoiningStudentController::class, 'apiShow']);

    Route::post('/joining-students', [JoiningStudentController::class, 'apiStore']);

    Route::put('/joining-students/{id}', [JoiningStudentController::class, 'apiUpdate']);

    Route::delete('/joining-students/{id}', [JoiningStudentController::class, 'apiDestroy']);

    Route::post('/joining-students/send-to-session', [JoiningStudentController::class, 'apiSendToSession']);



    // Helpdesk Articles

    Route::get('/helpdesk-articles', [HelpdeskArticleController::class, 'apiIndex']);

    Route::get('/helpdesk-articles/{id}', [HelpdeskArticleController::class, 'apiShow']);



    // Services Registrations

    Route::get('/services-registrations', [ServicesRegistrationController::class, 'apiIndex']);

    Route::get('/services-registrations/{id}', [ServicesRegistrationController::class, 'apiShow']);



    // Students Current

    Route::get('/students', [StudentController::class, 'apiIndex']);

    Route::get('/students/{id}', [StudentController::class, 'apiShow']);

    Route::post('/students', [StudentController::class, 'apiStore']);

    Route::put('/students/{id}', [StudentController::class, 'apiUpdate']);

    Route::delete('/students/{id}', [StudentController::class, 'apiDestroy']);



    // Verify Students (Navbar Items)

    Route::get('/verify-students-lists', [StudentController::class, 'apiVerifyStudents']);

    Route::get('/verify-students-link', [StudentController::class, 'apiVerifyStudentsLink']);



    // Close Students (Verifications in Dashboard)

    Route::get('/verify-students', [CloseStudenController::class, 'apiIndex']);

    Route::get('/verify-students/{id}', [CloseStudenController::class, 'apiShow']);

    Route::put('/verify-students/{id}', [CloseStudenController::class, 'apiUpdate']);

    Route::delete('/verify-students/{id}', [CloseStudenController::class, 'apiDestroy']);



    // Certificate Verification (Public/External)

    Route::post('/certificate-check', [StudentCertificateController::class, 'checkCertificate']);



    // Certificates

    Route::get('/certificates', [CertificateController::class, 'apiIndex']);

    Route::get('/certificates/{id}', [CertificateController::class, 'apiShow']);

    Route::put('/certificates/{id}', [CertificateController::class, 'apiUpdate']);



    // Student Evaluations

    Route::get('/student-evaluations', [StudentEvaluationController::class, 'apiIndex']);

    Route::get('/student-evaluations/{id}', [StudentEvaluationController::class, 'apiShow']);

    Route::post('/student-evaluations', [StudentEvaluationController::class, 'apiStore']);

    Route::put('/student-evaluations/{id}', [StudentEvaluationController::class, 'apiUpdate']);

    Route::delete('/student-evaluations/{id}', [StudentEvaluationController::class, 'apiDestroy']);



    // Fee Status

    Route::get('/fee-status', [FeeStatusController::class, 'apiIndex']);



    // Student Additional Letters

    Route::get('/student-additional-letters', [StudentAdditionalLetterController::class, 'apiIndex']);

    Route::get('/student-additional-letters/{id}', [StudentAdditionalLetterController::class, 'apiShow']);

    Route::post('/student-additional-letters', [StudentAdditionalLetterController::class, 'apiStore']);

    Route::put('/student-additional-letters/{id}', [StudentAdditionalLetterController::class, 'apiUpdate']);

    Route::delete('/student-additional-letters/{id}', [StudentAdditionalLetterController::class, 'apiDestroy']);



    // Students Accepted Letters

    Route::get('/students-accepted-letters', [StudentsAcceptedLetterController::class, 'apiIndex']);

    Route::get('/students-accepted-letters/{id}', [StudentsAcceptedLetterController::class, 'apiShow']);

    Route::post('/students-accepted-letters', [StudentsAcceptedLetterController::class, 'apiStore']);

    Route::put('/students-accepted-letters/{id}', [StudentsAcceptedLetterController::class, 'apiUpdate']);

    Route::delete('/students-accepted-letters/{id}', [StudentsAcceptedLetterController::class, 'apiDestroy']);



    // Office Tests

    Route::get('/office-tests', [OfficeTestController::class, 'apiIndex']);

    Route::get('/office-tests/{id}', [OfficeTestController::class, 'apiShow']);

    Route::post('/office-tests', [OfficeTestController::class, 'apiStore']);

    Route::put('/office-tests/{id}', [OfficeTestController::class, 'apiUpdate']);

    Route::delete('/office-tests/{id}', [OfficeTestController::class, 'apiDestroy']);



    // Pending Requests

    Route::get('/pending-requests', [StudentPendingController::class, 'apiIndex']);

    Route::post('/pending-requests/send-to-session', [StudentPendingController::class, 'apiSendToSession']);



    // HR Management - Employees

    Route::get('/employees', [EmployeeController::class, 'apiIndex']);

    Route::get('/employees/{id}', [EmployeeController::class, 'apiShow']);

    Route::post('/employees', [EmployeeController::class, 'apiStore']);

    Route::put('/employees/{id}', [EmployeeController::class, 'apiUpdate']);

    Route::delete('/employees/{id}', [EmployeeController::class, 'apiDestroy']);

    Route::get('/employees/{id}/idcard', [EmployeeController::class, 'apiDownloadIdCard']);

    Route::post('/employees/{id}/idcard/email', [EmployeeController::class, 'apiEmailIdCard']);



    // HR Management - Attendance

    Route::get('/attendance/employees', [AttendanceController::class, 'apiEmployeeList']);

    Route::post('/attendance/check-in', [AttendanceController::class, 'apiCheckIn']);

    Route::post('/attendance/check-out', [AttendanceController::class, 'apiCheckOut']);

    Route::get('/attendance/monthly-detail/{id?}', [AttendanceController::class, 'apiMonthlyDetail']);



    // HR Management - Letters

    Route::get('/letters', [LetterController::class, 'apiIndex']);

    Route::get('/letters/{id}', [LetterController::class, 'apiShow']);

    Route::post('/letters', [LetterController::class, 'apiStore']);

    Route::put('/letters/{id}', [LetterController::class, 'apiUpdate']);

    Route::delete('/letters/{id}', [LetterController::class, 'apiDestroy']);

    Route::get('/letters/{id}/download', [LetterController::class, 'apiDownload']);

    Route::post('/letters/{id}/send-email', [LetterController::class, 'apiSendEmail']);



    // HR Management - Accepted Letters

    Route::get('/accepted-letters', [AcceptedLetterController::class, 'apiIndex']);

    Route::get('/accepted-letters/{id}', [AcceptedLetterController::class, 'apiShow']);

    Route::post('/accepted-letters', [AcceptedLetterController::class, 'apiStore']);

    Route::put('/accepted-letters/{id}', [AcceptedLetterController::class, 'apiUpdate']);

    Route::delete('/accepted-letters/{id}', [AcceptedLetterController::class, 'apiDestroy']);

    Route::get('/accepted-letters/{id}/download', [AcceptedLetterController::class, 'apiDownload']);



    // HR Management - Salary Slips

    Route::get('/salary-slips', [SalarySlipController::class, 'apiIndex']);

    Route::get('/salary-slips/{id}', [SalarySlipController::class, 'apiShow']);

    Route::post('/salary-slips/generate', [SalarySlipController::class, 'apiGenerate']);

    Route::delete('/salary-slips/{id}', [SalarySlipController::class, 'apiDestroy']);

    Route::get('/salary-slips/{id}/download', [SalarySlipController::class, 'apiDownload']);

    Route::post('/salary-slips/{id}/send-email', [SalarySlipController::class, 'apiSendEmail']);



    /* =========================================

       MENTOR (TRAINER) SPECIFIC APIS

       ========================================= */

    Route::get('/mentor/sidebar', [AuthController::class, 'apiMentorSidebar']);

    Route::get('/mentor/batches', [BatchController::class, 'apiMentorBatches']);





    /* =========================================
       STUDENT SPECIFIC APIS
       ========================================= */
    Route::prefix('student')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\StudentDashboardApiController::class, 'index']);
        Route::get('/profile', [\App\Http\Controllers\Api\StudentDashboardApiController::class, 'profile']);
        Route::get('/attendance', [\App\Http\Controllers\Api\StudentDashboardApiController::class, 'attendance']);
        Route::get('/projects', [\App\Http\Controllers\Api\StudentDashboardApiController::class, 'projects']);
        Route::get('/fees', [\App\Http\Controllers\Api\StudentDashboardApiController::class, 'fees']);
    });
    
    
    
    /* =========================================
       SALES SPECIFIC APIS
       ========================================= */
    
     Route::middleware('api.token:sales_staff')->group(function () {
        Route::get('/sales/dashboard', [SalesDashboardController::class, 'apiDashboard']);
        Route::get('/sales/sidebar', [SalesDashboardController::class, 'apiSidebar']);
        Route::get('/sales/assigned-data', [\App\Http\Controllers\Sales\SalesEnquiryController::class, 'apiIndex']);
        Route::get('/sales/enquiries/{enquiry}', [\App\Http\Controllers\Sales\SalesEnquiryController::class, 'apiShow']);
        Route::get('/sales/colleges/{college}', [\App\Http\Controllers\Sales\SalesEnquiryController::class, 'apiCollegeDetails']);
        Route::post('/enquiries/{enquiry}/followups', [EnquiryFollowupController::class, 'apiStore']);
         Route::get('/sales/filters', [\App\Http\Controllers\Sales\SalesEnquiryController::class, 'apiFilters']);

        // Sales Support Tickets
        Route::get('/sales/support-tickets', [SupportTicketApiController::class, 'index']);
        Route::post('/sales/support-tickets', [SupportTicketApiController::class, 'store']);
        Route::get('/sales/support-tickets/{id}', [SupportTicketApiController::class, 'show']);
        Route::post('/sales/support-tickets/{id}/reply', [SupportTicketApiController::class, 'reply']);
    });


    

});

