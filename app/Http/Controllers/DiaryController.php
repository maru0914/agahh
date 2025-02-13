<?php

namespace App\Http\Controllers;

use App\Actions\GenerateDiaryCalendarDates;
use App\Http\Requests\DiaryIndexRequest;
use App\Http\Requests\StoreDiaryRequest;
use App\Http\Requests\UpdateDiaryRequest;
use App\Models\Diary;
use App\ValueObjects\YearMonth;
use Inertia\Inertia;

class DiaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        DiaryIndexRequest $request,
        GenerateDiaryCalendarDates $action
    ) {

        $yearMonth = YearMonth::fromString($request->input('yearMonth'));
        $calendarDates = $action->handle($request->user(), $yearMonth);

        return Inertia::render('Diary/Index', [
            'year' => $yearMonth->getYear(),
            'month' => $yearMonth->getMonth(),
            'isCurrentYearMonth' => $yearMonth->isCurrentYearMonth(),
            'previousLink' => route('diary.index', ['yearMonth' => (string) $yearMonth->previous()]),
            'nextLink' => route('diary.index', ['yearMonth' => (string) $yearMonth->next()]),
            'calendarDates' => $calendarDates,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDiaryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Diary $diary)
    {
        dd($diary);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Diary $diary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDiaryRequest $request, Diary $diary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Diary $diary)
    {
        //
    }
}
