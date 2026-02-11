import { useMemo, useState } from "react";
import { Link } from "react-router-dom";
import PageScaffold from "../components/PageScaffold";
import { useCartBadge } from "../context/CartBadgeContext";

export default function CheckoutPage() {
  const { cartLines, subtotal, minimumOrder } = useCartBadge();
  const [deliveryType, setDeliveryType] = useState("delivery");
  const [deliveryTime, setDeliveryTime] = useState("asap");
  const [paymentMethod, setPaymentMethod] = useState("card");
  const [formValues, setFormValues] = useState({
    fullName: "",
    phone: "",
    addressLine: "",
    city: "",
    note: "",
  });
  const [errors, setErrors] = useState({});
  const [submitted, setSubmitted] = useState(false);

  const deliveryFee = cartLines.length > 0 ? 2.5 : 0;
  const tax = subtotal * 0.1;
  const total = subtotal + deliveryFee + tax;
  const canCheckout = cartLines.length > 0 && subtotal >= minimumOrder;

  const summaryLines = useMemo(
    () =>
      cartLines.map((line) => ({
        id: line.id,
        title: `${line.name} x${line.quantity}`,
        amount: line.unitTotal * line.quantity,
      })),
    [cartLines],
  );

  const updateField = (field, value) => {
    setFormValues((prev) => ({ ...prev, [field]: value }));
    setErrors((prev) => ({ ...prev, [field]: "" }));
  };

  const validate = () => {
    const nextErrors = {};
    if (!formValues.fullName.trim()) {
      nextErrors.fullName = "Full name is required.";
    }
    if (!formValues.phone.trim()) {
      nextErrors.phone = "Phone number is required.";
    } else if (!/^[0-9+ -]{7,20}$/.test(formValues.phone.trim())) {
      nextErrors.phone = "Please enter a valid phone number.";
    }
    if (deliveryType === "delivery") {
      if (!formValues.addressLine.trim()) {
        nextErrors.addressLine = "Address is required for delivery.";
      }
      if (!formValues.city.trim()) {
        nextErrors.city = "City is required.";
      }
    }
    if (!canCheckout) {
      nextErrors.form = "Minimum order is not reached yet.";
    }
    return nextErrors;
  };

  const handleSubmit = (event) => {
    event.preventDefault();
    const nextErrors = validate();
    setErrors(nextErrors);
    setSubmitted(Object.keys(nextErrors).length === 0);
  };

  if (cartLines.length === 0) {
    return (
      <PageScaffold title="Checkout" subtitle="Delivery details and order confirmation.">
        <section className="space-y-4 rounded-2xl border border-[#E8B04A]/25 bg-[#F2E6D8] p-8 text-center text-[#333333] shadow-[0_8px_20px_rgba(51,51,51,0.06)]">
          <h2 className="text-lg font-semibold">No items to checkout</h2>
          <p className="text-sm text-[#333333]/75">
            Add products to your cart before opening checkout.
          </p>
          <Link
            to="/restaurants"
            className="inline-flex min-h-11 items-center rounded-xl bg-[#E8B04A] px-5 py-2 text-sm font-semibold text-[#333333] transition hover:brightness-95"
          >
            Browse restaurants
          </Link>
        </section>
      </PageScaffold>
    );
  }

  return (
    <PageScaffold title="Checkout" subtitle="Delivery details and order confirmation.">
      <section className="grid grid-cols-1 gap-4 pb-24 lg:grid-cols-[1fr_360px] lg:pb-0">
        <form id="checkout-form" className="space-y-4" onSubmit={handleSubmit} noValidate>
          <div className="rounded-2xl border border-[#E8B04A]/25 bg-[#F2E6D8] p-5 text-[#333333] shadow-[0_8px_20px_rgba(51,51,51,0.06)]">
            <h2 className="font-semibold">Delivery information</h2>
            <div className="mt-3 grid grid-cols-1 gap-3 md:grid-cols-2">
              <div>
                <input
                  value={formValues.fullName}
                  onChange={(event) => updateField("fullName", event.target.value)}
                  className="w-full rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-2.5 text-sm text-[#333333] outline-none focus:ring-2 focus:ring-[#E8B04A]/40"
                  placeholder="Full name"
                />
                {errors.fullName ? <p className="mt-1 text-xs text-[#D96C4A]">{errors.fullName}</p> : null}
              </div>
              <div>
                <input
                  value={formValues.phone}
                  onChange={(event) => updateField("phone", event.target.value)}
                  className="w-full rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-2.5 text-sm text-[#333333] outline-none focus:ring-2 focus:ring-[#E8B04A]/40"
                  placeholder="Phone number"
                />
                {errors.phone ? <p className="mt-1 text-xs text-[#D96C4A]">{errors.phone}</p> : null}
              </div>
            </div>
            <div className="mt-4 flex flex-wrap gap-2 text-sm">
              <button
                type="button"
                className={`min-h-11 rounded-full border px-4 py-2 text-sm ${
                  deliveryType === "delivery"
                    ? "border-[#E8B04A] bg-[#FFF8F0] text-[#333333]"
                    : "border-[#E8B04A]/35 text-[#333333]/80"
                }`}
                onClick={() => setDeliveryType("delivery")}
              >
                Delivery
              </button>
              <button
                type="button"
                className={`min-h-11 rounded-full border px-4 py-2 text-sm ${
                  deliveryType === "pickup"
                    ? "border-[#E8B04A] bg-[#FFF8F0] text-[#333333]"
                    : "border-[#E8B04A]/35 text-[#333333]/80"
                }`}
                onClick={() => setDeliveryType("pickup")}
              >
                Pickup
              </button>
            </div>
            {deliveryType === "delivery" ? (
              <div className="mt-3 grid grid-cols-1 gap-3 md:grid-cols-2">
                <div className="md:col-span-2">
                  <input
                    value={formValues.addressLine}
                    onChange={(event) => updateField("addressLine", event.target.value)}
                    className="w-full rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-2.5 text-sm text-[#333333] outline-none focus:ring-2 focus:ring-[#E8B04A]/40"
                    placeholder="Street and building"
                  />
                  {errors.addressLine ? (
                    <p className="mt-1 text-xs text-[#D96C4A]">{errors.addressLine}</p>
                  ) : null}
                </div>
                <div>
                  <input
                    value={formValues.city}
                    onChange={(event) => updateField("city", event.target.value)}
                    className="w-full rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-2.5 text-sm text-[#333333] outline-none focus:ring-2 focus:ring-[#E8B04A]/40"
                    placeholder="City"
                  />
                  {errors.city ? <p className="mt-1 text-xs text-[#D96C4A]">{errors.city}</p> : null}
                </div>
                <div>
                  <input
                    value={formValues.note}
                    onChange={(event) => updateField("note", event.target.value)}
                    className="w-full rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-2.5 text-sm text-[#333333] outline-none focus:ring-2 focus:ring-[#E8B04A]/40"
                    placeholder="Delivery note (optional)"
                  />
                </div>
              </div>
            ) : (
              <p className="mt-3 text-sm text-[#333333]/75">
                Pickup selected. We will prepare your order for in-store collection.
              </p>
            )}
          </div>

          <div className="rounded-2xl border border-[#E8B04A]/25 bg-[#F2E6D8] p-5 text-[#333333] shadow-[0_8px_20px_rgba(51,51,51,0.06)]">
            <h2 className="font-semibold">Time</h2>
            <div className="mt-3 flex flex-wrap gap-2 text-sm">
              <button
                type="button"
                className={`min-h-11 rounded-full border px-4 py-2 text-sm ${
                  deliveryTime === "asap"
                    ? "border-[#E8B04A] bg-[#FFF8F0] text-[#333333]"
                    : "border-[#E8B04A]/35 text-[#333333]/80"
                }`}
                onClick={() => setDeliveryTime("asap")}
              >
                ASAP
              </button>
              <button
                type="button"
                className={`min-h-11 rounded-full border px-4 py-2 text-sm ${
                  deliveryTime === "scheduled"
                    ? "border-[#E8B04A] bg-[#FFF8F0] text-[#333333]"
                    : "border-[#E8B04A]/35 text-[#333333]/80"
                }`}
                onClick={() => setDeliveryTime("scheduled")}
              >
                Schedule
              </button>
            </div>
            {deliveryTime === "scheduled" ? (
              <input
                type="datetime-local"
                className="mt-3 min-h-11 w-full rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-2.5 text-sm text-[#333333] outline-none focus:ring-2 focus:ring-[#E8B04A]/40 md:w-auto"
              />
            ) : null}
          </div>

          <div className="rounded-2xl border border-[#E8B04A]/25 bg-[#F2E6D8] p-5 text-[#333333] shadow-[0_8px_20px_rgba(51,51,51,0.06)]">
            <h2 className="font-semibold">Payment</h2>
            <div className="mt-3 space-y-2 text-sm">
              {[
                { key: "card", label: "Card on delivery" },
                { key: "cash", label: "Cash on delivery" },
                { key: "online", label: "Online payment" },
              ].map((option) => (
                <label
                  key={option.key}
                  className={`flex min-h-11 cursor-pointer items-center gap-3 rounded-xl border px-4 py-2 ${
                    paymentMethod === option.key
                      ? "border-[#E8B04A] bg-[#FFF8F0] text-[#333333]"
                      : "border-[#E8B04A]/35 text-[#333333]/80"
                  }`}
                >
                  <input
                    type="radio"
                    name="payment-method"
                    value={option.key}
                    checked={paymentMethod === option.key}
                    onChange={() => setPaymentMethod(option.key)}
                    className="h-4 w-4 border-[#E8B04A] text-[#E8B04A] focus:ring-[#E8B04A]/40"
                  />
                  {option.label}
                </label>
              ))}
            </div>
          </div>

          <div className="rounded-2xl border border-[#E8B04A]/25 bg-[#F2E6D8] p-5 text-[#333333] shadow-[0_8px_20px_rgba(51,51,51,0.06)]">
            <h2 className="font-semibold">Review and place order</h2>
            <p className="mt-2 text-sm text-[#333333]/75">
              Confirm your details and submit. API validation errors appear under each field.
            </p>
            {errors.form ? <p className="mt-2 text-xs text-[#D96C4A]">{errors.form}</p> : null}
            {submitted ? (
              <p className="mt-2 rounded-xl border border-[#7A9E7E]/40 bg-[#FFF8F0] px-3 py-2 text-sm text-[#7A9E7E]">
                Order submitted successfully (demo state).
              </p>
            ) : null}
            <button
              type="submit"
              disabled={!canCheckout}
              className={`mt-4 w-full rounded-xl px-4 py-2.5 text-sm font-semibold ${
                canCheckout
                  ? "bg-[#E8B04A] text-[#333333] hover:brightness-95"
                  : "cursor-not-allowed bg-[#d8cdbc] text-[#333333]/60"
              }`}
            >
              Place order
            </button>
          </div>
        </form>

        <aside className="space-y-4 lg:sticky lg:top-24 lg:h-fit">
          <details className="rounded-2xl border border-[#E8B04A]/25 bg-[#F2E6D8] p-4 text-[#333333] lg:hidden" open>
            <summary className="cursor-pointer text-sm font-semibold">Order summary</summary>
            <div className="mt-3 space-y-2 text-sm">
              {summaryLines.map((line) => (
                <div key={line.id} className="flex items-center justify-between gap-3">
                  <span className="truncate text-[#333333]/75">{line.title}</span>
                  <span>${line.amount.toFixed(2)}</span>
                </div>
              ))}
            </div>
          </details>

          <div className="hidden rounded-2xl border border-[#E8B04A]/25 bg-[#F2E6D8] p-4 text-[#333333] shadow-[0_8px_20px_rgba(51,51,51,0.06)] lg:block">
            <h2 className="font-semibold">Order summary</h2>
            <div className="mt-3 space-y-2 text-sm">
              {summaryLines.map((line) => (
                <div key={line.id} className="flex items-center justify-between gap-3">
                  <span className="truncate text-[#333333]/75">{line.title}</span>
                  <span>${line.amount.toFixed(2)}</span>
                </div>
              ))}
              <div className="mt-2 border-t border-[#E8B04A]/25 pt-2">
                <div className="flex items-center justify-between">
                  <span className="text-[#333333]/75">Subtotal</span>
                  <span>${subtotal.toFixed(2)}</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-[#333333]/75">Delivery</span>
                  <span>${deliveryFee.toFixed(2)}</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-[#333333]/75">Tax</span>
                  <span>${tax.toFixed(2)}</span>
                </div>
                <div className="mt-2 flex items-center justify-between text-base font-semibold">
                  <span>Total</span>
                  <span className="text-[#7A9E7E]">${total.toFixed(2)}</span>
                </div>
              </div>
            </div>
          </div>
        </aside>
      </section>

      {cartLines.length > 0 ? (
        <div className="fixed inset-x-0 bottom-0 z-40 border-t border-[#E8B04A]/25 bg-[#FFF8F0]/95 p-3 backdrop-blur lg:hidden">
          <div className="mx-auto flex w-full max-w-7xl items-center gap-3">
            <div className="min-w-0 flex-1">
              <p className="text-xs text-[#333333]/75">Total</p>
              <p className="text-sm font-semibold">${total.toFixed(2)}</p>
            </div>
            <button
              type="submit"
              form="checkout-form"
              disabled={!canCheckout}
              className={`inline-flex min-h-11 items-center justify-center rounded-xl px-5 py-2 text-sm font-semibold ${
                canCheckout
                  ? "bg-[#E8B04A] text-[#333333] hover:brightness-95"
                  : "cursor-not-allowed bg-[#d8cdbc] text-[#333333]/60"
              }`}
            >
              Place order
            </button>
          </div>
        </div>
      ) : null}
    </PageScaffold>
  );
}
