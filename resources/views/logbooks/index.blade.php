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
                                    
                                    <form action="{{ route('logbooks.destroy', $logbook->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 transition duration-300" onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="fas fa-trash-alt text-sm"></i> 
                                        </button>
                                    </form>
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
