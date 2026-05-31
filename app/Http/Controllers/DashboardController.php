<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Topics;
use App\Models\DocumentTemplateLog;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // User dengan contribution terbanyak (berdasarkan jumlah topik)
        $topContributors = User::withCount('topics')
            ->orderBy('topics_count', 'desc')
            ->limit(5)
            ->get();

        // Data topik mingguan (7 hari terakhir)
        $weeklyTopics = [];
        $weeklyTopicsLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $weeklyTopicsLabels[] = $date->format('d M');
            $weeklyTopics[] = Topics::whereDate('created_at', $date->format('Y-m-d'))->count();
        }

        // Data download template mingguan (7 hari terakhir)
        $weeklyDownloads = [];
        $weeklyDownloadsLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $weeklyDownloadsLabels[] = $date->format('d M');
            $weeklyDownloads[] = DocumentTemplateLog::whereDate('downloaded_at', $date->format('Y-m-d'))->count();
        }

        $data = [
            'user' => $user,
            'total_anggota' => User::role('anggota')->count(),
            'total_pengurus' => User::role('pengurus')->count(),
            'top_contributors' => $topContributors,
            'announcements' => Announcement::with('user')->latest()->take(5)->get(),
            'weekly_topics' => $weeklyTopics,
            'weekly_topics_labels' => $weeklyTopicsLabels,
            'weekly_downloads' => $weeklyDownloads,
            'weekly_downloads_labels' => $weeklyDownloadsLabels,
            // Permission flags for view
            'canManageUsers' => $user->can('users.view'),
            'canManageRoles' => $user->can('roles.manage'),
            'canManageCategories' => $user->can('categories.manage'),
            'canManageTemplates' => $user->can('templates.manage'),
            'canApproveTopics' => $user->can('topics.approve'),
        ];

        return view('dashboard', $data);
    }
}
