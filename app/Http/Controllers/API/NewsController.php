<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class NewsController extends Controller
{
    public function addNews(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'img' => 'nullable|image|max:5120', // max 5MB
            'tags' => 'nullable',
            'category' => 'required|string|max:100',
            'is_featured' => 'boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => false,
                'data' => $validator->errors()
            ]);
        }

        $news = new News();
        $news->title = $request->input('title');
        $news->content = $request->input('content');
        $news->uid = $request->header('uid');
        $news->category = $request->input('category');
        $news->is_featured = $request->input('is_featured', false);
        $news->tags = $request->input('tags', null);
        $news->img_url = $request->file('img') != null ? $request->file('img')->store('news_images', 'public') : null;
        $news->slug = $this->generateSlug($news->title);
        $news->save();
        return response()->json([
            'message' => 'News Added Successfully',
            'status' => true,
            'data' => $news
        ]);
    }

    public function getAllNews(Request $request)
    {
        $newsQuery = News::query();

        if ($request->has('search')) {
            $newsQuery->where('title', 'LIKE', "%" . $request->input('search') . "%");
        }
        if ($request->has('tag')) {
            $newsQuery->where('tags', 'LIKE', "%" . $request->input('tag') . "%");
        }
        if ($request->has('is_active')) {
            $newsQuery->where('is_active', $request->input('is_active'));
        }

        if ($request->has('category')) {
            $newsQuery->where('category', $request->input('category'));
        }

        if ($request->has('is_featured')) {
            $newsQuery->where('is_featured', $request->input('is_featured'));
        }

        $news = $newsQuery->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'News Retrieved Successfully',
            'status' => true,
            'data' => $news
        ]);
    }

    public function getNewsBySlug(Request $request, $slug)
    {
        $news = News::where('slug', $slug)->first();

        if (!$news) {
            return response()->json([
                'message' => 'News Not Found',
                'status' => false,
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'News Retrieved Successfully',
            'status' => true,
            'data' => $news
        ]);
    }

    public function updateNews(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'img' => 'nullable|image|max:5120', // max 5MB
            'tags' => 'nullable',
            'category' => 'sometimes|required|string|max:100',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => false,
                'data' => $validator->errors()
            ]);
        }

        $news = News::find($id);
        if (!$news) {
            return response()->json([
                'message' => 'News Not Found',
                'status' => false,
                'data' => null
            ], 404);
        }

        if ($request->has('title')) {
            $news->title = $request->input('title');
            $news->slug = $this->generateSlug($news->title);
        }
        if ($request->has('content')) {
            $news->content = $request->input('content');
        }
        if ($request->has('category')) {
            $news->category = $request->input('category');
            $news->slug = $this->generateSlug($news->title);
        }
        if ($request->has('is_featured')) {
            $news->is_featured = $request->input('is_featured');
            $news->slug = $this->generateSlug($news->title);
        }
        if ($request->has('is_active')) {
            $news->is_active = $request->input('is_active');
        }
        if ($request->has('tags')) {
            $news->tags = $request->input('tags');
        }
        if ($request->file('img') != null) {
            $news->img_url = $request->file('img')->store('news_images', 'public');
        }

        $news->save();

        return response()->json([
            'message' => 'News Updated Successfully',
            'status' => true,
            'data' => $news
        ]);
    }

    public function getNewsById(Request $request, $id)
    {
        $news = News::with('analytics')->find($id);

        if (!$news) {
            return response()->json([
                'message' => 'News Not Found',
                'status' => false,
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'News Retrieved Successfully',
            'status' => true,
            'data' => $news
        ]);
    }

    public function pushAnalytics(Request $request, $newsId)
    {
        $news = News::find($newsId);

        if (!$news) {
            return response()->json([
                'message' => 'News Not Found',
                'status'  => false,
                'data'    => null
            ], 404);
        }

        $agent = new Agent();

        // ----------------------------------
        // Core Request Info
        // ----------------------------------
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $sessionId = $request->session()->getId();

        // ----------------------------------
        // Device Detection
        // ----------------------------------
        $deviceType = $agent->isMobile() ? 'mobile'
            : ($agent->isTablet() ? 'tablet' : 'desktop');

        $browser = $agent->browser();
        $operatingSystem = $agent->platform();

        // ----------------------------------
        // Geo Location
        // ----------------------------------
        $location = geoip($ipAddress);

        // ----------------------------------
        // Unique View (per day, per news)
        // ----------------------------------
        $isUnique = !DB::table('analytics')
            ->where('news_id', $news->id)
            ->where('ip_address', $ipAddress)
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        // ----------------------------------
        // Build Analytics Payload
        // ----------------------------------
        $analyticsData = [
            'news_id'          => $news->id,
            'ip_address'       => $ipAddress,
            'user_agent'       => $userAgent,

            'country'          => $location->country ?? null,
            'region'           => $location->state_name ?? null,
            'city'             => $location->city ?? null,
            'latitude'         => $location->lat ?? null,
            'longitude'        => $location->lon ?? null,

            'device_type'      => $deviceType,
            'browser'          => $browser,
            'operating_system' => $operatingSystem,

            'source'           => $request->headers->get('referer') ?? 'direct',

            'session_id'       => $sessionId,
            'is_unique'        => $isUnique,

            // UTM Parameters (optional but correct)
            'utm_source'       => $request->query('utm_source'),
            'utm_medium'       => $request->query('utm_medium'),
            'utm_campaign'     => $request->query('utm_campaign'),
        ];

        $news->analytics()->create($analyticsData);

        return response()->json([
            'message' => 'Analytics Recorded Successfully',
            'status'  => true,
            'data'    => null
        ]);
    }

    public function likeNews(Request $request, $id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'message' => 'News Not Found',
                'status' => false,
                'data' => null
            ], 404);
        }

        $news->likes += 1;
        $news->save();

        return response()->json([
            'message' => 'News Liked Successfully',
            'status' => true,
            'data' => ['likes' => $news->likes]
        ]);
    }


    public function newsAnalyticsSummary(Request $request)
    {
        $params = [
            'start_date' => $request->query('start_date'),
            'end_date'   => $request->query('end_date'),
            'country'    => $request->query('country'),
            'device'     => $request->query('device_type'),
            'city'       => $request->query('city'),
        ];

        $cacheKey = $this->analyticsCacheKey('news_analytics_summary', $params);

        return Cache::remember($cacheKey, now()->addMinutes(3), function () use ($request) {

            $start = $request->query('start_date', now()->subDays(7));
            $end   = $request->query('end_date', now());

            $baseQuery = DB::table('news_analytics')
                ->whereBetween('created_at', [$start, $end]);

            if ($request->country) {
                $baseQuery->where('country', $request->country);
            }

            if ($request->device_type) {
                $baseQuery->where('device_type', $request->device_type);
            }
            if ($request->city) {
                $baseQuery->where('city', $request->city);
            }

            return [
                'totals' => [
                    'views' => (clone $baseQuery)->count(),
                    'unique_views' => (clone $baseQuery)->where('is_unique', true)->count(),
                ],

                'views_over_time' => (clone $baseQuery)
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as views')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),

                'device_split' => (clone $baseQuery)
                    ->selectRaw('device_type, COUNT(*) as views')
                    ->groupBy('device_type')
                    ->pluck('views', 'device_type'),

                'country_split' => (clone $baseQuery)
                    ->selectRaw('country, COUNT(*) as views')
                    ->groupBy('country')
                    ->orderByDesc('views')
                    ->limit(10)
                    ->get(),
                'city_split' => (clone $baseQuery)
                    ->selectRaw('city, COUNT(*) as views')
                    ->groupBy('city')
                    ->orderByDesc('views')
                    ->limit(10)
                    ->get(),
            ];
        });
    }


    public function newsAnalyticsDetail(Request $request, $newsId)
    {
        $params = [
            'news_id'    => $newsId,
            'start_date' => $request->query('start_date'),
            'end_date'   => $request->query('end_date'),
            'country'    => $request->query('country'),
            'device'     => $request->query('device_type'),
            'city'       => $request->query('city'),
        ];

        $cacheKey = $this->analyticsCacheKey('news_analytics_detail', $params);

        return Cache::remember($cacheKey, now()->addMinutes(3), function () use ($request, $newsId) {

            $news = News::findOrFail($newsId);

            $start = $request->query('start_date', now()->subDays(7));
            $end   = $request->query('end_date', now());

            $baseQuery = DB::table('news_analytics')
                ->where('news_id', $newsId)
                ->whereBetween('created_at', [$start, $end]);

            return [
                'news' => [
                    'id' => $news->id,
                    'title' => $news->title,
                ],

                'totals' => [
                    'views' => (clone $baseQuery)->count(),
                    'unique_views' => (clone $baseQuery)->where('is_unique', true)->count(),
                ],

                'views_over_time' => (clone $baseQuery)
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as views')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),

                'hourly_distribution' => (clone $baseQuery)
                    ->selectRaw('HOUR(created_at) as hour, COUNT(*) as views')
                    ->groupBy('hour')
                    ->orderBy('hour')
                    ->get(),

                'device_split' => (clone $baseQuery)
                    ->selectRaw('device_type, COUNT(*) as views')
                    ->groupBy('device_type')
                    ->pluck('views', 'device_type'),

                'country_split' => (clone $baseQuery)
                    ->selectRaw('country, COUNT(*) as views')
                    ->groupBy('country')
                    ->orderByDesc('views')
                    ->get(),
                'city_split' => (clone $baseQuery)
                    ->selectRaw('city, COUNT(*) as views')
                    ->groupBy('city')
                    ->orderByDesc('views')
                    ->get(),
            ];
        });
    }




    private function generateSlug($title)
    {
        $slug = Str::slug($title);
        $count = DB::table('news')->where('slug', 'LIKE', "{$slug}%")->count();
        return $count ? "{$slug}-" . ($count + 1) : $slug;
    }
}
