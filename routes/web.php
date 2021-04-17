<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\UploadedFileController;
use App\Http\Controllers\ClassroomGroupController;
use App\Http\Controllers\CurriculumSubjectController;
use App\Http\Controllers\CurriculumAcademicYearController;
use App\Http\Controllers\CurriculumClassroomGroupController;

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
Route::redirect('/', 'curriculumAcademicYears');

Route::resource('uploadedFiles', UploadedFileController::class)->only([
    'index', 'store', 'show'
])->middleware(['auth']);

Route::resource('curriculumAcademicYears', CurriculumAcademicYearController::class)->only([
    'index'
])->middleware(['auth']);

Route::resource('curriculumSubjects', CurriculumSubjectController::class)->only([
    'destroy', 'store', 'edit', 'update'
])->middleware(['auth']);

Route::resource('curriculums', CurriculumController::class)->middleware(['auth']);
Route::resource('academicYears', AcademicYearController::class)->middleware(['auth']);
Route::resource('departments', DepartmentController::class)->middleware(['auth']);
Route::resource('subjects', SubjectController::class)->middleware(['auth']);
Route::resource('classroomGroups', ClassroomGroupController::class)->middleware(['auth']);

Route::post('subjects/filter', [SubjectController::class, 'filter'])->middleware(['auth'])->name('subjects.filter');
Route::post('curriculumAcademicYears/filter', [CurriculumAcademicYearController::class, 'filter'])->middleware(['auth'])->name('curriculumAcademicYears.filter');
Route::post('classroomGroups/filter', [ClassroomGroupController::class, 'filter'])->middleware(['auth'])->name('classroomGroups.filter');

Route::get('curriculumAcademicYears/export', [CurriculumAcademicYearController::class, 'export'])->middleware(['auth'])->name('curriculumAcademicYears.export');

Route::get('curriculumSubjects/{academic_year_id}/{curriculum_id}', [CurriculumSubjectController::class, 'index'])->middleware(['auth'])->name('curriculumSubjects.index');
Route::get('curriculumSubjects/{academic_year_id}/{curriculum_id}/create', [CurriculumSubjectController::class, 'create'])->middleware(['auth'])->name('curriculumSubjects.create');
Route::post('curriculumSubjects/{academic_year_id}/{curriculum_id}/filter', [CurriculumSubjectController::class, 'filter'])->middleware(['auth'])->name('curriculumSubjects.filter');
Route::get('curriculumSubjects/{academic_year_id}/{curriculum_id}/filter', [CurriculumSubjectController::class, 'filter'])->middleware(['auth'])->name('curriculumSubjects.filter');

Route::get('curriculumClassroomGroups/{curriculum_subject_id}', [CurriculumClassroomGroupController::class, 'index'])->middleware(['auth'])->name('curriculumClassroomGroups.index');
Route::post('curriculumClassroomGroups/{curriculum_subject_id}', [CurriculumClassroomGroupController::class, 'update'])->middleware(['auth'])->name('curriculumClassroomGroups.update');

Route::get('uploadedFiles/{uploaded_file_id}/download', [UploadedFileController::class, 'download'])->middleware(['auth'])->name('uploadedFiles.download');
Route::get('uploadedFiles/{uploaded_file_id}/process', [UploadedFileController::class, 'process'])->middleware(['auth'])->name('uploadedFiles.process');


require __DIR__.'/auth.php';
