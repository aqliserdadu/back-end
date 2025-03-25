<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SensorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function loginService(Request $request){

        $user = "abu";
        $pass = "abu";
        $username = $request->get('username');
        $password = $request->get('password');

        if($username == $user && $pass == $password){

            return response()->json([
                "status" => true,
                "massage" => "Login Berhasil!",
                "user" => $username
            ],200);
        }else{

            return response()->json([
                "status" => false,
                'message' => 'Username atau password salah'
            ],401);

        }


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
