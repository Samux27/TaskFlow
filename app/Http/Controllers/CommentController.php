<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Comment;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
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

public function sendComment(Request $request, Task $task)
{ 
    
    $request->validate([
        'content' => 'required|string|max:1000',
    ]);

    $task->comments()->create([
        'user_id' => auth()->id(), // O el ID del usuario autenticado
        'content' => $request->input('content'),
    ]);
Log::create([
        'user_id'    => Auth::id(),
        'action'     => 'Mensaje de Tarea',
        'details'    =>  'usuario: ' . Auth::user()->name . ' ha enviado un comentario a la tarea: ' . $task->title . ' (ID: ' . $task->id . ')' . ' - Comentario: ' . $request->input('content') ,  
        
        'ip_address' => $request->ip(),
    ]);
    return back(); // O response()->json() si usas SPA
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
