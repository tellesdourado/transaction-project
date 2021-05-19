<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Services\Users\CreateUserService;
use App\Services\Gateway\CreateWalletService;
use Illuminate\Http\JsonResponse;
use Throwable;

class UserController extends Controller
{
    protected $userService;
    protected $createWalletService;

    public function __construct(
        CreateUserService $createUserService,
        CreateWalletService $createWalletService
    ) {
        $this->userService         = $createUserService;
        $this->createWalletService = $createWalletService;
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"users"},
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
}
