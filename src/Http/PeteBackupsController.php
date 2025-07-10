<?php


namespace Pete\PeteBackups\Http;

use App\Http\Controllers\PeteController;
use Illuminate\Support\Facades\Auth;
use App\Site;
use Illuminate\Http\Request;
use App\PeteOption;
use App\Backup;
use Validator;
use Illuminate\Support\Facades\Redirect;
use Log;
use View;
use DB;

class PeteBackupsController extends PeteController
{
	
	public function __construct(Request $request)
    {
		//Ensure system vars are loaded
        parent::__construct();          

        $this->middleware('auth');

        View::share([
            'dashboard_url' => env('PETE_DASHBOARD_URL'),
            'viewsw'        => '/import_wordpress'
        ]);
    }
  	
	
	public function index(){
		
		$current_user = Auth::user(); 
		
		$backups = Backup::orderBy('id', 'desc')->get();
		
		$viewsw = "/wordpress_backups";
		return view('pete-backups-plugin::index', compact('backups','viewsw','current_user'));
		
	}
	
	public function create(Request $request){
		
		$backup_label = $request->input('backup_label');
		$site_id = $request->input('site_id');
		$backup_label = preg_replace("/\s+/", "", $backup_label);
		
		if($backup_label == ""){
			return response()->json(['message'=> "Empty Label"]);
		}
		
		$check_backup = Backup::where("site_id",$site_id)->where("schedulling",$backup_label)->first();
		if(isset($check_backup)){
			return response()->json(['message'=> "Label already used"]);
		}
		
		$site = Site::findOrFail($site_id);
		$backup = $site->create_backup($backup_label);	
		$backup->save();
		
		return response()->json(['ok' => 'OK']);
	}
	
	public function restore(Request $request){
		
		$request_array = $request->all();
		$current_user = Auth::user(); 
		$backup = Backup::findOrFail($request->input('backup_id'));
		
		$backup_file = $backup->get_backup_file();	
		
		$import_params = array_merge(
		["template" => $backup_file,
		"action_name" => "Restore", 
		"wp_user" => $backup->wp_user, 
		"theme" => $backup->theme, 
		"user_id" => $current_user->id, 
		"first_password" => $backup->first_password,
		"site_url" =>  $request_array['backup_domain'],
		"action_name" => "Backup Restore"
		],$request_array);
		
		$new_site = new Site();
		$new_site->url = $request_array['backup_domain'];
		$new_site->import_wordpress($import_params);
		Site::reload_server();

		return response()->json(['ok' => 'OK']);
		
	}
	
	public function destroy(Request $request){
		
		$backup_id = $request->input('backup_id');
		$backup = Backup::findOrFail($backup_id);
		$backup->delete_backup();
		$backup->delete();
		return Redirect::to("/wordpress_backups");
		
	}
	
	
}
