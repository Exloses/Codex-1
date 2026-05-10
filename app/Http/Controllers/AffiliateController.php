<?php

namespace App\Http\Controllers;

use App\Http\Requests\Affiliate\GenerateLinkRequest;
use App\Http\Requests\Affiliate\PayoutMethodRequest;
use App\Http\Requests\Affiliate\PayoutRequest;
use App\Models\Affiliate;
use App\Models\AffiliateClick;
use App\Models\AffiliatePayout;
use App\Services\AffiliateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AffiliateController extends Controller
{
    public function landing(): Response
    {
        return Inertia::render('Affiliate/Landing');
    }

    public function track(string $code): RedirectResponse
    {
        $affiliate = Affiliate::query()->where('referral_code', $code)->where('is_active', true)->firstOrFail();
        $ip = request()->ip() ?? '127.0.0.1';

        AffiliateClick::query()->create([
            'affiliate_id' => $affiliate->id,
            'ip_address' => $ip,
            'user_agent' => request()->userAgent(),
            'referer' => request()->headers->get('referer'),
            'is_unique' => ! $affiliate->clicks()->where('ip_address', $ip)->exists(),
            'clicked_at' => now(),
        ]);

        $affiliate->increment('total_clicks');

        return redirect()->route('home')->withCookie(cookie('affiliate_code', $code, 60 * 24 * 30));
    }

    public function register(AffiliateService $affiliateService)
    {
        $affiliate = $affiliateService->register(auth()->user());

        return $this->ok(['affiliate' => $affiliate], 201);
    }

    public function dashboard(): Response
    {
        $affiliate = $this->affiliateForUser();

        return Inertia::render('Affiliate/Dashboard', [
            'affiliate' => $affiliate?->loadCount('clicks', 'commissions', 'payouts'),
            'recentCommissions' => $affiliate?->commissions()->with('order:id,order_number,total_usd')->latest()->limit(10)->get() ?? [],
            'recentPayouts' => $affiliate?->payouts()->latest()->limit(10)->get() ?? [],
        ]);
    }

    public function commissions(): Response
    {
        $affiliate = $this->requireAffiliate();

        return Inertia::render('Affiliate/Commissions', [
            'affiliate' => $affiliate,
            'commissions' => $affiliate->commissions()->with('order:id,order_number,total_usd,status')->latest()->paginate(20),
        ]);
    }

    public function storePayoutMethod(PayoutMethodRequest $request)
    {
        $affiliate = $this->requireAffiliate();

        if ($request->boolean('is_default')) {
            $affiliate->payoutMethods()->update(['is_default' => false]);
        }

        $method = $affiliate->payoutMethods()->create($request->validated());

        return $this->ok(['payout_method' => $method], 201);
    }

    public function requestPayout(PayoutRequest $request)
    {
        $affiliate = $this->requireAffiliate();
        $method = $affiliate->payoutMethods()->findOrFail($request->validated('payout_method_id'));
        $amount = (float) $request->validated('amount_usd');
        $available = (float) $affiliate->commissions()->where('status', 'available')->sum('commission_usd');

        abort_if($amount > $available, 422, 'Requested payout exceeds available commission balance.');

        $payout = AffiliatePayout::query()->create([
            'affiliate_id' => $affiliate->id,
            'payout_method_id' => $method->id,
            'amount_usd' => $amount,
            'fee_usd' => 0,
            'net_amount_usd' => $amount,
            'status' => 'pending',
            'payout_type' => $request->input('payout_type', 'standard'),
            'paypal_email' => $method->paypal_email,
            'wise_email' => $method->wise_email,
            'bank_account' => $method->bank_account,
        ]);

        return $this->ok(['payout' => $payout], 201);
    }

    public function payoutHistory(): Response
    {
        $affiliate = $this->requireAffiliate();

        return Inertia::render('Affiliate/Withdraw', [
            'affiliate' => $affiliate->load('payoutMethods'),
            'payouts' => $affiliate->payouts()->with('payoutMethod')->latest()->paginate(20),
            'availableBalanceUsd' => $affiliate->commissions()->where('status', 'available')->sum('commission_usd'),
        ]);
    }

    public function generateLink(GenerateLinkRequest $request)
    {
        $affiliate = $this->requireAffiliate();
        $target = $request->validated('url') ?: route('home');
        $separator = Str::contains($target, '?') ? '&' : '?';

        return $this->ok([
            'url' => $target.$separator.'ref='.$affiliate->referral_code,
            'code' => $affiliate->referral_code,
        ]);
    }

    private function affiliateForUser(): ?Affiliate
    {
        return auth()->user()->affiliate;
    }

    private function requireAffiliate(): Affiliate
    {
        $affiliate = $this->affiliateForUser();
        abort_if(! $affiliate, 404, 'Affiliate account not registered.');

        return $affiliate;
    }
}
