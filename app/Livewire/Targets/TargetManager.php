<?php

namespace App\Livewire\Targets;

use App\Models\Target;
use App\Models\User;
use App\Models\Category;
use App\Services\HierarchyService;
use Livewire\Component;

class TargetManager extends Component
{
    public int    $targetMonth = 0;
    public int    $targetYear  = 0;
    public int    $selectedUserId  = 0;
    public int    $selectedCatId   = 0;
    public float  $targetAmount    = 0;
    public int    $targetQty       = 0;
    public string $notes           = '';
    public bool   $showForm        = false;

    protected ?HierarchyService $hierarchy = null;

    public function boot(HierarchyService $hierarchy): void
    {
        $this->hierarchy = $hierarchy;
    }

    public function mount(): void
    {
        $this->targetMonth = now()->month;
        $this->targetYear  = now()->year;
    }

    private function hierarchy(): HierarchyService
    {
        return $this->hierarchy ??= app(HierarchyService::class);
    }

    public function saveTarget(): void
    {
        $this->validate([
            'selectedUserId' => 'required|exists:users,id',
            'targetMonth'    => 'required|integer|between:1,12',
            'targetYear'     => 'required|integer|min:2024',
            'targetAmount'   => 'required|numeric|min:0',
        ]);

        Target::updateOrCreate(
            [
                'user_id'     => $this->selectedUserId,
                'category_id' => $this->selectedCatId ?: null,
                'month'       => $this->targetMonth,
                'year'        => $this->targetYear,
            ],
            [
                'set_by_user_id' => auth()->id(),
                'target_amount'  => $this->targetAmount,
                'target_quantity'=> $this->targetQty ?: null,
                'notes'          => $this->notes ?: null,
            ]
        );

        $this->showForm    = false;
        $this->targetAmount = 0;
        $this->targetQty   = 0;
        session()->flash('success', 'Target saved.');
    }

    public function render()
    {
        $user        = auth()->user();
        $downlineIds = $user->getAllDownlineIds(includeSelf: false);

        $directReports  = $this->hierarchy()->getDirectReports($user);
        $categories     = Category::active()->get(['id','name']);

        $targets = Target::with(['user','category'])
            ->whereIn('user_id', $downlineIds)
            ->where('month', $this->targetMonth)
            ->where('year', $this->targetYear)
            ->get();

        $directRankings = $this->hierarchy()->getDirectReportRankings($user, 'month');

        return view('livewire.targets.target-manager', compact(
            'directReports', 'categories', 'targets', 'directRankings'
        ))->layout('layouts.app', ['title' => 'Targets']);
    }
}
