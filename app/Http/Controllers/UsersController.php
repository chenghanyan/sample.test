<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use Mail;
class UsersController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth',[
			'except' => ['show', 'create', 'store', 'index', 'confirmEmail']//不需要身份验证的方法
		]);
		//未登录用户只能访问注册
		$this->middleware('guest', [
			'only' => ['create']
		]);
	}
	/**
	 * 用户列表
	 */
	public function index()
	{
		$users = User::paginate(10);
		return view('users.index', compact('users'));
	}
	/**
	 * 用户注册
	 */
    public function create()
    {
    	return view('users.create');
    }
    /**
     * 更新页面
     */
    public function show(User $user)
    {
    	return view('users.show', compact('user'));
    }
    /**
     * 用户注册
     */
    public function store(Request $request)
    {
    	$this->validate($request,[
    		'name' => 'required|max:50',
    		'email' => 'required|email|unique:users|max:255',
    		'password' => 'required|confirmed|min:6'
    	]);
    	$user = User::create([
    		'name' => $request->name,
    		'email' => $request->email,
    		'password' => bcrypt($request->password)
    	]);
    	// Auth::login($user);//自动登录
        $this->sendEmailConfirmationTo($user);
    	session()->flash('success', '验证邮箱已发送，请注意查收！');
    	//redirect()->route('users.show', [$user->id]);
    	// return redirect()->route('users.show', [$user]); //route自动获取主键id
        return redirect('/');
    }
    /**
     * 更新页面
     */
    public function edit(User $user)
    {
    	$this->authorize('update', $user);
    	return view('users.edit', compact('user'));
    }
    /**
     * 用户更新
     */
    public function update(User $user, Request $request)
    {
    	$this->validate($request, [
    		'name' => 'required|max:50',
    		'password' => 'required|confirmed|min:6'
    	]);
    	$this->authorize('update', $user);
    	$data = [];
    	$data['name'] = $request->name;
    	if ($request->password) { //空密码不需要修改
    		$data['password'] = bcrypt($request->password);
    	}
    	$user->update($data);
    	session()->flash('success', '更新成功');
    	return redirect()->route('users.show', $user->id);
    }
    /**
     * 删除用户
     */
    public function destroy(User $user)
    {
    	$this->authorize('destroy', $user);
    	$user->delete();
    	session()->flash('success', '删除成功');
    	return back();
    }
    /**
     * 发邮件
     */
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'aufree@yousails.com';
        $name = 'Aufree';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }
    /**
     * 邮箱验证
     */
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();
        $user->activation_token = null;
        $user->activated = true;
        $user->save();
        Auth::login($user);
        session()->flash('success', '激活成功');
        return redirect()->route('users.show',$user->id);
    }
}
