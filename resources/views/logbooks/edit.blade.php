<x-app-layout>
    <x-slot name="header">
        <h2>Edit Logbook</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('logbooks.update', $logbook->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="logbookForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col space-y-6">
                                <div class="flex flex-col">
                                    <label for="date" class="mb-2 font-medium">Tanggal:</label>
                                    <input type="date" name="date" value="{{ old('date', $logbook->date) }}" required class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                
                                <div class="flex flex-col">
                                    <label for="image" class="mb-2 font-medium">Unggah Gambar:</label>
                                    <input type="file" name="image" accept="image/*" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <img id="preview" src="{{ $logbook->image ? Storage::url($logbook->image) : '#' }}" alt="Gambar Logbook" style="display: {{ $logbook->image ? 'block' : 'none' }}; max-width: 200px; margin-top: 10px;">
                                </div>
                            </div>
                            
                            <div class="flex flex-col">
                                <div class="flex justify-between items-center mb-2">
                                    <label for="content" class="font-medium">Aktivitas:</label>
                                    <span id="charCount" class="text-sm text-gray-500">0/800</span>
                                </div>
                                <textarea 
                                    name="content" 
                                    id="content"
                                    rows="8" 
                                    required 
                                    maxlength="800"
                                    class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-full"
                                    placeholder="Masukkan aktivitas Anda (maksimal 800 karakter)">{{ old('content', $logbook->content) }}</textarea>
                                <div id="charWarning" class="text-sm mt-1" style="display: none;"></div>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Back</a>
                            <button type="submit" id="submitBtn" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-800">Update Logbook</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('input[name="image"]').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        const contentTextarea = document.getElementById('content');
        const charCount = document.getElementById('charCount');
        const charWarning = document.getElementById('charWarning');
        const submitBtn = document.getElementById('submitBtn');
        const maxLength = 800;

        function updateCharCount() {
            const currentLength = contentTextarea.value.length;
            charCount.textContent = `${currentLength}/${maxLength}`;
            
            if (currentLength > maxLength * 0.9) {
                charCount.classList.remove('text-gray-500');
                charCount.classList.add('text-orange-500', 'font-semibold');
            } else if (currentLength > maxLength * 0.8) {
                charCount.classList.remove('text-gray-500', 'text-orange-500');
                charCount.classList.add('text-yellow-500');
            } else {
                charCount.classList.remove('text-orange-500', 'text-yellow-500', 'font-semibold');
                charCount.classList.add('text-gray-500');
            }
        }

        contentTextarea.addEventListener('input', updateCharCount);
        contentTextarea.addEventListener('paste', function() {
            setTimeout(updateCharCount, 10);
        });

        document.getElementById('logbookForm').addEventListener('submit', function(e) {
            const contentLength = contentTextarea.value.length;
            
            if (contentLength > maxLength) {
                e.preventDefault();
                alert(`Aktivitas terlalu panjang! Maksimal ${maxLength} karakter. Saat ini: ${contentLength} karakter.`);
                contentTextarea.focus();
                return false;
            }
            
            if (contentLength === 0) {
                e.preventDefault();
                alert('Aktivitas tidak boleh kosong!');
                contentTextarea.focus();
                return false;
            }
        });

        updateCharCount();
    </script>
</x-app-layout>