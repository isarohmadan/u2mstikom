<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ])->validate();

        try {
            Announcement::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->with('success', 'Pemberitahuan berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat pemberitahuan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            
            // Check authorization - hanya admin dan staff yang bisa hapus
            if (!auth()->user()->hasAnyRole(['administrator', 'pengurus'])) {
                return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus pemberitahuan.');
            }

            $announcement->delete();
            return redirect()->back()->with('success', 'Pemberitahuan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus pemberitahuan: ' . $e->getMessage());
        }
    }
}
