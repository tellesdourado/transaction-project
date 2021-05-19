<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Services\Users\CreateUserService;
use App\Services\Gateway\CreateWalletService;
use App\Services\Users\ShowUsersService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class UserController extends Controller
{
    protected $userService;
    protected $createWalletService;
    protected $showUsersService;

    public function __construct(
        CreateUserService $createUserService,
        CreateWalletService $createWalletService,
        ShowUsersService $showUsersService
    ) {
        $this->userService         = $createUserService;
        $this->createWalletService = $createWalletService;
        $this->showUsersService    = $showUsersService;
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"User"},
     *     summary="Create A New User",
     *     @OA\Response(
     *         response=200,
     *         description="success"
     *     ),
     *     @OA\RequestBody(
     *         description="User Input Data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="full_name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="cpf",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="user_type_id",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function create(CreateUserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $user = $this->userService->execute($data);
            $user->load("type");

            $this->createWalletService->execute(array("user_id" => $user->id));

            $user->load("wallet");
        } catch (Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }

        return response()->json($user);
    }


    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"User"},
     *     summary="Get All Users",
     *     @OA\Response(
     *         response=200,
     *         description="sucess"
     *     ),
     * )
     */
    public function show(): JsonResponse
    {
        try {
            $users = $this->showUsersService->execute();
        } catch (\Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }

        return response()->json($users);
    }
}
