<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Services\HierarchyService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserForm extends Component
{
    use WithFileUploads;

    public ?int    $userId       = null;
    public string  $name         = '';
    public string  $email        = '';
    public string  $phone        = '';
    public string  $password     = '';
    public string  $role         = '';
    public ?int    $parentId     = null;
    public ?int    $asmId        = null;
    public string  $zone         = '';
    public string  $state        = '';
    public string  $city         = '';
    public string  $employeeCode = '';
    public bool    $isActive     = true;
    public         $profilePhoto = null;

    protected ?HierarchyService $hierarchy = null;

    public function boot(HierarchyService $hierarchy): void
    {
        $this->hierarchy = $hierarchy;
    }

    private function hierarchy(): HierarchyService
    {
        return $this->hierarchy ??= app(HierarchyService::class);
    }

    public function mount(?User $user = null): void
    {
        if ($user && $user->exists) {
            $this->userId       = $user->id;
            $this->name         = $user->name;
            $this->email        = $user->email;
            $this->phone        = $user->phone ?? '';
            $this->role         = $user->role;
            $this->parentId     = $user->parent_id;
            $this->asmId        = $user->asm_id;
            $this->zone         = $user->zone ?? '';
            $this->state        = $user->state ?? '';
            $this->city         = $user->city ?? '';
            $this->employeeCode = $user->employee_code ?? '';
            $this->isActive     = $user->is_active;
        }
    }

    public function save(): void
    {
        $rules = [
            'name'         => 'required|string|max:150',
            'email'        => 'required|email|unique:users,email' . ($this->userId ? ",{$this->userId}" : ''),
            'phone'        => 'nullable|string|max:20',
            'role'         => 'required|in:' . implode(',', $this->manageableRoles),
            'parentId'     => 'nullable|exists:users,id',
            'zone'         => 'nullable|in:north,south,east,west',
            'state'        => 'nullable|string|max:100',
            'city'         => 'nullable|string|max:100',
            'employeeCode' => 'nullable|string|max:50|unique:users,employee_code' . ($this->userId ? ",{$this->userId}" : ''),
            'profilePhoto' => 'nullable|image|max:2048',
        ];

        if (!$this->userId) {
            $rules['password'] = ['required', Password::min(8)->letters()->numbers()];
        }

        $this->validate($rules);

        $photoPath = $this->profilePhoto?->store('profile-photos', 'public');

        $data = [
            'name'          => $this->name,
            'email'         => $this->email,
            'phone'         => $this->phone ?: null,
            'role'          => $this->role,
            'parent_id'     => $this->parentId ?: null,
            'asm_id'        => ($this->role === 'ba_state_supervisor') ? $this->asmId : null,
            'zone'          => in_array($this->role, ['zsm','rsm','asm','tl','psm']) ? ($this->zone ?: null) : null,
            'state'         => $this->state ?: null,
            'city'          => $this->city ?: null,
            'employee_code' => $this->employeeCode ?: null,
            'is_active'     => $this->isActive,
        ];

        if ($photoPath) $data['profile_photo'] = $photoPath;
        if ($this->password) $data['password'] = Hash::make($this->password);

        $user = $this->userId
            ? tap(User::findOrFail($this->userId))->update($data)
            : User::create($data);

        // Flush cache for parent's downline
        if ($user->parent) $user->parent->flushDownlineCache();

        session()->flash('success', $this->userId ? 'Team member updated.' : 'Team member created successfully.');
        $this->redirect(route('users.index'), navigate: true);
    }

    public function getManageableRolesProperty(): array
    {
        return $this->hierarchy()->getManageableRoles(auth()->user());
    }

    public function getPotentialManagersProperty(): \Illuminate\Database\Eloquent\Collection
    {
        $myDownline = auth()->user()->getAllDownlineIds(includeSelf: true);
        return User::whereIn('id', $myDownline)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'role']);
    }

    public function render()
    {
        return view('livewire.users.user-form', [
            'manageableRoles'   => $this->manageableRoles,
            'potentialManagers' => $this->potentialManagers,
        ])->layout('layouts.app', ['title' => $this->userId ? 'Edit Team Member' : 'Add Team Member']);
    }
}
