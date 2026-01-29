import { useState } from "react";
import { Link, useLocation, useNavigate } from "react-router-dom";
import PageScaffold from "../components/PageScaffold";
import { useAuth } from "../context/AuthContext";
import { Button, Input, Toast } from "../components/ui/primitives";
import AuthSplitLayout from "../components/auth/AuthSplitLayout";

export default function LoginPage() {
  const { login, loading } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const [form, setForm] = useState({ email: "", password: "", remember: false });
  const [errors, setErrors] = useState({});
  const [toastOpen, setToastOpen] = useState(false);

  const nextPath = location.state?.from || "/";

  const handleSubmit = async (event) => {
    event.preventDefault();
    const nextErrors = {};
    if (!form.email.trim()) {
      nextErrors.email = "Email is required.";
    }
    if (!form.password.trim()) {
      nextErrors.password = "Password is required.";
    }
    setErrors(nextErrors);
    if (Object.keys(nextErrors).length > 0) {
      return;
    }
    await login({ email: form.email, password: form.password });
    setToastOpen(true);
    window.setTimeout(() => navigate(nextPath, { replace: true }), 250);
  };

  return (
    <PageScaffold title="Welcome back 👋" subtitle="Let’s get you back to your table.">
      <AuthSplitLayout
        title="Welcome back 👋"
        subtitle="Let’s get you back to your table."
        quote="Simple meals, strong connections."
        imageUrl="https://picsum.photos/id/1060/1200/900"
      >
        <form
          onSubmit={handleSubmit}
          className="space-y-4 rounded-2xl bg-[#F2E6D8] p-5 shadow-[0_8px_20px_rgba(51,51,51,0.08)] sm:p-6"
        >
          <div className="space-y-1">
            <label htmlFor="login-email" className="text-sm font-medium text-[#333333]">
              Email
            </label>
            <Input
              id="login-email"
              type="email"
              value={form.email}
              onChange={(event) => setForm((prev) => ({ ...prev, email: event.target.value }))}
              placeholder="you@example.com"
              hint="Use the email associated with your account."
              error={errors.email}
              className="border-[#E8B04A]/35 bg-[#FFF8F0] text-[#333333] focus:ring-[#E8B04A]/40"
            />
          </div>

          <div className="space-y-1">
            <label htmlFor="login-password" className="text-sm font-medium text-[#333333]">
              Password
            </label>
            <Input
              id="login-password"
              type="password"
              value={form.password}
              onChange={(event) => setForm((prev) => ({ ...prev, password: event.target.value }))}
              placeholder="Enter password"
              hint="Minimum 8 characters recommended."
              error={errors.password}
              className="border-[#E8B04A]/35 bg-[#FFF8F0] text-[#333333] focus:ring-[#E8B04A]/40"
            />
          </div>

          <div className="flex flex-wrap items-center justify-between gap-2">
            <label className="inline-flex min-h-11 items-center gap-2 text-sm text-[#333333]/85">
              <input
                type="checkbox"
                checked={form.remember}
                onChange={(event) => setForm((prev) => ({ ...prev, remember: event.target.checked }))}
              />
              Remember me
            </label>
            <Link to="/forgot-password" className="text-sm text-[#7A9E7E] hover:text-[#E8B04A] hover:underline">
              Forgot password?
            </Link>
          </div>

          <Button
            type="submit"
            fullWidth
            disabled={loading}
            className="bg-[#E8B04A] text-[#333333] hover:bg-[#d9a13d]"
          >
            {loading ? "Logging in..." : "Login"}
          </Button>

          <p className="text-center text-sm text-[#333333]/80">
            No account?{" "}
            <Link to="/register" className="font-medium text-[#7A9E7E] hover:text-[#E8B04A] hover:underline">
              Create one
            </Link>
          </p>
        </form>
      </AuthSplitLayout>
      <Toast open={toastOpen} message="Login successful." onDone={() => setToastOpen(false)} />
    </PageScaffold>
  );
}
