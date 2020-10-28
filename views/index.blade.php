@extends('layout')

@section('header')

@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">

	 <div class="page-header">
	 <h3>Restore WordPress Site From Backup</h3>
	</div>
	
	</div>
	</div>
	
    <div class="row">
        <div class="col-md-12">
			<div class="content table-responsive">
            @if($backups->count())
                <table style="padding-left: 10px; padding-right: 10px;" class="table table-hover table-striped">
                    <thead>
                        <tr>
                        <th>Project Name</th>
                        <th>Url</th>
                        <th>Label</th>
						<th>Filename</th>
                         <th class="text-right">Options</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($backups as $backup)
                            <tr>
                     		   <td>{{$backup->name}}</td>           
                    		   <td><a href="http://{{$backup->url}}" target ='_blank'>{{$backup->url}}</a></td>  
							   <td>{{$backup->schedulling}}</td>
							   <td>{{$backup->file_name}}</td>
							   
                                <td class="text-right">
									
									<a class="option_button restore_backup_action" id="backup_{{$backup->schedulling}}" backup_url="{{$backup->url}}" backup_id="{{$backup->id}}" href="#">Restore</a>
									
                                    <form action="/wordpress_backups/destroy" method="POST" style="display: inline;" onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
										
                                      <input type="hidden" name="_method" value="POST">
									  <input type="hidden" name="view" value="snapshots">
                                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
										
										<input type="hidden" name="backup_id" value="{{$backup->id}}">
										
                                        <button type="submit" class="option_button" style="background-color: #f1592a; width: 100%">Delete</button>
                                    </form>
                                   
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            
            @else
               <table style="padding-left: 10px; padding-right: 10px;" class="table table-hover table-striped">
                    <thead>
                        <tr>
	                        <tr>
	                        <th>Id</th>
	                        <th>Project Name</th>
	                        <th>Url</th>
	                        <th>Label</th>
	                         <th class="text-right">Options</th>
	                        </tr>
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
			<form id="site_form" action="/restore_backup" style="display:none" method="POST">
				<input type="hidden" name="_method" value="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">         
				
				<input type="hidden" id="backup_id_form" name="backup_id_form" value="">
				<input type="hidden" id="domain_form" name="domain_form" value="">
				<input type="hidden" id="backup_action_form" name="backup_action_form" value="">
				
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
	
	
	
	<script>
		
		$(".restore_backup_action").click(function() {
			backup_id = $(this).attr("backup_id");
			
		    BootstrapDialog.show({
		          title: 'Restore Backup',
		          message: '<label>Domain</label><br /><input class="form-control" id="backup_domain" name="backup_domain" value="">',
		          buttons: [{
		               label: '<a class="form-control">Restore Backup</a>',
		              action: function(dialog) {
		                  // submit the form
						  backup_domain = $("#backup_domain").val();
						  
						  //activate_general_loader();
						  dialog.close();
						  activate_loader();
						  
						$.ajax({
							url: "/wordpress_backups/restore",
							dataType: 'JSON',
							type: 'POST',
							data: {backup_domain : backup_domain, backup_id: backup_id},
							success : function(result) {
								
								if(result["message"]){
	
									$.notify({
										icon: "",
										message: result["message"]

									},{
										type: 'info',
										timer: 4000
									});
									$("#loadMe").modal("hide");
									dialog.close();
								    return false;	
									
							  	}else{
									deactivate_loader();
									window.location.href = "/sites?success=true";
							  	}
								
							}		
						});		
						  
		              }
		          }]
		      });
			
		});		
		
	</script>
	

@endsection