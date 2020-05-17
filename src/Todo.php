<?php

namespace Amitav\Todo;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model{
	
	// Amitav\Todo\Todo::create(['user_id'=> 1, 'completed' => 1, 'todo' => 'Create tutorial']);
	
	protected $table = "todos";
	
	protected $fillable = ['user_id', 'completed', 'todo'];
	
}