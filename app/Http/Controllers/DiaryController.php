<?php

namespace App\Http\Controllers;

use App\Actions\GenerateDiaryCalenderDates;
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
        GenerateDiaryCalenderDates $action
    ) {

        $yearMonth = YearMonth::fromString($request->input('yearMonth'));
        $calenderDates = $action->handle($yearMonth);

        return Inertia::render('Diary/Index', [
            'year' => $yearMonth->getYear(),
            'month' => $yearMonth->getMonth(),
            'previousLink' => route('diary.index', ['yearMonth' => (string) $yearMonth->previous()]),
            'nextLink' => route('diary.index', ['yearMonth' => (string) $yearMonth->next()]),
            'calenderDates' => $calenderDates,
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
        //
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
