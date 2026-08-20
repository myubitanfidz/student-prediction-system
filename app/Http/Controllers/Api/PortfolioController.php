<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PortfolioController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'links'   => 'nullable|string',
            'files'   => 'nullable|array|max:5',
            'files.*' => 'file|max:102400',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = $request->user()->id;
        $uploadedPaths = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('portfolios', 'public');
                $uploadedPaths[] = asset('storage/' . $path);
            }
        }

        $portfolio = Portfolio::updateOrCreate(
            ['user_id' => $userId],
            [
                'links' => $request->links,
                'files' => !empty($uploadedPaths) ? $uploadedPaths : null,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Portofolio berhasil disimpan',
            'data'    => $portfolio,
        ]);
    }
}