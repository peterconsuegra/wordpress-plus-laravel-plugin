@extends('layout')

@section('header')
    
@endsection

@section('content')
<div id="loading_area"></div>
    @include('error')
	
	
    <div class="row">
        <div class="col-md-12">
			
			@if(isset($error))
			
				<div class="alert alert-danger">
				        <p>There were some problems with your input.</p>
				        <ul>
							@if($site->error_message1)
				             <li><i class="glyphicon glyphicon-remove"></i>{{$site->error_message1}}</li>
							@endif
							
							@if($site->error_message2)
				             <li><i class="glyphicon glyphicon-remove"></i>{{$site->error_message2}}</li>
							@endif
							
				        </ul>
				    </div>
			
			@endif
			
            <form action="#">
                
                <div class="form-group">
           
					<p>Id: {{$site->id}}</p>
                </div>
				
                <div class="form-group">
                   <p>Website: {{$site->name}}</p>
                </div>
				
                <div class="form-group">
                     <p>Url:
					
					 <a href="http://{{$site->url}}" target="_blank">{{$site->url}}</a></p>
                     
                </div>
				
				@if($site->app_name=="WordPress+Laravel")
				
	                <div class="form-group">
	                     <p>Check the built-in examples: <a href="http://{{$site->url}}/wordpress_plus_laravel_examples" target="_blank">{{$site->url}}/wordpress_plus_laravel_examples</a></p>
                     
	                </div>
				
				@endif
				
                
				
            </form>

        </div>
    </div>
	
	 
     <div class="row">
         <div class="col-md-12">
			
     <div class="form-group">
          <p>Output: </p>
          <pre>{{$site->output}}</pre>
     </div>
	 </div>
	 </div>
    

<script>
	
	$(document).ready(function(){
	
	$( "#show_db_info" ).click(function() {
	  $("#loading_area").html('<div id="loading_div"></div>');
  	  $.ajax({
  	        url: "/sites/get_db_info?id={{$site->id}}",
  	        type: "get",
  	        datatype: 'json',
  	        success: function(data){
			   $("#loading_area").html('');
			   aux = "<p>";
			   aux +="<strong>DB Name: </strong>"+data['db_name']+"<br/>";
			   aux +="<strong>DB User: </strong>"+data['db_user']+"<br/>";
			   aux +="<strong>DB Password: </strong>"+data['db_password']+"<br/>";
			   aux += "</p>";
			   
			   $("#db_info").html(aux);
  	        }
				
  	  });
		
		return false;
	});
	
	
	$( "#show_cms_info" ).click(function() {
	  $("#loading_area").html('<div id="loading_div"></div>');
  	  $.ajax({
  	        url: "/sites/get_cms_info?id={{$site->id}}",
  	        type: "get",
  	        datatype: 'json',
  	        success: function(data){
			   $("#loading_area").html('');
			   aux = "<p>";
			   aux +="<strong>DB User: </strong>"+data['cms_user']+"<br/>";
			   aux +="<strong>DB Password: </strong>"+data['cms_password']+"<br/>";
			   aux += "</p>";
			   
			   $("#cms_info").html(aux);
  	        }
				
  	  });
		
		return false;
	});
	
	
	/*	
	  $.ajax({
	        url: "/sites/restart",
	        type: "get",
	        datatype: 'json',
	        success: function(data){
	          //alert("success");			
	        }
				
	  });
	
	*/

@if($success)
	
	@if($success == "true")
	
 var delayInMilliseconds = 3000; //1 second

 setTimeout(function() {
   //your code to be executed after 1 second
	$("#loadMe").modal("hide");
 }, delayInMilliseconds);

  $.ajax({
        url: "/reload_server",
        type: "get",
        datatype: 'json',
	    data: {site_id : "{{$site->id}}"},
        success: function(data){
         // alert("success");	
		
        }
			
  });
  
  @endif	
  
 @endif	
		
	});
	
	</script>
	
	
@endsection