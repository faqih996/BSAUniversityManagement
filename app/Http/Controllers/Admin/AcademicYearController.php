<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AcademicYearSemester;
use App\Enums\MessageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AcademicYearRequest;
use App\Http\Resources\Admin\AcademicYearResource;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Throwable;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::query()
            ->select(['id', 'name', 'slug', 'start_date', 'end_date', 'semester', 'is_active', 'created_at'])
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->paginate(request()->load ?? 10);

        return Inertia('Admin/AcademicYears/Index', [
            'page_settings' => [
                'title' => 'Tahun Ajaran',
                'subtitle' => 'Menampilkan semua data tahun ajaran yang tersedia pada universitas ini'
            ],

            'academicYears' => AcademicYearResource::collection($academicYears)->additional([
                'meta' => [
                    'has_pages' => $academicYears->hasPages(),
                ],
            ]),
            'state' => [
                'page' => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load' => 10,
            ],
        ]);
    }

    public function create()
    {
        return Inertia('Admin/AcademicYears/Create', [
            'page_settings' => [
                'title' => 'Tambah Tahun Ajaran',
                'subtitle' => 'Buat tahun ajaran baru disini. klik simpan setelah selesai',
                'method' => 'POST',
                'action' => route('admin.academic-years.store'),
            ],

            'academicYearSemester' => AcademicYearSemester::options(),
        ]);
    }

    public function store(AcademicYearRequest $request): RedirectResponse
    {
        try {

            AcademicYear::create([
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'semester' => $request->semester,
                'is_active' => $request->is_active,
            ]);

            flashMessage(MessageType::CREATED->message('Tahun Ajaran'));
            return to_route('admin.academic-years.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.academic-years.index');
        }
    }

    public function edit(AcademicYear $academicYear): Response
    {
        return Inertia('Admin/AcademicYears/Edit', [
            'page_settings' => [
                'title' => 'Edit Tahun Ajaran',
                'subtitle' => 'Edit tahun ajaran disini. klik simpan setelah selesai',
                'method' => 'PUT',
                'action' => route('admin.academic-years.update', $academicYear),
            ],

            'academicYear' => $academicYear,
            'academicYearSemester' => AcademicYearSemester::options(),
        ]);
    }

    public function update(AcademicYearRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        try {

            $academicYear->update([
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'semester' => $request->semester,
                'is_active' => $request->is_active,
            ]);

            flashMessage(MessageType::UPDATED->message('Tahun Ajaran'));
            return to_route('admin.academic-years.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.academic-years.index');
        }
    }
    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        try {

            $academicYear->delete();
            // AcademicYear::destroy($academicYear->getKey());

            flashMessage(MessageType::DELETED->message('Tahun Ajaran'));

            return to_route('admin.academic-years.index');
        } catch (Throwable $e) {

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.academic-years.index');
        }
    }
}
