<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use App\Services\Operations;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MainController extends Controller
{
    public function index()
    {
        //load user's notes
        $id = session('user.id');
        $notes = User::find($id)
            ->notes()
            ->whereNull('deleted_at')
            ->get()
            ->toArray();

        //show home view
        return view('home', ['notes' => $notes]);
    }

    public function newNote()
    {
        //show new note view

        return view('new_note');
    }

    public function newNoteSubmit(Request $request)
    {
        //validate request

        $request->validate(
            [
                'text_title' => 'required|min:3|max:200',
                'text_note' => 'required|min:3|max:3000'
            ],
            [
                'text_title.required' => 'O título é obrigatório',
                'text_title.min' => 'O título deve ter no mínimo :min caracteres',
                'text_title.max' => 'O título deve ter no máximo :max caracteres',

                'text_note.required' => 'A nota é obrigatória',
                'text_note.min' => 'O título deve ter no mínimo :min caracteres',
                'text_note.max' => 'O título deve ter no máximo :max caracteres',
            ]
        );

        //get user id

        $id = session('user.id');

        //create new note

        $note = new Note();
        $note->user_id = $id;
        $note->title = $request->text_title;
        $note->text = $request->text_note;

        $note->save();

        return redirect()->route('home');
    }

    public function editNote($id)
    {
        $id = Operations::decryptId($id);

        if($id == null){
            return redirect()->route('home');
        }

        //load note
        $note = Note::find($id);

        return view('edit_note', ['note' => $note]);
    }

    public function editNoteSubmit(Request $request)
    {
        //validate request

        $request->validate(
            [
                'text_title' => 'required|min:3|max:200',
                'text_note' => 'required|min:3|max:3000'
            ],
            [
                'text_title.required' => 'O título é obrigatório',
                'text_title.min' => 'O título deve ter no mínimo :min caracteres',
                'text_title.max' => 'O título deve ter no máximo :max caracteres',

                'text_note.required' => 'A nota é obrigatória',
                'text_note.min' => 'O título deve ter no mínimo :min caracteres',
                'text_note.max' => 'O título deve ter no máximo :max caracteres',
            ]
        );

        //check if note_id exists
        if($request->note_id == null){
            return redirect()->route('home');
        }

        //decrypt note_id
        $id = Operations::decryptId($request->note_id);

        if($id == null){
            return redirect()->route('home');
        }

        //load note
        $note = Note::find($id);

        //update note
        $note->title = $request->text_title;
        $note->text = $request->text_note;
        $note->save();

        return redirect()->route('home');

    }

    public function deleteNote($id)
    {
        $id = Operations::decryptId($id);

        if($id == null){
            return redirect()->route('home');
        }

        //load note

        $note = Note::find($id);

        return view('delete_note', ['note' => $note]);
    }

    public function deleteNoteConfirm($id){
        $id = Operations::decryptId($id);

        if($id == null){
            return redirect()->route('home');
        }

        //load note

        $note = Note::find($id);

        $note->delete();

        return redirect()->route('home');
    }


}
