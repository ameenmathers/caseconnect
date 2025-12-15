<?php

namespace App\Http\Controllers;

use App\Models\Call;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = $this->calculateStats();
        $recentCalls = Call::latest()->limit(5)->get();
        $highValueLeads = Call::completed()->highValue()->latest()->limit(5)->get();

        return view('dashboard', compact('stats', 'recentCalls', 'highValueLeads'));
    }

    protected function calculateStats(): array
    {
        $totalCalls = Call::count();
        $completedCalls = Call::completed()->count();
        $pendingCalls = Call::pending()->count() + Call::processing()->count();
        $failedCalls = Call::failed()->count();

        $eligibleCalls = Call::eligible()->count();
        $highValueLeads = Call::highValue()->count();

        $avgScore = Call::completed()
            ->whereNotNull('lead_score')
            ->avg('lead_score');

        $conversionRate = $totalCalls > 0
            ? round(($eligibleCalls / $totalCalls) * 100, 1)
            : 0;

        $sentimentBreakdown = [
            'positive' => Call::completed()->where('sentiment', 'positive')->count(),
            'neutral' => Call::completed()->where('sentiment', 'neutral')->count(),
            'negative' => Call::completed()->where('sentiment', 'negative')->count(),
        ];

        return [
            'total_calls' => $totalCalls,
            'completed_calls' => $completedCalls,
            'pending_calls' => $pendingCalls,
            'failed_calls' => $failedCalls,
            'eligible_calls' => $eligibleCalls,
            'high_value_leads' => $highValueLeads,
            'avg_score' => round($avgScore ?? 0, 1),
            'conversion_rate' => $conversionRate,
            'sentiment_breakdown' => $sentimentBreakdown,
        ];
    }
}

