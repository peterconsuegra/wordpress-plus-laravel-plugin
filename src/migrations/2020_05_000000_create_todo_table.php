<?php


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTodoTable extends Migration
{
    
    public function up()
    {
        //
		Schema::create('todos', function(Blueprint $table) {
			
            $table->increments('id');
            $table->string('user_id')->nullable();
            $table->boolean('completed')->nullable();
			$table->text('todo')->nullable();
            $table->timestamps();
			
        });
    }

   
    public function down()
    {
        //
    }
}