<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAjaran;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahuns = TahunAjaran::orderBy('nama_tahun', 'desc')->get();
        return view('master.tahun-ajaran', compact('tahuns'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_tahun' => 'required|unique:tahun_ajarans,nama_tahun']);
        TahunAjaran::create($request->all());
        return back()->with('success', 'Tahun ajaran berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama_tahun' => 'required|unique:tahun_ajarans,nama_tahun,' . $id]);
        TahunAjaran::findOrFail($id)->update($request->all());
        return back()->with('success', 'Tahun ajaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        TahunAjaran::findOrFail($id)->delete();
        return back()->with('success', 'Tahun ajaran berhasil dihapus!');
    }
}