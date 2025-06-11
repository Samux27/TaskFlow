<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Task $task)
    {
        dd("hago lo que sea esto ");
        // Incluye usuario que escribió el comentario
        $comments = $task->comments()->with('user')->orderBy('created_at')->get();
        return response()->json($comments);
    }

    // Añadir comentario a una tarea
    public function store(Request $request, Task $task)
    {
        dd("mando");
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $comment = $task->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return response()->json($comment->load('user'));
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
