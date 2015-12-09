<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use App\Http\Controllers\Controller;
use DB;
use Session; 

class DepartmentsController extends Controller
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
        $data = DB::table('departments')
            ->join('companies', 'departments.company', '=', 'companies.company_id')
            ->select('departments.*', 'companies.commercial_name AS company_name')
            ->where('departments.company','LIKE',"%".$this->company."%")
            ->get();
        if (!$data) {
            return Response::json(['code'=>404,'message' => 'Not Found' ,'data' => []], 404);
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
        //
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
        $data = Department::where('department_id', '=', $id)->first();
        if (!$data) {
            return Response::json(['code'=>404,'message' => 'Not Found' ,'data' => []], 404);
        }
        return Response::json(['code'=>200,'message' => 'OK' , 'data' => $this->transform($data->toArray())], 200);
    }

     /**
     * Display the users for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function users($id)
    {
        $data = Department::where('department_id', '=', $id)->first()->users;
        if (!$data) {
            return Response::json(['code'=>404,'message' => 'Not Found' ,'data' => []], 404);
        }
        return Response::json(['code'=>200,'message' => 'OK' , 'data' => $this->transform($data->toArray())], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Department::where('department_id', '=', $id)->first();
        return view('pages.edit_department', ['department' => $data]);
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
        
        $attributes = $request->all();
        $attributes["active"] = (array_key_exists('active', $attributes)) ? intval($attributes["active"]) : 0;
        
        $parent = Department::where('department_id', '=', $attributes['parent'])->first();
        // $parents_objs = [$parent, $grandfather, $grand_grandfather, $grand_grand_grandfather, $grand_grand_grand_grandfather];
        $parents_objs = [];
        $parents = [];

        if ($parent) {
            $grandfather = Department::where('department_id', '=', $parent->parent)->first();
            array_push($parents,$parent->department_id);
            array_push($parents_objs,$parent);
        }
        if (isset($grandfather) && $grandfather) {
            $grand_grandfather = Department::where('department_id', '=', $grandfather->parent)->first();
            array_push($parents,$grandfather->department_id);
            array_push($parents_objs,$grandfather);
        }
        if (isset($grand_grandfather) && $grand_grandfather) {
            $grand_grand_grandfather = Department::where('department_id', '=', $grand_grandfather->parent)->first();
            array_push($parents,$grand_grandfather->department_id);
            array_push($parents_objs,$grand_grandfather);
        }
        if (isset($grand_grand_grandfather) && $grand_grand_grandfather) {
            $grand_grand_grand_grandfather = Department::where('department_id', '=', $grand_grand_grandfather->parent)->first();
            array_push($parents,$grand_grand_grandfather->department_id);
            array_push($parents_objs,$grand_grand_grandfather);
        }
        if (isset($grand_grand_grand_grandfather) && $grand_grand_grand_grandfather) {
            
            Session::flash('update', ['code' => 500, 'title' => 'Hierarchy constraint violation', 'message' => "Trying to add too many children, max level is 5"]);
            return redirect("/departments/$id/edit");

        }

        if (in_array($id, $parents)) {
            echo $parents_objs[array_search($id, $parents)]->name;

            Session::flash('update', ['code' => 500, 'title' => 'Hierarchy constraint violation', 'message' => "Parent pointing to child as parent, change ".$parents_objs[array_search($id, $parents) -1]->name."'s parent first"]);

            return redirect("/departments/$id/edit");

        }
        $department = Department::where('department_id', '=', $id)->first();
        $department->fill($attributes);
        $department->save();

        Session::flash('update', ['code' => 200, 'message' => 'Department info was updated']);
        // return back();
        return redirect("/departments/$id/edit");
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $department = Department::where('department_id', '=', $id)->first();
        if (!$department) {
            return Response::json(['code'=>404,'message' => 'Not Found' ,'data' => []], 404);
            exit;
        }

        $department->active = 0;
        $department->save();

        return Response::json(['code'=>200,'message' => 'OK' , 'data' => "$id DISABLED"] , 200);
        
    }

    public function transformCollection($departments)
    {
        if(is_array($departments)){
            return $departments;
        }
        return array_map([$this, 'transform'] , $departments->toArray());
    }

    private function transform ($department)
    {
        return $department;
    }
}
