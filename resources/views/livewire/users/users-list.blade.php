<div class="space-y-5 animate-fade-up">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-display text-2xl font-bold text-white">Team Members</h2>
            <p class="text-white/40 text-sm mt-1">Manage your downline team hierarchy</p>
        </div>
        @if(count($manageableRoles) > 0)
        <a href="{{ route('users.create') }}" wire:navigate
           class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity shadow-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Member
        </a>
        @endif
    </div>

    {{-- Role Count Badges --}}
    <div class="flex flex-wrap gap-2">
        @foreach($roleCounts as $role => $count)
        <button wire:click="$set('role', '{{ $role }}')"
                class="px-3 py-1.5 rounded-full text-xs font-medium border transition-all
                       {{ $role === $role ? 'border-[#C9A84C]/40 bg-[#C9A84C]/10 text-[#C9A84C]' : 'border-white/10 text-white/50 hover:border-white/30' }}">
            {{ \App\Models\User::make(['role' => $role])->getRoleLabel() }}: {{ $count }}
        </button>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="glass-card p-4 flex flex-wrap gap-3">
        <div class="flex-1 min-w-[200px] relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search name, email, phone, code..."
                   class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/50">
        </div>
        <select wire:model.live="role" class="bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
            <option value="">All Roles</option>
            @foreach($manageableRoles as $r)
            <option value="{{ $r }}">{{ \App\Models\User::make(['role' => $r])->getRoleLabel() }}</option>
            @endforeach
        </select>
        <select wire:model.live="zone" class="bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-[#C9A84C]/50">
            <option value="">All Zones</option>
            <option value="north">North</option>
            <option value="south">South</option>
            <option value="east">East</option>
            <option value="west">West</option>
        </select>
        <label class="flex items-center gap-2 text-sm text-white/50 cursor-pointer">
            <input wire:model.live="showInactive" type="checkbox" class="rounded border-white/20 bg-white/5 text-[#C9A84C]">
            Show Inactive
        </label>
    </div>

    {{-- Users Table --}}
    <div class="glass-card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Member</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Role</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Reports To</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Zone/State</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Code</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Status</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-white/40 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($users as $member)
                <tr class="hover:bg-white/3 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <img src="{{ $member->avatar_url }}" class="w-8 h-8 rounded-lg flex-shrink-0">
                            <div>
                                <p class="font-medium text-white text-sm">{{ $member->name }}</p>
                                <p class="text-xs text-white/40">{{ $member->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="badge badge-gold text-xs">{{ $member->role_label }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-white/50 text-xs">{{ $member->parent?->name ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-white/50 text-xs">
                        {{ $member->zone_label !== '—' ? $member->zone_label : ($member->state ?? '—') }}
                    </td>
                    <td class="px-5 py-3.5 text-white/40 text-xs font-mono">{{ $member->employee_code ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-center">
                        @if($member->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('users.edit', $member) }}" wire:navigate
                               class="text-white/40 hover:text-[#C9A84C] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <button wire:click="toggleActive({{ $member->id }})"
                                    class="{{ $member->is_active ? 'text-white/40 hover:text-rose-400' : 'text-white/40 hover:text-emerald-400' }} transition-colors">
                                @if($member->is_active)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center text-white/30">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        No team members found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-white/10">{{ $users->links() }}</div>
        @endif
    </div>
</div>
