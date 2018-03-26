<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Storage;

class PostsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Post::with('user')->latest()->limit(5)->get();
    }

    public function show(Post $post)
    {
        return $post;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'title' => 'required',
            'body' => 'required'
        ]);

        $post = new Post($request->all());

        // Get the file real name.
        $fileName =  time() . $_FILES['image']['name'];
        // Generate file object.
        $file = new File($_FILES['image']['tmp_name']);
        // Save the file to storage and save the url to post->image.
        Storage::putFileAs('public/images', $file, $fileName);
        $post->image = 'images/' . $fileName;

        // Associate post with authenticated user and save it.
        $request->user()->posts()->save($post);

        return $post;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required'
        ]);

        $post->update($request->all());
        return $post;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return $post;
    }
}
