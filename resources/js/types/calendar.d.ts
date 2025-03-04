export interface CalendarDate {
    id: number;
    diaryId: ?number;
    date: string;
    isCurrentMonth: boolean;
    text: ?string;
    isToday: boolean;
}
