<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jurusan;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::orderBy('kode_jurusan')->get();
        return view('master.jurusan', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jurusan' => 'required|unique:jurusans,kode_jurusan',
            'nama_jurusan' => 'required'
        ]);

        Jurusan::create($request->all());
        return back()->with('success', 'Data jurusan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_jurusan' => 'required|unique:jurusans,kode_jurusan,' . $id,
            'nama_jurusan' => 'required'
        ]);

        $jurusan = Jurusan::findOrFail($id);
        $jurusan->update($request->all());

        return back()->with('success', 'Data jurusan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $jurusan->delete();
        
        return back()->with('success', 'Data jurusan berhasil dihapus!');
    }
}