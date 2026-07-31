<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Services\HierarchyService;
use Livewire\Component;
use Livewire\WithPagination;

class UsersList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $role   = '';
    public string $zone   = '';
    public bool   $showInactive = false;

    protected ?HierarchyService $hierarchy = null;

    public function boot(HierarchyService $hierarchy): void
    {
        $this->hierarchy = $hierarchy;
    }

    private function hierarchy(): HierarchyService
    {
        return $this->hierarchy ??= app(HierarchyService::class);
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function toggleActive(int $userId): void
    {
        $target = User::findOrFail($userId);
        if (!auth()->user()->hasInDownline($userId)) abort(403);

        $target->update(['is_active' => !$target->is_active]);
        $target->flushDownlineCache();
    }

    public function render()
    {
        $user        = auth()->user();
        $downlineIds = $user->getAllDownlineIds(includeSelf: false);

        $users = User::whereIn('id', $downlineIds)
            ->with('parent')
            ->when($this->search, fn($q) =>
                $q->where('name', 'ilike', "%{$this->search}%")
                  ->orWhere('email', 'ilike', "%{$this->search}%")
                  ->orWhere('employee_code', 'ilike', "%{$this->search}%")
                  ->orWhere('phone', 'ilike', "%{$this->search}%")
            )
            ->when($this->role, fn($q) => $q->where('role', $this->role))
            ->when($this->zone, fn($q) => $q->where('zone', $this->zone))
            ->when(!$this->showInactive, fn($q) => $q->where('is_active', true))
            ->orderBy('role')->orderBy('name')
            ->paginate(25);

        $manageableRoles = $this->hierarchy()->getManageableRoles($user);

        $roleCounts = User::whereIn('id', $downlineIds)
            ->selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role');

        return view('livewire.users.users-list', compact('users', 'manageableRoles', 'roleCounts'))
            ->layout('layouts.app', ['title' => 'Team Members']);
    }
}
