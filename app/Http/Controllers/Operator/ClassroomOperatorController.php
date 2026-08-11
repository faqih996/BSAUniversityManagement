<?php

namespace App\Http\Controllers\Operator;

use App\Enums\MessageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Operator\ClassroomOperatorRequest;
use App\Http\Resources\Operator\ClassroomOperatorResource;
use App\Models\Classroom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Throwable;

class ClassroomOperatorController extends Controller
{
    public function index(): Response
    {
        $classrooms = Classroom::query()
            ->select(['id', 'faculty_id', 'department_id', 'academic_year_id', 'name', 'slug', 'created_at'])
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->with(['academicYear'])
            ->paginate(request()->load ?? 10);

        $faculty_name = auth()->user()->operator->faculty?->name;
        $department_name = auth()->user()->operator->department?->name;

        return inertia('Operators/Classrooms/Index', [
            'page_settings' => [
                'title' => 'Kelas',
                'subtitle' => "Menampilkan semua data kelas yang tersedia di {$faculty_name} dan Program Studi {$department_name}",
            ],

            'classrooms' => ClassroomOperatorResource::collection($classrooms)->additional([
                'meta' => [
                    'has_pages' => $classrooms->hasPages(),
                ],
            ]),

            'state' => [
                'page' => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load' => 10,
            ],
        ]);
    }

    public function create(): Response
    {
        return inertia('Operators/Classrooms/Create', props: [
            'page_settings' => [
                'title' => 'Tambah kelas',
                'subtitle' => 'Buat kelas baru disini. klik simpan setelah selesai',
                'method' => 'POST',
                'action' => route('operators.classrooms.store')
            ],
        ]);
    }

    public function store(ClassroomOperatorRequest $request): RedirectResponse
    {
        try {
            Classroom::create([
                'faculty_id' => auth()->user()->operator->faculty_id,
                'department_id' => auth()->user()->operator->department_id,
                'academic_year_id' => activeAcademicYear()->id,
                'name' => $request->name,
            ]);

            flashMessage(MessageType::CREATED->message('Kelas'));

            return to_route('operators.classrooms.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('operators.classrooms.index');
        }
    }

    public function edit(Classroom $classroom): Response
    {
        return inertia('Operators/Classrooms/Edit', props: [
            'page_settings' => [
                'title' => 'Edit kelas',
                'subtitle' => 'Edit kelas disini. klik simpan setelah selesai',
                'method' => 'PUT',
                'action' => route('operators.classrooms.update', $classroom)
            ],
            'classroom' => $classroom,
        ]);
    }

    public function update(ClassroomOperatorRequest $request, Classroom $classroom): RedirectResponse
    {
        try {
            $classroom->update([
                'name' => $request->name,
            ]);

            flashMessage(MessageType::UPDATED->message('Kelas'));

            return to_route('operators.classrooms.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('operators.classrooms.index');
        }
    }

    public function destroy(Classroom $classroom): RedirectResponse
    {
        try {

            $classroom->delete();

            flashMessage(MessageType::DELETED->message('Kelas'));

            return to_route('operators.classrooms.index');
        } catch (Throwable $e) {

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('operators.classrooms.index');
        }
    }
}
