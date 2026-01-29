const RAW_API_URL = import.meta.env.VITE_API_URL || "";
const API_ORIGIN = RAW_API_URL.trim().replace(/\/+$/, "");
const API_BASE_URL = API_ORIGIN ? `${API_ORIGIN}/api/v1` : "/api/v1";

export const AUTH_TOKEN_STORAGE_KEY = "food-delivery.auth.token";
export const AUTH_USER_STORAGE_KEY = "food-delivery.auth.user";

function buildHeaders(token, hasBody) {
  const headers = {
    Accept: "application/json",
  };
  if (hasBody) {
    headers["Content-Type"] = "application/json";
  }
  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }
  return headers;
}

async function request(path, { method = "GET", token, body } = {}) {
  const response = await fetch(`${API_BASE_URL}${path}`, {
    method,
    headers: buildHeaders(token, body !== undefined),
    body: body !== undefined ? JSON.stringify(body) : undefined,
  });

  if (response.status === 204) {
    return null;
  }

  let json = null;
  try {
    json = await response.json();
  } catch {
    json = null;
  }

  if (!response.ok) {
    const message = json?.message || `Request failed: ${response.status}`;
    const error = new Error(message);
    error.status = response.status;
    error.payload = json;
    throw error;
  }

  return json;
}

export function getStoredToken() {
  if (typeof window === "undefined") {
    return "";
  }
  return window.localStorage.getItem(AUTH_TOKEN_STORAGE_KEY) || "";
}

export function setStoredAuth({ token, user }) {
  if (typeof window === "undefined") {
    return;
  }
  if (!token || !user) {
    window.localStorage.removeItem(AUTH_TOKEN_STORAGE_KEY);
    window.localStorage.removeItem(AUTH_USER_STORAGE_KEY);
    return;
  }
  window.localStorage.setItem(AUTH_TOKEN_STORAGE_KEY, token);
  window.localStorage.setItem(AUTH_USER_STORAGE_KEY, JSON.stringify(user));
}

export function getStoredUser() {
  if (typeof window === "undefined") {
    return null;
  }
  try {
    const raw = window.localStorage.getItem(AUTH_USER_STORAGE_KEY);
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

export function clearStoredAuth() {
  if (typeof window === "undefined") {
    return;
  }
  window.localStorage.removeItem(AUTH_TOKEN_STORAGE_KEY);
  window.localStorage.removeItem(AUTH_USER_STORAGE_KEY);
}

export async function loginApi({ email, password }) {
  return request("/auth/login", {
    method: "POST",
    body: { email, password },
  });
}

export async function registerApi({ name, email, password, passwordConfirmation }) {
  return request("/auth/register", {
    method: "POST",
    body: {
      name,
      email,
      password,
      password_confirmation: passwordConfirmation,
    },
  });
}

export async function fetchCurrentUser(token) {
  const json = await request("/auth/user", { token });
  return json?.user || null;
}

export async function updateProfileApi(token, payload) {
  const json = await request("/auth/user", {
    method: "PATCH",
    token,
    body: payload,
  });
  return json?.user || null;
}

export async function logoutApi(token) {
  return request("/auth/logout", {
    method: "POST",
    token,
  });
}

export async function fetchUserAddressesApi(token) {
  const json = await request("/user/addresses", { token });
  return Array.isArray(json?.data) ? json.data : [];
}

export async function createUserAddressApi(token, payload) {
  const json = await request("/user/addresses", {
    method: "POST",
    token,
    body: payload,
  });
  return json?.data || null;
}

export async function updateUserAddressApi(token, addressId, payload) {
  const json = await request(`/user/addresses/${addressId}`, {
    method: "PATCH",
    token,
    body: payload,
  });
  return json?.data || null;
}

export async function deleteUserAddressApi(token, addressId) {
  await request(`/user/addresses/${addressId}`, {
    method: "DELETE",
    token,
  });
}
