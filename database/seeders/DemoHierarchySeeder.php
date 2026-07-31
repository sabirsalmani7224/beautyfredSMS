<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoHierarchySeeder extends Seeder
{
    public function run(): void
    {
        // ── Top Management ─────────────────────────────────────────────────
        $md = User::where('role', 'md')->first();

        $nsm = User::updateOrCreate(['email' => 'nsm@beautyfed.in'], [
            'name'          => 'Rahul Sharma',
            'phone'         => '9800000001',
            'role'          => 'nsm',
            'parent_id'     => $md->id,
            'employee_code' => 'NSM-001',
            'password'      => Hash::make('Beautyfed@NSM2024!'),
            'is_active'     => true,
        ]);

        // ── BA National Head ───────────────────────────────────────────────
        $baNH = User::updateOrCreate(['email' => 'ba.head@beautyfed.in'], [
            'name'          => 'Priya Verma',
            'phone'         => '9800000009',
            'role'          => 'ba_national_head',
            'parent_id'     => $nsm->id,
            'employee_code' => 'BAH-001',
            'password'      => Hash::make('Beautyfed@BA2024!'),
            'is_active'     => true,
        ]);

        // ── Zonal Sales Managers ───────────────────────────────────────────
        $zones = [
            ['name' => 'Vikram Mehta',  'email' => 'zsm.north@beautyfed.in', 'zone' => 'north', 'code' => 'ZSM-N01'],
            ['name' => 'Sunita Patel',  'email' => 'zsm.south@beautyfed.in', 'zone' => 'south', 'code' => 'ZSM-S01'],
            ['name' => 'Arjun Kumar',   'email' => 'zsm.east@beautyfed.in',  'zone' => 'east',  'code' => 'ZSM-E01'],
            ['name' => 'Deepa Singh',   'email' => 'zsm.west@beautyfed.in',  'zone' => 'west',  'code' => 'ZSM-W01'],
        ];

        $zsmMap = [];
        foreach ($zones as $z) {
            $zsmMap[$z['zone']] = User::updateOrCreate(['email' => $z['email']], [
                'name'          => $z['name'],
                'phone'         => '9810' . rand(100000, 999999),
                'role'          => 'zsm',
                'parent_id'     => $nsm->id,
                'zone'          => $z['zone'],
                'employee_code' => $z['code'],
                'password'      => Hash::make('Beautyfed@ZSM2024!'),
                'is_active'     => true,
            ]);
        }

        // ── Regional Sales Managers (2 per zone) ──────────────────────────
        $regions = [
            // North
            ['name' => 'Amit Kapoor',  'email' => 'rsm.delhi@beautyfed.in',   'zone' => 'north', 'state' => 'Delhi',     'code' => 'RSM-N01'],
            ['name' => 'Nisha Gupta',  'email' => 'rsm.punjab@beautyfed.in',  'zone' => 'north', 'state' => 'Punjab',    'code' => 'RSM-N02'],
            // South
            ['name' => 'Kavya Nair',   'email' => 'rsm.kerala@beautyfed.in',  'zone' => 'south', 'state' => 'Kerala',    'code' => 'RSM-S01'],
            ['name' => 'Ravi Krishnan','email' => 'rsm.tamilnadu@beautyfed.in','zone' => 'south', 'state' => 'Tamil Nadu','code' => 'RSM-S02'],
            // West
            ['name' => 'Pooja Shah',   'email' => 'rsm.mumbai@beautyfed.in',  'zone' => 'west',  'state' => 'Maharashtra','code'=> 'RSM-W01'],
            // East
            ['name' => 'Suresh Das',   'email' => 'rsm.kolkata@beautyfed.in', 'zone' => 'east',  'state' => 'West Bengal','code'=> 'RSM-E01'],
        ];

        $rsmMap = [];
        foreach ($regions as $r) {
            $rsmMap[$r['code']] = User::updateOrCreate(['email' => $r['email']], [
                'name'          => $r['name'],
                'phone'         => '9820' . rand(100000, 999999),
                'role'          => 'rsm',
                'parent_id'     => $zsmMap[$r['zone']]->id,
                'zone'          => $r['zone'],
                'state'         => $r['state'],
                'employee_code' => $r['code'],
                'password'      => Hash::make('Beautyfed@RSM2024!'),
                'is_active'     => true,
            ]);
        }

        // ── Area Sales Managers (pick 3 RSMs to have ASMs) ────────────────
        $asmData = [
            ['name' => 'Meena Joshi',    'email' => 'asm.delhi1@beautyfed.in',  'rsm' => 'RSM-N01', 'state' => 'Delhi',       'city' => 'New Delhi',  'code' => 'ASM-N01'],
            ['name' => 'Rahul Tiwari',   'email' => 'asm.mumbai1@beautyfed.in', 'rsm' => 'RSM-W01', 'state' => 'Maharashtra', 'city' => 'Mumbai',     'code' => 'ASM-W01'],
            ['name' => 'Lakshmi Menon',  'email' => 'asm.kerala1@beautyfed.in', 'rsm' => 'RSM-S01', 'state' => 'Kerala',      'city' => 'Kochi',      'code' => 'ASM-S01'],
        ];

        $asmMap = [];
        foreach ($asmData as $a) {
            $asmMap[$a['code']] = User::updateOrCreate(['email' => $a['email']], [
                'name'          => $a['name'],
                'phone'         => '9830' . rand(100000, 999999),
                'role'          => 'asm',
                'parent_id'     => $rsmMap[$a['rsm']]->id,
                'state'         => $a['state'],
                'city'          => $a['city'],
                'employee_code' => $a['code'],
                'password'      => Hash::make('Beautyfed@ASM2024!'),
                'is_active'     => true,
            ]);
        }

        // ── Team Leaders ──────────────────────────────────────────────────
        $tlData = [
            ['name' => 'Sanjay Mishra',  'email' => 'tl.delhi1@beautyfed.in',  'asm' => 'ASM-N01', 'code' => 'TL-N01'],
            ['name' => 'Anjali Sharma',  'email' => 'tl.mumbai1@beautyfed.in', 'asm' => 'ASM-W01', 'code' => 'TL-W01'],
            ['name' => 'Vivek Thomas',   'email' => 'tl.kerala1@beautyfed.in', 'asm' => 'ASM-S01', 'code' => 'TL-S01'],
        ];

        $tlMap = [];
        foreach ($tlData as $t) {
            $asm = $asmMap[$t['asm']];
            $tlMap[$t['code']] = User::updateOrCreate(['email' => $t['email']], [
                'name'          => $t['name'],
                'phone'         => '9840' . rand(100000, 999999),
                'role'          => 'tl',
                'parent_id'     => $asm->id,
                'state'         => $asm->state,
                'city'          => $asm->city,
                'employee_code' => $t['code'],
                'password'      => Hash::make('Beautyfed@TL2024!'),
                'is_active'     => true,
            ]);
        }

        // ── Product Sales Managers (field staff) ──────────────────────────
        $psmData = [
            ['name' => 'Rohit Yadav',    'email' => 'psm.delhi1@beautyfed.in',  'tl' => 'TL-N01', 'code' => 'PSM-N01'],
            ['name' => 'Preeti Sharma',  'email' => 'psm.delhi2@beautyfed.in',  'tl' => 'TL-N01', 'code' => 'PSM-N02'],
            ['name' => 'Kiran Patil',    'email' => 'psm.mumbai1@beautyfed.in', 'tl' => 'TL-W01', 'code' => 'PSM-W01'],
            ['name' => 'Shreya Nair',    'email' => 'psm.kerala1@beautyfed.in', 'tl' => 'TL-S01', 'code' => 'PSM-S01'],
        ];

        foreach ($psmData as $p) {
            $tl = $tlMap[$p['tl']];
            User::updateOrCreate(['email' => $p['email']], [
                'name'          => $p['name'],
                'phone'         => '9850' . rand(100000, 999999),
                'role'          => 'psm',
                'parent_id'     => $tl->id,
                'state'         => $tl->state,
                'city'          => $tl->city,
                'employee_code' => $p['code'],
                'password'      => Hash::make('Beautyfed@PSM2024!'),
                'is_active'     => true,
            ]);
        }

        // ── BA State Supervisors ──────────────────────────────────────────
        $baSupData = [
            ['name' => 'Ananya Iyer',    'email' => 'ba.sup.delhi@beautyfed.in',  'asm_code' => 'ASM-N01', 'code' => 'BAS-N01'],
            ['name' => 'Megha Pillai',   'email' => 'ba.sup.kerala@beautyfed.in', 'asm_code' => 'ASM-S01', 'code' => 'BAS-S01'],
        ];

        foreach ($baSupData as $b) {
            User::updateOrCreate(['email' => $b['email']], [
                'name'          => $b['name'],
                'phone'         => '9860' . rand(100000, 999999),
                'role'          => 'ba_state_supervisor',
                'parent_id'     => $baNH->id,
                'asm_id'        => $asmMap[$b['asm_code']]->id,
                'employee_code' => $b['code'],
                'password'      => Hash::make('Beautyfed@BA2024!'),
                'is_active'     => true,
            ]);
        }

        $this->command->info('✅ Demo hierarchy seeded: NSM → 4 ZSMs → 6 RSMs → 3 ASMs → 3 TLs → 4 PSMs → 2 BA Supervisors');
        $this->command->info('   Total users: ' . User::count());
    }
}
