<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRestaurantWithdrawalRequest;
use App\Http\Resources\WithdrawalRequestResource;
use App\Models\Restaurant;
use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use App\Services\WithdrawalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RestaurantWithdrawalController extends Controller
{
    public function __construct(
        private WithdrawalService $withdrawalService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $restaurantIds = Restaurant::query()
            ->where('owner_id', $request->user()->id)
            ->pluck('id');

        $walletIds = Wallet::query()
            ->where('holder_type', Restaurant::class)
            ->whereIn('holder_id', $restaurantIds)
            ->pluck('id');

        $withdrawals = WithdrawalRequest::query()
            ->whereIn('wallet_id', $walletIds)
            ->latest()
            ->paginate(20);

        return WithdrawalRequestResource::collection($withdrawals);
    }

    public function store(StoreRestaurantWithdrawalRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $bank = [
            'bank_name' => $validated['bank_name'] ?? null,
            'account_number' => $validated['account_number'] ?? null,
            'account_holder_name' => $validated['account_holder_name'] ?? null,
            'routing_number' => $validated['routing_number'] ?? null,
            'payment_method' => $validated['payment_method'] ?? 'bank_transfer',
            'payment_details' => $validated['payment_details'] ?? null,
        ];

        $withdrawal = $this->withdrawalService->requestRestaurantWithdrawal(
            $request->user(),
            (int) $validated['restaurant_id'],
            (float) $validated['amount'],
            $bank
        );

        return (new WithdrawalRequestResource($withdrawal))
            ->response()
            ->setStatusCode(201);
    }
}
