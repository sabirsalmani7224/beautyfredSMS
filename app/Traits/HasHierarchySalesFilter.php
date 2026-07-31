<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Collection;

trait HasHierarchySalesFilter
{
    // ── Sales Hierarchy Track ──────────────────────────────────────────────
    public ?int $selectedZsmId = null;
    public ?int $selectedRsmId = null;
    public ?int $selectedAsmId = null;
    public ?int $selectedTlId  = null;
    public ?int $selectedPsmId = null;

    // ── BA Hierarchy Track ─────────────────────────────────────────────────
    public ?int $selectedBaNationalHeadId    = null;
    public ?int $selectedBaStateSupervisorId = null;
    public ?int $selectedBaId               = null;

    // ── Updated Hooks: Sales Track ────────────────────────────────────────

    public function updatedSelectedZsmId(): void
    {
        $this->selectedRsmId = null;
        $this->selectedAsmId = null;
        $this->selectedTlId  = null;
        $this->selectedPsmId = null;
        $this->resetBaTrack();
        if (method_exists($this, 'resetPage')) $this->resetPage();
    }

    public function updatedSelectedRsmId(): void
    {
        $this->selectedAsmId = null;
        $this->selectedTlId  = null;
        $this->selectedPsmId = null;
        $this->resetBaTrack();
        if (method_exists($this, 'resetPage')) $this->resetPage();
    }

    public function updatedSelectedAsmId(): void
    {
        $this->selectedTlId  = null;
        $this->selectedPsmId = null;
        $this->resetBaTrack();
        if (method_exists($this, 'resetPage')) $this->resetPage();
    }

    public function updatedSelectedTlId(): void
    {
        $this->selectedPsmId = null;
        $this->resetBaTrack();
        if (method_exists($this, 'resetPage')) $this->resetPage();
    }

    public function updatedSelectedPsmId(): void
    {
        $this->resetBaTrack();
        if (method_exists($this, 'resetPage')) $this->resetPage();
    }

    // ── Updated Hooks: BA Track ───────────────────────────────────────────

    public function updatedSelectedBaNationalHeadId(): void
    {
        $this->selectedBaStateSupervisorId = null;
        $this->selectedBaId               = null;
        $this->resetSalesTrack();
        if (method_exists($this, 'resetPage')) $this->resetPage();
    }

    public function updatedSelectedBaStateSupervisorId(): void
    {
        $this->selectedBaId = null;
        $this->resetSalesTrack();
        if (method_exists($this, 'resetPage')) $this->resetPage();
    }

    public function updatedSelectedBaId(): void
    {
        $this->resetSalesTrack();
        if (method_exists($this, 'resetPage')) $this->resetPage();
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function resetBaTrack(): void
    {
        $this->selectedBaNationalHeadId    = null;
        $this->selectedBaStateSupervisorId = null;
        $this->selectedBaId               = null;
    }

    private function resetSalesTrack(): void
    {
        $this->selectedZsmId = null;
        $this->selectedRsmId = null;
        $this->selectedAsmId = null;
        $this->selectedTlId  = null;
        $this->selectedPsmId = null;
    }

    public function resetHierarchyFilters(): void
    {
        $this->resetSalesTrack();
        $this->resetBaTrack();
        if (method_exists($this, 'resetPage')) $this->resetPage();
    }

    // ── Core: Build the filtered user ID list ────────────────────────────

    /**
     * Get user IDs to scope sales queries, honouring whichever track (Sales or BA) is active.
     */
    public function getFilteredUserIds(): array
    {
        $authUser = auth()->user();

        // ── BA Track selection ─────────────────────────────────────────
        if ($this->selectedBaId) {
            return [$this->selectedBaId];
        }

        if ($this->selectedBaStateSupervisorId) {
            $baSS = User::find($this->selectedBaStateSupervisorId);
            return $baSS ? $baSS->getAllDownlineIds(includeSelf: true) : [$this->selectedBaStateSupervisorId];
        }

        if ($this->selectedBaNationalHeadId) {
            $baNH = User::find($this->selectedBaNationalHeadId);
            return $baNH ? $baNH->getAllDownlineIds(includeSelf: true) : [$this->selectedBaNationalHeadId];
        }

        // ── Sales Track selection ──────────────────────────────────────
        if ($this->selectedPsmId) {
            return [$this->selectedPsmId];
        }

        if ($this->selectedTlId) {
            $tl = User::find($this->selectedTlId);
            return $tl ? $tl->getAllDownlineIds(includeSelf: true) : [$this->selectedTlId];
        }

        if ($this->selectedAsmId) {
            $asm = User::find($this->selectedAsmId);
            return $asm ? $asm->getAllDownlineIds(includeSelf: true) : [$this->selectedAsmId];
        }

        if ($this->selectedRsmId) {
            $rsm = User::find($this->selectedRsmId);
            return $rsm ? $rsm->getAllDownlineIds(includeSelf: true) : [$this->selectedRsmId];
        }

        if ($this->selectedZsmId) {
            $zsm = User::find($this->selectedZsmId);
            return $zsm ? $zsm->getAllDownlineIds(includeSelf: true) : [$this->selectedZsmId];
        }

        // ── Default: full downline of auth user ───────────────────────
        return $authUser->getAllDownlineIds(includeSelf: true);
    }

    // ── Build filter option lists ─────────────────────────────────────────

    /**
     * Returns showXxx booleans and option collections for both tracks.
     */
    public function getHierarchyFilterOptions(): array
    {
        $authUser = auth()->user();

        // ── Sales Track Visibility ─────────────────────────────────────
        $showZsm = $authUser->isTopManagement();
        $showRsm = $authUser->isTopManagement() || $authUser->isZSM();
        $showAsm = $authUser->isTopManagement() || $authUser->isZSM() || $authUser->isRSM();
        $showTl  = $authUser->isTopManagement() || $authUser->isZSM() || $authUser->isRSM() || $authUser->isASM();
        $showPsm = $authUser->isTopManagement() || $authUser->isZSM() || $authUser->isRSM() || $authUser->isASM() || $authUser->isTL();

        // ── BA Track Visibility ────────────────────────────────────────
        // BA National Head filter: NSM + top management only
        $showBaNH  = $authUser->isNSM() || in_array($authUser->role, ['super_admin', 'md', 'ceo']);

        // BA State Supervisor filter: ASM and above + BA National Head
        $showBaSS  = $authUser->isASM() || $authUser->isRSM() || $authUser->isZSM()
                  || $authUser->isNSM() || $authUser->isBANationalHead()
                  || in_array($authUser->role, ['super_admin', 'md', 'ceo']);

        // Individual BA filter: BA State Supervisors + everyone above them
        $showBa    = $authUser->isBAStateSupervisor() || $authUser->isASM() || $authUser->isRSM()
                  || $authUser->isZSM() || $authUser->isNSM() || $authUser->isBANationalHead()
                  || in_array($authUser->role, ['super_admin', 'md', 'ceo']);

        // ── Build option lists: Sales Track ───────────────────────────

        // 1. ZSM
        $zsmOptions = collect();
        if ($showZsm) {
            $zsmOptions = User::where('role', 'zsm')->active()->orderBy('name')->get();
        }

        // 2. RSM (filtered under selected ZSM if any)
        $rsmOptions = collect();
        if ($showRsm) {
            if ($this->selectedZsmId) {
                $zsm = User::find($this->selectedZsmId);
                $dl  = $zsm ? $zsm->getAllDownlineIds(includeSelf: false) : [];
                $rsmOptions = User::whereIn('id', $dl)->where('role', 'rsm')->active()->orderBy('name')->get();
            } else {
                $rootIds    = $authUser->getAllDownlineIds(includeSelf: false);
                $rsmOptions = $authUser->isTopManagement()
                    ? User::where('role', 'rsm')->active()->orderBy('name')->get()
                    : User::whereIn('id', $rootIds)->where('role', 'rsm')->active()->orderBy('name')->get();
            }
        }

        // 3. ASM
        $asmOptions = collect();
        if ($showAsm) {
            if ($this->selectedRsmId) {
                $rsm = User::find($this->selectedRsmId);
                $dl  = $rsm ? $rsm->getAllDownlineIds(includeSelf: false) : [];
                $asmOptions = User::whereIn('id', $dl)->where('role', 'asm')->active()->orderBy('name')->get();
            } elseif ($this->selectedZsmId) {
                $zsm = User::find($this->selectedZsmId);
                $dl  = $zsm ? $zsm->getAllDownlineIds(includeSelf: false) : [];
                $asmOptions = User::whereIn('id', $dl)->where('role', 'asm')->active()->orderBy('name')->get();
            } else {
                $rootIds    = $authUser->getAllDownlineIds(includeSelf: false);
                $asmOptions = $authUser->isTopManagement()
                    ? User::where('role', 'asm')->active()->orderBy('name')->get()
                    : User::whereIn('id', $rootIds)->where('role', 'asm')->active()->orderBy('name')->get();
            }
        }

        // 4. TL
        $tlOptions = collect();
        if ($showTl) {
            if ($this->selectedAsmId) {
                $asm = User::find($this->selectedAsmId);
                $dl  = $asm ? $asm->getAllDownlineIds(includeSelf: false) : [];
                $tlOptions = User::whereIn('id', $dl)->where('role', 'tl')->active()->orderBy('name')->get();
            } elseif ($this->selectedRsmId) {
                $rsm = User::find($this->selectedRsmId);
                $dl  = $rsm ? $rsm->getAllDownlineIds(includeSelf: false) : [];
                $tlOptions = User::whereIn('id', $dl)->where('role', 'tl')->active()->orderBy('name')->get();
            } elseif ($this->selectedZsmId) {
                $zsm = User::find($this->selectedZsmId);
                $dl  = $zsm ? $zsm->getAllDownlineIds(includeSelf: false) : [];
                $tlOptions = User::whereIn('id', $dl)->where('role', 'tl')->active()->orderBy('name')->get();
            } else {
                $rootIds   = $authUser->getAllDownlineIds(includeSelf: false);
                $tlOptions = $authUser->isTopManagement()
                    ? User::where('role', 'tl')->active()->orderBy('name')->get()
                    : User::whereIn('id', $rootIds)->where('role', 'tl')->active()->orderBy('name')->get();
            }
        }

        // 5. PSM (under TL > ASM > RSM > ZSM chain)
        $psmOptions = collect();
        if ($showPsm) {
            if ($this->selectedTlId) {
                $tl = User::find($this->selectedTlId);
                $dl = $tl ? $tl->getAllDownlineIds(includeSelf: false) : [];
                $psmOptions = User::whereIn('id', $dl)->where('role', 'psm')->active()->orderBy('name')->get();
            } elseif ($this->selectedAsmId) {
                $asm = User::find($this->selectedAsmId);
                $dl  = $asm ? $asm->getAllDownlineIds(includeSelf: false) : [];
                $psmOptions = User::whereIn('id', $dl)->where('role', 'psm')->active()->orderBy('name')->get();
            } elseif ($this->selectedRsmId) {
                $rsm = User::find($this->selectedRsmId);
                $dl  = $rsm ? $rsm->getAllDownlineIds(includeSelf: false) : [];
                $psmOptions = User::whereIn('id', $dl)->where('role', 'psm')->active()->orderBy('name')->get();
            } elseif ($this->selectedZsmId) {
                $zsm = User::find($this->selectedZsmId);
                $dl  = $zsm ? $zsm->getAllDownlineIds(includeSelf: false) : [];
                $psmOptions = User::whereIn('id', $dl)->where('role', 'psm')->active()->orderBy('name')->get();
            } else {
                $rootIds    = $authUser->getAllDownlineIds(includeSelf: false);
                $psmOptions = $authUser->isTopManagement()
                    ? User::where('role', 'psm')->active()->orderBy('name')->get()
                    : User::whereIn('id', $rootIds)->where('role', 'psm')->active()->orderBy('name')->get();
            }
        }

        // ── Build option lists: BA Track ──────────────────────────────

        // 6. BA National Head options (NSM / Top Management)
        $baNHOptions = collect();
        if ($showBaNH) {
            $baNHOptions = User::where('role', 'ba_national_head')->active()->orderBy('name')->get();
        }

        // 7. BA State Supervisor options (filtered under selected BA National Head if any)
        $baSSOptions = collect();
        if ($showBaSS) {
            if ($this->selectedBaNationalHeadId) {
                $baNH = User::find($this->selectedBaNationalHeadId);
                $dl   = $baNH ? $baNH->getAllDownlineIds(includeSelf: false) : [];
                $baSSOptions = User::whereIn('id', $dl)->where('role', 'ba_state_supervisor')->active()->orderBy('name')->get();
            } elseif ($authUser->isBANationalHead()) {
                // BA National Head sees their own state supervisors
                $dl  = $authUser->getAllDownlineIds(includeSelf: false);
                $baSSOptions = User::whereIn('id', $dl)->where('role', 'ba_state_supervisor')->active()->orderBy('name')->get();
            } else {
                // ASM and above: fetch all State Supervisors in their downline
                $rootIds = $authUser->getAllDownlineIds(includeSelf: false);
                $baSSOptions = ($authUser->isTopManagement() || $authUser->isNSM())
                    ? User::where('role', 'ba_state_supervisor')->active()->orderBy('name')->get()
                    : User::whereIn('id', $rootIds)->where('role', 'ba_state_supervisor')->active()->orderBy('name')->get();
            }
        }

        // 8. Individual BA options (filtered under selected BASS or BANH if any)
        $baOptions = collect();
        if ($showBa) {
            if ($this->selectedBaStateSupervisorId) {
                $baSS = User::find($this->selectedBaStateSupervisorId);
                $dl   = $baSS ? $baSS->getAllDownlineIds(includeSelf: false) : [];
                $baOptions = User::whereIn('id', $dl)->where('role', 'ba')->active()->orderBy('name')->get();
            } elseif ($this->selectedBaNationalHeadId) {
                $baNH = User::find($this->selectedBaNationalHeadId);
                $dl   = $baNH ? $baNH->getAllDownlineIds(includeSelf: false) : [];
                $baOptions = User::whereIn('id', $dl)->where('role', 'ba')->active()->orderBy('name')->get();
            } elseif ($authUser->isBAStateSupervisor()) {
                // BA State Supervisor sees their own BAs
                $dl       = $authUser->getAllDownlineIds(includeSelf: false);
                $baOptions = User::whereIn('id', $dl)->where('role', 'ba')->active()->orderBy('name')->get();
            } elseif ($authUser->isBANationalHead()) {
                $dl       = $authUser->getAllDownlineIds(includeSelf: false);
                $baOptions = User::whereIn('id', $dl)->where('role', 'ba')->active()->orderBy('name')->get();
            } else {
                $rootIds  = $authUser->getAllDownlineIds(includeSelf: false);
                $baOptions = ($authUser->isTopManagement() || $authUser->isNSM())
                    ? User::where('role', 'ba')->active()->orderBy('name')->get()
                    : User::whereIn('id', $rootIds)->where('role', 'ba')->active()->orderBy('name')->get();
            }
        }

        return [
            // Sales Track
            'showZsm'    => $showZsm,
            'showRsm'    => $showRsm,
            'showAsm'    => $showAsm,
            'showTl'     => $showTl,
            'showPsm'    => $showPsm,
            'zsmOptions' => $zsmOptions,
            'rsmOptions' => $rsmOptions,
            'asmOptions' => $asmOptions,
            'tlOptions'  => $tlOptions,
            'psmOptions' => $psmOptions,

            // BA Track
            'showBaNH'    => $showBaNH,
            'showBaSS'    => $showBaSS,
            'showBa'      => $showBa,
            'baNHOptions' => $baNHOptions,
            'baSSOptions' => $baSSOptions,
            'baOptions'   => $baOptions,
        ];
    }
}
