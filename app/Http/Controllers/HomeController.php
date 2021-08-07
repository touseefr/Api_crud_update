<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\FuncCall;
use App\Models\Attendance;

class HomeController extends Controller
{

    public function login(Request $req)
    {
        $validator = Validator::make($req->all(), [
            "email" => "required",
            "password" => "required"
        ]);
        if ($validator->fails()) {
            // return response()->json(['status_code' => 200, 'message' => 'bad request']);
            return response()->json($validator->errors(), 401);
        }

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                "status_code" => 500,
                "message" => "Unautharized"
            ]);
        }
        $user = User::where('email', $req->email)->first();
        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status_code' => 200,
            'user' => $user,
            'token' => $tokenResult
        ]);
    }
    // public function register(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         "name" => "required",
    //         "email" => "required",
    //         "password" => "required",
    //        // "role" => "admin"
    //     ]);
    //     if ($validator->fails()) {
    //       //  return response()->json(['status_code' => 200, 'message' => 'bad request']);
    //       return response()->json($validator->errors(),401);
    //     }
    //     $user = new User;
    //     $user->name = $req->name;
    //     $user->email = $req->email;
    //     $user->password = bcrypt($req->password);
    //     $user->role=$req->role;
    //     $user->save();
    //     return $user;
    //   //  return response()->json(['status_code' => 200, 'message' => 'User added successfully']);
    // }
    public function createemployee(Request $req)
    {
        $validator = Validator::make($req->all(), [
            "name" => "required",
            "email" => "required",
            "password" => "required"
            
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }
        $user = new User;
        $user->name = $req->name;
        $user->email = $req->email;
        $user->password = bcrypt($req->password);
        $user->role ='employee';
        $user->save();

        return response()->json(
            [
                'status' => '200',
                'message' => 'Employee added successfully...'
            ]
        );
    }
    //fetch all employee data
    public function list()
    {
       // return User::all();
      return  User::where('role', 'employee')->get();
 // return   User::all('id', '!=', '1')->get();
    }
    // fetch by employee id
    public function byid($id)
    {
     
        $user= User::where('id',$id)->with('attendances')->first();
        return $user;
        //  if($result)
        //  {
        //   //  $user= User::with('attendances')->get();
        //      return $result;
        //  }
        //  else{
        //   return response()->json(
        //       [
        //          'message'=>'Not found'
        //       ]
        //       );
        //  }  
    }
    //update employee
    public function update(Request $req,$id)
    {
        $user = User::find($id);
        $user->name = $req->name;
        $user->email = $req->email;
        $user->password = bcrypt($req->password);
  //      $user->role = $req->role;
        $user->save();

        return response()->json([
            'status' => '200',
            'message' => 'Emplyee updated successfully...'
        ]);
    }
    //delete employee
    public function delete($id)
    {
        $user = User::find($id);
        $result = $user->delete();
        if ($result) {
            return ["Result" => "deleted successfully.." . $id];
        } else {
            return ["Result" => "Not Found"];
        }
    }
    //search employee
    public function search($name)
    {
        $result=User::where('name','like','%'.$name.'%')->get();
        if(count($result))
        {
            return $result;
        }
        else{
            return ["Result"=>"Not found"];
        }
    }

   

    public function employee($id)
    {
        $user=User::find($id);
      //  dd($user->attendances);
       // return $emp->addattendances;
       if(!$user)
       {
        return response()->json([   
        'message'=>'no result'
        ]);
       }
      else{
       foreach($user->attendances as $attendance)
       {
           echo  $attendance->checkin . "<br>",
             $attendance->checkout . "<br>";   
       }
    }
     
    //    else{
    //        return ['message'=>'No Found Attendance'];
    //    }
   
    }
    public function addcheckin(Request $req)
    {
        $validator=Validator::make($req->all(),[
            'user_id'=>'required',
            'date'=>'required',
            'checkin'=>'required'
        ]);

        if($validator->fails())
        {
               return response()->json($validator->errors(),401);     
        }
        $data=$req->all();
        $a=Attendance::where("date",$data['date'])->where('user_id',$data['user_id'])->get();
        if(count($a)>0){
            $at=$a->first();
            $at->update(['checkin'=>$data['checkin']]);
            return response()->json([
                'status_code'=>'200',
                'message'=>'checkin update successfully...'
            ]);
        }
        $user= new Attendance;    
        $user->user_id=$req->user_id;
        $user->date=$req->date;
        $user->checkin=$req->checkin;
        $user->save();

        return response()->json([
            'status_code'=>'200',
            'message'=>'attendance checkin added successfully...'
        ]);
    }
    public function addcheckout(Request $req)
    {

        $data = $req->all();
       // print_r($data);
        $a = Attendance::where("date",$data['date'])->where('user_id',$data['user_id'])->get();
        if(count($a)>0){
            $att = $a->first();
          //  print_r($at->user_id);
            $att->update(['checkout'=> $data['checkout']]);
            return response()->json([
                'status_code'=>'200',
                'message'=>'checkout update successfully...'
            ]);
        }else{
            $user= new Attendance;
            $user->user_id=$req->user_id;
            $user->date=$req->date;
            $user->checkout=$req->checkout;
            $user->save();

            return response()->json([
                'status_code'=>'200',
                'message'=>'attendance added successfully...'
            ]);
        }

        // print_r($a);
      //  exit();
        // $user= User::with('attendances')->get();
     //   return response()->json(["status"=> 200,'user'=> $data]);

    }
    //fetch all dates
    public function date(Request $req)
    {
        $data=$req->all();
       // print_r($data);
       $a=Attendance::where('user_id', $data['user_id'])->whereBetween('date',[$data['start'],$data['end']])->get();
       return $a;
      //$user= User::find($id);
    //   $from = date('2021-04-20');
    //   $to = date('2021-04-23');
    //   $a=Attendance::find($id)->whereBetween('date', [$from, $to])->get();
    //   return $a;
    }

}

