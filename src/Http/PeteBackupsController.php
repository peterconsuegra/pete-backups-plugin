<?php


namespace Pete\PeteBackups\Http;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Site;
use Input;
use Illuminate\Http\Request;
use App\PeteOption;
use App\Backup;
use Validator;
use Illuminate\Support\Facades\Redirect;
use Log;
use View;
use DB;

class PeteBackupsController extends Controller
{
	
	public function __construct(Request $request){
	    
	    $this->middleware('auth');
		$dashboard_url = env("PETE_DASHBOARD_URL");
		$viewsw = "/sites";
		
		//DEBUGING PARAMS
		$debug = env('PETE_DEBUG');
		if($debug == "active"){
			$inputs = $request->all();
			Log::info($inputs);
		}
		
		$system_vars = parent::__construct();
		$pete_options = $system_vars["pete_options"];
		$sidebar_options = $system_vars["sidebar_options"];
		$os_distribution = $system_vars["os_distribution"];
		View::share(compact('dashboard_url','viewsw','pete_options','system_vars','sidebar_options','os_distribution'));
		   
	}
	
	public function index(){
		
		$current_user = Auth::user(); 
		
		$backups = Backup::orderBy('backups.created_at', 'desc')
			    ->select(DB::raw('backups.id, backups.schedulling, backups.file_name,sites.name, sites.url'))
				->join('sites', 'sites.id', '=', 'backups.site_id')
				->where("sites.user_id",$current_user->id)->get();
		
		$viewsw = "/wordpress_backups";
		return view('pete-backups-plugin::index', compact('backups','viewsw','current_user'));
		
	}
	
	public function create(){
		
		$backup_label = Input::get('backup_label');
		$site_id = Input::get('site_id');
		$backup_label = preg_replace("/\s+/", "", $backup_label);
		
		if($backup_label == ""){
			return response()->json(['message'=> "Empty Label"]);
		}
		
		$check_backup = Backup::where("site_id",$site_id)->where("schedulling",$backup_label)->first();
		if(isset($check_backup)){
			return response()->json(['message'=> "Label already used"]);
		}
		
		$site = Site::findOrFail($site_id);
		$backup = $site->snapshot_creation($backup_label);	
		$backup->save();
		
		return response()->json(['ok' => 'OK']);
	}
	
	public function restore(){
			
		$backup_id = Input::get('backup_id');
		$backup_domain = Input::get('backup_domain');
		$backup_domain = preg_replace("/\s+/", "", $backup_domain);
		$site_name = str_replace(".","",$backup_domain); 
		
		if($backup_domain == ""){
			return response()->json(['message'=> "Empty Domain"]);
		}
		
		$site = Site::where("url",$backup_domain)->first();
		if(isset($site)){
			return response()->json(['message'=> "Domain Taken"]);
		}
		
		$backup = Backup::findOrFail($backup_id);
		
		$base_path = base_path();
		$backup_file = "$base_path/backups/$backup->site_id/$backup->name-$backup->schedulling.tar.gz";
			
		$new_site = new Site();
		$new_site->theme = $backup->theme;
		$new_site->action_name = "Backup Restore";
		$new_site->name = $site_name;
		$new_site->url = $backup_domain;
		
		$new_site->import_wordpress($backup_file);
		return response()->json(['ok' => 'OK']);
	}
	
	public function destroy(){
		
		$backup = Backup::findOrFail(Input::get('backup_id'));
		$backup->delete();
		return Redirect::to("/wordpress_backups");
		
	}
	
	
}
