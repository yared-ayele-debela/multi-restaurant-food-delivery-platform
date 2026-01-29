import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import PageScaffold from "../components/PageScaffold";
import { useAuth } from "../context/AuthContext";
import { Button, Input } from "../components/ui/primitives";
import AuthSplitLayout from "../components/auth/AuthSplitLayout";

export default function RegisterPage() {
  const { register, loading } = useAuth();
  const navigate = useNavigate();
  const [form, setForm] = useState({
    fullName: "",
    email: "",
    password: "",
    confirmPassword: "",
    acceptTerms: false,
  });
  const [errors, setErrors] = useState({});

  const handleSubmit = async (event) => {
    event.preventDefault();
    const nextErrors = {};
    if (!form.fullName.trim()) {
      nextErrors.fullName = "Full name is required.";
    }
    if (!form.email.trim()) {
      nextErrors.email = "Email is required.";
    }
    if (!form.password.trim()) {
      nextErrors.password = "Password is required.";
    } else if (form.password.length < 8) {
      nextErrors.password = "Password must be at least 8 characters.";
    }
    if (form.confirmPassword !== form.password) {
      nextErrors.confirmPassword = "Passwords do not match.";
    }
    if (!form.acceptTerms) {
      nextErrors.acceptTerms = "Please accept Terms & Conditions.";
    }
    setErrors(nextErrors);
    if (Object.keys(nextErrors).length > 0) {
      return;
    }
    await register({ fullName: form.fullName, email: form.email, password: form.password });
    navigate("/", { replace: true });
  };

  return (
    <PageScaffold title="Create your cozy account" subtitle="Simple meals start with one small step.">
      <AuthSplitLayout
        title="Create your cozy account"
        subtitle="Simple meals start with one small step."
        quote="Welcome to your warm kitchen corner."
        imageUrl="https://picsum.photos/id/292/1200/900"
      >
        <form
          onSubmit={handleSubmit}
          className="space-y-4 rounded-2xl bg-[#F2E6D8] p-5 shadow-[0_8px_20px_rgba(51,51,51,0.08)] sm:p-6"
        >
          <div className="space-y-1">
            <label htmlFor="register-full-name" className="text-sm font-medium text-[#333333]">
              Full name
            </label>
            <Input
              id="register-full-name"
              value={form.fullName}
              onChange={(event) => setForm((prev) => ({ ...prev, fullName: event.target.value }))}
              placeholder="Your full name"
              error={errors.fullName}
              className="border-[#E8B04A]/35 bg-[#FFF8F0] text-[#333333] focus:ring-[#E8B04A]/40"
            />
          </div>

          <div className="space-y-1">
            <label htmlFor="register-email" className="text-sm font-medium text-[#333333]">
              Email
            </label>
            <Input
              id="register-email"
              type="email"
              value={form.email}
              onChange={(event) => setForm((prev) => ({ ...prev, email: event.target.value }))}
              placeholder="you@example.com"
              error={errors.email}
              className="border-[#E8B04A]/35 bg-[#FFF8F0] text-[#333333] focus:ring-[#E8B04A]/40"
            />
          </div>

          <div className="space-y-1">
            <label htmlFor="register-password" className="text-sm font-medium text-[#333333]">
              Password
            </label>
            <Input
              id="register-password"
              type="password"
              value={form.password}
              onChange={(event) => setForm((prev) => ({ ...prev, password: event.target.value }))}
              placeholder="Create password"
              hint="At least 8 characters."
              error={errors.password}
              className="border-[#E8B04A]/35 bg-[#FFF8F0] text-[#333333] focus:ring-[#E8B04A]/40"
            />
          </div>

          <div className="space-y-1">
            <label htmlFor="register-confirm-password" className="text-sm font-medium text-[#333333]">
              Confirm password
            </label>
            <Input
              id="register-confirm-password"
              type="password"
              value={form.confirmPassword}
              onChange={(event) => setForm((prev) => ({ ...prev, confirmPassword: event.target.value }))}
              placeholder="Repeat password"
              error={errors.confirmPassword}
              className="border-[#E8B04A]/35 bg-[#FFF8F0] text-[#333333] focus:ring-[#E8B04A]/40"
            />
          </div>

          <div className="space-y-1">
            <label className="inline-flex min-h-11 items-center gap-2 text-sm text-[#333333]/85">
              <input
                type="checkbox"
                checked={form.acceptTerms}
                onChange={(event) => setForm((prev) => ({ ...prev, acceptTerms: event.target.checked }))}
              />
              I agree to Terms & Conditions
            </label>
            {errors.acceptTerms ? (
              <p className="text-xs text-[#D96C4A]">{errors.acceptTerms}</p>
            ) : null}
          </div>

          <Button
            type="submit"
            fullWidth
            disabled={loading}
            className="bg-[#E8B04A] text-[#333333] hover:bg-[#d9a13d]"
          >
            {loading ? "Creating account..." : "Register"}
          </Button>
          <p className="text-center text-sm text-[#333333]/80">
            Already have an account?{" "}
            <Link to="/login" className="font-medium text-[#7A9E7E] hover:text-[#E8B04A] hover:underline">
              Login
            </Link>
          </p>
        </form>
      </AuthSplitLayout>
    </PageScaffold>
  );
}
