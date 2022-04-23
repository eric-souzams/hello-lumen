<?php

namespace App\Http\Controllers\Transactions;

use App\Exceptions\NotEnoughBalanceException;
use App\Exceptions\TransactionDeniedException;
use App\Http\Controllers\Controller;
use App\Repositories\Transaction\TransactionRepository;
use Illuminate\Http\Request;
use PHPUnit\Framework\InvalidDataProviderException;

class TransactionsController extends Controller
{
    private $repository;

    public function __construct(TransactionRepository $repository)
    {
        // $this->middleware('auth:users');
        // $this->middleware('auth:retailers');

        $this->repository = $repository;
    }

    public function postTransaction(Request $request)
    {
        $this->validate($request, [
            'provider' => 'required|in:user,retailer',
            'payee_id' => 'required',
            'amount'   => 'required|numeric',
        ]);

        try {
            $fields = $request->only(['provider', 'payee_id', 'amount']);
            $result = $this->repository->handle($fields);
        } catch (InvalidDataProviderException | NotEnoughBalanceException $exception) {
            return response()->json(['errors' => ['main' => $exception->getMessage()]], 422);
        } catch (TransactionDeniedException $exception) {
            return response()->json(['errors' => ['main' => $exception->getMessage()]], 401);
        }

        return response()->json($result);
    }
     
}