<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShowWalletRequest;
use App\Services\Gateway\ShowUserWalletService;
use Illuminate\Http\JsonResponse;

class WalletController extends Controller
{
    protected $showUserWalletService;

    public function __construct(ShowUserWalletService $showUserWalletService)
    {
        $this->showUserWalletService = $showUserWalletService;
    }

    /**
     * @OA\Get(
     *     path="/api/wallet/{id}",
     *     tags={"Wallet"},
     *     summary="Get A User Wallet",
     *     @OA\Response(
     *         response=200,
     *         description="success"
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="user id",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     * )
     */
    public function show(ShowWalletRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $wallet = $this->showUserWalletService->execute($data);
        } catch (\Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }

        return response()->json($wallet);
    }
}
