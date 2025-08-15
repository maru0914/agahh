<?php

namespace App\Http\Requests\StudyLog;

use App\Models\StudyLog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Validator;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'studied_at' => ['required', 'date', 'before_or_equal:today'],
            'subject' => ['required', 'string', 'max:255'],
            'started_time' => ['required', 'date_format:H:i'],
            'ended_time' => ['required', 'date_format:H:i', 'after:started_time'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $studiedAt = $this->input('studied_at');
            $startTime = $this->input('started_time');
            $endTime = $this->input('ended_time');

            if (! $studiedAt || ! $startTime || ! $endTime) {
                return;
            }

            $start = Carbon::createFromFormat('Y-m-d H:i', $studiedAt.' '.$startTime);
            $end = Carbon::createFromFormat('Y-m-d H:i', $studiedAt.' '.$endTime);

            $currentId = optional($this->route('study_log'))->id ?? optional($this->route('studyLog'))->id ?? null;

            $exists = StudyLog::query()
                ->where('user_id', $this->user()->id)
                ->whereDate('studied_at', $studiedAt)
                ->when($currentId, fn ($q) => $q->where('id', '!=', $currentId))
                ->whereNotNull('started_at')
                ->whereNotNull('ended_at')
                ->where('started_at', '<', $end)
                ->where('ended_at', '>', $start)
                ->exists();

            if ($exists) {
                $validator->errors()->add('started_time', '選択した時間帯は既存の学習と重複しています。');
                $validator->errors()->add('ended_time', '選択した時間帯は既存の学習と重複しています。');
            }
        });
    }
}
