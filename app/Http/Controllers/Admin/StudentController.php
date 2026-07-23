<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use App\Enums\MessageType;
use App\Http\Resources\Admin\StudentResource;
use PhpParser\Node\Stmt\TryCatch;
use Inertia\Inertia;
use Throwable;

class StudentController extends Controller
{
    public function index(): Response
    {
        $students = Student::query()
            ->select(['students.id', 'students.user_id', 'students.faculty_id', 'students.department_id', 'students.classroom_id', 'students.fee_group_id', 'students.student_number', 'students.semester', 'students.batch', 'students.created_at'])
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->with(['user', 'faculty', 'department', 'feeGroup', 'classroom'])

            // this line to get data by student role only
            ->whereHas('user', function ($query) {
                $query->whereHas('roles', fn($query) => $query->where('name', 'Student'));
            })
            ->paginate(request()->load ?? 10);

        return inertia('Admin/Students/Index', [
            'page_settings' => [
                'title' => 'Mahasiswa',
                'subtitle' => 'Menampilkan semua data mahasiswa yang terdaftar pada universitas ini.',
            ],
            'students' => StudentResource::collection($students)->additional([
                'meta' => [
                    'has_pages' => $students->hasPages(),
                ],
            ]),
            'state' => [
                'page' => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load' => 10,
            ]
        ]);
    }
}