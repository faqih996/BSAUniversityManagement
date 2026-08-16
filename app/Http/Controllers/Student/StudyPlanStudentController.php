<?php

namespace App\Http\Controllers\Student;

use App\Enums\MessageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StudyPlanStudentRequest;
use App\Http\Resources\Admin\ScheduleResource;
use App\Http\Resources\Student\StudyPlanScheduleStudentResource;
use App\Http\Resources\Student\StudyPlanStudentResource;
use App\Models\Schedule;
use App\Models\StudyPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Throwable;

class StudyPlanStudentController extends Controller
{
    public function index(): Response
    {
        $studyPlans = StudyPlan::query()
            ->select(['id', 'student_id', 'academic_year_id', 'status', 'created_at'])
            ->where('student_id', auth()->user()->student->id)
            ->with(['academicYear'])
            ->latest('created_at')
            ->paginate(request()->load ?? 10);

        return inertia('Students/StudyPlans/Index', [
            'page_settings' => [
                'title' => 'Kartu Rencana Study',
                'subtitle' => "Menampilkan semua kartu rencana studi anda",
            ],
            'studyPlans' => StudyPlanStudentResource::collection($studyPlans)->additional([
                'meta' => [
                    'has_pages' => $studyPlans->hasPages(),
                ],
            ]),
            'state' => [
                'page' => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load' => 10,
            ],
        ]);
    }

    public function create(): Response | RedirectResponse
    {
        if(!activeAcademicYear()) return back();

        $schedules = Schedule::query()
            ->where('faculty_id', auth()->user()->student->faculty_id)
            ->where('department_id', auth()->user()->student->department_id)
            ->where('academic_year_id', activeAcademicYear()->id)
            ->with(['course', 'classroom'])
            ->withCount(['studyPlans as taken_quota' => fn($query) => $query->where('academic_year_id', activeAcademicYear()->id)])
            ->orderByDesc('day_of_week')
            ->get();

        if($schedules->isEmpty()){
            flashMessage('Tidak ada jadwal tersedia...', 'warning');
            return to_route('students.study-plans.index');
        };

        return inertia('Students/StudyPlans/Create', props: [
            'page_settings' => [
                'title' => 'Tambah kartu rencana studi',
                'subtitle' => 'Harap pilih mata kuliah yang sesuai dengan kelas anda',
                'method' => 'POST',
                'action' => route('students.study-plans.store')
            ],

            'schedules' => ScheduleResource::collection($schedules),
        ]);
    }

    public function store(StudyPlanStudentRequest $request): RedirectResponse
    {
        try {
            DB::BeginTransaction();
            $studyPlan = StudyPlan::create([
                'student_id' => auth()->user()->student->id,
                'academic_year_id' => activeAcademicYear()->id,
            ]);

            $studyPlan->schedules()->attach($request->schedule_id);

            DB::commit();

            flashMessage('Berhasil Mengajukan KRS');

            return to_route('students.study-plans.index');
        } catch (Throwable $e) {

            DB::rollBack();

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('students.study-plans.index');
        }
    }

    public function show(StudyPlan $studyPlan): Response
    {
        return inertia('Students/StudyPlans/Show', [
            'page_settings' => [
                'title' => 'Detail kartu rencana studi',
                'subtitle' => 'Anda dapat melihat detail kartu rencana studi anda disini',
            ],

            'studyPlan' => new StudyPlanScheduleStudentResource($studyPlan->load(['schedules'])),
        ]);
    }
}
