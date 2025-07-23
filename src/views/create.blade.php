@extends('layout')

@section('header')

	
@endsection

@section('content')
@include('error')
	
<form action="/wordpress_plus_laravel/store" id ="SiteForm" method="POST" enctype="multipart/form-data">

	<input type="hidden" name="_token" value="{{ csrf_token() }}">		
	
	
	<div class="row">
		<div class="col-md-12">	
			<div class="page-header">
				<h3>Create WordPress+Laravel Instance</h3>
				
				<ul>

					<li>Each Laravel integration is created in a separate folder</li>
					<li>You can have more than one Laravel application integrated with your WordPress site. WordPress Pete makes it possible for you to access all WordPress data from your each Laravel application</li>
					<li>The "Laravel app name" is used to create the URL of the Laravel application and depends on the "Laravel Integration Type", for "Same domain" it would be <i>mywordpressite.com/myapp</i> and for "Separate Subdomain" it would be: <i>myapp.mywordpressite.com</i></li>
					<li>To see WordPress + Laravel tutorials <a href="https://wordpresspete.com/tutorials/">click here.</a></li>
				</ul>
				
				
			</div>
		</div>
	</div>
	
	
		
	<div class="row">
		<div class="col-md-6">
				
							
			<select class="form-control" id="action_name-field" name="action_name">
				<option value="">Select Action</option>
				<option value="new_wordpress_laravel">New</option>
				<option value="import_wordpress_laravel">Import</option>	
			</select>
								
			<br />
							
		</div>
							
	</div>
	
	
	
	<div class="row">
		<div class="col-md-6">
									
					
			<div class="form-group new_git_fields" id="selected_version_div" style="display: none;">
				
				<select class="form-control" id="selected_version" name="selected_version">
					<option value="">Select Laravel version</option>
					<option value="8.*">8.*</option>
					<option value="9.*">9.*</option>
					<option value="10.*">10.*</option>
					<option value="11.*">11.*</option>
					<option value="12.*">12.*</option>
				</select>	
				
			</div>
				
		</div>
	</div>
							
	
	<div class="row">
		<div class="col-md-12">
			<div class="form-group import_git_fields" id="wordpress_laravel_git_help" style="display: none;">
				<p>Import a Laravel instance from a Git repository</p>
				<ul>
					<li>WordPress+Laravel is only available for Laravel 5.5+</li>
					<li>To import a Laravel project, ensure it has public read permissions and use the URL that starts with HTTPS://. You can then put the project back in private if necessary.</li>
				</ul>
			</div>
		</div>
	</div>
	
			
	<div class="row">
		<div class="col-md-8">
					
			<div class="form-group import_git_fields" id="wordpress_laravel_git" style="display: none;">
			
				<input type="text" id="wordpress_laravel_git-field" name="wordpress_laravel_git" class="form-control" placeholder="https://github.com/peterconsuegra/example.git" />
                   
				<div id="wordpress_laravel_git_error_area"> 
				</div>
			</div>
				
		</div>
		
		<div class="col-md-4">
				
			<div class="form-group import_git_fields" id="wordpress_laravel_git_branch" style="display: none;">
				
				<input type="text" id="wordpress_laravel_git_branch-field"  placeholder="Git branch" name="wordpress_laravel_git_branch" class="form-control"  />
               
				<div id="wordpress_laravel_git_error_area"> 
				</div>
			</div>
			
		</div>
				
	</div>    
	
	<div class="row">
		<div class="col-md-6">
						
			<div class="form-group integration_type" id="integration_type" style="display: none;">
			
			<select class="form-control" id="integration_type-field" name="integration_type">';
			 <option value="">Laravel Integration Type</option>
			 <option value="inside_wordpress">Same domain</option>
			 <option value="separate_subdomain">Separate subdomain</option>
		 	</select>
			
			</div>
							
		</div>
							
	</div>
	
				
	<div class="row">
		<div class="col-md-6">
			
			
						
			<div id="url_div_wordpress_laravel" class="git_fields" style="display: none;">
				
				<div id="integration_param">
				
					
				
				</div>
					 
			</div>
		
		</div>
		
		<div class="col-md-6">
			<div id="massive_form">
	
			</div>
		</div>
				
	</div>
           
               
	<button type="submit" id="create_button" class="btn btn-primary btn-lg">
                    <span class="glyphicon glyphicon-plus"></span> Create
                </button>
</form>

	
<script>

	$("#action_name-field").change(function() {
		form_logic($("#app_name-field").val(),$("#action_name-field").val());
	});
	
	
	$("#integration_type-field").change(function() {
		if($(this).val()=="inside_wordpress"){
			input = '<input type="text" id="wordpress_laravel_name-field" placeholder="Laravel app name" name="wordpress_laravel_name" class="form-control inline_class url_wordpress_laravel" />';
		}else{
			input = '<input type="text" id="wordpress_laravel_name-field" placeholder="Laravel app name" name="wordpress_laravel_name" class="form-control inline_class url_wordpress_laravel" />';
		}
		$("#integration_param").html(input);
	});
	
				
	
	function form_logic(app,action){
		if((app != 0) && (action != 0)){
			hide_fields();
				
			
				
				if(action=="new_wordpress_laravel"){
				  
					$("#loading_area").html('<div id="loading_div"></div>');
					$.ajax({
						url: "/sites/get_sites",
						type: "get",
						datatype: 'json',
						data: { app_name: "Wordpress+laravel"},
						success: function(data){
							$("#loading_area").html('');
							console.log(data);
							
							
							select_aux = '<select id="wordpress_laravel_target-field" name="wordpress_laravel_target" class = "form-control" >';	
							select_aux +='<option value="">Select the WordPress instance to integrate</option>';
							var arrayLength = data.length;
							for (var i = 0; i < arrayLength; i++) {	
								select_aux +='<option value="'+data[i].id+'">'+data[i].url+'</option>';
							}
						
							select_aux +='</select><br/>';
							$("#massive_form").html(select_aux);
	    				    
							$(".git_fields").show();	
							$(".new_git_fields").show();
							$("#integration_type").show();
							
						}
					
					});
				
				}else if (action=="import_wordpress_laravel"){
				  
			  
					$("#loading_area").html('<div id="loading_div"></div>');
					$.ajax({
						url: "/sites/get_sites",
						type: "get",
						datatype: 'json',
						data: { app_name: "Wordpress+laravel"},
						success: function(data){
							$("#loading_area").html('');
							console.log(data);
							
							
							
							select_aux = '<div class="row">';
							select_aux += '<div class="col-md-12">';
							select_aux += '<select id="wordpress_laravel_target-field" name="wordpress_laravel_target" class = "form-control" >';	
							select_aux +='<option value="">Select the WordPress instance to integrate</option>';
							var arrayLength = data.length;
							for (var i = 0; i < arrayLength; i++) {	
								select_aux +='<option value="'+data[i].id+'">'+data[i].url+'</option>';
							}
						
							select_aux +='</select><br/>';
							select_aux +='</div>';
							select_aux +='</div>';
							$("#massive_form").html(select_aux);
	    				
							$(".git_fields").show();	
							$(".import_git_fields").show();	
							$("#integration_type").show();
							
							
						}
						
						
					
					});
					
					
				}
			}
	}
	
	
	
	function hide_fields(){
		
		$(".git_fields").hide();	
		$(".import_git_fields").hide();	
		$(".new_git_fields").hide();	
		$("#integration_type").hide();		  
	}
				
	  
	$(document).ready(function(){
					  
		$('#name-field').keyup(function(e){
			$('#wordpress_laravel_name-field').val($('#name-field').val());
		});
					  
		@if($pete_options->get_meta_value('environment') == "development")
					  
		$('#name-field').keyup(function(e){
			$('#url-field').val($('#name-field').val());
		});
					  
		@endif
					  
		$('#wordpress_laravel_git-field').keyup(function(e){
						  
			var n = $(this).val().startsWith("https://");
			var n2 = $(this).val().startsWith("git@");
			if((n==true) || (n2 ==true)){
				$(this).removeClass("text_area_fancy_error").addClass("text_area_fancy_done");
				console.log("nice");
			}else{
				$(this).removeClass("text_area_fancy_done").addClass("text_area_fancy_error");
							 
				console.log("bad");
			}
		});
					  
	});
		
	
	</script>


	
	@endsection