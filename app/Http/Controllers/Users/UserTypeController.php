<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\Users\ShowUserTypesService;
use Illuminate\Http\JsonResponse;
use Throwable;

class UserTypeController extends Controller
{
    protected $showUserTypesService;

    public function __construct(ShowUserTypesService $showUserTypesService)
    {
        $this->showUserTypesService = $showUserTypesService;
    }

    /**
     * @OA\Get(
     *     path="/api/user-types",
     *     tags={"User"},
     *     summary="Get All User Types",
     *     @OA\Response(
     *         response=200,
     *         description="sucess"
     *     ),
     * )
     */
    public function show(): JsonResponse
    {
        try {
            $userTypes = $this->showUserTypesService->execute();
        } catch (Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }
        return response()->json($userTypes);
    }
}
