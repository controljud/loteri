export default {
    setToken(token) {
        window.localStorage.setItem('token', token);
    },

    getToken() {
        return window.localStorage.getItem('token');
    },

    removeToken() {
        window.localStorage.removeItem('token');
    },

    hasToken() {
        window.localStorage.hasItem('token');
    }
}