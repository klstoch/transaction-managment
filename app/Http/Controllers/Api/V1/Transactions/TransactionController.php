<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Transactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\DepositRequest;
use App\Http\Requests\Transaction\TransactionFilterRequest;
use App\Http\Requests\Transaction\TransferRequest;
use App\Http\Requests\Transaction\WithdrawRequest;
use App\Models\User;
use App\Repositories\Transaction\TransactionRepository;
use App\Services\AuthService;
use App\Services\Transaction\DepositService;
use App\Services\Transaction\TransferService;
use App\Services\Transaction\WithdrawService;
use App\ValueObject\MoneyVO;
use Illuminate\Http\JsonResponse;
use Throwable;

class TransactionController extends Controller
{
    private readonly User $user;

    public function __construct(
        private readonly DepositService $depositService,
        private readonly WithdrawService $withdrawService,
        private readonly TransferService $transferService,
        private readonly AuthService $authService,
    ) {
        $this->user = $this->authService->getAuthenticatedUser();
    }

    /**
     * @throws Throwable
     */
    public function deposit(DepositRequest $request): JsonResponse
    {

        $this->depositService->deposit($this->user, MoneyVO::create($request->amount, $request->currency));

        return response()->json(['message' => 'Deposit successful']);
    }

    /**
     * @throws Throwable
     */
    public function withdraw(WithdrawRequest $request): JsonResponse
    {
        $this->withdrawService->withdraw($this->user, MoneyVO::create($request->amount, $request->currency));

        return response()->json(['message' => 'Withdrawal successful']);
    }

    /**
     * @throws Throwable
     */
    public function transfer(TransferRequest $request): JsonResponse
    {
        $this->transferService->transfer($this->user, MoneyVO::create($request->amount, $request->currency), $request->recipient_email);

        return response()->json(['message' => 'Transfer successful']);
    }

    public function transactionHistory(TransactionFilterRequest $request, TransactionRepository $transactionRepository): JsonResponse
    {
        $filters = $request->filters();

        $transactions = $transactionRepository->filterBy($filters);

        return response()->json($transactions);
    }
}
