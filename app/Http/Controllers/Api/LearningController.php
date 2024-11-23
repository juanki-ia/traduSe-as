<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class LearningController extends Controller
{
    public function get()
    {
        try {
            $categories = Category::with('words')->get();
            // Transformar los datos para incluir solo los campos necesarios
            $transformedCategories = $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'words' => $category->words->map(function ($word) {
                        return [
                            'id' => $word->id,
                            'name' => $word->name,
                            'gif_path' => $word->gif_path,
                        ];
                    }),
                ];
            });
            return response()->json([
                'categories' => $transformedCategories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se encontraron las categorías, TG'
            ], 404);
        }
    }
}
