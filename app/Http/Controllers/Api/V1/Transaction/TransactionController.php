<?php

declare(strict_types=1);


namespace App\Http\Controllers\Api\V1\Transaction;

use App\DTO\TransferData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\DepositRequest;
use App\Http\Requests\Transaction\TransferRequest;
use App\Http\Requests\Transaction\WithdrawRequest;
use App\Models\User;
use App\Repositories\Transaction\TransactionRepository;
use App\Services\Transaction\DepositService;
use App\Services\Transaction\TransferService;
use App\Services\Transaction\WithdrawService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

class TransactionController extends Controller
{
    public function __construct(
        private readonly DepositService  $depositService,
        private readonly WithdrawService $withdrawService,
        private readonly TransferService $transferService,
        private readonly User            $user,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function deposit(DepositRequest $request): JsonResponse
    {

        $this->depositService->deposit($this->user, $request->amount, $request->currency);

        return response()->json(['message' => 'Deposit successful']);
    }

    /**
     * @throws Throwable
     */
    public function withdraw(WithdrawRequest $request): JsonResponse
    {
        $this->withdrawService->withdraw($this->user, $request->amount, $request->currency);

        return response()->json(['message' => 'Withdrawal successful']);
    }

    /**
     * @throws Throwable
     */
    public function transfer(TransferRequest $request): JsonResponse
    {
        $this->transferService->transfer($this->user, $request->amount, $request->recipientEmail);

        return response()->json(['message' => 'Transfer successful']);
    }

    public function transactionHistory(Request $request, TransactionRepository $transactionRepository): JsonResponse
    {
        $filters = $request->only(['type', 'from_date', 'to_date']);
        $filters['user_id'] = $this->user->id;

        $transactions = $transactionRepository->filterBy(array_filter($filters));

        return response()->json($transactions);
    }

    /* private function validateTypeModel(): User
     {
         $user = Auth::user();
         if (!($user instanceof User)) {
             throw new RuntimeException('Пользователь не авторизован или неверный тип пользователя');
         }
         return $user;
     }*/
}
