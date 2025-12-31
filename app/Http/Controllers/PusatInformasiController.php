<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PusatInformasiController extends Controller
{
    public function intruksiKerjaGI()
    {
        $files = $this->getFiles('gardu-induk');
        return view('pusat-informasi.ik-gardu-induk', ['files' => $files]);
    }

    public function uploadGI(Request $request)
    {
        $this->handleUpload($request, 'gardu-induk');
        return redirect()->route('admin.ik.gardu-induk.index');
    }

    public function deleteGI(Request $request)
    {
        $this->handleDelete($request, 'gardu-induk');
        return redirect()->route('admin.ik.gardu-induk.index');
    }

    public function intruksiKerjaJaringan()
    {
        $files = $this->getFiles('jaringan');
        return view('pusat-informasi.ik-jaringan', ['files' => $files]);
    }

    public function uploadJaringan(Request $request)
    {
        $this->handleUpload($request, 'jaringan');
        return redirect()->route('admin.ik.jaringan.index');
    }

    public function deleteJaringan(Request $request)
    {
        $this->handleDelete($request, 'jaringan');
        return redirect()->route('admin.ik.jaringan.index');
    }

    private function getValidPath(string $tipe)
    {
        if (!in_array($tipe, ['gardu-induk', 'jaringan'])) {
            abort(404, 'Jenis instruksi kerja tidak valid.');
        }

        $path = public_path('storage/intruksi-kerja/' . $tipe);

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        return $path;
    }

    private function getFiles(string $tipe)
    {
        $path = $this->getValidPath($tipe);

        $allFiles = File::files($path);

        return collect($allFiles)->filter(function ($file) {
            return $file->getExtension() == 'pdf';
        });
    }

    private function handleUpload(Request $request, string $tipe)
    {
        $path = $this->getValidPath($tipe);

        $request->validate([
            'file_pdf' => 'required|mimes:pdf|max:15360',
        ], [
            'file_pdf.required' => 'File PDF wajib diunggah.',
            'file_pdf.mimes' => 'Format file harus berupa PDF.',
            'file_pdf.max' => 'Ukuran file maksimal 15 MB.',
        ]);

        $file = $request->file('file_pdf');

        $originalName = $file->getClientOriginalName();
        $safeName = preg_replace('/[^A-Za-z0-9\._-]/', '', $originalName);

        try {
            $file->move($path, $safeName);
            session()->flash('success', 'File berhasil di-upload.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal meng-upload file: ' . $e->getMessage());
        }
    }

    private function handleDelete(Request $request, string $tipe)
    {
        $path = $this->getValidPath($tipe);
        $filename = $request->input('filename');

        if (str_contains($filename, '..') || str_contains($filename, '/')) {
            session()->flash('error', 'Nama file tidak valid.');
            return;
        }

        $filePath = $path . '/' . $filename;

        if (File::exists($filePath)) {
            File::delete($filePath);
            session()->flash('success', 'File berhasil dihapus.');
        } else {
            session()->flash('error', 'File tidak ditemukan.');
        }
    }
}
