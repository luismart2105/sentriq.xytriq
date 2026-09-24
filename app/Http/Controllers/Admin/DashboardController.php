<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kit;
use App\Models\Prospect;
use App\Models\Review;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'kitCount' => Kit::count(),
            'activeKitCount' => Kit::where('active', true)->count(),
            'pendingReviewCount' => Review::where('status', 'pending')->count(),
            'approvedReviewCount' => Review::where('status', 'approved')->count(),
            'newProspectCount' => Prospect::where('stage', 'new')->count(),
            'dueFollowUpCount' => Prospect::whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<=', now()->endOfDay())->count(),
        ]);
    }
}
