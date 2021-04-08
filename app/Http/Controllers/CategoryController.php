<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    
    public function index()
    {
        $categories = Category::get();

        return $categories;

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors();
            return $message;
        }
        
        
        $categories = Category::create([
            'name'=>$request->input('category'),
        ]);

        return $categories;

    }
}
