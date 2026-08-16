// resources/js/app.js
import Alpine from 'alpinejs';
import { api, getUser, isLoggedIn, clearSession, categoryColor } from './api.js';

window.Alpine = Alpine;

// ---------- Guard global: halaman selain login/register wajib sudah login ----------
Alpine.store('auth', {
    user: getUser(),
    logout() {
        clearSession();
        window.location.href = '/login';
    },
});

// ---------- Login ----------
Alpine.data('loginPage', () => ({
    email: '', password: '', error: '', loading: false,
    async submit() {
        this.loading = true; this.error = '';
        try {
            await api.login({ email: this.email, password: this.password });
            window.location.href = '/beranda';
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

// ---------- Register ----------
Alpine.data('registerPage', () => ({
    name: '', email: '', password: '', error: '', loading: false,
    async submit() {
        this.loading = true; this.error = '';
        try {
            await api.register({ name: this.name, email: this.email, password: this.password });
            window.location.href = '/beranda';
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

// ---------- Beranda: profil + daftar ujian dikelompokkan per kategori ----------
Alpine.data('berandaPage', () => ({
    loading: true, error: '', kategori: [], user: getUser(), openCategory: null,
    isCategoryComplete(category) {
        return category.ujian.length > 0 && category.ujian.every((exam) => exam.completed);
    },
    async init() {
        try {
            const json = await api.getExams(this.user?.id);
            // ASUMSI bentuk respons: { status, data: { Bahasa: [...], IT: [...], Karakter: [...] } }
            // atau { status, data: [ { category, exams: [...] } ] }. Kode di bawah menangani dua-duanya.
            const raw = json?.data ?? json ?? {};
            this.kategori = Array.isArray(raw)
                ? raw.map((k) => ({ nama: k.category, slug: slugify(k.category), warna: categoryColor(k.category), ujian: k.exams ?? [] }))
                : Object.entries(raw).map(([nama, ujian]) => ({ nama, slug: slugify(nama), warna: categoryColor(nama), ujian }));
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

// ---------- Ujian: ambil soal, jawab, submit ----------
Alpine.data('examPage', (examId) => ({
    loading: true, submitting: false, error: '', tab: 'pg',
    exam: null, pilihanGanda: [], essai: [], jawaban: {},
    async init() {
        try {
            const json = await api.getExam(examId);
            const data = json?.data ?? json;
            this.exam = { ...data.exam, warna: categoryColor(data.exam?.category) };
            this.pilihanGanda = (data.questions ?? []).filter((q) => q.type === 'multiple_choice');
            this.essai = (data.questions ?? []).filter((q) => q.type === 'essay');
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
    get answeredCount() {
        return this.pilihanGanda.filter((q) => this.jawaban[q.id]).length;
    },
    async submit() {
        this.submitting = true; this.error = '';
        try {
            const answers = Object.entries(this.jawaban)
                .filter(([, v]) => v !== '' && v != null)
                .map(([question_id, answer_text]) => ({ question_id: Number(question_id), answer_text }));
            await api.submitExam({ user_id: getUser()?.id, exam_id: this.exam.id, answers });
            window.location.href = '/profile';
        } catch (e) {
            this.error = e.message;
        } finally {
            this.submitting = false;
        }
    },
}));

// ---------- Portofolio ----------
Alpine.data('portofolioPage', () => ({
    linkGithub: '', linkYoutube: '', files: [], maxFiles: 5,
    submitting: false, error: '', success: false,
    addFiles(fileList) {
        const room = this.maxFiles - this.files.length;
        this.files.push(...Array.from(fileList).slice(0, room));
    },
    removeFile(i) { this.files.splice(i, 1); },
    async submit() {
        this.submitting = true; this.error = ''; this.success = false;
        try {
            const links = [this.linkGithub, this.linkYoutube].filter(Boolean).join(', ');
            const formData = new FormData();
            formData.append('user_id', getUser()?.id);
            formData.append('links', links);
            this.files.forEach((f) => formData.append('files[]', f));
            await api.submitPortfolio(formData);
            this.success = true;
            this.files = []; this.linkGithub = ''; this.linkYoutube = '';
        } catch (e) {
            this.error = e.message;
        } finally {
            this.submitting = false;
        }
    },
}));

// ---------- Profile: placement results grouped by learning area ----------
Alpine.data('profilePage', () => ({
    loading: true, error: '', student: null, languageStats: [], itStats: [], characterStats: [], portfolio: null,
    async init() {
        const user = getUser();
        if (!user) { this.error = 'Belum login.'; this.loading = false; return; }
        try {
            const json = await api.getDashboard(user.id);
            const data = json?.data ?? json;
            this.student = data.student;
            const stats = (data.exam_stats ?? []).map((s) => ({
                ...s,
                warna: categoryColor(s.category),
                nilaiPersen: Math.max(0, Math.min(100, Number(s.compatibility) || 0)),
            }));
            this.languageStats = stats.filter((item) => item.category === 'Bahasa');
            this.itStats = stats.filter((item) => item.category === 'IT');
            this.characterStats = stats.filter((item) => item.category === 'Karakter');
            this.portfolio = data.portfolio;
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
    languageLevel(percent) {
        if (percent < 20) return 'A1';
        if (percent < 40) return 'A2';
        if (percent < 60) return 'B1';
        if (percent < 75) return 'B2';
        if (percent < 90) return 'C1';
        return 'C2';
    },
    itLevel(percent) {
        if (percent < 34) return 'Rookie';
        if (percent < 67) return 'Amateur';
        return 'Pro';
    },
}));

function slugify(str = '') {
    return str.toString().trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
}

Alpine.start();
