<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PortfolioController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'links'   => 'nullable|string',
            'files'   => 'nullable|array|max:5',
            'files.*' => 'file|max:102400', // 102400 KB = 100 MB per file
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $uploadedPaths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Simpan ke storage/app/public/portfolios
                $path = $file->store('portfolios', 'public');
                $uploadedPaths[] = asset('storage/' . $path);
            }
        }

        $portfolio = Portfolio::updateOrCreate(
            ['user_id' => $request->user_id],
            [
                'links' => $request->links,
                'files' => $uploadedPaths,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Portofolio berhasil disimpan',
            'data'    => $portfolio,
        ]);
    }
}