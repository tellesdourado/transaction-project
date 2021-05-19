<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTransactionRequest;
use App\Http\Requests\RollbackTransactionRequest;
use App\Services\Gateway\PreTransactionService;
use App\Services\Gateway\RollBackTransactionService;
use Illuminate\Http\JsonResponse;
use Throwable;

class TransactionController extends Controller
{
    protected $preTransactionService;
    protected $rollBackTransactionService;

    public function __construct(
        PreTransactionService $preTransactionService,
        RollBackTransactionService $rollBackTransactionService
    ) {
        $this->preTransactionService   = $preTransactionService;
        $this->rollBackTransactionService = $rollBackTransactionService;
    }


    /**
     * @OA\Post(
     *     path="/api/transaction",
     *     tags={"Transaction"},
     *     summary="Send Money Between Accounts",
     *     @OA\Response(
     *         response=200,
     *         description="success"
     *     ),
     *     @OA\RequestBody(
     *         description="Input Data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="value",
     *                     type="number",
     *                 ),
     *                 @OA\Property(
     *                     property="payer",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="payee",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function create(CreateTransactionRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $transaction = $this->preTransactionService->execute($data);
        } catch (Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }


        return response()->json($transaction);
    }

    /**
     * @OA\Post(
     *     path="/api/transaction/{id}/rollback",
     *     tags={"Transaction"},
     *     summary="RollBack a Transaction",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Transaction Id",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success"
     *     ),
     * )
     */
    public function rollback(RollbackTransactionRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $transaction = $this->rollBackTransactionService->execute($data);
        } catch (Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }

        return response()->json($transaction);
    }
}
