<?php

namespace App\Http\Controllers\Admin;

use App\Models\page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class pageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pages = page::orderBy("created_at", "desc")->paginate(10);
        if($request->get('keyword')) {
           $pages = page::where('name','like','%'.$request->get('keyword').'%')->paginate(10);
        }
        return view('admin.pages.list', compact("pages"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:pages,slug',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        } else {
            $page = new page;
            $page->name = $request->name;
            $page->slug = $request->slug;
            $page->content = $request->content;
            $page->save();
            session()->flash('success','Static Page Created Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Static Page Created Successfully'
            ]);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $page = page::find($id);

        if ($page == null) {
            return response()->json([
                'status' => false,
                'message' => 'Record Not Found'
            ]);
        }
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // return $id;
        $page = page::find($id);
        if($page == null) {
            return response()->json([
                'status' => true,
                'message' => 'Record Not Found'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:pages,slug,' . $id . ',id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        } else {

            $page->name = $request->name;
            $page->slug = $request->slug;
            $page->content = $request->content;
            $page->save();
            session()->flash('success','Static Page Updated Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Static Page Updated Successfully'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $req, $id)
    {
        $page = page::where('id', $id)->first();
        if ($page == null) {
            session()->flash('error','Page not Found');
            return response()->json([
                'status' => true,
            ]);
        } else {
            $page->delete();
            return response()->json([
                'status' => true,
                'message' => "<div class='alert alert-success'>Page remove Successfully</div>"
            ]);

        }
    }
}
