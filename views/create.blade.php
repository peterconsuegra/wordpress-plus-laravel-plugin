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
				<h4>Create WordPress+Laravel</h4>
	
				<ul>
					<li>Check that the php version is compatible with the Laravel version you want to integrate with your WordPress.</li>
					<li>You can upgrade the php version of your WordPress Pete, check the installation guide for your operating system: <a href="https://wordpresspete.com/wordpresspete-mac-osx-installation/" target="_blank">MacOS</a> <a href="https://wordpresspete.com/wordpresspete-linux-installation/" target="_blank">Linux</a></li>
					<li>For more information check this <a target="_blank" href="https://wordpresspete.com/2018/11/03/create-a-wordpress-laravel-integration-with-wordpresspete-part-one/">tutorial</a></li>
				</ul>
	
			</div>
									
							
			<select class="form-control" id="action_name-field" name="action_name">
				<option value="">Select Action</option>
				<option value="new_wordpress_laravel">New</option>
				<option value="import_wordpress_laravel">Import</option>
							
			</select>
								
			<br />
							
		</div>
							
	</div>
	
	<div class="row">
		<div class="col-md-12">
									
					
			<div class="form-group" id="selected_version_div" style="display: none;">
				<p> Select Laravel version</p>
				<select class="form-control" id="selected_version" name="selected_version">
					
					<option value="5.5">5.5</option>
					<option value="5.6">5.6</option>
					<option value="5.7">5.7</option>
					<option value="5.8">5.8</option>
					<option value="6.*">6.*</option>
					<option value="7.*">7.*</option>
					<option value="8.*">8.*</option>
					
				</select>	
				
                   
				<div id="name_error_area"> 
				</div>
			</div>
				
		</div>
	</div>
							
							
				
	<div class="row">
		<div class="col-md-12">
									
			<div id="project_to_clone">
					
			</div>
					
			<div class="form-group" id="name_div" style="display: none;">
				<p> Project name</p>
				<input type="text" id="name-field" name="name" class="form-control" value="{{ old("name") }}" />
                   
				<div id="name_error_area"> 
				</div>
			</div>
				
		</div>
	</div>
				
				
	<div class="row">
		<div class="col-md-12">
			<div class="form-group" id="import_from_git" style="display: none;">
				
				<p><input type="checkbox" id="import_from_git_checkbox" name="import_from_git_checkbox"> Import from GIT</p>
 			   
			</div>
				
		</div>
	</div>
	
			
	<div class="row">
		<div class="col-md-12">
					
			<div class="form-group" id="wordpress_laravel_git" style="display: none;">
				<p>Git URL.</p>
			<ul>
				<li>WordPress+Laravel is only available for Laravel 5.5+</li>
				<li>To import a repository that starts with https:// please make sure it's public (Recommended).</li>
				<li>To import a repository that starts with @git make sure you have added the ssh keys of your machine to the repository account.</li>
				
			</ul>
				<input type="text" id="wordpress_laravel_git-field" name="wordpress_laravel_git" class="form-control" />
                   
				<div id="wordpress_laravel_git_error_area"> 
				</div>
			</div>
				
		</div>
				
	</div>    
	
	<div class="row">
	<div class="col-md-12">
				
		<div class="form-group" id="wordpress_laravel_git_branch" style="display: none;">
			<p>Git branch</p>
			
			<input type="text" id="wordpress_laravel_git_branch-field" name="wordpress_laravel_git_branch" class="form-control"  />
               
			<div id="wordpress_laravel_git_error_area"> 
			</div>
		</div>
			
	</div>
	</div>
	  
	<div class="row">
		<div class="col-md-12">
									
			<div class="form-group" id="url_div" style="display: none;">
				<p>URL</p>
				<input type="text" id="url-field" name="url" class="form-control " value="{{ old("url") }}" />
					   
				<div id="url_error_area"> 
				</div>
					   
			</div>
		</div>
	</div>
				
	<div class="row">
		<div class="col-md-12">
						
			<div id="url_div_wordpress_laravel" style="display: none;">
				<p>URL</p>
				<input type="text" id="wordpress_laravel_name-field" name="wordpress_laravel_name" class="inline_class url_wordpress_laravel" />
				<div id="url_wordpress_laravel_helper" class="inline_class">.</div>
				 
			</div>
			<br />
		</div>
				
	</div>
				
							
	<div id="massive_form">
		
	</div>
                    
               
	<button type="submit" id="create_button" style="width:100%;" class="btnpete">Create</button>
	<br /><br />
</form>
			
			
			

        
	
<script>
	
	var patt = new RegExp(/^[a-z0-9]+$/i);
	var patturl = new RegExp(/^[a-z0-9\.]+$/i);
	
	$("#url_template").change(function() {
		if($("#url-field").prop("readonly")){
			$("#url-field").prop("readonly", false);
		}else{
			$("#url-field").prop("readonly", true);
		}
		//alert("hola");
	});
	
	$("#big_file").click(function() {
		
		//alert("hi big");
		$("#big_file_route").toggle();
	});
	
	$("#action_name-field").change(function() {
		form_logic($("#app_name-field").val(),$("#action_name-field").val());
	});
	
	$("#import_from_git_checkbox").change(function() {
		$("#wordpress_laravel_git").toggle();
		$("#wordpress_laravel_git_branch").toggle();
	});
	
	function wordpress_laravel_select(){
		$("#wordpress_laravel_target-field").change(function() {
			$("#url_wordpress_laravel_helper").html("."+$(this).find('option:selected').text());
		});
	}
				
	function hide_all(){
		$("#url_div_wordpress_laravel").hide();
		$("#url_div_wordpress_helper").hide();
		$("#name_div").hide();
		$("#wordpress_laravel_git").hide();
		$("#wordpress_laravel_git_branch").hide();
		$("#big_file_container").hide();
		$("#big_file").hide();
		$("#big_file_route").hide();
		$("#name_div").hide();
		$("#url_div").hide();
		$("#wordpress_laravel_target-field").hide();
	}
	
	function form_logic(app,action){
		if((app != 0) && (action != 0)){
			hide_fields();
			//$("#name_div").show();
			
			console.log(app);
			console.log(action);
			
				
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
							select_aux = '<div class="row">';
							select_aux += '<div class="col-md-12">';
							select_aux += '<select id="wordpress_laravel_target-field" name="wordpress_laravel_target" class = "form-control" >';	
							select_aux +='<option value="">Select WordPress project</option>';
							var arrayLength = data.length;
							for (var i = 0; i < arrayLength; i++) {	
								select_aux +='<option value="'+data[i].id+'">'+data[i].url+'</option>';
							}
						
							select_aux +='</select><br/>';
							select_aux +='</div>';
							select_aux +='</div>';
	    				    
							$("#selected_version_div").show();
							$("#url_div_wordpress_laravel").show();
							$("#url_div_wordpress_helper").show();
							$("#name_div").show();
							//$("#wordpress_laravel_git").show();
							//$("#wordpress_laravel_git_branch").show();
							$("#massive_form").html(select_aux);
							$("#massive_form").show();
							$("#big_file_container").hide();
							$("#big_file").hide();
							$("#big_file_route").hide();
							wordpress_laravel_select();
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
							select_aux +='<option value="">Select a Wordpress project</option>';
							var arrayLength = data.length;
							for (var i = 0; i < arrayLength; i++) {	
								select_aux +='<option value="'+data[i].id+'">'+data[i].url+'</option>';
							}
						
							select_aux +='</select><br/>';
							select_aux +='</div>';
							select_aux +='</div>';
	    				
							$("#url_div_wordpress_laravel").show();
							$("#url_div_wordpress_helper").show();
							$("#name_div").show();
							
							//$("#wordpress_laravel_git").show();
							//$("#wordpress_laravel_git_branch").show();
							$("#massive_form").html(select_aux);
							$("#massive_form").show();
							$("#selected_version_div").hide();		  
									
							$("#wordpress_laravel_git").show();
							$("#wordpress_laravel_git_branch").show();
						  
							wordpress_laravel_select();
						}
					
					});
					
					
				}
			}
	}
	
	
	
	function hide_fields(){
				
		$("#url_div_wordpress_laravel").hide();
		$("#url_div_wordpress_helper").hide();
		$("#name_div").hide();
		$("#wordpress_laravel_git").hide();
		$("#wordpress_laravel_git_branch").hide();
		$("#big_file_container").hide();
		$("#big_file").hide();
		$("#big_file_route").hide();
		$("#name_div").hide();
		$("#url_div").hide();
		$("#wordpress_laravel_target-field").hide();
				
		$("#massive_form").hide();
		$("#name_div").hide();
		$("#url_div").hide();
		$("#zip_file_url_div").hide();
		$("#db_root_pass_div").hide();
	  
		$("#url_div_wordpress_laravel").hide();
		$("#url_div_wordpress_helper").hide();
		$("#wordpress_laravel_git").hide();
		$("#wordpress_laravel_git_branch").hide();
				  
		$("#project_to_clone").hide();
				  
	}
				
	  
	$("#create_button").click(function() {
	  
		app_name = $("#app_name-field").val();
		action_name = $("#action_name-field").val();
		
		$('<input>').attr({
		    type: 'hidden',
		    id: 'action_name',
		    name: 'action_name',
			value: action_name
		}).appendTo('SiteForm');
		
	});
				  
				  
				  
	//Wordpress+laravel input logic
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