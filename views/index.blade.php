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
    
        	
            		<img alt="w00t!" src="/pete.png" style="height: 204px">
			
					<p style="font-size: 13px; ">Get the best of both worlds by integrating WordPress with Laravel.</p>
		
			</div>
		
		 <div class="col-md-6">
			 <br /><br />
				 <a style="margin-top: 120px" class="btnpete" href="/wordpress_plus_laravel/create"><i class="glyphicon glyphicon-plus"></i> Create WordPress+Laravel Instance</a>
			<br /><br /><br />
			 
		</div>
		
		</div>
		
	<div class="row">
				
	<div class="col-md-12">
						
		<a class="table_tab <?php if($tab_index == "index") echo 'index_selected'; ?>" href="/wordpress_plus_laravel">WordPress+Laravel</a>
						
	    <a class="table_tab <?php if($tab_index == "trash") echo 'index_selected'; ?>" href="/wordpress_plus_laravel/trash">Trash</a>
						
	</div>
				
	</div>
	
    <div class="row">
        <div class="col-md-12">
			
			<div class="content table-responsive">
            @if($sites->count())
                <table style="padding-left: 10px; padding-right: 10px;" class="table table-hover table-striped">
                    <thead>
                        <tr>
                         <th>Id</th>
                        <th>Project Name</th>
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
					<td>{{$site->app_name}}
					
						@if($site->app_name == "WordPress+Laravel")
							@if($site->action_name == "New")
							<br />
							Laravel version: {{$site->laravel_version}}
							@endif
						@endif
					
					</td>
					
                                <td class="text-right">
                                  	
									 <a class="option_button" role="group" href="/wordpress_plus_laravel/{{$site->id}}/edit"> Options</a>
									 
                                    <form action="/wordpress_plus_laravel/destroy" method="POST" style="display: inline;" onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
										
                                        <input type="hidden" name="site_id" value="{{$site->id}}">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="option_button" style="background-color: #f1592a; width: 100%">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $sites->render() !!}
            @else
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                           	<th>Project Name</th>
                        	<th>Url</th>
                        	<th>Action</th>
							<th>App</th>
                         	<th class="text-right">Options</th>
                        </tr>
                    </thead>

                    <tbody>
					</tbody>
					
				</table>
            @endif
			</div>
        </div>
    </div>
	
	
	
	<div class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	        <h4 class="modal-title">Modal title</h4>
	      </div>
	      <div class="modal-body">
			<form id="site_form" action="/snapshot_creation" style="display:none" method="POST">
				<input type="hidden" name="_method" value="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">         
			    <input type="hidden" id="snapshot_label_form" name="snapshot_label_form" value="">
				<input type="hidden" id="site_id_form" name="site_id_form" value="">
			    <input type="submit" />
			</form>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary">Save changes</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	
	
	<script type="text/javascript">
	
	
	   
	
    	$(document).ready(function(){
			
  			@if(isset($exporturl))
				console.log("download url: {{$exporturl}}");
  		  		window.location.assign("/{{$exporturl}}");
  		
  		  @endif
				
			
			$(".create_snapshot").click(function() {
				site_id = $(this).attr("site_id");
				
			    BootstrapDialog.show({
			          title: 'Create Snapshot',
			          message: '<label>Label</label><br /><input id="snapshot_label" name="snapshot_label" value="">',
			          buttons: [{
			               label: '<a class ="btnpete">Create Snapshot</a>',
			              action: function(dialog) {
			                  // submit the form
							  snapshot_label = $("#snapshot_label").val();
							  $("#site_id_form").val(site_id);
							  $("#snapshot_label_form").val(snapshot_label);
			                  $('#site_form').submit()
			              }
			          }]
			      });
				
			});
			
        	//demo.initChartist();
			/*
        	$.notify({
            	icon: 'pe-7s-arc',
            	message: "Welcome to the future of OZONE. Welcome to <b>Massive Server</b>"

            },{
                type: 'info',
                timer: 4000
            });
			*/
			
			
		   @if(isset($success))
			
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
				   data: {site_id : "{{$site_id}}}"},
		           success: function(data){
		            // alert("success");	
	
		           }
		
		     });

		    @endif
			 
		  @endif	
		   

    	});
		
		
	</script>
	

@endsection