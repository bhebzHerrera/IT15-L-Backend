<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->query('category');

        $categories = Category::orderBy('name')->get();

        $postsQuery = Post::with('category')->orderBy('created_at', 'desc');
        if ($categoryId) {
            $postsQuery->where('category_id', $categoryId);
        }

        $posts = $postsQuery->get();
        $activeCategory = $categories->firstWhere('id', (int) $categoryId);

        return view('welcome', [
            'categories' => $categories,
            'posts' => $posts,
            'activeCategory' => $activeCategory,
        ]);
    }
}
