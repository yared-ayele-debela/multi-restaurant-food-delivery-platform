import { createContext, useContext, useEffect, useMemo, useState } from "react";
import {
  clearStoredAuth,
  fetchCurrentUser,
  getStoredToken,
  getStoredUser,
  loginApi,
  logoutApi,
  registerApi,
  setStoredAuth,
  updateProfileApi,
} from "../api/authApi";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(() => getStoredUser());
  const [token, setToken] = useState(() => getStoredToken());
  const [loading, setLoading] = useState(false);

  const login = async ({ email, password }) => {
    setLoading(true);
    try {
      const response = await loginApi({ email, password });
      setToken(response?.token || "");
      setUser(response?.user || null);
    } finally {
      setLoading(false);
    }
  };

  const register = async ({ fullName, email, password }) => {
    setLoading(true);
    try {
      const response = await registerApi({
        name: fullName,
        email,
        password,
        passwordConfirmation: password,
      });
      setToken(response?.token || "");
      setUser(response?.user || null);
    } finally {
      setLoading(false);
    }
  };

  const logout = async () => {
    const currentToken = token;
    if (currentToken) {
      try {
        await logoutApi(currentToken);
      } catch {
        // Clear local auth even if backend token revoke fails.
      }
    }
    setToken("");
    setUser(null);
  };

  const updateProfile = async (payload) => {
    if (!token) {
      throw new Error("You must be logged in.");
    }
    setLoading(true);
    try {
      const nextUser = await updateProfileApi(token, payload);
      setUser(nextUser);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (!token) {
      clearStoredAuth();
      return;
    }
    if (user) {
      setStoredAuth({ token, user });
    }
  }, [token, user]);

  useEffect(() => {
    let mounted = true;
    async function hydrateUser() {
      if (!token || user) {
        return;
      }
      try {
        const nextUser = await fetchCurrentUser(token);
        if (mounted) {
          setUser(nextUser);
        }
      } catch {
        if (mounted) {
          clearStoredAuth();
          setToken("");
          setUser(null);
        }
      }
    }
    hydrateUser();
    return () => {
      mounted = false;
    };
  }, [token, user]);

  const value = useMemo(
    () => ({
      user,
      token,
      loading,
      login,
      register,
      updateProfile,
      logout,
    }),
    [user, token, loading],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error("useAuth must be used inside AuthProvider");
  }
  return context;
}
