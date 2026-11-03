<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::orderBy('nama')->get();
        return view('guru.guru', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nip' => 'nullable|unique:gurus,nip'
        ]);

        Guru::create($request->all());
        return back()->with('success', 'Data guru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'nip' => 'nullable|unique:gurus,nip,' . $id
        ]);

        Guru::findOrFail($id)->update($request->all());
        return back()->with('success', 'Data guru berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Guru::findOrFail($id)->delete();
        return back()->with('success', 'Data guru berhasil dihapus!');
    }
}