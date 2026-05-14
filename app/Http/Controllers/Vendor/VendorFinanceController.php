<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\WithdrawalRequest;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class VendorFinanceController extends Controller
{
    public function index(): Response
    {
        $vendor = auth()->user()->vendor;
        abort_if(! $vendor, 403, 'Vendor profile not found.');
        $this->authorize('manage', $vendor);

        return Inertia::render('Vendor/Finance/Index', [
            'vendor' => $vendor,
            'dropshipOrders' => $vendor->dropshipOrders()
                ->select(['id', 'order_id', 'vendor_id', 'dropship_number', 'status', 'vendor_total_idr', 'is_paid_to_vendor', 'paid_at', 'created_at'])
                ->with('order:id,order_number,total_usd,status')
                ->latest()
                ->paginate(20),
            'withdrawals' => Schema::hasTable('withdrawals')
                ? $vendor->withdrawals()->select(['id', 'vendor_id', 'amount_idr', 'status', 'notes', 'created_at'])->latest()->paginate(20)
                : [],
        ]);
    }

    public function requestWithdrawal(WithdrawalRequest $request)
    {
        $vendor = $request->user()->vendor;
        abort_if(! $vendor, 403, 'Vendor profile not found.');
        $this->authorize('manage', $vendor);
        abort_if(! Schema::hasTable('withdrawals'), 422, 'Withdrawal storage is not available yet.');

        $amount = (float) $request->validated('amount_idr');
        abort_if($amount > (float) $vendor->balance_idr, 422, 'Withdrawal amount exceeds available balance.');

        $withdrawal = Withdrawal::query()->create([
            'vendor_id' => $vendor->id,
            'amount_idr' => $amount,
            'status' => 'pending',
            'notes' => $request->validated('notes'),
        ]);

        $vendor->decrement('balance_idr', $amount);

        return $this->ok(['withdrawal' => $withdrawal], 201);
    }
}
