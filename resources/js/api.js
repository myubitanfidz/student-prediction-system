/**
 * Central API Client Wrapper - Talenta Santri
 */
const API_BASE = import.meta.env.VITE_API_BASE_URL || '/api';

export function getToken() {
    return localStorage.getItem('ts_token');
}

export function getUser() {
    try {
        return JSON.parse(localStorage.getItem('ts_user') || 'null');
    } catch {
        return null;
    }
}

export function setSession(token, user) {
    if (token) localStorage.setItem('ts_token', token);
    if (user) localStorage.setItem('ts_user', JSON.stringify(user));
}

export function clearSession() {
    localStorage.removeItem('ts_token');
    localStorage.removeItem('ts_user');
}

export function isLoggedIn() {
    return !!getUser();
}

async function request(path, { method = 'GET', body, isForm = false } = {}) {
    const headers = { Accept: 'application/json' };
    if (!isForm) headers['Content-Type'] = 'application/json';
    
    const token = getToken();
    if (token) headers['Authorization'] = `Bearer ${token}`;

    const res = await fetch(`${API_BASE}${path}`, {
        method,
        headers,
        body: isForm ? body : (body ? JSON.stringify(body) : undefined),
    });

    let json = null;
    try {
        json = await res.json();
    } catch {
        /* response kosong / non-json */
    }

    if (!res.ok) {
        if (res.status === 401) {
            clearSession();
            window.location.href = '/login';
        }
        const message = json?.message || (json?.errors ? Object.values(json.errors).flat().join(' ') : null);
        throw new Error(message || `Terjadi kesalahan (${res.status})`);
    }

    return json;
}

function extractSession(json) {
    const data = json?.data ?? json ?? {};
    const token = data.token || data.access_token || json?.token || null;
    const user = data.user || data.santri || (data.id ? data : null);
    return { token, user };
}

export const api = {
    register(payload) {
        return request('/register', { method: 'POST', body: payload }).then((json) => {
            const { token, user } = extractSession(json);
            setSession(token, user);
            return json;
        });
    },

    login(payload) {
        return request('/login', { method: 'POST', body: payload }).then((json) => {
            const { token, user } = extractSession(json);
            setSession(token, user);
            return json;
        });
    },

    logout() {
        return request('/logout', { method: 'POST' }).finally(() => {
            clearSession();
        });
    },

    // Santri Endpoints
    getExams() {
        return request('/exams');
    },

    getExam(id) {
        return request(`/exams/${id}`);
    },

    submitExam(payload) {
        // payload: { exam_id, answers: [{ question_id, answer_text }] }
        return request('/exams/submit', { method: 'POST', body: payload });
    },

    submitPortfolio(formData) {
        // formData berisi links & files[]
        return request('/portfolios', { method: 'POST', body: formData, isForm: true });
    },

    getDashboard() {
        return request('/dashboard');
    },

    // Admin Endpoints
    getAdminStudents() {
        return request('/admin/students');
    },

    getAdminStudentAnswers(userId) {
        return request(`/admin/students/${userId}/answers`);
    },

    gradeAnswer(payload) {
        // payload: { answer_id, score }
        return request('/admin/grade', { method: 'POST', body: payload });
    },

    allowRetake(payload) {
        // payload: { user_id, exam_id }
        return request('/admin/retake', { method: 'POST', body: payload });
    },

    getAdminExams() {
        return request('/admin/exams');
    },

    createExam(payload) {
        return request('/admin/exams', { method: 'POST', body: payload });
    },

    deleteExam(id) {
        return request(`/admin/exams/${id}`, { method: 'DELETE' });
    },

    updateExam(id, payload) {
        return request(`/admin/exams/${id}`, { method: 'PUT', body: payload });
    },

    getAdminQuestions(examId) {
        return request(`/admin/exams/${examId}/questions`);
    },

    createQuestion(payload) {
        return request('/admin/questions', { method: 'POST', body: payload });
    },

    updateQuestion(id, payload) {
        return request(`/admin/questions/${id}`, { method: 'PUT', body: payload });
    },

    deleteQuestion(id) {
        return request(`/admin/questions/${id}`, { method: 'DELETE' });
    }
};

export function categoryColor(categoryName = '') {
    const key = categoryName.trim().toLowerCase();
    if (key === 'bahasa') return 'blue';
    if (key === 'it') return 'green';
    if (key === 'karakter') return 'orange';
    return 'blue';
}