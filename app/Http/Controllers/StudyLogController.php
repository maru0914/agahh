<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudyLog\StoreRequest;
use App\Http\Requests\StudyLog\UpdateRequest;
use App\Models\StudyLog;
use App\ValueObjects\YearMonth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class StudyLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', StudyLog::class);

        $yearMonthParam = $request->input('yearMonth');
        $yearMonth = $yearMonthParam ? YearMonth::fromString($yearMonthParam) : YearMonth::current();
        $user = $request->user();

        $query = StudyLog::query()
            ->whereBelongsTo($user)
            ->whereBetween('studied_at', [
                $yearMonth->toCarbon()->startOfMonth()->format('Y-m-d'),
                $yearMonth->toCarbon()->endOfMonth()->format('Y-m-d'),
            ])
            ->orderByDesc('studied_at');

        $logs = $query->get();
        $totalMinutes = (int) $logs->sum('duration_minutes');

        return Inertia::render('StudyLog/Index', [
            'year' => $yearMonth->getYear(),
            'month' => $yearMonth->getMonth(),
            'isCurrentYearMonth' => $yearMonth->isCurrentYearMonth(),
            'previousLink' => route('study-logs.index', ['yearMonth' => (string) $yearMonth->previous()]),
            'nextLink' => route('study-logs.index', ['yearMonth' => (string) $yearMonth->next()]),
            'logs' => $logs,
            'totalMinutes' => $totalMinutes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        Gate::authorize('create', StudyLog::class);

        $date = $request->input('date', now()->format('Y-m-d'));
        $user = $request->user();

        $occupied = StudyLog::query()
            ->whereBelongsTo($user)
            ->whereDate('studied_at', $date)
            ->whereNotNull('started_at')
            ->whereNotNull('ended_at')
            ->orderBy('started_at')
            ->get(['started_at', 'ended_at'])
            ->map(fn ($log) => [
                'start' => \Illuminate\Support\Carbon::make($log->started_at)->format('H:i'),
                'end' => \Illuminate\Support\Carbon::make($log->ended_at)->format('H:i'),
            ]);

        return Inertia::render('StudyLog/Create', [
            'defaultDate' => $date,
            'occupiedRanges' => $occupied,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        Gate::authorize('create', StudyLog::class);

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        // Compose started_at / ended_at if times provided
        if (! empty($data['started_time']) && ! empty($data['ended_time'])) {
            $data['started_at'] = $data['studied_at'].' '.($data['started_time']);
            $data['ended_at'] = $data['studied_at'].' '.($data['ended_time']);
            unset($data['started_time'], $data['ended_time']);

            // Derive duration from time range
            $start = \Illuminate\Support\Carbon::make($data['started_at']);
            $end = \Illuminate\Support\Carbon::make($data['ended_at']);
            $data['duration_minutes'] = abs($end->diffInMinutes($start, false));
        }

        $log = StudyLog::create($data);

        return to_route('study-logs.show', $log);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, StudyLog $studyLog)
    {
        Gate::authorize('view', $studyLog);

        $user = $request->user();
        $date = $studyLog->studied_at->format('Y-m-d');
        $occupied = StudyLog::query()
            ->whereBelongsTo($user)
            ->whereDate('studied_at', $date)
            ->where('id', '!=', $studyLog->id)
            ->whereNotNull('started_at')
            ->whereNotNull('ended_at')
            ->orderBy('started_at')
            ->get(['started_at', 'ended_at'])
            ->map(fn ($log) => [
                'start' => \Illuminate\Support\Carbon::make($log->started_at)->format('H:i'),
                'end' => \Illuminate\Support\Carbon::make($log->ended_at)->format('H:i'),
            ]);

        return Inertia::render('StudyLog/Show', [
            'log' => $studyLog,
            'occupiedRanges' => $occupied,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, StudyLog $studyLog)
    {
        Gate::authorize('update', $studyLog);

        $data = $request->validated();

        if (! empty($data['started_time']) && ! empty($data['ended_time'])) {
            $data['started_at'] = $data['studied_at'].' '.($data['started_time']);
            $data['ended_at'] = $data['studied_at'].' '.($data['ended_time']);
            unset($data['started_time'], $data['ended_time']);

            $start = \Illuminate\Support\Carbon::make($data['started_at']);
            $end = \Illuminate\Support\Carbon::make($data['ended_at']);
            $data['duration_minutes'] = abs($end->diffInMinutes($start, false));
        }

        $studyLog->update($data);

        return to_route('study-logs.show', $studyLog);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, StudyLog $studyLog)
    {
        Gate::authorize('delete', $studyLog);

        $studyLog->delete();

        return to_route('study-logs.index');
    }
}
