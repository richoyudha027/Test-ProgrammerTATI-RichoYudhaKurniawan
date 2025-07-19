<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class LogbookController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;
    /**
     * Menampilkan dashboard dengan logbook berdasarkan peran pengguna
     */
    public function dashboard()
    {
        return view('dashboard');
    }

    /**
     * Menampilkan semua logbook berdasarkan peran pengguna
     */
    public function index()
    {
        $user = Auth::user();
        $logbooks = [];

        switch ($user->role) {
            case 'Kepala Dinas':
                $logbooks = [];
                break;

            case 'Kepala Bagian 1':
            case 'Kepala Bagian 2':
                $logbooks = Logbook::where('user_id', $user->id)
                    ->where('verification_status', 'pending')
                    ->get();
                break;

            case 'Staf Bagian 1':
            case 'Staf Bagian 2':
                $logbooks = Logbook::where('user_id', $user->id)
                    ->where('verification_status', 'pending')
                    ->get();
                break;
        }

        return view('logbooks.index', compact('logbooks'));
    }

    /**
     * Menampilkan form untuk membuat logbook
     */
    public function create()
    {
        return view('logbooks.create');
    }

    /**
     * Menyimpan logbook baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'content' => 'required|string|max:800',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = (new Logbook)->storeImage($request->file('image'));

        Logbook::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'content' => $request->content,
            'image' => $imagePath,
            'verification_status' => 'pending',
        ]);

        return redirect()->route('dashboard')->with('status', 'Logbook berhasil dibuat.');
    }

    /**
     * Menampilkan form untuk mengedit logbook
     */
    public function edit(Logbook $logbook)
    {
        $this->authorize('update', $logbook);
        
        return view('logbooks.edit', compact('logbook'));
    }

    /**
     * Memperbarui logbook
     */
    public function update(Request $request, Logbook $logbook)
    {
        $this->authorize('update', $logbook);
        
        $request->validate([
            'date' => 'required|date',
            'content' => 'required|string|max:800',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($request->hasFile('image')) {
            Storage::delete($logbook->image);
            $logbook->image = $logbook->storeImage($request->file('image'));
        }

        $logbook->update([
            'date' => $request->date,
            'content' => $request->content,
        ]);

        return redirect()->route('dashboard')->with('status', 'Logbook berhasil diperbarui.');
    }

    /**
     * Menghapus logbook
     */
    public function destroy(Logbook $logbook)
    {
        $this->authorize('delete', $logbook);
        
        if ($logbook->image) {
            Storage::delete($logbook->image);
        }

        $logbook->delete();

        return redirect()->route('dashboard')->with('status', 'Logbook berhasil dihapus.');
    }

    /**
     * Menyetujui logbook
     */
    public function verify(Logbook $logbook)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['Kepala Dinas', 'Kepala Bagian 1', 'Kepala Bagian 2'])) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak untuk menyetujui logbook.');
        }

        if ($logbook->verification_status !== 'pending') {
            return redirect()->route('dashboard')->with('error', 'Logbook telah diverifikasi.');
        }

        $logbook->update([
            'verification_status' => 'disetujui',
            'verified_by' => Auth::id(),
        ]);

        return redirect()->route('dashboard')->with('status', 'Logbook disetujui.');
    }

    /**
     * Menolak logbook
     */
    public function reject(Logbook $logbook)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['Kepala Dinas', 'Kepala Bagian 1', 'Kepala Bagian 2'])) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak untuk menolak logbook.');
        }

        if ($logbook->verification_status !== 'pending') {
            return redirect()->route('dashboard')->with('error', 'Logbook telah diverifikasi.');
        }

        $logbook->update([
            'verification_status' => 'ditolak',
            'verified_by' => Auth::id(),
        ]);

        return redirect()->route('dashboard')->with('status', 'Logbook ditolak.');
    }

    /**
     * Menampilkan logbook untuk diverifikasi berdasarkan hierarki
     */
    public function verifyIndex()
    {
        $user = Auth::user();
        $logbooks = [];

        switch ($user->role) {
            case 'Kepala Dinas':
                $logbooks = Logbook::whereHas('user', function($query) {
                    $query->whereIn('role', ['Kepala Bagian 1', 'Kepala Bagian 2']);
                })->where('verification_status', 'pending')->with('user')->get();
                break;

            case 'Kepala Bagian 1':
                $logbooks = Logbook::whereHas('user', function($query) {
                    $query->where('role', 'Staf Bagian 1');
                })->where('verification_status', 'pending')->with('user')->get();
                break;

            case 'Kepala Bagian 2':
                $logbooks = Logbook::whereHas('user', function($query) {
                    $query->where('role', 'Staf Bagian 2');
                })->where('verification_status', 'pending')->with('user')->get();
                break;

            default: abort(403, 'Unauthorized action.');
        }

        return view('logbooks.verify', compact('logbooks'));
    }

    public function history()
    {
        $user = Auth::user();

        $logbooks = Logbook::where('user_id', Auth::id())
            ->where('verification_status', '!=', 'pending')
            ->with('verifiedBy')
            ->get();

        $verifiedLogbooks = collect();
        if (in_array($user->role, ['Kepala Dinas', 'Kepala Bagian 1', 'Kepala Bagian 2'])) {
            $verifiedLogbooks = Logbook::where('verified_by', Auth::id())
                ->whereIn('verification_status', ['disetujui', 'ditolak'])
                ->with('user')
                ->get();
        }

        return view('history', compact('logbooks', 'verifiedLogbooks'));
    }

    public function historyVerify()
    {
        $user = Auth::user();
  
        $logbooks = Logbook::whereNotNull('verified_by')->whereIn('verification_status', ['disetujui', 'ditolak'])->get();

        return view('history', compact('logbooks'));
    }
}