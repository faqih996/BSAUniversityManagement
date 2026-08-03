<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Enums\MessageType;

use App\Http\Requests\Admin\OperatorRequest;
use App\Http\Resources\Admin\OperatorResource;

use App\Models\Operator;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;

use App\Traits\HasFile;

use PhpParser\Node\Stmt\TryCatch;

use Inertia\Response;
use Inertia\Inertia;
use Throwable;

class OperatorController extends Controller
{
    use HasFile;

    public function index(): Response
    {
        $operators = Operator::query()
            ->select(['operators.id', 'operators.user_id', 'operators.faculty_id', 'operators.department_id','operators.employee_number', 'operators.created_at'])
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->with(['user', 'faculty', 'department'])
            // this line to get data by student role only
            ->whereHas('user', function ($query) {
                $query->whereHas('roles', fn($query) => $query->where('name', 'Operator'));
            })
            ->paginate(request()->load ?? 10);

        return inertia('Admin/Operators/Index', [
            'page_settings' => [
                'title' => 'Operator',
                'subtitle' => 'Menampilkan semua data operator yang terdaftar pada universitas ini.',
            ],
            'operators' => OperatorResource::collection($operators)->additional([
                'meta' => [
                    'has_pages' => $operators->hasPages(),
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
        return inertia('Admin/Operators/Create', props: [
            'page_settings' => [
                'title' => 'Tambah Operator',
                'subtitle' => 'Buat operator baru disini. klik simpan setelah selesai',
                'method' => 'POST',
                'action' => route('admin.operators.store')
            ],
            'faculties' => Faculty::query()->select(['id', 'name'])->orderBy('name')->get()->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->name
            ]),
            'departments' => Department::query()->select(['id', 'name'])->orderBy('name')->get()->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->name
            ]),
        ]);
    }

    public function store(OperatorRequest $request): RedirectResponse
    {
        try {

            DB::beginTransaction();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'avatar' => $this->upload_file($request, 'avatar', 'users')
            ]);

            $user->operator()->create([
                'faculty_id' => $request->faculty_id,
                'department_id' => $request->department_id,
                'employee_number' => $request->employee_number,
            ]);

            $user->assignRole('Operator');
            DB::commit();
            flashMessage(MessageType::CREATED->message('Operator'));

            return to_route('admin.operators.index');
        } catch (Throwable $e) {

            DB::rollBack();
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.operators.index');
        }
    }

    public function edit(Operator $operator): Response
    {
        return inertia('Admin/Operators/Edit', props: [
            'page_settings' => [
                'title' => 'Edit Operator',
                'subtitle' => 'Edit operator disini. klik simpan setelah selesai',
                'method' => 'PUT',
                'action' => route('admin.operators.update', $operator)
            ],
            'operator' => $operator->load('user'),
            'faculties' => Faculty::query()->select(['id', 'name'])->orderBy('name')->get()->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->name
            ]),
            'departments' => Department::query()->select(['id', 'name'])->orderBy('name')->get()->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->name
            ]),
        ]);
    }

    public function update(Operator $operator, OperatorRequest $request): RedirectResponse
    {
        try {

            DB::beginTransaction();

            $operator->update([
                'faculty_id' => $request->faculty_id,
                'department_id' => $request->department_id,
                'employee_number' => $request->employee_number,
            ]);

            $operator->user()->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : $operator->user->password,
                'avatar' => $this->update_file($request, $operator->user, 'avatar', 'users')
            ]);

            DB::commit();
            flashMessage(MessageType::UPDATED->message('Operator'));

            return to_route('admin.operators.index');
        } catch (Throwable $e) {

            DB::rollBack();
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.operators.index');
        }
    }

    public function destroy(Operator $operator): RedirectResponse
    {
        try {

            $this->delete_file($operator->user, 'avatar');
            $operator->delete();

            flashMessage(MessageType::DELETED->message('Mahasiswa'));

            return to_route('admin.operators.index');
        } catch (Throwable $e) {

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.operators.index');
        }
    }
}
