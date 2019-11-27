<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\User;
use App\DynamicModel;
use Validator;
use Illuminate\Support\Facades\Hash;
use Auth;

class EmployeesController extends Controller {
    
    public $loginGuard;
    
    public function __construct() {
        
        if(Auth::guard('admin')->check()){
            $this->middleware('auth:admin');
            $this->loginGuard = 'admin.';
        }
        if(Auth::guard('web')->check()){
            $this->middleware('auth');
        }
    }

    public function index() {
        
        $data['menu_selected'] = 'employees';
        $data['loginGuard'] = $this->loginGuard;
        return view('employees.view')->with($data);
    }

    public function formdata(Request $request) {

        $form_id = $request->id;
        
        $Employee_Model = new Employee;
        $query = $Employee_Model->select('*');
        $query->where('id',$form_id);
        if(Auth::guard('web')->check() && $form_id != '-1'){
            $query->where('company_id', Auth::user()->id);
        }
        $data['form_details'] = $query->first();
//        $data['form_details'] = Employee::find($form_id);

        $data['companies_list'] = User::all();
        $data['id'] = $form_id;

        if ($form_id == '-1') {
            $title = 'Add';
        } else {
            $title = 'Edit';
        }

        $data['title'] = $title;

        $returnHTML = view('employees.form', $data)->render(); // or method that you prefere to return data + RENDER is the key here
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    public function formValidation(Request $request) {

        $form_id = $request->id;

        $exists_id = '';
        if ($form_id != '-1') {
            $exists_id = ',' . $form_id;
        }

        // Validation Custom Message
        $custom_msg = array(
            'fullname.required' => 'Full Name is required field.',
            'email.required' => 'Email is required field.',
            'email.regex' => 'Please type valide email.',
            'email.unique' => 'This email has already exists. Please try another email.',
            'phone.required' => 'Phone is required field.',
        );
        
        if(Auth::guard('admin')->check()){
            $custom_msg['company_id.required']='Company is required field.';
        }
        // Validation fields
        $validation_fields = array(
            'fullname' => 'required',
            'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:employees,email' . $exists_id,
            'phone' => 'required',
        );
        if(Auth::guard('admin')->check()){
            $validation_fields['company_id']='required';
        }

        $validator = Validator::make($request->all(), $validation_fields, $custom_msg);

        if ($validator->fails()) {
            $errors = array(
                'fullname' => $validator->errors()->first('fullname'),
                'email' => $validator->errors()->first('email'),
                'phone' => $validator->errors()->first('phone'),
            );
            if(Auth::guard('admin')->check()){
                $errors['company_id']=$validator->errors()->first('company_id');
            }
            
            echo json_encode($errors);
        } else {
            echo '';
        }
        exit;
    }

    public function store($id, Request $request) {

        if ($id == '-1') {
            $exists_id = '';
        } else {
            $exists_id = ',' . $id;
        }

        // Validation Custom Message
        $custom_msg = array(
            'fullname.required' => 'Full Name is required field.',
            'email.required' => 'Email is required field.',
            'email.regex' => 'Please type valide email.',
            'email.unique' => 'This email has already exists. Please try another email.',
            'phone.required' => 'Phone is required field.',
        );
        
        if(Auth::guard('admin')->check()){
            $custom_msg['company_id.required']='Company is required field.';
        }

        // Validation fields
        $validation_fields = array(
            'fullname' => 'required',
            'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:employees,email' . $exists_id,
            'phone' => 'required',
        );
        
        if(Auth::guard('admin')->check()){
            $validation_fields['company_id']='required';
        }

        $validator = Validator::make($request->all(), $validation_fields, $custom_msg);

        if ($validator->fails()) {
            return redirect()->route($this->loginGuard.'employees')
                            ->withErrors($validator)
                            ->withInput()
                            ->with('error', 'Form not saved. please try again.');
        } else {

            if ($id == -1) {
                $details = new Employee;
                $details->fullname = $request->fullname;
                
                if(Auth::guard('admin')->check()){
                    $details->company_id = $request->company_id;
                }else{
                    $details->company_id = Auth::user()->id;
                }

                $details->email = $request->email;
                $details->phone = $request->phone;
                $details->created_at = date('Y-m-d H:i:s');
                $details->save();

                $msg = 'Employee save successfully!';
            } else {

                $employee_details = Employee::find($id);
                $update_data = array(
                    'fullname' => $request->fullname,
                    'email' => $request->email,
                    'phone' => $request->phone
                );
                
                if(Auth::guard('admin')->check()){
                    $update_data['company_id'] = $request->company_id;
                }else{
                    $update_data['company_id'] = Auth::user()->id;
                }
                
                $update_data['updated_at'] = date('Y-m-d H:i:s');

                // Update data 
                Employee::where('id', $id)->update($update_data);

                $msg = 'Employee updated successfully!';
            }

            return redirect()->route($this->loginGuard.'employees')->with('success', $msg);
        }
    }

    public function get_data(Request $request) {
        $select_array = array('employees.id', 'fullname', 'users.name', 'employees.email', 'phone');
        $Dynamic_Model = new DynamicModel;
        $Dynamic_Model->setTable('employees');
        $query = $Dynamic_Model->select($select_array);
        $query = $query->join('users', 'users.id', '=', 'employees.company_id');
        $table_search = $request->search['value'];

        if ($table_search) {
            $query->where(function($like_query) use ($table_search, $select_array) {
                foreach ($select_array as $value) {
                    $va = explode('as', $value);
                    if (count($va) > 1) {
                        $value = trim($va[0]);
                    }
                    $like_query->orWhere($value, 'like', '%' . $table_search . '%');
                }
            });
        }
        
        if(Auth::guard('web')->check()){
            $query->where('company_id', Auth::user()->id);
        }

        if (isset($select_array[$request->order[0]['column']])) {
            $query->orderBy($select_array[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query->orderBy('employees.created_at', 'desc');
        }

        if (strlen($request->length)) {
            $query->offset($request->start)->limit($request->length);
        }
        $getData = $query->get();


        $resultcount = count($getData);
        if ($request->start == 0) {
            $sr = 1;
        } else {
            $sr = $request->start + 1;
        }
        $result = array();
        if (!empty($getData)) {
            foreach ($getData as $row) {

                $result[] = array(
                    $sr,
                    $row->fullname,
                    $row->name,
                    $row->email,
                    $row->phone,
                    '<button data-id="' . $row->id . '" data-toggle="modal" data-target="#modal-lg" class="btn bg-grey btn-sm employee_form" ><i class="fa fa-edit"></i></button> &nbsp;<button data-href="' . route($this->loginGuard.'employees.delete', [$row->id]) . '" title="Delete" class="btn bg-red btn-sm delete_row" ><i class="fa fa-trash"></i></button>'
                );

                $sr++;
            }
        }

        $json_data = array(
            "draw" => intval($request->draw),
            "recordsTotal" => intval($resultcount),
            "recordsFiltered" => intval($resultcount),
            "data" => $result
        );

        echo json_encode($json_data);  // send data as json format
    }

    public function delete($id) {

        $result = '';
        if (!empty($id)) {
            $details = Employee::find($id);
            if (!empty($details)) {
                $result = $details->delete();
            }
        }

        echo json_encode($result);
        exit;
    }

}
