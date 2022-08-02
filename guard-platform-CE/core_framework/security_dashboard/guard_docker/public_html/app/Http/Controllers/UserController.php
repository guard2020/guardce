<?php


namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        if($this->checkUserPermission()) {
            $data['user'] = Auth::user();
            $data['permission'] = $this->checkUserPermission();
            return view('users.profile', $data);
        } else {
            return redirect('users/profile')->with('warning', 'You are not authorize to access this page');
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function profile(){
        $data['user'] = Auth::user();
        $data['permission'] = $this->checkUserPermission();
        return view('users.profile', $data);
    }

    /**
     * @return Application|Factory|View
     */
    public function setting(){
        $data['user'] = Auth::user();
        $data['permission'] = $this->checkUserPermission();
        return view('users.profile', $data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id){
        $user = User::query()->find($id);
        if(!empty($user)){
            $user->email = $request->input('email');
            $user->name = $request->input('name');
            if($user->update()){

                return redirect()->route('users.profile')->with('success', 'User profile updated successfully!');
            }

            return redirect()->back()->with('error', 'Failed to updated Information! Try again');
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSetting(Request $request, $id){
        $user = User::query()->find($id);
        if (!(Hash::check($request->old_password, Auth::user()->password))) {

            return redirect()->back()->with('error', 'Your current password do not match! Try again');
        }

        if(!empty($user)){
            $user->password = Hash::make($request->input('new_password'));
            if($user->update()){

                return redirect()->route('users.profile')->with('success', 'Password changed successfully!');
            }

            return redirect()->back()->with('error', 'Failed to change password! Try again');
        }
    }

    /**
     * @uses: This function used to check the user access permission based on roles
     * @return bool|null
     */
    private function checkUserPermission(){
        $isAdmin = null;
        $userRole = Role::query()->where('id', Auth::user()->role_id)->first();
        if(!empty($userRole) && $userRole->name === 'Administrator'){
            $isAdmin = true;
        }

        return $isAdmin;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatables(){
        $usersData = User::latest()->get();
        $roles = Role::pluck('name', 'id');
        $status = User::getUserStatus();

        $data = datatables($usersData)
            ->addColumn('actions', function($user) {
                if($user->email === 'admin@mindsandsparks.org' && $user->role_id === 1){
                    return null;
                } else {
                    return view('users.partials.action-user-list', [
                        'user' => $user,
                        'userPermission' => $this->checkUserPermission(),
                    ])->render();
                }
            })
            ->editColumn('role_id', function($user) use ($roles) {
                if($user->email === 'admin@mindsandsparks.org' && $user->role_id === 1){
                    return 'Administrator';
                } else {
                    return view('users.partials.role-user-list', [
                        'user' => $user,
                        'roles' => $roles,
                    ])->render();
                }
            })
            ->editColumn('created_at', function($user) {
               return date('Y-m-d', strtotime($user->created_at));
            })
            ->editColumn('status', function($user) use ($status) {
                if($user->email === 'admin@mindsandsparks.org' && $user->role_id === 1){
                    return "Approved";
                } else {
                    return view('users.partials.status-user-list', [
                        'user' => $user,
                        'options' => $status,
                    ])->render();
                }
            })
            ->rawColumns(['actions','role_id','status'])
            ->toJson();

        return $data;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id){
        if(!empty($id)){
            $user = User::find($id);
            $user->delete();
            return redirect('users/list')->with('success', 'User deleted successfully!');

        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request){
        $input = $request->input();
        if(!empty($input['user_id'])) {
            $user = User::find($input['user_id']);
            $user->status = $input['status'];
            $user->update();
            return response()->json(['message' => 'User status updated successfully', 'status' => 'success']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRole(Request $request){
        $input = $request->input();
        if(!empty($input['user_id'])){
            $user = User::find($input['user_id']);
            $user->role_id = $input['role'];
            $user->update();

            return response()->json(['message' => 'User role updated successfully', 'status' => 'success']);
        }
    }
}

