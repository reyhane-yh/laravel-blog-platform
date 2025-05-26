<?php

namespace App\Console\Commands;

use App\Exports\PostsExport;
use App\Models\Post;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class GetWeeklyReport extends Command
{
    protected $signature = 'app:get-weekly-report {--since= : Start date for the weekly report}';

    protected $description = 'Get a weekly report of all blog posts.';

    public function handle()
    {
        // Get the 'since' option
        $startDate = $this->option('since');

        // Check if it's valid
        if ($startDate && !$this->isValidDate($startDate)) {
            $this->error("Invalid date format. Please use Y-m-d.");
            return;
        }

        $startDate = $startDate ? Carbon::createFromFormat('Y-m-d', $startDate) : now();

        $currentDate = now();

        while ($startDate < $currentDate) {
            $endDate = $startDate->copy()->addWeek();

            // Get current week's posts
            $posts = Post::getPostsbyDate($startDate, $endDate);

            $fileName = 'weekly_reports/posts_' . $startDate->format('Y_m_d') . '.xlsx';

            // Check if there are any posts to export
            if ($posts->isEmpty()) {
                $this->info(
                    "No posts found from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}.");

            } else {
                Excel::store(new PostsExport($posts), $fileName, 'local');
                $this->info("Weekly report generated: $fileName");
            }

            // Move to the next week
            $startDate = $endDate;
        }
    }

    private function isValidDate($date)
    {
        return DateTime::createFromFormat('Y-m-d', $date) !== false;
    }
}
