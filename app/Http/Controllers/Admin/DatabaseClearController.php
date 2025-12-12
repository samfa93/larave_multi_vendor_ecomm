<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class DatabaseClearController extends Controller
{
    function index() : View{
        return view('admin.database-clear.index');
    }

    function clearDatabase()
    {
        Artisan::call('migrate:fresh', ['--seed' => true]);
        if(File::exists(public_path('uploads'))){
            File::cleanDirectory(public_path('uploads'));
            File::put(public_path('uploads') . DIRECTORY_SEPARATOR . '.gitkeep', '');
        }
        return response()->json(['success' => true, 'message' => 'Database cleared successfully']);
    }
}
