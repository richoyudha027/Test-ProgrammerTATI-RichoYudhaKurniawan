<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProvinsiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $provinsi = Provinsi::all();
            
            return response()->json([
                'success' => true,
                'message' => 'Data provinsi berhasil diambil',
                'data' => $provinsi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data provinsi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode' => 'required|string|unique:provinsi,kode',
                'nama' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $provinsi = Provinsi::create([
                'kode' => $request->kode,
                'nama' => $request->nama
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Provinsi berhasil ditambahkan',
                'data' => $provinsi
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan provinsi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $provinsi = Provinsi::find($id);

            if (!$provinsi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provinsi tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data provinsi berhasil diambil',
                'data' => $provinsi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data provinsi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $provinsi = Provinsi::find($id);

            if (!$provinsi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provinsi tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'kode' => 'required|string|unique:provinsi,kode,' . $id,
                'nama' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $provinsi->update([
                'kode' => $request->kode,
                'nama' => $request->nama
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Provinsi berhasil diperbarui',
                'data' => $provinsi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui provinsi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $provinsi = Provinsi::find($id);

            if (!$provinsi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provinsi tidak ditemukan'
                ], 404);
            }

            $provinsi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Provinsi berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus provinsi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}