@php
    $user = Auth::user();
    $logbooks = App\Models\Logbook::where('user_id', $user->id)
        ->where('verification_status', 'pending')
        ->get();
@endphp

<div class="mb-8">
    <div class="flex justify-between items-center mb-4">
        <h4 class="font-semibold text-lg">
            Logbook yang Sedang Anda Ajukan
        </h4>
        <a href="{{ route('logbooks.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-800 hover:text-white hover:scale-105 hover:shadow-lg transition-all duration-300 ease-in-out">
            Buat Logbook
        </a>
    </div>
    
    @if($logbooks->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 text-center border-b">Tanggal</th>
                        <th class="py-2 px-4 text-left border-b">Aktivitas</th>
                        <th class="py-2 px-4 text-center border-b">Gambar</th>
                        <th class="py-2 px-4 text-center border-b min-w-[12rem]">Status</th>
                        <th class="py-2 px-4 text-center border-b min-w-[12rem]">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logbooks as $logbook)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 text-center border-b">
                                <div class="text-xs text-gray-600">
                                    {{ \Carbon\Carbon::parse($logbook->created_at)->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($logbook->created_at)->setTimezone('Asia/Jakarta')->diffForHumans() }}
                                </div>
                            </td>
                            
                            <td class="py-2 px-4 text-justify border-b min-w-[12rem]">{{ Str::limit($logbook->content, 400) }}</td>

                            <td class="py-2 px-4 text-center border-b">
                                @if($logbook->image)
                                    <img src="{{ Storage::url($logbook->image) }}" alt="Logbook Image" class="w-16 h-16 object-cover rounded mx-auto">
                                @else
                                    <span class="text-gray-400">Tidak ada gambar</span>
                                @endif
                            </td>

                            <td class="py-2 px-4 text-center border-b min-w-[12rem]">
                                @if($logbook->verification_status === 'pending')
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                        Menunggu Verifikasi
                                    </span>
                                @elseif($logbook->verification_status === 'disetujui')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            <td class="py-2 px-4 text-center border-b min-w-[12rem]">
                                @if($logbook->verification_status === 'pending')
                                    <a href="{{ route('logbooks.edit', $logbook->id) }}" class="inline-block px-3 py-2 text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition duration-300">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    
                                    <button type="button" 
                                            class="px-3 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 transition duration-300 ml-2"
                                            onclick="openDeleteModal({{ $logbook->id }})">
                                        <i class="fas fa-trash-alt text-sm"></i> 
                                    </button>
                                @else
                                    <span class="text-gray-400 text-xs">Sudah diverifikasi</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8">
            <p class="text-gray-600 mb-4">Anda belum memiliki logbook yang diajukan.</p>
        </div>
    @endif
</div>

<!-- Modal Delete Confirmation -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
    <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Konfirmasi Hapus</h3>
            <p class="text-sm text-gray-500 text-center mb-6">
                Yakin ingin menghapus logbook ini? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex justify-center space-x-4">
                <button type="button" 
                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors"
                        onclick="confirmDelete()">
                    Ya, Hapus
                </button>
                <button type="button" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors"
                        onclick="closeDeleteModal()">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    let currentLogbookId = null;

    function openDeleteModal(logbookId) {
        currentLogbookId = logbookId;
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
        currentLogbookId = null;
    }

    function confirmDelete() {
        if (currentLogbookId) {
            const form = document.getElementById('deleteForm');
            form.action = `/logbooks/${currentLogbookId}`;
            form.submit();
        }
    }

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>
