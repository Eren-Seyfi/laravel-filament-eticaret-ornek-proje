<?php

namespace App\Models;

use App\Enums\TrackEventType;
use Carbon\Carbon;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

class TrackEvent extends Model
{
    protected $fillable = [
        'event',
        'event_id',
        'page_key',
        'trackable_type',
        'trackable_id',
        'session_id',
        'ip',
        'user_agent',
        'user_id',
        'referrer',
        'url',
        'started_at',
        'ended_at',
        'duration_ms',
    ];

    protected $casts = [
        'event' => TrackEventType::class,
        'user_id' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_ms' => 'integer',
    ];

    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    /* --------------------------
     | Query Scopes (Genel)
     -------------------------- */

    public function scopeForTrackable(Builder $query, Model $trackable): Builder
    {
        return $query
            ->where('trackable_type', $trackable::class)
            ->where('trackable_id', $trackable->getKey());
    }

    public function scopeForEvent(Builder $query, TrackEventType|string $event): Builder
    {
        $value = $event instanceof TrackEventType ? $event->value : (string) $event;

        return $query->where('event', $value);
    }

    public function scopeWithSession(Builder $query): Builder
    {
        return $query->whereNotNull('session_id');
    }

    public function scopeBetweenDates(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    public function scopeOnDate(Builder $query, Carbon|string $date): Builder
    {
        $dateString = $date instanceof Carbon ? $date->toDateString() : (string) $date;

        return $query->whereDate('created_at', $dateString);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->onDate(now());
    }

    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->betweenDates(now()->startOfWeek(), now()->endOfWeek());
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->betweenDates(now()->startOfMonth(), now()->endOfMonth());
    }

    /* --------------------------
     | Query Scopes (Mevcut)
     -------------------------- */

    public function scopeOnlyPageViewEvents(Builder $query): Builder
    {
        return $query->where('event', TrackEventType::PageView->value);
    }

    public function scopeFilterByPageKey(Builder $query, ?string $pageKey): Builder
    {
        return $pageKey ? $query->where('page_key', $pageKey) : $query;
    }

    /* --------------------------
     | Builder Helpers (Infolist için)
     -------------------------- */

    public static function views(Model $trackable): Builder
    {
        return self::query()->forTrackable($trackable)->forEvent(TrackEventType::View);
    }

    public static function clicks(Model $trackable): Builder
    {
        return self::query()->forTrackable($trackable)->forEvent(TrackEventType::Click);
    }

    public static function uniqueViews(Model $trackable): Builder
    {
        return self::views($trackable)->withSession();
    }

    /* --------------------------
     | Count Helpers (Tek satır)
     -------------------------- */

    public static function countViewsToday(Model $trackable): int
    {
        return self::views($trackable)->today()->count();
    }

    public static function countUniqueViewsToday(Model $trackable): int
    {
        return (int) self::uniqueViews($trackable)->today()->distinct('session_id')->count('session_id');
    }

    public static function countClicksToday(Model $trackable): int
    {
        return self::clicks($trackable)->today()->count();
    }

    public static function countViewsThisWeek(Model $trackable): int
    {
        return self::views($trackable)->thisWeek()->count();
    }

    public static function countUniqueViewsThisWeek(Model $trackable): int
    {
        return (int) self::uniqueViews($trackable)->thisWeek()->distinct('session_id')->count('session_id');
    }

    public static function countClicksThisWeek(Model $trackable): int
    {
        return self::clicks($trackable)->thisWeek()->count();
    }

    public static function countViewsThisMonth(Model $trackable): int
    {
        return self::views($trackable)->thisMonth()->count();
    }

    public static function countUniqueViewsThisMonth(Model $trackable): int
    {
        return (int) self::uniqueViews($trackable)->thisMonth()->distinct('session_id')->count('session_id');
    }

    public static function countClicksThisMonth(Model $trackable): int
    {
        return self::clicks($trackable)->thisMonth()->count();
    }

    /* --------------------------
     | Analytics
     -------------------------- */

    /**
     * Period values:
     * - daily  (Son 30 gün)
     * - weekly (Son 12 hafta)
     * - monthly (Son 12 ay)
     * - yearly (Son 5 yıl)
     *
     * Return:
     * Collection<array{
     *   label: string,
     *   totalVisits: int,
     *   averageMinutes: float
     * }>
     */
    public static function trafficSeries(string $period = 'daily', ?string $pageKey = 'home'): Collection
    {
        [$startDate, $endDate] = self::getDateRangeForPeriod($period);

        $baseQuery = self::query()
            ->onlyPageViewEvents()
            ->filterByPageKey($pageKey);

        if (in_array($period, ['daily', 'monthly', 'yearly'], true)) {
            return self::buildTrafficSeriesWithTrend(
                query: $baseQuery,
                startDate: $startDate,
                endDate: $endDate,
                period: $period
            );
        }

        return self::buildWeeklyTrafficSeriesFromDailyTrend(
            query: $baseQuery,
            startDate: $startDate,
            endDate: $endDate
        );
    }

    private static function buildTrafficSeriesWithTrend(
        Builder $query,
        Carbon $startDate,
        Carbon $endDate,
        string $period
    ): Collection {
        $trendQueryForCount = self::buildTrendQueryForPeriod($query, $startDate, $endDate, $period);
        $trendQueryForDuration = self::buildTrendQueryForPeriod($query, $startDate, $endDate, $period);

        $countTrendValues = $trendQueryForCount->count(); // ziyaret sayısı
        $durationTrendValues = $trendQueryForDuration->sum('duration_ms'); // toplam ms

        return self::mergeDailyMonthlyYearlyCountAndDurationAsAverage(
            countTrendValues: $countTrendValues,
            durationTrendValues: $durationTrendValues,
            period: $period
        );
    }

    private static function buildWeeklyTrafficSeriesFromDailyTrend(
        Builder $query,
        Carbon $startDate,
        Carbon $endDate
    ): Collection {
        $dailyTrendQueryForCount = self::buildTrendQueryForPeriod($query, $startDate, $endDate, 'daily');
        $dailyTrendQueryForDuration = self::buildTrendQueryForPeriod($query, $startDate, $endDate, 'daily');

        $dailyCountTrendValues = $dailyTrendQueryForCount->count();
        $dailyDurationTrendValues = $dailyTrendQueryForDuration->sum('duration_ms');

        return self::mergeWeeklyCountAndDurationFromDailyTrendsAsAverage(
            dailyCountTrendValues: $dailyCountTrendValues,
            dailyDurationTrendValues: $dailyDurationTrendValues
        );
    }

    private static function buildTrendQueryForPeriod(
        Builder $query,
        Carbon $startDate,
        Carbon $endDate,
        string $period
    ): Trend {
        $trendQuery = Trend::query($query)->between(start: $startDate, end: $endDate);

        return match ($period) {
            'monthly' => $trendQuery->perMonth(),
            'yearly' => $trendQuery->perYear(),
            default => $trendQuery->perDay(),
        };
    }

    private static function getDateRangeForPeriod(string $period): array
    {
        return match ($period) {
            'weekly' => [now()->subWeeks(11)->startOfWeek(), now()->endOfWeek()],
            'monthly' => [now()->subMonths(11)->startOfMonth(), now()->endOfMonth()],
            'yearly' => [now()->subYears(4)->startOfYear(), now()->endOfYear()],
            default => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
        };
    }

    /**
     * Daily/Monthly/Yearly için: toplam ms / toplam ziyaret => ortalama dakika
     */
    private static function mergeDailyMonthlyYearlyCountAndDurationAsAverage(
        Collection $countTrendValues,
        Collection $durationTrendValues,
        string $period
    ): Collection {
        $durationTrendValuesByDate = $durationTrendValues->keyBy(
            fn(TrendValue $trendValue) => $trendValue->date
        );

        return $countTrendValues
            ->map(function (TrendValue $countTrendValue) use ($durationTrendValuesByDate, $period) {
                $dateKey = $countTrendValue->date;

                $totalVisits = (int) $countTrendValue->aggregate;
                $totalDurationInMilliseconds = (int) (($durationTrendValuesByDate[$dateKey]->aggregate ?? 0));

                $averageMinutes = 0.0;
                if ($totalVisits > 0) {
                    $averageMinutes = round(($totalDurationInMilliseconds / $totalVisits) / 1000 / 60, 2);
                }

                return [
                    'label' => self::formatLabelForPeriod(dateKey: $dateKey, period: $period),
                    'totalVisits' => $totalVisits,
                    'averageMinutes' => $averageMinutes,
                ];
            })
            ->values();
    }

    /**
     * Weekly için: haftaya göre toplamla, sonra ortalama hesapla
     */
    private static function mergeWeeklyCountAndDurationFromDailyTrendsAsAverage(
        Collection $dailyCountTrendValues,
        Collection $dailyDurationTrendValues
    ): Collection {
        $dailyDurationTrendValuesByDate = $dailyDurationTrendValues->keyBy(
            fn(TrendValue $trendValue) => $trendValue->date
        );

        $dailyRows = $dailyCountTrendValues->map(function (TrendValue $dailyCountTrendValue) use ($dailyDurationTrendValuesByDate) {
            $date = Carbon::parse($dailyCountTrendValue->date);

            $isoWeekYear = $date->isoWeekYear;
            $isoWeekNumber = $date->isoWeek;

            $weekLabel = $isoWeekYear . ' - ' . str_pad((string) $isoWeekNumber, 2, '0', STR_PAD_LEFT) . '. Hafta';

            $dailyVisits = (int) $dailyCountTrendValue->aggregate;
            $dailyDurationInMilliseconds = (int) (($dailyDurationTrendValuesByDate[$dailyCountTrendValue->date]->aggregate ?? 0));

            return [
                'weekLabel' => $weekLabel,
                'dailyVisits' => $dailyVisits,
                'dailyDurationInMilliseconds' => $dailyDurationInMilliseconds,
            ];
        });

        return $dailyRows
            ->groupBy('weekLabel')
            ->map(function (Collection $groupedWeekItems, string $weekLabel) {
                $totalVisits = (int) $groupedWeekItems->sum('dailyVisits');
                $totalDurationInMilliseconds = (int) $groupedWeekItems->sum('dailyDurationInMilliseconds');

                $averageMinutes = 0.0;
                if ($totalVisits > 0) {
                    $averageMinutes = round(($totalDurationInMilliseconds / $totalVisits) / 1000 / 60, 2);
                }

                return [
                    'label' => $weekLabel,
                    'totalVisits' => $totalVisits,
                    'averageMinutes' => $averageMinutes,
                ];
            })
            ->values();
    }

    private static function formatLabelForPeriod(string $dateKey, string $period): string
    {
        return $dateKey;
    }
}
