import { useEffect, useMemo, useState } from "react";
import {
  createUserAddressApi,
  deleteUserAddressApi,
  fetchUserAddressesApi,
  updateUserAddressApi,
} from "../api/authApi";
import { useAuth } from "../context/AuthContext";
import PageScaffold from "../components/PageScaffold";

const DEFAULT_LATITUDE = 9.03;
const DEFAULT_LONGITUDE = 38.74;

export default function AccountPage() {
  const { user, token, logout, loading, updateProfile } = useAuth();
  const [profile, setProfile] = useState({
    fullName: user?.name || "",
    email: user?.email || "",
    phone: user?.phone || "",
  });
  const [profileErrors, setProfileErrors] = useState({});
  const [profileMessage, setProfileMessage] = useState("");

  const [addresses, setAddresses] = useState([]);
  const [addressesLoading, setAddressesLoading] = useState(false);
  const [addressSaving, setAddressSaving] = useState(false);
  const [addressErrors, setAddressErrors] = useState({});
  const [addressMessage, setAddressMessage] = useState("");
  const [editingAddressId, setEditingAddressId] = useState("");
  const [addressForm, setAddressForm] = useState({
    label: "Home",
    addressLine1: "",
    city: "",
    state: "",
    postalCode: "",
    instructions: "",
    isDefault: false,
  });

  useEffect(() => {
    setProfile({
      fullName: user?.name || "",
      email: user?.email || "",
      phone: user?.phone || "",
    });
  }, [user]);

  useEffect(() => {
    let mounted = true;
    async function loadAddresses() {
      if (!token) {
        return;
      }
      try {
        setAddressesLoading(true);
        const data = await fetchUserAddressesApi(token);
        if (!mounted) {
          return;
        }
        setAddresses(data);
      } catch (error) {
        if (mounted) {
          setAddressMessage(error?.message || "Failed to load addresses.");
        }
      } finally {
        if (mounted) {
          setAddressesLoading(false);
        }
      }
    }
    loadAddresses();
    return () => {
      mounted = false;
    };
  }, [token]);

  const profileIsDirty = useMemo(() => {
    return (
      profile.fullName !== (user?.name || "") ||
      profile.email !== (user?.email || "") ||
      profile.phone !== (user?.phone || "")
    );
  }, [profile, user]);

  const updateProfileField = (field, value) => {
    setProfileMessage("");
    setProfileErrors((prev) => ({ ...prev, [field]: "" }));
    setProfile((prev) => ({ ...prev, [field]: value }));
  };

  const validateProfile = () => {
    const nextErrors = {};
    if (!profile.fullName.trim()) {
      nextErrors.fullName = "Name is required.";
    }
    if (!profile.email.trim()) {
      nextErrors.email = "Email is required.";
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(profile.email.trim())) {
      nextErrors.email = "Enter a valid email address.";
    }
    if (profile.phone && !/^[0-9+ -]{7,20}$/.test(profile.phone.trim())) {
      nextErrors.phone = "Enter a valid phone number.";
    }
    return nextErrors;
  };

  const handleProfileSubmit = async (event) => {
    event.preventDefault();
    const nextErrors = validateProfile();
    setProfileErrors(nextErrors);
    setProfileMessage("");
    if (Object.keys(nextErrors).length > 0) {
      return;
    }
    try {
      await updateProfile({
        name: profile.fullName.trim(),
        email: profile.email.trim(),
        phone: profile.phone.trim() || null,
      });
      setProfileMessage("Profile updated successfully.");
    } catch (error) {
      setProfileMessage(error?.message || "Failed to update profile.");
    }
  };

  const updateAddressField = (field, value) => {
    setAddressMessage("");
    setAddressErrors((prev) => ({ ...prev, [field]: "" }));
    setAddressForm((prev) => ({ ...prev, [field]: value }));
  };

  const validateAddressForm = () => {
    const nextErrors = {};
    if (!addressForm.label.trim()) {
      nextErrors.label = "Label is required.";
    }
    if (!addressForm.addressLine1.trim()) {
      nextErrors.addressLine1 = "Street address is required.";
    }
    if (!addressForm.city.trim()) {
      nextErrors.city = "City is required.";
    }
    return nextErrors;
  };

  const resetAddressForm = () => {
    setEditingAddressId("");
    setAddressForm({
      label: "Home",
      addressLine1: "",
      city: "",
      state: "",
      postalCode: "",
      instructions: "",
      isDefault: false,
    });
    setAddressErrors({});
  };

  const handleAddressSubmit = async (event) => {
    event.preventDefault();
    const nextErrors = validateAddressForm();
    setAddressErrors(nextErrors);
    setAddressMessage("");
    if (Object.keys(nextErrors).length > 0 || !token) {
      return;
    }

    const payload = {
      label: addressForm.label.trim(),
      address_line_1: addressForm.addressLine1.trim(),
      city: addressForm.city.trim(),
      state: addressForm.state.trim() || null,
      postal_code: addressForm.postalCode.trim() || null,
      instructions: addressForm.instructions.trim() || null,
      latitude: DEFAULT_LATITUDE,
      longitude: DEFAULT_LONGITUDE,
      is_default: Boolean(addressForm.isDefault),
    };

    try {
      setAddressSaving(true);
      if (editingAddressId) {
        const updated = await updateUserAddressApi(token, editingAddressId, payload);
        setAddresses((prev) => prev.map((item) => (String(item.id) === String(updated.id) ? updated : item)));
        setAddressMessage("Address updated successfully.");
      } else {
        const created = await createUserAddressApi(token, payload);
        setAddresses((prev) => [created, ...prev]);
        setAddressMessage("Address added successfully.");
      }
      resetAddressForm();
    } catch (error) {
      setAddressMessage(error?.message || "Failed to save address.");
    } finally {
      setAddressSaving(false);
    }
  };

  const handleEditAddress = (item) => {
    setEditingAddressId(String(item.id));
    setAddressForm({
      label: item.label || "Home",
      addressLine1: item.address_line_1 || "",
      city: item.city || "",
      state: item.state || "",
      postalCode: item.postal_code || "",
      instructions: item.instructions || "",
      isDefault: Boolean(item.is_default),
    });
    setAddressErrors({});
    setAddressMessage("");
  };

  const handleDeleteAddress = async (addressId) => {
    if (!token) {
      return;
    }
    try {
      await deleteUserAddressApi(token, addressId);
      setAddresses((prev) => prev.filter((item) => String(item.id) !== String(addressId)));
      if (String(addressId) === editingAddressId) {
        resetAddressForm();
      }
      setAddressMessage("Address removed.");
    } catch (error) {
      setAddressMessage(error?.message || "Failed to remove address.");
    }
  };

  return (
    <PageScaffold title="My Profile" subtitle="Manage personal details and addresses.">
      <section className="grid grid-cols-1 gap-4 lg:grid-cols-[260px_1fr]">
        <aside className="h-fit space-y-2 rounded-2xl border border-[#E8B04A]/25 bg-[#F2E6D8] p-4 text-[#333333] shadow-[0_8px_20px_rgba(51,51,51,0.06)]">
          {["Profile", "Orders", "Addresses", "Settings"].map((item, index) => (
            <button
              key={item}
              type="button"
              className={`min-h-11 w-full rounded-xl px-3 py-2 text-left text-sm transition ${
                index === 0
                  ? "bg-[#E8B04A] font-semibold text-[#333333]"
                  : "bg-[#FFF8F0] text-[#333333]/80 hover:bg-[#E8B04A]/20"
              }`}
            >
              {item}
            </button>
          ))}
        </aside>

        <article className="space-y-4">
          <form
            onSubmit={handleProfileSubmit}
            className="rounded-2xl border border-[#E8B04A]/25 bg-[#F2E6D8] p-5 text-[#333333] shadow-[0_8px_20px_rgba(51,51,51,0.06)]"
          >
            <h2 className="font-semibold">Profile details</h2>
            <div className="mt-3 grid grid-cols-1 gap-3 md:grid-cols-2">
              <label className="space-y-1 md:col-span-2">
                <span className="text-sm font-medium">Name</span>
                <input
                  value={profile.fullName}
                  onChange={(event) => updateProfileField("fullName", event.target.value)}
                  className="min-h-11 w-full rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#E8B04A]/40"
                  placeholder="Full name"
                />
                {profileErrors.fullName ? <p className="text-xs text-[#D96C4A]">{profileErrors.fullName}</p> : null}
              </label>
              <label className="space-y-1">
                <span className="text-sm font-medium">Email</span>
                <input
                  value={profile.email}
                  onChange={(event) => updateProfileField("email", event.target.value)}
                  className="min-h-11 w-full rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#E8B04A]/40"
                  placeholder="Email"
                />
                {profileErrors.email ? <p className="text-xs text-[#D96C4A]">{profileErrors.email}</p> : null}
              </label>
              <label className="space-y-1">
                <span className="text-sm font-medium">Phone</span>
                <input
                  value={profile.phone}
                  onChange={(event) => updateProfileField("phone", event.target.value)}
                  className="min-h-11 w-full rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#E8B04A]/40"
                  placeholder="Phone"
                />
                {profileErrors.phone ? <p className="text-xs text-[#D96C4A]">{profileErrors.phone}</p> : null}
              </label>
            </div>

            <button
              type="submit"
              disabled={!profileIsDirty || loading}
              className={`mt-4 min-h-11 rounded-xl px-5 py-2 text-sm font-semibold transition ${
                !profileIsDirty || loading
                  ? "cursor-not-allowed bg-[#d8cdbc] text-[#333333]/60"
                  : "bg-[#E8B04A] text-[#333333] hover:brightness-95"
              }`}
            >
              {loading ? "Updating..." : "Update Profile"}
            </button>
            {profileMessage ? <p className="mt-2 text-sm text-[#7A9E7E]">{profileMessage}</p> : null}
          </form>

          <div className="rounded-2xl border border-[#E8B04A]/25 bg-[#F2E6D8] p-5 text-[#333333] shadow-[0_8px_20px_rgba(51,51,51,0.06)]">
            <h2 className="font-semibold">Saved addresses</h2>
            <form onSubmit={handleAddressSubmit} className="mt-3 grid grid-cols-1 gap-3 md:grid-cols-2">
              <label className="space-y-1">
                <span className="text-sm font-medium">Label</span>
                <input
                  value={addressForm.label}
                  onChange={(event) => updateAddressField("label", event.target.value)}
                  className="min-h-11 w-full rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#E8B04A]/40"
                  placeholder="Home / Office"
                />
                {addressErrors.label ? <p className="text-xs text-[#D96C4A]">{addressErrors.label}</p> : null}
              </label>
              <label className="space-y-1">
                <span className="text-sm font-medium">City</span>
                <input
                  value={addressForm.city}
                  onChange={(event) => updateAddressField("city", event.target.value)}
                  className="min-h-11 w-full rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#E8B04A]/40"
                  placeholder="City"
                />
                {addressErrors.city ? <p className="text-xs text-[#D96C4A]">{addressErrors.city}</p> : null}
              </label>
              <label className="space-y-1 md:col-span-2">
                <span className="text-sm font-medium">Address line</span>
                <input
                  value={addressForm.addressLine1}
                  onChange={(event) => updateAddressField("addressLine1", event.target.value)}
                  className="min-h-11 w-full rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#E8B04A]/40"
                  placeholder="Street, building, landmark"
                />
                {addressErrors.addressLine1 ? (
                  <p className="text-xs text-[#D96C4A]">{addressErrors.addressLine1}</p>
                ) : null}
              </label>
              <label className="space-y-1">
                <span className="text-sm font-medium">State (optional)</span>
                <input
                  value={addressForm.state}
                  onChange={(event) => updateAddressField("state", event.target.value)}
                  className="min-h-11 w-full rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#E8B04A]/40"
                  placeholder="State"
                />
              </label>
              <label className="space-y-1">
                <span className="text-sm font-medium">Postal code (optional)</span>
                <input
                  value={addressForm.postalCode}
                  onChange={(event) => updateAddressField("postalCode", event.target.value)}
                  className="min-h-11 w-full rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#E8B04A]/40"
                  placeholder="Postal code"
                />
              </label>
              <label className="space-y-1 md:col-span-2">
                <span className="text-sm font-medium">Delivery instructions (optional)</span>
                <input
                  value={addressForm.instructions}
                  onChange={(event) => updateAddressField("instructions", event.target.value)}
                  className="min-h-11 w-full rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#E8B04A]/40"
                  placeholder="Gate code, floor, notes..."
                />
              </label>
              <label className="inline-flex min-h-11 items-center gap-2 text-sm md:col-span-2">
                <input
                  type="checkbox"
                  checked={addressForm.isDefault}
                  onChange={(event) => updateAddressField("isDefault", event.target.checked)}
                />
                Set as default address
              </label>
              <div className="flex gap-2 md:col-span-2">
                <button
                  type="submit"
                  disabled={addressSaving}
                  className={`min-h-11 rounded-xl px-4 py-2 text-sm font-semibold ${
                    addressSaving
                      ? "cursor-not-allowed bg-[#d8cdbc] text-[#333333]/60"
                      : "bg-[#E8B04A] text-[#333333] hover:brightness-95"
                  }`}
                >
                  {addressSaving ? "Saving..." : editingAddressId ? "Save address" : "Add address"}
                </button>
                {editingAddressId ? (
                  <button
                    type="button"
                    onClick={resetAddressForm}
                    className="min-h-11 rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-4 py-2 text-sm"
                  >
                    Cancel
                  </button>
                ) : null}
              </div>
            </form>

            {addressMessage ? <p className="mt-2 text-sm text-[#7A9E7E]">{addressMessage}</p> : null}

            <ul className="mt-3 space-y-2 text-sm">
              {addressesLoading ? (
                <li className="rounded-xl border border-[#E8B04A]/25 bg-[#FFF8F0] p-3 text-[#333333]/75">
                  Loading addresses...
                </li>
              ) : null}
              {!addressesLoading && addresses.length === 0 ? (
                <li className="rounded-xl border border-[#E8B04A]/25 bg-[#FFF8F0] p-3 text-[#333333]/75">
                  No saved addresses yet.
                </li>
              ) : null}
              {addresses.map((item) => (
                <li
                  key={item.id}
                  className={`rounded-xl border bg-[#FFF8F0] p-3 ${
                    editingAddressId === String(item.id) ? "border-[#E8B04A]" : "border-[#E8B04A]/25"
                  }`}
                >
                  <div className="flex flex-wrap items-start justify-between gap-3">
                    <div>
                      <p className="font-medium">
                        {item.label || "Address"} {item.is_default ? <span className="text-xs text-[#7A9E7E]">(Default)</span> : null}
                      </p>
                      <p className="text-[#333333]/75">
                        {item.address_line_1}
                        {item.city ? `, ${item.city}` : ""}
                      </p>
                    </div>
                    <div className="flex gap-2">
                      <button
                        type="button"
                        onClick={() => handleEditAddress(item)}
                        className="min-h-11 rounded-xl border border-[#E8B04A]/35 bg-[#FFF8F0] px-3 py-1 text-xs"
                      >
                        Edit
                      </button>
                      <button
                        type="button"
                        onClick={() => handleDeleteAddress(item.id)}
                        className="min-h-11 rounded-xl border border-[#D96C4A]/35 bg-[#FFF8F0] px-3 py-1 text-xs text-[#D96C4A]"
                      >
                        Delete
                      </button>
                    </div>
                  </div>
                </li>
              ))}
            </ul>
          </div>

          <div className="rounded-2xl border border-[#E8B04A]/25 bg-[#F2E6D8] p-5 text-[#333333] shadow-[0_8px_20px_rgba(51,51,51,0.06)]">
            <h2 className="font-semibold">Session</h2>
            <p className="mt-1 text-sm text-[#333333]/75">
              Use logout to end this account session on this device.
            </p>
            <button
              type="button"
              onClick={logout}
              className="mt-3 min-h-11 rounded-xl border border-[#D96C4A]/35 bg-[#FFF8F0] px-4 py-2 text-sm text-[#D96C4A]"
            >
              Logout
            </button>
          </div>
        </article>
      </section>
    </PageScaffold>
  );
}
