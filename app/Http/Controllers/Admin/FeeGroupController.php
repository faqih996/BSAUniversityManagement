<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use App\Enums\MessageType;
use App\Http\Requests\Admin\FeeGroupRequest;
use App\Http\Resources\Admin\FeeGroupResource;
use App\Models\FeeGroup;
use PhpParser\Node\Stmt\TryCatch;
use Inertia\Inertia;
use Throwable;

class FeeGroupController extends Controller
{
    public function index(): Response
    {
        $feeGroups = FeeGroup::query()
            ->select(['id', 'amount', 'group', 'created_at'])
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->paginate(request()->load ?? 10);

        return inertia('Admin/FeeGroup/Index', [
            'page_settings' => [
                'title' => 'Golongan Biaya',
                'subtitle' => 'Menampilkan semua data golongan biaya yang tersedia pada universitas ini.',
            ],
            'feeGroups' => FeeGroupResource::collection($feeGroups)->additional([
                'meta' => [
                    'has_pages' => $feeGroups->hasPages(),
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
        return inertia('Admin/FeeGroup/Create', props: [
            'page_settings' => [
                'title' => 'Tambah Golongan',
                'subtitle' => 'Buat golongan baru disini. klik simpan setelah selesai',
                'method' => 'POST',
                'action' => route('admin.fee-groups.store')
            ],
        ]);
    }

    public function store(FeeGroupRequest $request): RedirectResponse
    {
        try {

            FeeGroup::create([
                'group' => $request->group,
                'amount' => $request->amount,
            ]);

            flashMessage(MessageType::CREATED->message('Golongan UKT'));

            return to_route('admin.fee-groups.index');
        } catch (Throwable $e) {

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.fee-groups.index');
        }
    }

    public function edit(FeeGroup $feeGroup): Response
    {
        return inertia('Admin/FeeGroup/Edit', props: [
            'page_settings' => [
                'title' => 'Edit Golongan',
                'subtitle' => 'Edit golongan ini disini. klik simpan setelah selesai',
                'method' => 'PUT',
                'action' => route('admin.fee-groups.update', $feeGroup)
            ],

            'feeGroup' => $feeGroup
        ]);
    }

    public function update(FeeGroupRequest $request, FeeGroup $feeGroup): RedirectResponse
    {
        try {

            $feeGroup->update([
                'group' => $request->group,
                'amount' => $request->amount,
            ]);

            flashMessage(MessageType::UPDATED->message('Golongan UKT'));

            return to_route('admin.fee-groups.index');
        } catch (Throwable $e) {

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.fee-groups.index');
        }
    }

    public function destroy(FeeGroup $feeGroup): RedirectResponse
    {
        try {

            $feeGroup->delete();

            flashMessage(MessageType::DELETED->message('Golongan UKT'));

            return to_route('admin.fee-groups.index');
        } catch (Throwable $e) {

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.fee-groups.index');
        }
    }
}
