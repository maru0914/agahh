export interface CalenderDate {
    id: number;
    date: number;
    isCurrentMonth: boolean;
    text: ?string;
    isToday: boolean;
    isChecked: boolean;
}
