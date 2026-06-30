<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class VisitFileUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240', // 10MB
        ]);

        $uploadedPaths = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $extension = $file->getClientOriginalExtension();
                $cleanName = Str::slug(Str::before($file->getClientOriginalName(), '.'));
                $filename = now()->format('Ymd_Hi') . '_' . Str::random(5) . '_' . $cleanName . '.' . $extension;
                
                // GeÃ§ici bir klasÃ¶re kaydet
                $path = $file->storeAs('temp/ziyaret', $filename, 'public');
                $uploadedPaths[] = [
                    'path' => $path,
                    'url' => asset('storage/' . $path),
                    'name' => $file->getClientOriginalName(),
                    'isImage' => in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])
                ];
            }
        }

        return response()->json(['files' => $uploadedPaths]);
    }

    public function delete(Request $request)
    {
        $path = $request->input('path');
        if ($path && str_starts_with($path, 'temp/ziyaret')) {
            Storage::disk('public')->delete($path);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
    }
}
