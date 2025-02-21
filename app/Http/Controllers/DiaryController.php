<?php

namespace App\Http\Controllers;

use App\Actions\GenerateDiaryCalendarDates;
use App\Http\Requests\Diary\IndexRequest;
use App\Http\Requests\Diary\StoreRequest;
use App\Http\Requests\Diary\UpdateRequest;
use App\Http\Resources\DiaryResource;
use App\Models\Diary;
use App\ValueObjects\YearMonth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        IndexRequest $request,
        GenerateDiaryCalendarDates $action
    ) {

        $yearMonth = YearMonth::fromString($request->input('yearMonth'));
        $calendarDates = $action->handle($request->user(), $yearMonth);

        return Inertia::render('Diary/Index', [
            'year' => $yearMonth->getYear(),
            'month' => $yearMonth->getMonth(),
            'isCurrentYearMonth' => $yearMonth->isCurrentYearMonth(),
            'previousLink' => route('diaries.index', ['yearMonth' => (string) $yearMonth->previous()]),
            'nextLink' => route('diaries.index', ['yearMonth' => (string) $yearMonth->next()]),
            'calendarDates' => $calendarDates,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, string $date)
    {
        $existingDiary = Diary::where('date', $date)
            ->whereBelongsTo($request->user())
            ->first();

        if ($existingDiary) {
            return to_route('diaries.index');
        }

        return Inertia::render('Diary/Create', [
            'date' => $date,
        ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request, string $date)
    {
        Diary::create([
            'user_id' => $request->user()->id,
            'date' => $date,
            'content' => $request->input('content'),
        ]);

        return to_route('diaries.show', $date);
    }

    /**
     * Display the specified resource.
     */
    public function show(Diary $diary)
    {
        return Inertia::render('Diary/Show', [
            'diary' => DiaryResource::make($diary),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Diary $diary)
    {
        $diary->update([
            'content' => $request->input('content'),
        ]);

        return to_route('diaries.show', $diary->date->format('Y-m-d'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Diary $diary)
    {
        $diary->delete();

        return to_route('diaries.index');
    }
}
