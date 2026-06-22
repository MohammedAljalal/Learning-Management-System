// ────────────────────────────────────────────────────────────
// Global Types for the LMS Application
// ────────────────────────────────────────────────────────────

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string | null;
    roles?: string[];
}

export interface Category {
    id: number;
    name: string;
    slug?: string;
}

export interface Course {
    id: number;
    title: string;
    slug: string;
    description: string;
    price: number;
    difficulty: 'beginner' | 'intermediate' | 'expert';
    thumbnail_url?: string | null;
    category?: Category | null;
    instructor?: User | null;
    instructor_id?: number;
    created_at: string;
    sections?: import('./index').Section[];
    enrollments_count?: number;
    lessons_count?: number;
    duration_minutes?: number;
    quizzes?: Quiz[];
}

export interface Enrollment {
    id: number;
    course: Course;
    enrolled_at: string;
    completed_at?: string | null;
}

export interface Lesson {
    id: number;
    title: string;
    order: number;
    duration_seconds?: number;
    is_completed?: boolean;
    type?: 'video' | 'text' | 'quiz';
}

export interface Quiz {
    id: number;
    title: string;
    description?: string;
    time_limit_minutes: number;
    is_practice: boolean;
    is_final_exam: boolean;
    order: number;
}

export interface Section {
    id: number;
    title: string;
    order: number;
    lessons: Lesson[];
    quizzes?: Quiz[];
}

export interface Certificate {
    id: number;
    uuid: string;
    course?: Course | null;
    issued_at: string;
    download_url: string;
    verify_url: string;
}

export interface XpTransaction {
    id: number;
    amount: number;
    description: string;
    created_at: string;
}

export interface Notification {
    id: string;
    data: { message?: string; [key: string]: unknown };
    read_at: string | null;
    created_at: string;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

export interface PageProps {
    auth: {
        user: (User & { roles?: string[]; instructor_status?: string }) | null;
    };
    flash?: {
        success?: string;
        error?: string;
    };
}
