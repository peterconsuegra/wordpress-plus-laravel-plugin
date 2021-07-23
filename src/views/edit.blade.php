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
                </div>
				
                
                <div class="form-group">
                     <p>URL:
					
					 <a href="http://{{$site->url}}" target="_blank">{{$site->url}}</a></p>
                     
                </div>
				
				@if($site->app_name=="WordPress+Laravel")
				
	                <div class="form-group">
	                     <p>Check the built-in examples: <a href="http://{{$site->url}}/wordpress_plus_laravel_examples" target="_blank">{{$site->url}}/wordpress_plus_laravel_examples</a></p>
                     
	                </div>
				
				@endif
				
				<div class="form-group">
				<p>For more information check this <a target="_blank" href="https://wordpresspete.com/2018/11/03/create-a-wordpress-laravel-integration-with-wordpresspete-part-one/">tutorial</a></p>
				
				</div>
                
				
            </form>

        </div>
		
	 <div class="col-md-6">
		 
		 @if($pete_options->get_meta_value('ssl_feature') == "on")
		 
		 	 <br />
		 	 <p>Curent status SSL: {{$site->ssl}}</p>
		
			 <form action="/sites/delete_ssl" id ="SiteForm" method="POST">
				 
				 <input type="hidden" name="site_id" value="{{ $site->id }}">    
				 <input type="hidden" name="_token" value="{{ csrf_token() }}">          
 				 <button type="submit" id="create_button" class="btnpete">Delete SSL</button>
	    	
 			 </form>
	    
	 
			 <h3>SSL Activation File</h3>
	    	
			 <form action="/sites/upload_activation_file" id ="SiteForm" method="POST" enctype="multipart/form-data">
				 <input type="hidden" name="site_id" value="{{ $site->id }}">    
				 <input type="hidden" name="_token" value="{{ csrf_token() }}">          
				 <label>Activation File</label>
				 <input type="file" id="activation_file" name="activation_file">
				 <br />
			 
 				 <button type="submit" id="create_button" class="btnpete">Upload Activation File</button>
	    	
 			 </form>
	    	
			 <h3>Upload SSL</h3>
	    	
			 <form action="/sites/upload_ssl" id ="sslform" method="POST" enctype="multipart/form-data">
			   <input type="hidden" name="site_id" value="{{ $site->id }}">    
			   <input type="hidden" name="_token" value="{{ csrf_token() }}">          
			 <label>SSL CRT</label>
			 <input type="file" id="ssl_crt" name="ssl_crt">
			 <br />
	    	
			 <label>SSL Key</label>
			 <input type="file" id="ssl_key" name="ssl_key">
			 <br />
	    	
			 <label>SSL Bundle</label>
			 <input type="file" id="ssl_bundle" name="ssl_bundle">
			 <br />
	    	
			 <button type="submit" id="sslform_button" class="btnpete">Upload SSL</button>
	    	
	 		</form>
			
		@endif
	 				 
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