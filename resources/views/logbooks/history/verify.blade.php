@php
    $user = Auth::user();

    $search = request('vh_search', '');
    $status = request('vh_status', '');
    $from_date = request('vh_from_date', '');
    $to_date = request('vh_to_date', '');
    $perPage = request('vh_per_page', 10);
    
    $query = App\Models\Logbook::query()
        ->with(['user', 'verifiedBy'])
        ->where('verified_by', $user->id)
        ->whereIn('verification_status', ['disetujui', 'ditolak']);
    
    switch ($user->role) {
        case 'Kepala Dinas':
            $query->whereHas('user', function($q) {
                $q->whereIn('role', ['Kepala Bagian 1', 'Kepala Bagian 2']);
            });
            break;

        case 'Kepala Bagian 1':
            $query->whereHas('user', function($q) {
                $q->where('role', 'Staf Bagian 1');
            });
            break;

        case 'Kepala Bagian 2':
            $query->whereHas('user', function($q) {
                $q->where('role', 'Staf Bagian 2');
            });
            break;
            
        default:
            $query->where('id', 0);
            break;
    }
    
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('content', 'LIKE', "%{$search}%")
              ->orWhereHas('user', function($userQuery) use ($search) {
                  $userQuery->where('name', 'LIKE', "%{$search}%");
              });
        });
    }
    
    if ($status) {
        $query->where('verification_status', $status);
    }
    
    if ($from_date) {
        $query->where('updated_at', '>=', $from_date . ' 00:00:00');
    }
    
    if ($to_date) {
        $query->where('updated_at', '<=', $to_date . ' 23:59:59');
    }
    
    $logbooks = $query->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'vh_page');
    
    $verificationParams = array_filter([
        'vh_search' => $search,
        'vh_status' => $status,
        'vh_from_date' => $from_date,
        'vh_to_date' => $to_date,
        'vh_per_page' => $perPage,
    ]);
    
    $myLogbooksParams = array_filter([
        'my_search' => request('my_search'),
        'my_status' => request('my_status'),
        'my_from_date' => request('my_from_date'),
        'my_to_date' => request('my_to_date'),
        'my_per_page' => request('my_per_page'),
    ]);
    
    $logbooks->appends(array_merge($verificationParams, $myLogbooksParams));
    
    $baseQuery = App\Models\Logbook::where('verified_by', $user->id)
        ->whereIn('verification_status', ['disetujui', 'ditolak']);
        
    switch ($user->role) {
        case 'Kepala Dinas':
            $baseQuery->whereHas('user', function($q) {
                $q->whereIn('role', ['Kepala Bagian 1', 'Kepala Bagian 2']);
            });
            break;
        case 'Kepala Bagian 1':
            $baseQuery->whereHas('user', function($q) {
                $q->where('role', 'Staf Bagian 1');
            });
            break;
        case 'Kepala Bagian 2':
            $baseQuery->whereHas('user', function($q) {
                $q->where('role', 'Staf Bagian 2');
            });
            break;
    }
    
    $totalVerified = $baseQuery->count();
    $approvedCount = (clone $baseQuery)->where('verification_status', 'disetujui')->count();
    $rejectedCount = (clone $baseQuery)->where('verification_status', 'ditolak')->count();
@endphp

<div class="mb-8">
    <h3 class="font-semibold text-lg mb-4">
        Riwayat Logbook yang Telah Anda Verifikasi
    </h3>

    @if(!in_array($user->role, ['Kepala Dinas', 'Kepala Bagian 1', 'Kepala Bagian 2']))
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
            <p>Anda tidak memiliki riwayat verifikasi logbook.</p>
        </div>
    @else
        <form method="GET" id="verification-history-form" class="mb-6 flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="vh_search" class="block text-sm font-medium text-gray-700 mb-1">Cari Logbook</label>
                <input type="text" name="vh_search" id="vh_search" value="{{ $search }}" placeholder="Cari berdasarkan aktivitas atau nama..." class="w-full p-2 border border-gray-300 rounded-md">
            </div>
            
            <div class="min-w-[150px]">
                <label for="vh_status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="vh_status" id="vh_status" class="w-full p-2 border border-gray-300 rounded-md">
                    <option value="">Semua</option>
                    <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            
            <div class="min-w-[150px]">
                <label for="vh_from_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="vh_from_date" id="vh_from_date" value="{{ $from_date }}" class="w-full p-2 border border-gray-300 rounded-md">
            </div>
            
            <div class="min-w-[150px]">
                <label for="vh_to_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="vh_to_date" id="vh_to_date" value="{{ $to_date }}" class="w-full p-2 border border-gray-300 rounded-md">
            </div>
            
            <div class="min-w-[100px]">
                <label for="vh_per_page" class="block text-sm font-medium text-gray-700 mb-1">Per Halaman</label>
                <select name="vh_per_page" id="vh_per_page" class="w-full p-2 border border-gray-300 rounded-md">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
            
            {{-- Preserve "my logbooks" parameters --}}
            @if(request('my_search'))
                <input type="hidden" name="my_search" value="{{ request('my_search') }}">
            @endif
            @if(request('my_status'))
                <input type="hidden" name="my_status" value="{{ request('my_status') }}">
            @endif
            @if(request('my_from_date'))
                <input type="hidden" name="my_from_date" value="{{ request('my_from_date') }}">
            @endif
            @if(request('my_to_date'))
                <input type="hidden" name="my_to_date" value="{{ request('my_to_date') }}">
            @endif
            @if(request('my_per_page'))
                <input type="hidden" name="my_per_page" value="{{ request('my_per_page') }}">
            @endif
            @if(request('my_page'))
                <input type="hidden" name="my_page" value="{{ request('my_page') }}">
            @endif
            
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                Filter
            </button>
        </form>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="text-center">
                    <span class="font-semibold text-gray-700">Total Diverifikasi:</span>
                    <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ $totalVerified }}</span>
                </div>
                <div class="text-center">
                    <span class="font-semibold text-gray-700">Disetujui:</span>
                    <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 rounded">{{ $approvedCount }}</span>
                </div>
                <div class="text-center">
                    <span class="font-semibold text-gray-700">Ditolak:</span>
                    <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 rounded">{{ $rejectedCount }}</span>
                </div>
            </div>
        </div>

        @if($logbooks->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4 text-center border-b w-36">Tanggal</th>
                            <th class="py-2 px-4 text-center border-b w-48">Yang Mengajukan</th>
                            <th class="py-2 px-4 text-center border-b w-32">Jabatan</th>
                            <th class="py-2 px-4 text-left border-b min-w-[300px]">Aktivitas</th>
                            <th class="py-2 px-4 text-center border-b w-32">Gambar</th>
                            <th class="py-2 px-4 text-center border-b w-32">Status</th>
                            <th class="py-2 px-4 text-center border-b w-40">Tanggal Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logbooks as $logbook)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 text-center border-b w-36">
                                    <div class="text-xs text-gray-600">
                                        {{ \Carbon\Carbon::parse($logbook->created_at)->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($logbook->created_at)->setTimezone('Asia/Jakarta')->diffForHumans() }}
                                    </div>
                                </td>

                                <td class="py-2 px-4 text-center border-b w-48">
                                    {{ $logbook->user->name }}
                                </td>

                                <td class="py-2 px-4 text-center border-b w-32">
                                    {{ $logbook->user->role }}
                                </td>

                                <td class="py-2 px-4 text-justify border-b min-w-[300px]">
                                    <div id="vh-limited-content-{{ $logbook->id }}">
                                        <p class="text-sm">{{ Str::limit($logbook->content, 200) }}</p>
                                        @if(strlen($logbook->content) > 200)
                                            <button class="text-blue-500 text-xs hover:underline mt-1" onclick="toggleVerificationContent('{{ $logbook->id }}', true)">
                                                Lihat selengkapnya
                                            </button>
                                        @endif
                                    </div>
                                    @if(strlen($logbook->content) > 200)
                                        <div id="vh-full-content-{{ $logbook->id }}" class="hidden mt-2 text-sm">
                                            {{ $logbook->content }}
                                            <button class="text-blue-500 text-xs hover:underline mt-1" onclick="toggleVerificationContent('{{ $logbook->id }}', false)">
                                                Lihat lebih sedikit
                                            </button>
                                        </div>
                                    @endif
                                </td>

                                <td class="py-2 px-4 border-b w-32">
                                    @if($logbook->image)
                                        <img src="{{ Storage::url($logbook->image) }}" alt="Logbook Image" class="w-16 h-16 object-cover rounded cursor-pointer mx-auto" onclick="showVerificationImageModal('{{ Storage::url($logbook->image) }}')">
                                    @else
                                        <span class="text-gray-400 text-xs">Tidak ada gambar</span>
                                    @endif
                                </td>

                                <td class="py-2 px-4 text-center border-b w-32">
                                    @if($logbook->verification_status === 'disetujui')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                           <i class="fas fa-check-circle mr-1"></i>Disetujui
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>Ditolak
                                        </span>
                                    @endif
                                </td>

                                <td class="py-2 px-4 text-center border-b w-40">
                                    <div class="text-xs text-gray-600">
                                        {{ \Carbon\Carbon::parse($logbook->updated_at)->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($logbook->updated_at)->setTimezone('Asia/Jakarta')->diffForHumans() }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 flex justify-center">
                {{ $logbooks->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <p class="text-gray-600 mb-2">
                    @switch($user->role)
                        @case('Kepala Dinas')
                            Belum ada logbook dari Kepala Bagian yang Anda verifikasi.
                            @break
                        @case('Kepala Bagian 1')
                            Belum ada logbook dari Staf Bagian 1 yang Anda verifikasi.
                            @break
                        @case('Kepala Bagian 2')
                            Belum ada logbook dari Staf Bagian 2 yang Anda verifikasi.
                            @break
                        @default
                            Belum ada riwayat verifikasi logbook.
                    @endswitch
                </p>
                <p class="text-sm text-gray-500">
                    Riwayat akan muncul setelah Anda melakukan verifikasi logbook.
                </p>
            </div>
        @endif
    @endif
</div>

<!-- Modal -->
<div id="verificationImageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Gambar Logbook (Verifikasi)</h3>
                <button id="closeVerificationImageModalX" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
            </div>
            <div class="mt-4">
                <img id="verificationModalImage" src="" alt="Logbook Image" class="max-w-full max-h-96 mx-auto rounded shadow-lg">
            </div>
            <div class="items-center px-4 py-3 mt-4">
                <button id="closeVerificationImageModal" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleVerificationContent(id, showFull) {
    const limited = document.getElementById('vh-limited-content-' + id);
    const full = document.getElementById('vh-full-content-' + id);
    
    if (showFull) {
        limited.classList.add('hidden');
        full.classList.remove('hidden');
    } else {
        full.classList.add('hidden');
        limited.classList.remove('hidden');
    }
}

function showVerificationImageModal(imageSrc) {
    document.getElementById('verificationModalImage').src = imageSrc;
    document.getElementById('verificationImageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeVerificationImageModal() {
    document.getElementById('verificationImageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

document.getElementById('closeVerificationImageModal').addEventListener('click', closeVerificationImageModal);
document.getElementById('closeVerificationImageModalX').addEventListener('click', closeVerificationImageModal);

document.getElementById('verificationImageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeVerificationImageModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeVerificationImageModal();
    }
});
</script>