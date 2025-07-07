<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectCategoryController extends Controller
{
    public function index()
    {
        return ProjectCategory::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ],[
            'name.required'=>'Name is required.',
            'name.max'=>'Name must be more than 100 character.',
        ]);

        return ProjectCategory::create([
            'name' => $request->name,
            'created_by' => Auth::id(),
        ]);
    }

    public function show(ProjectCategory $projectCategory)
    {
        return $projectCategory;
    }

    public function update(Request $request, ProjectCategory $projectCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $projectCategory->update([
            'name' => $request->name,
            'updated_by' => Auth::id(),
        ]);

        return $projectCategory;
    }

    public function destroy(ProjectCategory $projectCategory)
    {
        $projectCategory->delete();
        return response()->json(['message' => 'Deleted successfully!']);
    }
}
