<?php

namespace Cmisoft\Http\Middleware;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Closure;

class Admin
{
    protected $auth;

    public function __construct(Guard $auth){
        $this->auth = $auth;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->user()->id != 1) {
            Session::flash('message-error','El usuario no tiene privilegios');
            return Redirect::to('/semaforo');
        }

        return $next($request);
    }
}
