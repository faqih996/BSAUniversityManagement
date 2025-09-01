<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DepartmentResource;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Inertia\Response;

class DepartmentController extends Controller
{

    public function index(): Response
    {
        $departments = Department::query()
            ->select(['id', 'faculty_id', 'name', 'code', 'slug', 'created_at'])
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->with('faculty')
            ->paginate(request()->load ?? 10);

        return inertia('Admin/Departments/Index', [
            'page_settings' => [
                'title' => 'Program Studi',
                'subtitle' => 'Menampilkan semua program studi yang tersedia pada universitas ini.',
            ],
            'departments' => DepartmentResource::collection($departments)->additional([
                'meta' => [
                    'has_pages' => $departments->hasPages(),
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
        return inertia('Admin/Departments/Create', props: [
            'page_settings' => [
                'title' => 'Tambah program studi',
                'subtitle' => 'Buat program studi baru disini. klik simpan setelah selesai',
                'method' => 'POST',
                'action' => route('admin.departments.store')
            ],
            'faculties' => Faculty::query()->select(['id', 'name'])->orderBy('name')->get()->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->name
            ]),
        ]);
    }
}
