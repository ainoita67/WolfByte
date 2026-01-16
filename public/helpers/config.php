// config.js

export const BASE_URL = "https://midominio.com/api"; // Endpoint principal de la API

// Rutas de páginas internas
export const ROUTES = {
  LOGIN: "/pages/login.html",
  DASHBOARD: "/pages/dashboard.html",
  PROFILE: "/pages/profile.html",
  ADMIN: "/pages/admin.html"
};

// Selectores globales (opcional)
export const SELECTORS = {
  NAV: "#mainNav",
  LOGIN_FORM: "#loginForm",
  LOGOUT_BTN: "#logoutBtn"
};

// Constantes de la aplicación
export const APP_CONSTANTS = {
  TOKEN_KEY: "app_token",
  USER_ROLE_ADMIN: "admin",
  USER_ROLE_USER: "user"
};
