<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\DynamicModel;
use Validator;
use Illuminate\Support\Facades\Hash;

class CompaniesController extends Controller {
    
    public $loginGuard;
    
    public function __construct() {
        $this->middleware('auth:admin');
        $this->loginGuard = 'admin.';
    }

    public function index() {

        $data['menu_selected'] = 'companies';
        $data['loginGuard'] = $this->loginGuard;

        return view('companies.view')->with($data);
    }

    public function formdata(Request $request) {

        $form_id = $request->id;
        $data['form_details'] = User::find($form_id);
        $data['id'] = $form_id;

        if ($form_id == '-1') {
            $title = 'Add';
        } else {
            $title = 'Edit';
        }

        $data['title'] = $title;

        $returnHTML = view('companies.form', $data)->render(); // or method that you prefere to return data + RENDER is the key here
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
            'name.required' => 'Company Name is required field.',
            'email.required' => 'Email is required field.',
            'email.regex' => 'Please type valide email.',
            'email.unique' => 'This email has already exists. Please try another email.',
            'website.regex' => 'Please type valid website.',
        );

        // Validation fields
        $validation_fields = array(
            'name' => 'required',
            'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email' . $exists_id,
            'website' => 'required|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
        );

        if ($form_id == '-1') {
            $validation_fields['password'] = ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'];
            $custom_msg['password.regex'] = 'Must be one uppercase, one lowercase,one number and one symbol.';

            $validation_fields['logo'] = 'required';
            $custom_msg['logo.required'] = 'Logo is required field.';
        }

        $validator = Validator::make($request->all(), $validation_fields, $custom_msg);

        if ($validator->fails()) {
            $errors = array(
                'name' => $validator->errors()->first('name'),
                'email' => $validator->errors()->first('email'),
                'password' => $validator->errors()->first('password'),
                'website' => $validator->errors()->first('website'),
                'logo' => $validator->errors()->first('logo'),
            );
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
            'name.required' => 'Company Name is required field.',
            'email.required' => 'Email is required field.',
            'email.regex' => 'Please type valide email.',
            'email.unique' => 'This email has already exists. Please try another email.',
            'website.regex' => 'Please type valid website.',
        );

        // Validation fields
        $validation_fields = array(
            'name' => 'required',
            'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email' . $exists_id,
            'website' => 'required|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
        );

        if ($id == '-1') {
            $validation_fields['password'] = ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'];
            $custom_msg['password.regex'] = 'Must be one uppercase, one lowercase,one number and one symbol.';
        }

        $validator = Validator::make($request->all(), $validation_fields, $custom_msg);

        if ($validator->fails()) {
            return redirect()->route('admin.companies')
                            ->withErrors($validator)
                            ->withInput()
                            ->with('error', 'Form not saved. please try again.');
        } else {

            $image_path = '';
            if ($request->hasFile('image')) {

                if (!file_exists('storage/app/public/images')) {
                    mkdir('storage/app/public/images', 0777, true);
                }

                $image = $request->file('image');
                $name = strtotime(date('Y-m-d H:i:s')) . '.' . $image->getClientOriginalExtension();
                $image->move('./storage/app/public/images/', $name);

                $image_path = './storage/app/public/images/' . $name;
            } else {
                if ($id == -1) {
                    return redirect()->route('admin.companies')->with('error', 'Please upload logo.');
                }
            }

            if ($id == -1) {
                $details = new User;
                $details->name = $request->name;
                $details->email = $request->email;
                $details->password = Hash::make($request->password);
                $details->website = $request->website;
                $details->created_at = date('Y-m-d H:i:s');
                if (!empty($image_path)) {
                    $details->logo = $image_path;
                }
                $details->save();

                $msg = 'Company save successfully!';
            } else {

                $companies_details = User::find($id);
                // Delete image when upload new image
                if (!empty($image_path)) {
                    if (file_exists($companies_details->logo)) {
                        unlink($companies_details->logo);
                    }
                }

                $update_data = array(
                    'name' => $request->name,
                    'email' => $request->email,
                    'website' => $request->website
                );
                $update_data['updated_at'] = date('Y-m-d H:i:s');

                if (!empty($image_path)) {
                    $update_data['logo'] = $image_path;
                }

                if ($request->password != '') {
                    $update_data['password'] = Hash::make($request->password);
                }

                // Update data 
                User::where('id', $id)->update($update_data);

                $msg = 'Company updated successfully!';
            }

            return redirect()->route('admin.companies')->with('success', $msg);
        }
    }

    public function get_data(Request $request) {
        $select_array = array('id', 'name', 'email', 'logo', 'website', 'created_at');
        $Dynamic_Model = new DynamicModel;
        $Dynamic_Model->setTable('users');
        $query = $Dynamic_Model->select($select_array);
//        $query->where('status', 1);
        $table_search = $request->search['value'];

        if ($table_search) {
            $query->where(function($like_query) use ($table_search, $select_array) {
                foreach ($select_array as $value) {
                    $like_query->orWhere($value, 'like', '%' . $table_search . '%');
                }
            });
        }

        if (isset($select_array[$request->order[0]['column']])) {
            $query->orderBy($select_array[$request->order[0]['column']], $request->order[0]['dir']);
        } else {
            $query->orderBy('created_at', 'desc');
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
                    $row->name,
                    $row->email,
                    '<img src="' . url($row->logo) . '" height="50px" width="50px" />',
                    $row->website,
                    '<button data-id="' . $row->id . '" data-toggle="modal" data-target="#modal-lg" class="btn bg-grey btn-sm company_form" ><i class="fa fa-edit"></i></button> &nbsp;<button data-href="' . route('admin.companies.delete', [$row->id]) . '" title="Delete" class="btn bg-red btn-sm delete_row" ><i class="fa fa-trash"></i></button>'
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
            $details = User::find($id);
            if (!empty($details)) {
                $result = $details->delete();
            }
        }

        echo json_encode($result);
        exit;
    }

}
