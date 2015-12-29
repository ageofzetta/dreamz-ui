<?php

namespace App\Http\Controllers;
use App\User;
use App\UserDetail;
use App\Education_level;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;
use DB;
use Session; 
use Uuid; 
use Hash; 
use Auth; 
class UsersController extends Controller
{
    private $company;
    function __construct() {
        $this->company = '';
        if ( session('company')) {
            $this->company = session('company');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ( ! Auth::user()->can("list-users")){
            return HomeController::returnError(403);
        }
        $data = DB::table('users')
            ->join('positions', 'users.position', '=', 'positions.position_id')
            ->join('departments', 'users.department', '=', 'departments.department_id')
            ->select('users.*', 'positions.name AS position_name', 'departments.name AS department_name')
            ->where('users.company','LIKE',"%".$this->company."%")
            ->get();
        if (!$data) {
            return HomeController::returnError(404);
        }
        return Response::json(['code'=>200,'message' => 'OK' , 'data' => $this->transformCollection($data)], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ( ! Auth::user()->can("edit-users")){
            return HomeController::returnError(403);
        }
        return view('pages.create_user', ['id' => Uuid::generate(4), 'user' => Auth::user() ]);
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = DB::table('users')
            ->join('positions', 'users.position', '=', 'positions.position_id')
            ->join('departments', 'users.department', '=', 'departments.department_id')
            ->select('users.*', 'positions.position_id AS pst_id','departments.department_id AS dpt_id', 'positions.name AS position_name', 'departments.name AS department_name')
            ->where('user_id', '=', $id)
            ->first();
        if (!$data) {
            return HomeController::returnError(404);
        }
        return Response::json(['code'=>200,'message' => 'OK' , 'data' => $this->transform($data)], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ( Auth::user()->user_id == $id || Auth::user()->can("edit-users")){
            $data = DB::table('users')
                ->join('positions', 'users.position', '=', 'positions.position_id')
                ->join('departments', 'users.department', '=', 'departments.department_id')
                ->join('user_details', 'users.user_id', '=', 'user_details.user')
                ->select('users.*', 'user_details.*', 'positions.name AS position_name', 'departments.name AS department_name')
                ->where('user_id', '=', $id)
                ->first();
            if (!$data) {
                return HomeController::returnError(404);
            }
            return view('pages.edit_user', ['user' => $data]);
        } else {
        return HomeController::returnError(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ( Auth::user()->user_id == $id || Auth::user()->can("edit-users")){

        $user_details = UserDetail::where('user', '=', $id)->first();

        $validateto = [
                'user_id' => 'required',
                'employee_number' => 'required',
                'lastname' => 'required',
                'department' => 'required',
                'position' => 'required',
                'email' => 'required|email',
            ];
        
        if (!$user_details) {
            $validateto['email'] = 'required|email|unique:users';
        }

        $this->validate($request, $validateto);

        $attributes = $request->all();
        $user_attributes = $attributes;

        if ( ! Auth::user()->can("edit-users") && Auth::user()->user_id == $id){
            $user_attributes['email'] = Auth::user()->email;
            $user_attributes['active'] = Auth::user()->active;
            $user_attributes['high_potential'] = Auth::user()->high_potential;
            $user_attributes['position'] = Auth::user()->position;
            $user_attributes['department'] = Auth::user()->department;
            $user_attributes['admission_date'] = $user_details->admission_date;
            
        }

        unset($user_attributes['user']);
        unset($user_attributes['mobile']);
        unset($user_attributes['phone']);
        unset($user_attributes['birth_date']);
        unset($user_attributes['education']);
        unset($user_attributes['blood_type']);
        unset($user_attributes['alergies']);
        unset($user_attributes['emergency_contact']);
        unset($user_attributes['admission_date']);
        unset($user_attributes['facebook']);
        unset($user_attributes['twitter']);
        unset($user_attributes['instagram']);
        unset($user_attributes['linkedin']);
        unset($user_attributes['googlep']);

        $user_attributes["user_id"] = $id;
        $user_attributes["active"] = (array_key_exists('active', $user_attributes)) ? intval($user_attributes["active"]) : 0;
        $user_attributes["high_potential"] = (array_key_exists('high_potential', $user_attributes)) ? intval($user_attributes["high_potential"]) : 0;
        $user_details_attributes = array_diff($attributes, $user_attributes);

        $user = User::where('user_id', '=', $id)->first();
        if ($user) {
            unset($user_attributes["user_id"]);
            $user->fill($user_attributes);
            Session::flash('update', ['code' => 200, 'message' => trans('general.http.200u')]);
        }else{
            $user_attributes['password'] = Hash::make(rand());
            $user = User::create($user_attributes);
            Session::flash('update', ['code' => 200, 'message' => trans('general.http.200up')]);
        }
        $user->save();

        $user_details2 = UserDetail::first();
        $user_details = UserDetail::where('user', '=', $id)->first();
        if ($user_details) {
            $user_details->fill($user_details_attributes);
            Session::flash('update', ['code' => 200, 'message' => trans('general.http.200u')]);
            $user_details->save();
        }else{
            UserDetail::create([
                'user_details_id' => Uuid::generate(4),
                'user' => $id,
                'birth_date' => '1988-05-30', 
                'education' => Education_level::find(1)->education_level_id,
                'mobile' => '',
                'alergies' => '',
                'blood_type' => '',
                'emergency_contact' => '',
                'phone' => '',
                'admission_date' => '1988-05-30', 
                'facebook' => "facebook.com/",
                'twitter' => "twitter.com/@",
                'instagram' => "instagram.com/",
                'linkedin' => "linkedin.com/",
                'googlep' => "googlep.com/"

            ]);
        }

        // return back();
        return redirect("/users/$id/edit");

        }else{
            return HomeController::returnError(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ( ! Auth::user()->can("edit-users")){
            return HomeController::returnError(403);
        }

        $user = User::where('user_id', '=', $id)->first();
        if (!$user) {
            return HomeController::returnError(404);
        }

        $user->delete();

        return Response::json(['code'=>204,'message' => 'OK' , 'data' => "$id " . trans('general.http.204')] , 204);
        
    }

    public function transformCollection($users)
    {
        if (is_array($users)){
            return $users;
        }
        return array_map([$this, 'transform'] , $users->toArray());
    }

    private function transform ($user)
    {
        return $user;
    }
}
