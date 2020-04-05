<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Client\Provider\GenericProvider;

use App\User;

class LoginController extends Controller
{
    private $provider;

    public function __construct()
    {
        $this->provider = new GenericProvider([
            'clientId'                => env('OAUTH_APP_ID'),
            'clientSecret'            => env('OAUTH_APP_SECRET'),
            'redirectUri'             => env('OAUTH_REDIRECT_URI'),
            'urlAuthorize'            => env('OAUTH_AUTHORIZE_ENDPOINT'),
            'urlAccessToken'          => env('OAUTH_TOKEN_ENDPOINT'),
            'scopes'                  => env('OAUTH_SCOPES'),
            'urlResourceOwnerDetails' => '',
        ]);
    }

    public function login()
    {
        $this->provider->authorize();
    }

    public function logout()
    {
        Auth::guard('profile')->logout();
        Auth::guard('post')->logout();
        auth()->logout();
        Session()->flush();

        return redirect('/');
    }

    public function callback(Request $request)
    {
        $request->validate(['code' => ['required', 'alpha_dash']]);

        try
        {
            $code  = $request->input('code');
            $token = $this->provider->getAccessToken('authorization_code', ['code' => $code]);

            $request  = $this->provider->getAuthenticatedRequest('GET', 'https://graph.facebook.com/v6.0/me?fields=id,name,picture', $token);
            $contents = $this->provider->getParsedResponse($request);

            $user = User::find($contents['id']);

            if (empty($user))
            {
                $user = new User();
                $user->id       = $contents['id'];
                $user->name     = $contents['name'];
                $user->picture  = $contents['picture']['data']['url'];

                $user->save();
            }

            Auth::guard('profile')->login($user, true);
            Auth::guard('post')->login($user, true);


            return redirect('/');
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
            return view('error', ['message' => 'Sorry, we were unable to authenticate you at this time.']);
        }
    }
}
