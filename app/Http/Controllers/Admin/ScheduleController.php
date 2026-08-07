<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    use HasFile;

    public function index(): Response
    {
        $schedules = Schedule::query()
            ->select(['schedules.id',  'schedules.faculty_id', 'schedules.department_id', 'schedules.course_id', 'schedules.classroom_id', 'schedules.academic_year_id', 'schedules.start_time', 'schedules.end_time', 'schedules.day_of_week', 'schedules.quota', 'schedules.created_at'])
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->with(['faculty', 'department', 'course', 'classroom', 'academicYear' ])
            ->paginate(request()->load ?? 10);

        return inertia('Admin/Schedules/Index', [
            'page_settings' => [
                'title' => 'Jadwal',
                'subtitle' => 'Menampilkan semua data jadwal yang terdaftar pada universitas ini.',
            ],
            'schedules' => ScheduleResource::collection($schedules)->additional([
                'meta' => [
                    'has_pages' => $schedules->hasPages(),
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
