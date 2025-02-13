export interface CalendarDate {
    id: number;
    diaryId: ?number;
    date: number;
    isCurrentMonth: boolean;
    text: ?string;
    isToday: boolean;
}
