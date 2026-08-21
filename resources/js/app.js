import Alpine from 'alpinejs';
import { api, getUser, clearSession, categoryColor } from './api.js';

window.Alpine = Alpine;

// ---------- Global Auth Store ----------
Alpine.store('auth', {
    user: getUser(),
    async logout() {
        try {
            await api.logout();
        } catch {
            clearSession();
        } finally {
            this.user = null;
            window.location.href = '/login';
        }
    },
});

// ---------- 1. Login Page ----------
Alpine.data('loginPage', () => ({
    email: '',
    password: '',
    error: '',
    loading: false,

    async submit() {
        this.loading = true;
        this.error = '';
        try {
            const res = await api.login({ email: this.email, password: this.password });
            const user = getUser() || res.user;
            Alpine.store('auth').user = user;
            window.location.href = user?.role === 'admin' ? '/admin/dashboard' : '/beranda';
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

// ---------- 2. Register Page ----------
Alpine.data('registerPage', () => ({
    name: '',
    email: '',
    password: '',
    error: '',
    loading: false,

    async submit() {
        this.loading = true;
        this.error = '';
        try {
            const res = await api.register({ name: this.name, email: this.email, password: this.password });
            Alpine.store('auth').user = getUser() || res.user;
            window.location.href = '/beranda';
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

// ---------- 3. Beranda (List Ujian Santri) ----------
Alpine.data('berandaPage', () => ({
    loading: true,
    error: '',
    exams: {},

    async init() {
        try {
            const json = await api.getExams();
            this.exams = json?.data ?? json ?? {};
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

// ---------- 4. Pengerjaan Ujian ----------
Alpine.data('examPage', (examId) => ({
    loading: true,
    submitting: false,
    error: '',
    tab: 'pg',
    exam: null,
    pilihanGanda: [],
    essai: [],
    jawaban: {},

    get answeredCount() {
        return this.pilihanGanda.filter((q) => this.jawaban[q.id] !== undefined && this.jawaban[q.id] !== '').length;
    },

    async init() {
        try {
            const json = await api.getExam(examId);
            const data = json?.data ?? json;
            this.exam = { ...data.exam, warna: categoryColor(data.exam?.category) };
            this.pilihanGanda = (data.questions ?? []).filter((q) => q.type === 'multiple_choice');
            this.essai = (data.questions ?? []).filter((q) => q.type === 'essay');

            if (!this.pilihanGanda.length && this.essai.length) {
                this.tab = 'essai';
            }
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },

    async submit() {
        this.submitting = true;
        this.error = '';

        const answers = Object.entries(this.jawaban)
            .filter(([, v]) => v !== '' && v != null)
            .map(([question_id, answer_text]) => ({ question_id: Number(question_id), answer_text }));

        if (!answers.length) {
            this.error = 'Harap isi minimal satu pertanyaan sebelum mengumpulkan.';
            this.submitting = false;
            return;
        }

        try {
            await api.submitExam({ exam_id: this.exam.id, answers });
            window.location.href = '/dashboard';
        } catch (e) {
            this.error = e.message;
        } finally {
            this.submitting = false;
        }
    },
}));

// ---------- 5. Portofolio ----------
Alpine.data('portofolioPage', () => ({
    linkGithub: '',
    linkYoutube: '',
    files: [],
    maxFiles: 5,
    submitting: false,
    error: '',
    success: false,

    addFiles(fileList) {
        const room = this.maxFiles - this.files.length;
        this.files.push(...Array.from(fileList).slice(0, room));
    },

    removeFile(i) {
        this.files.splice(i, 1);
    },

    async submit() {
        this.submitting = true;
        this.error = '';
        this.success = false;

        try {
            const links = [this.linkGithub, this.linkYoutube].filter(Boolean).join(', ');
            const formData = new FormData();
            if (links) formData.append('links', links);
            this.files.forEach((f) => formData.append('files[]', f));

            await api.submitPortfolio(formData);
            this.success = true;
            this.files = [];
            this.linkGithub = '';
            this.linkYoutube = '';
            window.location.href = '/dashboard';
        } catch (e) {
            this.error = e.message;
        } finally {
            this.submitting = false;
        }
    },
}));

// ---------- 6. Profile & Prediksi Kemampuan ----------
Alpine.data('profilePage', () => ({
    loading: true,
    error: '',
    profile: null,
    stats: [],
    portfolio: null,
    languageLevel: '-',
    languageAccuracy: 0,
    itLevel: '-',
    itAccuracy: 0,

    async init() {
        try {
            const json = await api.getDashboard();
            const data = json?.data ?? json;
            this.profile = {
                ...data.student,
                role: Alpine.store('auth').user?.role ?? 'student'
            };
            this.stats = data.exam_stats ?? [];
            this.portfolio = data.portfolio ?? null;
            this.calculatePredictions();
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },

    calculatePredictions() {
        // Prediksi Bahasa
        const bahasaStats = this.stats.filter((s) => s.category?.toLowerCase() === 'bahasa');
        if (bahasaStats.length > 0) {
            const avg = bahasaStats.reduce((acc, curr) => acc + (Number(curr.mc_accuracy_pct) || 0), 0) / bahasaStats.length;
            this.languageAccuracy = Math.round(avg);

            if (avg < 20) this.languageLevel = 'A1';
            else if (avg < 40) this.languageLevel = 'A2';
            else if (avg < 60) this.languageLevel = 'B1';
            else if (avg < 75) this.languageLevel = 'B2';
            else if (avg < 90) this.languageLevel = 'C1';
            else this.languageLevel = 'C2';
        }

        // Prediksi IT
        const itStats = this.stats.filter((s) => s.category?.toLowerCase() === 'it');
        if (itStats.length > 0) {
            const avg = itStats.reduce((acc, curr) => acc + (Number(curr.mc_accuracy_pct) || 0), 0) / itStats.length;
            this.itAccuracy = Math.round(avg);

            if (avg < 34) this.itLevel = 'Rookie';
            else if (avg < 67) this.itLevel = 'Amateur';
            else this.itLevel = 'Pro';
        }
    },
}));

// ---------- 7. Admin Dashboard ----------
Alpine.data('adminDashboardPage', () => ({
    loading: true,
    error: '',
    students: [],

    async init() {
        const user = getUser();
        if (!user || user.role !== 'admin') {
            this.error = 'Akses ditolak.';
            this.loading = false;
            return;
        }

        try {
            const json = await api.getAdminStudents();
            this.students = json?.data ?? json ?? [];
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

// ---------- 8. Admin Koreksi Jawaban ----------
Alpine.data('adminKoreksiPage', () => ({
    loading: true,
    error: '',
    student: null,
    answers: [],
    scores: {},

    async init() {
        const user = getUser();
        if (!user || user.role !== 'admin') {
            this.error = 'Akses ditolak.';
            this.loading = false;
            return;
        }

        const parts = window.location.pathname.split('/').filter(Boolean);
        const userId = parts[parts.length - 1];

        try {
            const json = await api.getAdminStudentAnswers(userId);
            const data = json?.data ?? json ?? {};
            this.student = data.student ?? null;
            this.answers = data.answers ?? [];
            this.answers.forEach((a) => {
                if (a.current_score != null) this.scores[a.answer_id] = a.current_score;
            });
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },

    async saveScore(answerId) {
        const score = this.scores[answerId];
        if (score === undefined || score === '') {
            alert('Masukkan nilai 0-100 terlebih dahulu');
            return;
        }

        try {
            await api.gradeAnswer({ answer_id: answerId, score: parseFloat(score) });
            const answer = this.answers.find((a) => a.answer_id === answerId);
            if (answer) answer.current_score = parseFloat(score);
            alert('Nilai berhasil disimpan');
        } catch (e) {
            alert(e.message);
        }
    },
}));

Alpine.start();