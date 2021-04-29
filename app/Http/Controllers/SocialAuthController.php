<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(Request $request)
    {
        $url = Socialite::driver($request->provider)->stateless()->redirect()->getTargetUrl();
        return response()->json(['url' => $url]);
    }

    public function facebook(Request $request)
    {
        return $this->authSocialUser($request, User::SOCIALITE_FACEBOOK);
    }

    public function vkontakte(Request $request)
    {
        return $this->authSocialUser($request, User::SOCIALITE_VK);
    }

    public function google(Request $request)
    {
        return $this->authSocialUser($request, User::SOCIALITE_GOOGLE);
    }

    protected function getItemByEmail($email) 
    {
        return User::where('email', strtolower($email))->first();
    }

    protected function itemCreate(array $data)
    {
        $item = User::create($data);
        $item->save();
        return $item;
    }

    protected function fillSocialite(User $user, $data, string $provider)
    {
        $socialite = $user->socialites()->firstOrNew(['provider' => $provider]);
        $socialite->api_data = json_encode($data);
        $socialite->save();
        return $socialite;
    }

    protected function authSocialUser(Request $request, string $provider)
    {
        try {
            $social_user = Socialite::driver($provider)->stateless()->user();

            if (empty($social_user->email)) {
                return response()->json(['message' => 'You must set email in your account to login'], 400);
            }

            $local_user = $this->getItemByEmail($social_user->email);

            if ($local_user) {

                $this->fillSocialite($local_user, $social_user, $provider);

                return $this->loginExistsUser($local_user);

            } else {
                $temp_password = str_random(10);
                $dataUser = [
                    'email' => $social_user->email,
                    'password' => bcrypt($temp_password),
                    'active' => true,
                    'phone' => ! empty($social_user->phone) ? $social_user->phone : '', 
                    'name' => ! empty($social_user->name) ? $social_user->name : '',
                ];

                $local_user = $this->itemCreate($dataUser);
                $this->fillSocialite($local_user, $social_user, $provider);
                $auth = $this->getAuth(['email' => $local_user->email, 'password' => $temp_password]);
                $this->itemUpdate($local_user, ['password' => '']);
                
                return redirect('/account');
            }

        } catch(\Exception $e) {            
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    protected function itemUpdate(User $user, array $data)
    {
        $user->fill($data);
        $user->save();
        return $user;
    }

    protected function getAuth($data) {
        return auth()->attempt($data);
    }

    protected function loginExistsUser(User $user)
    {
        $old_password = $user->password;
        $temp_password = str_random(10);

        $this->itemUpdate($user, ['password' => bcrypt($temp_password)]);
        $auth = $this->getAuth(['email' => $user->email, 'password' => $temp_password]);
        $this->itemUpdate($user, ['password' => $old_password]);
        return redirect('/account');
    }
}
