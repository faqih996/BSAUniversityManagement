<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Enums\MessageType;

use App\Http\Requests\Admin\CourseRequest;
use App\Http\Resources\Admin\CourseResource;

use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use App\Models\Teacher;

use App\Traits\HasFile;
use App\Helpers\helpers;

use PhpParser\Node\Stmt\TryCatch;

use Inertia\Response;
use Inertia\Inertia;
use Throwable;

class CourseController extends Controller
{
    use HasFile;

    public function index(): Response
    {
        $courses = Course::query()
            ->select(['courses.id', 'courses.teacher_id', 'courses.faculty_id', 'courses.department_id', 'courses.code', 'courses.name', 'courses.semester', 'courses.credit', 'courses.created_at'])
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->with(['teacher', 'faculty', 'department'])
            ->paginate(request()->load ?? 10);

        return inertia('Admin/Courses/Index', [
            'page_settings' => [
                'title' => 'Mata Kuliah',
                'subtitle' => 'Menampilkan semua data mata kuliah yang terdaftar pada universitas ini.',
            ],
            'courses' => CourseResource::collection($courses)->additional([
                'meta' => [
                    'has_pages' => $courses->hasPages(),
                ],
            ]),
            'state' => [
                'page' => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load' => 10,
            ]
        ]);
    }

    public function create(): Response
    {
        return inertia('Admin/Courses/Create', props: [
            'page_settings' => [
                'title' => 'Tambah Mata Kuliah',
                'subtitle' => 'Buat mata kuliah baru disini. klik simpan setelah selesai',
                'method' => 'POST',
                'action' => route('admin.courses.store')
            ],
            'faculties' => Faculty::query()->select(['id', 'name'])->orderBy('name')->get()->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->name
            ]),
            'departments' => Department::query()->select(['id', 'name'])->orderBy('name')->get()->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->name
            ]),
            'teachers' => Teacher::query()->select(['id', 'user_id'])->whereHas('user', function($query){
            $query->whereHas('roles', fn($query) => $query->where('name', 'Teacher'))->orderBy('name');
            })->get()->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->user?->name
            ])
        ]);
    }

    public function store(CourseRequest $request): RedirectResponse
    {
        try {

            Course::create([
                'faculty_id' => $request->faculty_id,
                'department_id' => $request->department_id,
                'teacher_id' => $request->teacher_id,
                'academic_year_id' => activeAcademicYear()->id,
                'name' => $request->name,
                'code' => Str()->random(10),
                'credit' => $request->credit,
                'semester' => $request->semester,
            ]);

            flashMessage(MessageType::CREATED->message('Mata Kuliah'));

            return to_route('admin.courses.index');
        } catch (Throwable $e) {

            DB::rollBack();
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.courses.index');
        }
    }

    public function edit(Course $course): Response
    {
        return inertia('Admin/Courses/Edit', props: [
            'page_settings' => [
                'title' => 'Edit Mata Kuliah',
                'subtitle' => 'Edit mata kuliah baru disini. klik simpan setelah selesai',
                'method' => 'PUT',
                'action' => route('admin.courses.update', $course)
            ],
            'course' => $course,
            'faculties' => Faculty::query()->select(['id', 'name'])->orderBy('name')->get()->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->name
            ]),
            'departments' => Department::query()->select(['id', 'name'])->orderBy('name')->get()->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->name
            ]),
            'teachers' => Teacher::query()->select(['id', 'user_id'])->whereHas('user', function($query){
            $query->whereHas('roles', fn($query) => $query->where('name', 'Teacher'))->orderBy('name');
            })->get()->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->user?->name
            ])
        ]);
    }

    public function update(Course $course, CourseRequest $request): RedirectResponse
    {
        try {

            $course->update([
                'faculty_id' => $request->faculty_id,
                'department_id' => $request->department_id,
                'teacher_id' => $request->teacher_id,
                'academic_year_id' => activeAcademicYear()->id,
                'name' => $request->name,
                'credit' => $request->credit,
                'semester' => $request->semester,
            ]);

            flashMessage(MessageType::UPDATED->message('Mata Kuliah'));

            return to_route('admin.courses.index');
        } catch (Throwable $e) {

            DB::rollBack();
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.courses.index');
        }
    }

    public function destroy(Course $course): RedirectResponse
    {
        try {
            $course->delete();

            flashMessage(MessageType::DELETED->message('Mata Kuliah'));

            return to_route('admin.courses.index');
        } catch (Throwable $e) {

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.courses.index');
        }
    }
}
