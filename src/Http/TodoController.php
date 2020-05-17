<?php


namespace Pete\WordPressPlusLaravel\Http;

use Pete\WordPressPlusLaravel\Todo;

use App\Http\Controllers\Controller;

class TodoController extends Controller
{
    public function getUserTodoList()
    {
		$todos = Todo::orderBy('created_at')->get();
        return view("todo::todo-list")->with('todos',$todos);
    }
}
