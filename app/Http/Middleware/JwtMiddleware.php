<?php
namespace App\Http\Middleware;
use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Illuminate\Http\Request;
class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        try {
           $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json([
                    'success' => false,
                    'message' => 'Token Invalid',    
                ]);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json([
                    'success' => false,
                    'message' => 'Token Expired',
            ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Authorization Token Not Found',
            ]);
            }
        }

        if ($user && in_array($user->role, $roles)) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'You are Unauthorize User',
    ]);
    }
}
