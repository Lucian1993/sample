<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    public function create()
    {
    	return view('sessions.create');
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'email' => 'required|email|max:255',
    		'password' => 'required'
    	]);

    	$credentials = [
    		'email' => $request->email,
    		'password' => $request->password,
    	];

    	if (Auth::attempt($credentials, $request->has('remember'))) {
    		//登录成功后的操作
            if(Auth::user()->activated) {
                session()->flash('success', 'welcome back!');
                return redirect()->intended(route('users.show', [Auth::user()]));
            } else {
                Auth::logout();
                session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }
    	
    	} else {
    		//登录失败后的操作
    		session()->flash('danger', 'sorry,你的邮箱和密码不匹配。');
    		return redirect()->back();
    	}

    	return;
    }

    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您已成功推出');
        return redirect('login');
    }
}
