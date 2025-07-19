@php
    $user = Auth::user();
    
    $search = request('search', '');
    $status = request('status', '');
    $from_date = request('from_date', '');
    $to_date = request('to_date', '');
    $perPage = request('per_page', 10);
    
    $query = App\Models\Logbook::where('user_id', $user->id)
        ->with(['verifiedBy']);
    
    if ($search) {
        $query->where('content', 'LIKE', "%{$search}%");
    }
    
    if ($status) {
        $query->where('verification_status', $status);
    }
    
    if ($from_date) {
        $query->where('created_at', '>=', $from_date . ' 00:00:00');
    }
    
    if ($to_date) {
        $query->where('created_at', '<=', $to_date . ' 23:59:59');
    }
    
    $logbooks = $query->orderBy('date', 'desc')->paginate($perPage);
    
    $logbooks->appends(request()->query());
    
    $totalLogbooks = App\Models\Logbook::where('user_id', $user->id)->count();
    $pendingCount = App\Models\Logbook::where('user_id', $user->id)->where('verification_status', 'pending')->count();
    $approvedCount = App\Models\Logbook::where('user_id', $user->id)->where('verification_status', 'disetujui')->count();
    $rejectedCount = App\Models\Logbook::where('user_id', $user->id)->where('verification_status', 'ditolak')->count();
@endphp

<div class="mb-8 w-full">
    <h3 class="font-semibold text-lg mb-4">
        Riwayat Logbook Anda
    </h3>

    @if($totalLogbooks > 0)
        <form method="GET" class="mb-6 flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Logbook</label>
                <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Cari berdasarkan aktivitas..." class="w-full p-2 border border-gray-300 rounded-md">
            </div>
            
            <div class="min-w-[150px]">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full p-2 border border-gray-300 rounded-md">
                    <option value="">Semua</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            
            <div class="min-w-[150px]">
                <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="from_date" id="from_date" value="{{ $from_date }}" class="w-full p-2 border border-gray-300 rounded-md">
            </div>
            
            <div class="min-w-[150px]">
                <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="to_date" id="to_date" value="{{ $to_date }}" class="w-full p-2 border border-gray-300 rounded-md">
            </div>
            
            <div class="min-w-[100px]">
                <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">Per Halaman</label>
                <select name="per_page" id="per_page" class="w-full p-2 border border-gray-300 rounded-md">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
            
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                Filter
            </button>
        </form>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg w-full">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div class="text-center">
                    <span class="font-semibold text-gray-700">Total Logbook:</span>
                    <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ $totalLogbooks }}</span>
                </div>
                <div class="text-center">
                    <span class="font-semibold text-gray-700">Menunggu:</span>
                    <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 rounded">{{ $pendingCount }}</span>
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
            <div class="w-full overflow-x-auto">
                <table class="min-w-full w-full bg-white border border-gray-300 table-fixed">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="w-1/6 py-3 px-4 text-center border-b font-semibold">Tanggal</th>
                            <th class="w-2/6 py-3 px-4 text-center border-b font-semibold">Aktivitas</th>
                            <th class="w-1/6 py-3 px-4 text-center border-b font-semibold">Gambar</th>
                            <th class="w-1/6 py-3 px-4 text-center border-b font-semibold">Status</th>
                            <th class="w-1/6 py-3 px-4 text-center border-b font-semibold">Diverifikasi Oleh</th>
                            <th class="w-1/6 py-3 px-4 text-center border-b font-semibold">Tanggal Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logbooks as $logbook)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-center border-b">
                                    <div class="text-xs text-gray-600">
                                        {{ \Carbon\Carbon::parse($logbook->created_at)->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($logbook->created_at)->setTimezone('Asia/Jakarta')->diffForHumans() }}
                                    </div>
                                </td>
                                
                                <td class="py-3 px-4 text-justify border-b">
                                    <div id="content-container-{{ $logbook->id }}">
                                        <div id="limited-content-{{ $logbook->id }}">
                                            <p class="text-sm leading-relaxed">{{ Str::limit($logbook->content, 150) }}</p>
                                            @if(strlen($logbook->content) > 150)
                                                <button class="text-blue-500 text-xs hover:underline mt-1" onclick="showFullContent('{{ $logbook->id }}')">
                                                    Lihat selengkapnya
                                                </button>
                                            @endif
                                        </div>
                                        
                                        <div id="full-content-{{ $logbook->id }}" style="display: none;">
                                            <p class="text-sm leading-relaxed">{{ $logbook->content }}</p>
                                            @if(strlen($logbook->content) > 150)
                                                <button class="text-blue-500 text-xs hover:underline mt-1" onclick="showLimitedContent('{{ $logbook->id }}')">
                                                    Lihat lebih sedikit
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3 px-4 text-center border-b">
                                    @if($logbook->image)
                                        <img src="{{ Storage::url($logbook->image) }}" alt="Logbook Image" class="w-16 h-16 object-cover rounded cursor-pointer mx-auto" onclick="showImageModal('{{ Storage::url($logbook->image) }}')">
                                    @else
                                        <span class="text-gray-400 text-xs">Tidak ada</span>
                                    @endif
                                </td>

                                <td class="py-3 px-4 text-center border-b">
                                    @if($logbook->verification_status === 'pending')
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 inline-flex items-center">
                                            <i class="fas fa-clock mr-1"></i>Menunggu
                                        </span>
                                    @elseif($logbook->verification_status === 'disetujui')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 inline-flex items-center">
                                            <i class="fas fa-check-circle mr-1"></i>Disetujui
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 inline-flex items-center">
                                            <i class="fas fa-times-circle mr-1"></i>Ditolak
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3 px-4 text-center border-b">
                                    @if($logbook->verifiedBy)
                                        <div class="text-sm font-medium">{{ $logbook->verifiedBy->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $logbook->verifiedBy->role }}</div>
                                    @else
                                        <span class="text-gray-400 text-sm">Belum diverifikasi</span>
                                    @endif
                                </td>


                                <td class="py-3 px-4 text-center border-b">
                                    @if($logbook->verification_status !== 'pending')
                                        <div class="text-xs text-gray-600">
                                            {{ \Carbon\Carbon::parse($logbook->updated_at)->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($logbook->updated_at)->diffForHumans() }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 flex justify-center w-full">
                {{ $logbooks->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-gray-600 mb-2">Logbook tidak tersedia</p>
            </div>
        @endif
    @else
        <div class="text-center py-8">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="text-gray-600 mb-2">Anda belum memiliki logbook.</p>
            <p class="text-sm text-gray-500 mb-4">Mulai buat logbook pertama Anda untuk melacak aktivitas kerja.</p>
            <a href="{{ route('logbooks.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors">
                Buat Logbook Baru
            </a>
        </div>
    @endif
</div>

<!-- Modal -->
<div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Gambar Logbook</h3>
                <button id="closeImageModalX" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
            </div>
            <div class="mt-4">
                <img id="modalImage" src="" alt="Logbook Image" class="max-w-full max-h-96 mx-auto rounded shadow-lg">
            </div>
            <div class="items-center px-4 py-3 mt-4">
                <button id="closeImageModal" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showFullContent(id) {
    document.getElementById('limited-content-' + id).style.display = 'none';
    document.getElementById('full-content-' + id).style.display = 'block';
}

function showLimitedContent(id) {
    document.getElementById('limited-content-' + id).style.display = 'block';
    document.getElementById('full-content-' + id).style.display = 'none';
}

function showImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

document.getElementById('closeImageModal').addEventListener('click', closeImageModal);
document.getElementById('closeImageModalX').addEventListener('click', closeImageModal);

document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) closeImageModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>