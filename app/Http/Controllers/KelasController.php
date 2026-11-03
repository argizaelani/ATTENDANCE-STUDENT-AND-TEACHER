<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;

class KelasController extends Controller
{
    public function index()
    {
        $kelases = Kelas::orderBy('nama_kelas')->get();
        return view('master.kelas', compact('kelases'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_kelas' => 'required|unique:kelas,nama_kelas']);
        Kelas::create($request->all());
        return back()->with('success', 'Data kelas berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama_kelas' => 'required|unique:kelas,nama_kelas,' . $id]);
        Kelas::findOrFail($id)->update($request->all());
        return back()->with('success', 'Data kelas berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Kelas::findOrFail($id)->delete();
        return back()->with('success', 'Data kelas berhasil dihapus!');
    }
}