import { useState } from "react";
import { Link } from "react-router-dom";
import AuthSplitLayout from "../components/auth/AuthSplitLayout";
import PageScaffold from "../components/PageScaffold";
import { Button, Input, Toast } from "../components/ui/primitives";

export default function ForgotPasswordPage() {
  const [email, setEmail] = useState("");
  const [error, setError] = useState("");
  const [pending, setPending] = useState(false);
  const [toastOpen, setToastOpen] = useState(false);

  const handleSubmit = async (event) => {
    event.preventDefault();
    if (!email.trim()) {
      setError("Email is required.");
      return;
    }
    setError("");
    setPending(true);
    await new Promise((resolve) => window.setTimeout(resolve, 700));
    setPending(false);
    setToastOpen(true);
  };

  return (
    <PageScaffold title="Reset your password" subtitle="We’re here to help you get back quickly.">
      <AuthSplitLayout
        title="Forgot your password?"
        subtitle="We’ll send you a link to reset your password."
        quote="Comfort starts with one simple step."
        imageUrl="https://picsum.photos/id/1080/1200/900"
      >
        <form
          onSubmit={handleSubmit}
          className="space-y-4 rounded-2xl bg-[#F2E6D8] p-5 shadow-[0_8px_20px_rgba(51,51,51,0.08)] sm:p-6"
        >
          <div className="space-y-1">
            <label htmlFor="forgot-email" className="text-sm font-medium text-[#333333]">
              Email
            </label>
            <Input
              id="forgot-email"
              type="email"
              value={email}
              onChange={(event) => setEmail(event.target.value)}
              placeholder="you@example.com"
              hint="Enter the email linked to your account."
              error={error}
              className="border-[#E8B04A]/35 bg-[#FFF8F0] text-[#333333] focus:ring-[#E8B04A]/40"
            />
          </div>

          <Button
            type="submit"
            fullWidth
            disabled={pending}
            className="bg-[#E8B04A] text-[#333333] hover:bg-[#d9a13d]"
          >
            {pending ? "Sending reset link..." : "Send Reset Link"}
          </Button>

          <p className="text-center text-sm text-[#333333]/80">
            Remembered your password?{" "}
            <Link to="/login" className="font-medium text-[#7A9E7E] hover:text-[#E8B04A] hover:underline">
              Back to Login
            </Link>
          </p>
        </form>
      </AuthSplitLayout>
      <Toast open={toastOpen} message="Reset link sent (demo)." onDone={() => setToastOpen(false)} />
    </PageScaffold>
  );
}
