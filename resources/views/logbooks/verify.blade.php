@php
    $user = Auth::user();
    $logbooks = collect();

    switch ($user->role) {
        case 'Kepala Dinas':
            // Kepala Dinas hanya memverifikasi logbook dari Kepala Bagian 1 dan Kepala Bagian 2
            $logbooks = App\Models\Logbook::whereHas('user', function($query) {
                $query->whereIn('role', ['Kepala Bagian 1', 'Kepala Bagian 2']);
            })
            ->where('verification_status', 'pending')
            ->with('user')
            ->get();
            break;

        case 'Kepala Bagian 1':
            // Kepala Bagian 1 hanya memverifikasi logbook dari Staf Bagian 1
            $logbooks = App\Models\Logbook::whereHas('user', function($query) {
                $query->where('role', 'Staf Bagian 1');
            })
            ->where('verification_status', 'pending')
            ->with('user')
            ->get();
            break;

        case 'Kepala Bagian 2':
            // Kepala Bagian 2 hanya memverifikasi logbook dari Staf Bagian 2
            $logbooks = App\Models\Logbook::whereHas('user', function($query) {
                $query->where('role', 'Staf Bagian 2');
            })
            ->where('verification_status', 'pending')
            ->with('user')
            ->get();
            break;
            
        default:
            // Role lain (Staf) tidak bisa memverifikasi logbook
            $logbooks = collect();
            break;
    }
@endphp

<div class="mb-8">
    <h4 class="font-semibold text-lg mb-4">
        Logbook untuk Diverifikasi
        @if(in_array($user->role, ['Kepala Dinas', 'Kepala Bagian 1', 'Kepala Bagian 2']))
        @endif
    </h4>
    
    @if(!in_array($user->role, ['Kepala Dinas', 'Kepala Bagian 1', 'Kepala Bagian 2']))
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
            <p>Anda tidak memiliki akses untuk memverifikasi logbook.</p>
        </div>
    @elseif($logbooks->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 text-center border-b w-36">Tanggal</th>
                        <th class="py-2 px-4 text-center border-b w-32">Yang Mengajukan</th>
                        <th class="py-2 px-4 text-center border-b w-32">Jabatan</th>
                        <th class="py-2 px-4 text-center border-b min-w-[300px]">Aktivitas</th>
                        <th class="py-2 px-4 text-center border-b w-32">Gambar</th>
                        <th class="py-2 px-4 text-center border-b w-56">Status</th>
                        <th class="py-2 px-4 text-center border-b w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logbooks as $logbook)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b text-center w-36">
                                <div class="text-xs text-gray-600">
                                    {{ \Carbon\Carbon::parse($logbook->created_at)->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($logbook->created_at)->setTimezone('Asia/Jakarta')->diffForHumans() }}
                                </div>
                            </td>
                            <td class="py-2 px-4 border-b text-center w-32">
                                {{ $logbook->user->name }}
                            </td>
                            <td class="py-2 px-4 border-b text-center w-32">
                                {{ $logbook->user->role }}
                            </td>
                            <td class="py-2 px-4 border-b min-w-[300px]">
                                <div id="limited-content-{{ $logbook->id }}">
                                    <p class="text-sm">{{ Str::limit($logbook->content, 400) }}</p>
                                    @if(strlen($logbook->content) > 400)
                                        <button class="text-blue-500 text-xs hover:underline" onclick="toggleFullContent({{ $logbook->id }}, true)">
                                            Lihat selengkapnya
                                        </button>
                                    @endif
                                </div>
                                @if(strlen($logbook->content) > 400)
                                    <div id="full-content-{{ $logbook->id }}" class="hidden mt-2 text-sm">
                                        {{ $logbook->content }}
                                        <button class="text-blue-500 text-xs hover:underline" onclick="toggleFullContent({{ $logbook->id }}, false)">
                                            Lihat lebih sedikit
                                        </button>
                                    </div>
                                @endif
                            </td>
                            <td class="py-2 px-4 border-b w-32">
                                @if($logbook->image)
                                    <img src="{{ Storage::url($logbook->image) }}" alt="Logbook Image" class="w-16 h-16 object-cover rounded cursor-pointer" onclick="showImageModal('{{ Storage::url($logbook->image) }}')">
                                @else
                                    <span class="text-gray-400 text-xs">Tidak ada gambar</span>
                                @endif
                            </td>
                            <td class="py-2 px-4 border-b w-56">
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                    Menunggu Verifikasi
                                </span>
                            </td>
                            <td class="py-2 px-4 border-b w-32">
                                <div class="flex space-x-2 justify-center">
                                    <button onclick="showVerifyModal({{ $logbook->id }}, '{{ $logbook->user->name }}')" class="bg-green-500 text-white p-2 rounded-full hover:bg-green-600 transition-colors" title="Setujui">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    <button onclick="showRejectModal({{ $logbook->id }}, '{{ $logbook->user->name }}')" class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors" title="Tolak">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="text-gray-600">
                @switch($user->role)
                    @case('Kepala Dinas')
                        Tidak ada logbook dari Kepala Bagian yang perlu diverifikasi.
                        @break
                    @case('Kepala Bagian 1')
                        Tidak ada logbook dari Staf Bagian 1 yang perlu diverifikasi.
                        @break
                    @case('Kepala Bagian 2')
                        Tidak ada logbook dari Staf Bagian 2 yang perlu diverifikasi.
                        @break
                    @default
                        Tidak ada logbook yang perlu diverifikasi.
                @endswitch
            </p>
        </div>
    @endif
</div>

<!-- Modal untuk melihat gambar -->
<div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
    <div class="relative mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Gambar Logbook</h3>
                <button id="closeModalX" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div class="mt-4">
                <img id="modalImage" src="" alt="Logbook Image" class="max-w-full max-h-96 mx-auto rounded shadow-lg">
            </div>
            <div class="items-center px-4 py-3 mt-4">
                <button id="closeModal" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Verifikasi -->
<div id="verifyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
    <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Konfirmasi Verifikasi</h3>
            <p class="text-sm text-gray-500 text-center mb-6">
                Yakin ingin menyetujui logbook dari <span id="verifyUserName" class="font-semibold"></span>?
            </p>
            <div class="flex justify-center space-x-4">
                <form id="verifyForm" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                        Ya, Setujui
                    </button>
                </form>
                <button id="cancelVerify" class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Penolakan -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
    <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Konfirmasi Penolakan</h3>
            <p class="text-sm text-gray-500 text-center mb-6">
                Yakin ingin menolak logbook dari <span id="rejectUserName" class="font-semibold"></span>?
            </p>
            <div class="flex justify-center space-x-4">
                <form id="rejectForm" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                        Ya, Tolak
                    </button>
                </form>
                <button id="cancelReject" class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFullContent(id, showFull) {
    const limited = document.getElementById('limited-content-' + id);
    const full = document.getElementById('full-content-' + id);
    
    if (showFull) {
        limited.classList.add('hidden');
        full.classList.remove('hidden');
    } else {
        full.classList.add('hidden');
        limited.classList.remove('hidden');
    }
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

function showVerifyModal(logbookId, userName) {
    document.getElementById('verifyUserName').textContent = userName;
    document.getElementById('verifyForm').action = `/logbooks/${logbookId}/verify`;
    document.getElementById('verifyModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeVerifyModal() {
    document.getElementById('verifyModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function showRejectModal(logbookId, userName) {
    document.getElementById('rejectUserName').textContent = userName;
    document.getElementById('rejectForm').action = `/logbooks/${logbookId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Event listeners untuk modal gambar
document.getElementById('closeModal').addEventListener('click', closeImageModal);
document.getElementById('closeModalX').addEventListener('click', closeImageModal);

// Event listeners untuk modal verifikasi
document.getElementById('cancelVerify').addEventListener('click', closeVerifyModal);

// Event listeners untuk modal penolakan
document.getElementById('cancelReject').addEventListener('click', closeRejectModal);

// Close modal when clicking outside
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

document.getElementById('verifyModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeVerifyModal();
    }
});

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
        closeVerifyModal();
        closeRejectModal();
    }
});
</script>