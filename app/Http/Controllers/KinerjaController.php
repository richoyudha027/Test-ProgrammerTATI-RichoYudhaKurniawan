<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KinerjaController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function predikat_kinerja(Request $request)
    {
        $validated = $request->validate([
            'hasil_kerja' => 'required',
            'perilaku' => 'required',
        ]);

        $hasil_kerja = $request->input('hasil_kerja');
        $perilaku = $request->input('perilaku');

        $predikat = '';

        if ($hasil_kerja == 'diatas ekspektasi' && $perilaku == 'diatas ekspektasi') {
            $predikat = 'Sangat Baik';
        } elseif ($hasil_kerja == 'diatas ekspektasi' && $perilaku == 'sesuai ekspektasi') {
            $predikat = 'Sangat Baik';
        } elseif ($hasil_kerja == 'diatas ekspektasi' && $perilaku == 'dibawah ekspektasi') {
            $predikat = 'Baik';
        } elseif ($hasil_kerja == 'sesuai ekspektasi' && $perilaku == 'diatas ekspektasi') {
            $predikat = 'Baik';
        } elseif ($hasil_kerja == 'sesuai ekspektasi' && $perilaku == 'sesuai ekspektasi') {
            $predikat = 'Baik';
        } elseif ($hasil_kerja == 'sesuai ekspektasi' && $perilaku == 'dibawah ekspektasi') {
            $predikat = 'Butuh perbaikan';
        } elseif ($hasil_kerja == 'dibawah ekspektasi' && $perilaku == 'diatas ekspektasi') {
            $predikat = 'Butuh perbaikan';
        } elseif ($hasil_kerja == 'dibawah ekspektasi' && $perilaku == 'sesuai ekspektasi') {
            $predikat = 'Butuh perbaikan';
        } elseif ($hasil_kerja == 'dibawah ekspektasi' && $perilaku == 'dibawah ekspektasi') {
            $predikat = 'Butuh perbaikan';
        }

        return redirect()->route('predikat-kinerja.index')
            ->with('hasil_kerja', $hasil_kerja)
            ->with('perilaku', $perilaku)
            ->with('predikat', $predikat);
    }
}