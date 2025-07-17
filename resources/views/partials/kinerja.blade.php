<head>
    <title>Predikat Kinerja</title>
</head>

<section class="container">
    <h1>Predikat Kinerja Pegawai</h1>

    <form action="{{ route('predikat-kinerja') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="hasil_kerja">Hasil Kerja:</label>
            @error('hasil_kerja')
                <div class="alert-danger">{{ $message }}</div>
            @enderror
            <select name="hasil_kerja" id="hasil_kerja">
                <option value="" disabled {{ old('hasil_kerja') ? '' : 'selected' }}>Masukkan hasil kerja pegawai.</option>
                <option value="diatas ekspektasi" {{ old('hasil_kerja') == 'diatas ekspektasi' || session('hasil_kerja') == 'diatas ekspektasi' ? 'selected' : '' }}>Diatas Ekspektasi</option>
                <option value="sesuai ekspektasi" {{ old('hasil_kerja') == 'sesuai ekspektasi' || session('hasil_kerja') == 'sesuai ekspektasi' ? 'selected' : '' }}>Sesuai Ekspektasi</option>
                <option value="dibawah ekspektasi" {{ old('hasil_kerja') == 'dibawah ekspektasi' || session('hasil_kerja') == 'dibawah ekspektasi' ? 'selected' : '' }}>Dibawah Ekspektasi</option>
            </select>
        </div>

        <div class="form-group">
            <label for="perilaku">Perilaku:</label>
            @error('perilaku')
                <div class="alert-danger">{{ $message }}</div>
            @enderror
            <select name="perilaku" id="perilaku">
                <option value="" disabled {{ old('perilaku') ? '' : 'selected' }}>Masukkan perilaku pegawai.</option>
                <option value="diatas ekspektasi" {{ old('perilaku') == 'diatas ekspektasi' || session('perilaku') == 'diatas ekspektasi' ? 'selected' : '' }}>Diatas Ekspektasi</option>
                <option value="sesuai ekspektasi" {{ old('perilaku') == 'sesuai ekspektasi' || session('perilaku') == 'sesuai ekspektasi' ? 'selected' : '' }}>Sesuai Ekspektasi</option>
                <option value="dibawah ekspektasi" {{ old('perilaku') == 'dibawah ekspektasi' || session('perilaku') == 'dibawah ekspektasi' ? 'selected' : '' }}>Dibawah Ekspektasi</option>
            </select>
        </div>

        <button type="submit">Prediksi Kinerja</button>
    </form>

    @if(session('predikat'))
        <div id="resultContainer">
            <p>Predikat Kinerja: {{ session('predikat') }}</p>
        </div>
    @endif
</section>
