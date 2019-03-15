<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
class SessionsController extends Controller
{
	public function __construct()
	{
		//Auth提供guest选项，只允许未登录用户访问的动作
		$this->middleware('guest', [
			'only' => ['create']
		]);
	}
	/**
	 * 登录页面
	 */
    public function create()
    {
    	return view('sessions.create');
    }
    /**
     * 登录提交
     */
    public function store(Request $request)
    {
    	$credentials = $this->validate($request, [
    		'email' => 'required|email|max:255',
    		'password' => 'required'
    	]);
    	if (Auth::attempt($credentials, $request->has('remember'))) {
    		//登录成功
    		session()->flash('success', '欢迎回来');
    		return redirect()->intended(route('users.show', [Auth::user()]));
    	} else {
    		//登录失败
    		session()->flash('danger', '用户名密码错误');
    		return redirect()->back();
    	}
    }
    public function destroy()
    {
    	Auth::logout();
    	session()->flash('success', '您已成功推出');
    	return redirect('login');
    }
}
