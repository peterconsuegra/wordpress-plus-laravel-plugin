@extends('layout')

@section('header')
   
@endsection

@section('content')

		<div class="row">
	            
	        <div class="col-md-12">
	
	@if (count($errors) > 0)
	    <div class="alert alert-danger">
	        <strong>Whoops! Something went wrong!</strong>

	        <br><br>
	        <ul>
	            @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	    </div>
	@endif
	
	 </div>
	 
	  </div>
	
	

		<div class="row">
	            
	        <div class="col-md-6">
    
    				<div class="pete_container">
            		<img alt="w00t!" src="/pete.png" style="height: 204px">
			
					<p style="font-size: 13px; ">Get the best of both worlds by integrating WordPress with Laravel.</p>
				</div>
		
			</div>
		
		 <div class="col-md-6">
			 <br /><br />
				 <a style="margin-top: 120px" class="btnpete" href="/wordpress_plus_laravel/create"><i class="glyphicon glyphicon-plus"></i> Create WordPress+Laravel</a>
			<br /><br /><br />
			 
		</div>
		
		</div>
		
	<div class="row">
				
	<div class="col-md-12">
						
		<a class="table_tab <?php if($tab_index == "index") echo 'index_selected'; ?>" href="/wordpress_plus_laravel">WordPress+Laravel</a>
						
	    <a class="table_tab <?php if($tab_index == "trash") echo 'index_selected'; ?>" href="wordpress_plus_laravel_trash">Trash</a>
						
	</div>
				
	</div>
	
    <div class="row">
		
        <div class="col-md-12">
			
			<div class="content table-responsive">
				
            @if(isset($sites))
                <table style="padding-left: 10px; padding-right: 10px;" class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                           <th>Name</th>
                        <th>Url</th>
                        
                        <th>Action</th>
						<th>App</th>

                         <th class="text-right">Options</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($sites as $site)
                            <tr>
                                <td>{{$site->id}}</td>
                     <td>{{$site->name}}</td>           
                    <td>
					<a href="http://{{$site->url}}" target ='_blank'>{{$site->url}}</a>
					</td>
                    
                    <td>{{$site->action_name}}</td>
					<td>{{$site->app_name}}</td>
					
                                <td class="text-right">

								    <a class="option_button" href="/wordpress_plus_laravel/restore?id={{$site->id}}" id="restore_{{$site->name}}"></i>Restore</a>

									@if($current_user->admin)
									
                                    <form action="/wordpress_plus_laravel/force_delete" method="POST" style="display: inline;" onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
                                        <input type="hidden" name="site_id" value="{{$site->id}}">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="option_button" style="background-color: #f1592a; width: 100%">Force delete</button>
                                    </form>
									
									
									 @endif
									
                                   
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
               
            @else
                <h3 class="text-center alert alert-info">Empty!</h3>
            @endif
			</div>
        </div>
    </div>
	
	@include('sites/_sites_js')

@endsection