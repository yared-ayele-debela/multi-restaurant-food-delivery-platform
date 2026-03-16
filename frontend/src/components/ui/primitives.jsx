import { useEffect } from "react";

const buttonVariants = {
  primary: "bg-[var(--color-accent)] text-white hover:bg-[var(--color-accent-hover)]",
  secondary: "border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text)] hover:bg-slate-50 dark:hover:bg-slate-800",
  ghost: "text-[var(--color-text)] hover:bg-slate-100 dark:hover:bg-slate-800",
};

const buttonSizes = {
  sm: "px-3 py-1.5 text-sm",
  md: "px-4 py-2 text-sm",
  lg: "px-5 py-2.5 text-sm",
};

export function Button({ variant = "primary", size = "md", fullWidth = false, className = "", ...props }) {
  return (
    <button
      className={`inline-flex min-h-11 items-center justify-center rounded-lg font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 disabled:cursor-not-allowed disabled:opacity-60 ${buttonVariants[variant]} ${buttonSizes[size]} ${fullWidth ? "w-full" : ""} ${className}`}
      {...props}
    />
  );
}

export function Input({ error = "", hint = "", className = "", ...props }) {
  return (
    <div className="space-y-1">
      <input
        className={`w-full min-h-11 rounded-lg border border-[var(--color-border)] bg-transparent px-3 py-2.5 text-sm outline-none ring-sky-400 focus:ring-2 ${error ? "border-rose-400 ring-rose-300" : ""} ${className}`}
        {...props}
      />
      {error ? <p className="text-xs text-rose-700">{error}</p> : null}
      {!error && hint ? <p className="text-xs text-[var(--color-muted)]">{hint}</p> : null}
    </div>
  );
}

export function Select({ error = "", className = "", children, ...props }) {
  return (
    <div className="space-y-1">
      <select
        className={`w-full min-h-11 rounded-lg border border-[var(--color-border)] bg-transparent px-3 py-2.5 text-sm outline-none ring-sky-400 focus:ring-2 ${error ? "border-rose-400" : ""} ${className}`}
        {...props}
      >
        {children}
      </select>
      {error ? <p className="text-xs text-rose-700">{error}</p> : null}
    </div>
  );
}

export function Checkbox({ label, ...props }) {
  return (
    <label className="inline-flex min-h-11 cursor-pointer items-center gap-2 text-sm">
      <input type="checkbox" {...props} />
      <span>{label}</span>
    </label>
  );
}

export function Radio({ name, options, value, onChange }) {
  return (
    <div className="flex flex-wrap gap-2">
      {options.map((option) => (
        <label key={option.value} className="inline-flex min-h-11 cursor-pointer items-center gap-2 text-sm">
          <input
            type="radio"
            name={name}
            value={option.value}
            checked={value === option.value}
            onChange={(event) => onChange(event.target.value)}
          />
          <span>{option.label}</span>
        </label>
      ))}
    </div>
  );
}

export function Card({ className = "", children }) {
  return (
    <article className={`rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-4 shadow-sm ${className}`}>
      {children}
    </article>
  );
}

export function Badge({ children, className = "" }) {
  return (
    <span className={`rounded-full border border-[var(--color-border)] px-2.5 py-1 text-xs ${className}`}>
      {children}
    </span>
  );
}

export function Modal({ open, title, onClose, children }) {
  if (!open) {
    return null;
  }
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div className="w-full max-w-md rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-4 shadow-xl">
        <div className="flex items-center justify-between">
          <h3 className="font-semibold">{title}</h3>
          <button type="button" onClick={onClose} className="text-sm">
            Close
          </button>
        </div>
        <div className="mt-3">{children}</div>
      </div>
    </div>
  );
}

export function Drawer({ open, title, onClose, children }) {
  return (
    <div
      className={`fixed inset-y-0 right-0 z-50 w-full max-w-sm transform border-l border-[var(--color-border)] bg-[var(--color-surface)] p-4 shadow-xl transition ${
        open ? "translate-x-0" : "translate-x-full"
      }`}
    >
      <div className="flex items-center justify-between">
        <h3 className="font-semibold">{title}</h3>
        <button type="button" onClick={onClose} className="text-sm">
          Close
        </button>
      </div>
      <div className="mt-3">{children}</div>
    </div>
  );
}

export function Skeleton({ className = "" }) {
  return <div className={`animate-pulse rounded bg-slate-200 dark:bg-slate-800 ${className}`} />;
}

export function Toast({ open, message, onDone, variant = "success" }) {
  useEffect(() => {
    if (!open) {
      return;
    }
    const timer = window.setTimeout(() => onDone?.(), 1800);
    return () => window.clearTimeout(timer);
  }, [open, onDone]);

  if (!open) {
    return null;
  }

  return (
    <div className="fixed bottom-4 right-4 z-50">
      <div
        className={`rounded-lg border px-4 py-2 text-sm shadow-lg ${
          variant === "error"
            ? "border-rose-200 bg-rose-50 text-rose-700"
            : "border-emerald-200 bg-emerald-50 text-emerald-700"
        }`}
      >
        {message}
      </div>
    </div>
  );
}
