@extends('layout')

@section('header')
    
@endsection

@section('content')
<div id="loading_area"></div>
    @include('error')
	
	
    <div class="row">
        <div class="col-md-6">
			
			
			
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
                
				 <div class="page-header">
				 <h3>Options</h3>
				</div>
				
                <div class="form-group">
           
					<p>Id: {{$site->id}}</p>
					
					<p>Integration type: {{$site->integration_type}}</p>
					
                </div>
				
                
                <div class="form-group">
					 
					 @if($site->integration_type=="separate_subdomain")
                     	<p>URL: <a href="http://{{$site->wordpress_laravel_url}}" target="_blank">{{$site->wordpress_laravel_url}}</a></p>
					 @else
					 	<p>URL: <a href="http://{{$target_site->url}}/{{$site->name}}" target="_blank">{{$target_site->url}}/{{$site->name}}</a></p>
					 @endif
                     
                </div>
				
				
				<div class="form-group">
				
					@if($site->integration_type=="separate_subdomain")
	                 	<p>Check the built-in examples: <a href="http://{{$site->url}}/wordpress_plus_laravel_examples" target="_blank">{{$site->url}}/wordpress_plus_laravel_examples</a></p>
					 @else
	                 	<p>Check the built-in examples: <a href="http://{{$target_site->url}}/{{$site->name}}/wordpress_plus_laravel_examples" target="_blank">{{$target_site->url}}/{{$site->name}}/wordpress_plus_laravel_examples</a></p>		
					 @endif
				</div>
				
				<div class="form-group">
				<p>For more information check the  <a target="_blank" href="https://github.com/peterconsuegra/wordpress-pete-docker/wiki">WIKI</a></p>
				
				</div>
                
				
            </form>

        </div>
		
	 <div class="col-md-6">
		 
			@include('sites/_ssl_form')
							 
	   </div>
		
    </div>
	
	 
   <div class="row">
       <div class="col-md-12">
	 	<p>Terminal output: </p>	
  	 	<div class="console_area">
       
        	  <pre>{{$site->output}}</pre>
		 
   	 	</div>
	 </div>
 </div>
 
   <div class="row" style="margin-top: 10px">
       <div class="col-md-12">
	 	<p>Web server Apache error log: </p>	
		<i>{{$web_server_error_file}}</i>
  	 	<div class="console_area">
       
        	  <pre>{{$web_server_error_content}}</pre>
		  
   	 	</div>
	 </div>
 </div>
 
 
   <div class="row" style="margin-top: 10px">
       <div class="col-md-12">
	 	<p>Web server Apache access log: </p>	
		<i>{{$web_server_access_file}}</i>
  	 	<div class="console_area">
       
        	  <pre>{{$web_server_access_content}}</pre>
		  
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