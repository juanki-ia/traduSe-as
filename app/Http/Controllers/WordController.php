<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Word;
use Illuminate\Support\Facades\Storage;

class WordController extends Controller
{
    public function index($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $words = Word::where('category_id', $categoryId)->get();
        return view('learning.words.index', compact('category', 'words'));
    }

    public function manage($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $words = Word::where('category_id', $categoryId)->get();
        return view('learning.words.manage', compact('category', 'words'));
    }

    public function create($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        return view('learning.words.form', compact('category'));
    }

    public function show($categoryId, Word $word)
    {
        $category = Category::findOrFail($categoryId);

        if ($category->name == "Varios") {
            return view('learning.words.show2', compact('category', 'word'));
        }

        if ($category->name == "Alfabeto") {
            return view('learning.words.show3', compact('category', 'word'));
        }

        if ($category->name == "Numeros") {
            return view('learning.words.show4', compact('category', 'word'));
        }

        if ($category->name == "Saludos") {
            return view('learning.words.show', compact('category', 'word'));
        }

    }

    public function store(Request $request, $categoryId)
    {
        $request->validate([
            'name' => 'required|string|max:30|unique:words',
            'gif_path' => 'required|file|mimes:mp4',
        ]);

        $category = Category::findOrFail($categoryId);

        // Reemplazar los espacios en el nombre por guiones bajos
        $sanitizedFileName = str_replace(' ', '_', $request->name);
        $videoFileName = $sanitizedFileName . '.' . $request->file('gif_path')->getClientOriginalExtension();
        $videoPath = $request->file('gif_path')->storeAs('public/videos/' . $category->name, $videoFileName);

        Word::create([
            'name' => $request->name,
            'gif_path' => $videoFileName,
            'category_id' => $categoryId,
        ]);

        return redirect()->route('words.manage', $categoryId);
    }


    public function edit($categoryId, Word $word)
    {
        $category = Category::findOrFail($categoryId);
        return view('learning.words.form', compact('category', 'word'));
    }

    public function update(Request $request, $categoryId, Word $word)
    {
        $request->validate([
            'name' => 'required|string|max:30|unique:words,name,' . $word->id,
            'gif_path' => 'file|mimes:mp4',
        ]);

        $category = Category::findOrFail($categoryId);

        // Guardar el nombre original del archivo antes de la actualización
        $originalVideoFileName = $word->gif_path;

        if ($request->hasFile('gif_path')) {
            // Eliminar el video anterior si existe
            if ($word->gif_path) {
                Storage::delete('public/videos/' . $category->name . '/' . $word->gif_path);
            }

            // Reemplazar los espacios en el nombre por guiones bajos
            $sanitizedFileName = str_replace(' ', '_', $request->name);
            $newVideoFileName = $sanitizedFileName . '.' . $request->file('gif_path')->getClientOriginalExtension();
            $videoPath = $request->file('gif_path')->storeAs('public/videos/' . $category->name, $newVideoFileName);
            $word->gif_path = $newVideoFileName;
        } else {
            // Si solo se cambia el nombre de la palabra, pero no el video
            if ($request->name !== $word->name) {
                $sanitizedFileName = str_replace(' ', '_', $request->name);
                $newVideoFileName = $sanitizedFileName . '.' . pathinfo($word->gif_path, PATHINFO_EXTENSION);
                Storage::move('public/videos/' . $category->name . '/' . $originalVideoFileName, 'public/videos/' . $category->name . '/' . $newVideoFileName);
                $word->gif_path = $newVideoFileName;
            }
        }

        // Si el nombre de la palabra ha cambiado, actualizarlo
        if ($request->name !== $word->name) {
            $word->name = $request->name;
        }

        $word->save();

        return redirect()->route('words.manage', $categoryId);
    }


    public function destroy($categoryId, Word $word)
    {
        $category = Category::findOrFail($categoryId);
        // Eliminar el video asociado antes de eliminar la palabra
        if ($word->gif_path) {
            Storage::delete('public/videos/' . $category->name . '/' . $word->gif_path);
        }
        $word->delete();

        return redirect()->route('words.manage', $categoryId);
    }
}
