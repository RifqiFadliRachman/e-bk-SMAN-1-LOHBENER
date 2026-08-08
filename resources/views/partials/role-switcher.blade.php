{{-- Demo Role Switcher Bar (RBAC Demo) --}}
@if(config('app.debug'))
<div class="bg-[#4E614C] text-[#F5F2EB] border-b border-[#3E4F3C] px-4 py-2 text-xs">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
        <div class="flex items-center gap-2 text-[#E6E2D3]">
            <span class="bg-[#8BA888]/30 text-[#E8F0E7] font-mono px-2.5 py-0.5 rounded-lg border border-[#8BA888]/40 font-semibold text-[11px] flex items-center gap-1.5">
                <i data-lucide="user-check" class="w-3.5 h-3.5 text-[#A2C49F]"></i> DEMO MULTI-PERAN (RBAC)
            </span>
            <span class="hidden md:inline text-[#D5CEBE]">
                Pilih peran pengguna untuk menguji workflow masing-masing aktor:
            </span>
        </div>

        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
            @foreach(\Illuminate\Support\Facades\DB::table('users')->get() as $u)
                @php
                    $isSelected = (Auth::id() ?? 1) == $u->id;
                    $roleLabel = $u->role === 'siswa' ? ($u->kelas ?? 'Siswa') : ($u->role === 'guru_bk' ? 'Guru BK' : 'Kepsek');
                @endphp
                <a
                    href="{{ route('demo.switch-role', $u->id) }}"
                    class="flex items-center gap-1.5 px-3 py-1 rounded-xl transition-all text-xs font-medium whitespace-nowrap {{ $isSelected ? 'bg-[#8BA888] text-white font-bold shadow-xs' : 'bg-[#3E4F3C]/60 text-[#E6E2D3] hover:bg-[#3E4F3C] hover:text-white border border-[#5A6D58]' }}"
                >
                    @if($u->role === 'siswa')
                        <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                    @elseif($u->role === 'guru_bk')
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    @else
                        <i data-lucide="school" class="w-3.5 h-3.5"></i>
                    @endif
                    <span>{{ explode(' ', $u->name)[0] }} ({{ $roleLabel }})</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif
